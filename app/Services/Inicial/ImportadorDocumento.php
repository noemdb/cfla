<?php

namespace App\Services\Inicial;

use App\Models\app\Academy\Lapso;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Base de los asistentes de importación de documentos de Educación Inicial
 * desde s2526.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ UNA BASE (y no un servicio por documento copiado)
 * ─────────────────────────────────────────────────────────────────────────────
 * La importación de `eiplanningwks` (semanal) es idéntica en estructura a la de
 * `eiplanningbwks` (quincenal) y muy parecida a la de proyecto/especial: misma
 * cabecera (profesor/grado/sección), mismas hijas sin pevaluación (estrategias)
 * e hijas con pevaluación (resúmenes que hay que remapear), mismo anti-duplicado
 * por cabecera. Copiar el servicio 6 veces reproduciría ~2.000 líneas casi
 * iguales y el bug de un documento se corregiría solo en uno.
 *
 * Esta clase fija el TEMPLATE (`importar`) y lo que es idéntico para todos
 * (`cargasVigentes`, `origenDisponible`, `pevaluacionVigenteParaResumen`,
 * `existeIdentico`). Cada documento declara SOLO lo que le es propio:
 * tabla/modelo de cabecera, qué hijas copiar y qué remapear.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * REGLAS (idénticas para todos los documentos)
 * ─────────────────────────────────────────────────────────────────────────────
 *  · El plan legacy entra solo si su contexto (grado, sección, y lapso en
 *    evaluación) coincide con la carga VIGENTE del docente (lapso en curso,
 *    pestudio 6). No se filtra por `profesor_id` de origen: sirve como plantilla.
 *  · Cada plan nace con ID NUEVO (nunca se copia el id legacy).
 *  · Las hijas sin pevaluación se copian tal cual; las que llevan `pevaluacion_id`
 *    se remapean a la pevaluación vigente (misma asignatura+sección) o se omiten
 *    con conteo.
 *  · Anti-duplicado por cabecera completa.
 *  · El proyecto vinculado (`eiprojectk_id`) no se copia (ids del período viejo).
 *  · s2526 es SOLO lectura; todo va dentro de `DatabaseTransactions`.
 */
abstract class ImportadorDocumento
{
    public function __construct(
        protected string $origen = 's2526',
    ) {}

    /**
     * Cargas vigentes del docente: lapso en curso + pestudio 6.
     *
     * @return Collection<int, array{pevaluacion_id: int, grado_id: int, seccion_id: int, asignatura_id: int}>
     */
    public function cargasVigentes(int $profesorId): Collection
    {
        $lapsoId = Lapso::current()?->id;

        if (! $lapsoId) {
            return collect();
        }

        return \App\Models\app\Academy\Pevaluacion::query()
            ->select('pevaluacions.id as pevaluacion_id', 'pensums.grado_id', 'pevaluacions.seccion_id', 'pensums.asignatura_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->where('pevaluacions.profesor_id', $profesorId)
            ->where('pevaluacions.lapso_id', $lapsoId)
            ->where('grados.pestudio_id', config('inicial.pestudio_id'))
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->get()
            ->map(fn ($fila) => [
                'pevaluacion_id' => (int) $fila->pevaluacion_id,
                'grado_id' => (int) $fila->grado_id,
                'seccion_id' => (int) $fila->seccion_id,
                'asignatura_id' => (int) $fila->asignatura_id,
            ]);
    }

    /** ¿La conexión legacy responde? (Solo lectura.) */
    public function origenDisponible(): bool
    {
        try {
            DB::connection($this->origen)->select('SELECT 1');

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Importa los documentos legacy indicados al período actual del docente.
     *
     * @param  array<int, int|string>  $legacyIds
     * @return array{creados: array<int, array<string, mixed>>, omitidos: array<int, array{legacy_id: int, motivo: string}>}
     */
    public function importar(array $legacyIds, int $profesorId): array
    {
        $legacyIds = array_values(array_unique(array_map('intval', $legacyIds)));

        if ($legacyIds === []) {
            throw new \InvalidArgumentException('Seleccione al menos un plan para importar.');
        }

        $cargas = $this->cargasVigentes($profesorId);

        if ($cargas->isEmpty()) {
            throw new \RuntimeException('El docente no tiene carga vigente en Educación Inicial para anclar la importación.');
        }

        $reporte = ['creados' => [], 'omitidos' => []];

        foreach ($legacyIds as $legacyId) {
            $legacy = DB::connection($this->origen)->table($this->tablaCabecera())->where('id', $legacyId)->first();

            if (! $legacy) {
                $reporte['omitidos'][] = ['legacy_id' => $legacyId, 'motivo' => 'El plan ya no existe en s2526.'];

                continue;
            }

            $carga = $this->coincideConCarga($cargas, $legacy);

            if (! $carga) {
                $reporte['omitidos'][] = ['legacy_id' => $legacyId, 'motivo' => $this->motivoFueraDeCarga()];

                continue;
            }

            if ($this->existeIdentico($profesorId, $carga, $legacy)) {
                $reporte['omitidos'][] = ['legacy_id' => $legacyId, 'motivo' => 'Ya existe un plan idéntico en el período actual (duplicado evitado).'];

                continue;
            }

            $reporte['creados'][] = DB::transaction(function () use ($legacy, $legacyId, $profesorId, $carga) {
                $nuevo = $this->crearCabecera($carga, $legacy, $profesorId);

                $sinPevaluacion = $this->copiarHijasSinPevaluacion($nuevo, $legacyId);
                $conPevaluacion = $this->copiarHijasConPevaluacion($nuevo, $legacyId, $carga, $profesorId);

                return [
                    'legacy_id' => $legacyId,
                    'nuevo_id' => $nuevo->id,
                    ...$sinPevaluacion,
                    ...$conPevaluacion,
                    'proyecto_desvinculado' => isset($legacy->eiprojectk_id) && $legacy->eiprojectk_id !== null,
                ];
            });
        }

        return $reporte;
    }

    /**
     * ¿Ya existe un plan idéntico en el período actual?
     *
     * El criterio de duplicidad es la cabecera completa declarada por
     * {@see camposDuplicidad()}: profesor, grado, sección, fechas y diagnóstico.
     */
    protected function existeIdentico(int $profesorId, array $carga, object $legacy): bool
    {
        /** @var class-string $modelo */
        $modelo = $this->modeloCabecera();

        $query = $modelo::query()
            ->where('profesor_id', $profesorId)
            ->where('grado_id', $carga['grado_id'])
            ->where('seccion_id', $carga['seccion_id'])
            ->whereDate('finicial', substr((string) $legacy->finicial, 0, 10))
            ->whereDate('ffinal', substr((string) $legacy->ffinal, 0, 10))
            ->get([$this->campoDiagnostico()]);

        $diagnostico = trim((string) $legacy->{$this->campoDiagnostico()});

        return $query->contains(fn ($plan) => trim((string) $plan->{$this->campoDiagnostico()}) === $diagnostico);
    }

    /**
     * Pevaluación vigente del docente equivalente a la de una hija legacy
     * (resumen/posición/actividad): misma asignatura (vía pensum) y misma
     * sección, en el lapso en curso.
     */
    protected function pevaluacionVigenteParaResumen(?int $legacyPevaluacionId, array $carga, int $profesorId): ?int
    {
        if (! $legacyPevaluacionId) {
            return null;
        }

        try {
            $legacyPev = DB::connection($this->origen)->table('pevaluacions as p')
                ->select('p.seccion_id', 'pens.asignatura_id')
                ->join('pensums as pens', 'pens.id', '=', 'p.pensum_id')
                ->where('p.id', $legacyPevaluacionId)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }

        if (! $legacyPev) {
            return null;
        }

        $cargas = $this->cargasVigentes($profesorId);

        $match = $cargas->first(fn ($c) => $c['seccion_id'] === (int) $legacyPev->seccion_id
            && $c['asignatura_id'] === (int) $legacyPev->asignatura_id);

        return $match ? $match['pevaluacion_id'] : null;
    }

    // ─── Puntos de extensión por documento ──────────────────────

    /** Tabla legacy de la cabecera (p. ej. `eiplanningwks`). */
    abstract protected function tablaCabecera(): string;

    /** Modelo Eloquent de destino de la cabecera. */
    abstract protected function modeloCabecera(): string;

    /** Campo de cabecera que hace de "diagnóstico" (duplicidad + candidato). */
    abstract protected function campoDiagnostico(): string;

    /** ¿Este legacy encaja en alguna carga vigente? Devuelve la carga o null. */
    abstract protected function coincideConCarga(Collection $cargas, object $legacy): ?array;

    /** Motivo cuando no encaja en la carga vigente. */
    abstract protected function motivoFueraDeCarga(): string;

    /** Crea la cabecera en destino, anclada a la carga vigente, con ID nuevo. */
    abstract protected function crearCabecera(array $carga, object $legacy, int $profesorId): object;

    /**
     * Copia las hijas SIN pevaluación (estrategias, reviews).
     *
     * @return array<string, int> p. ej. `['estrategias' => n]`
     */
    abstract protected function copiarHijasSinPevaluacion(object $nuevo, int $legacyId): array;

    /**
     * Copia/remapea las hijas CON pevaluación (resúmenes, posiciones, actos).
     *
     * @return array<string, int> p. ej. `['resumenes_ok' => n, 'resumenes_omitidos' => n]`
     */
    abstract protected function copiarHijasConPevaluacion(object $nuevo, int $legacyId, array $carga, int $profesorId): array;
}
