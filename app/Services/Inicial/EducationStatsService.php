<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eifinalk;
use Illuminate\Database\Eloquent\Builder;

/**
 * Indicadores de planificación de Educación Inicial (perspectiva Evaluación).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * PORT DE `saefl/s2526/app/Services/EducationStatsService.php`
 * ─────────────────────────────────────────────────────────────────────────────
 * El legacy repetía el bloque `if ($grado_id) … if ($profesor_id) …` seis veces,
 * una por documento, y sumaba a mano. Aquí los filtros son un mapa y se aplican
 * en bucle, de modo que un filtro nuevo (sección) se añade en un sitio.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * DIFERENCIAS DELIBERADAS CON EL LEGACY
 * ─────────────────────────────────────────────────────────────────────────────
 *  · `seccion_id`: el legacy lo filtraba en la vista pero no lo pasaba al
 *    servicio, así que los contadores no cuadraban con las tablas que tenía
 *    encima. Aquí los filtros son los mismos para las cifras y para las
 *    pestañas (bug "filtros de sección uniformes", F5).
 *  · `eifinalks` se filtra por `pevaluacion.seccion_id` y no por un `grado_id`
 *    que esa tabla no tiene: hereda grado y sección de la carga académica.
 *  · Los MÁXIMOS salen de `config('inicial.max_number')` (decisión D6) y valen
 *    `null` porque las columnas `peducativos.max_number_*` no existen ni en
 *    `s2526` ni en `s2627`. Con máximo `null` la vista muestra el dato ABSOLUTO
 *    y nunca un porcentaje ni una barra contra un techo inventado. El legacy
 *    pintaba además badges fijos (+12 %, +5, 67 %) que no medían nada: no se
 *    portan en ninguna circunstancia.
 */
class EducationStatsService
{
    /**
     * Contadores de los 6 documentos, en el mismo orden de las pestañas.
     *
     * @var array<int, string>
     */
    private const DOCUMENTOS = [
        'eiplanningwks',
        'eiplanningbwks',
        'eiprojectks',
        'eispecialks',
        'eievaluationks',
        'eifinalks',
    ];

    /**
     * @return array<string, int|null> contadores + `totalRecords`,
     *                                 `activeProjects`, `completedEvaluations`
     */
    public function getEducationStats(
        ?int $profesorId = null,
        ?int $gradoId = null,
        ?int $seccionId = null
    ): array {
        $filtros = array_filter([
            'profesor_id' => $profesorId,
            'grado_id' => $gradoId,
            'seccion_id' => $seccionId,
        ], fn ($valor) => $valor !== null);

        $stats = [];

        foreach (self::DOCUMENTOS as $entidad) {
            $stats[$entidad] = $this->contar($entidad, $filtros);
        }

        $stats['totalRecords'] = array_sum($stats);

        // Proyectos abiertos: con fecha de inicio y sin fecha de cierre.
        $stats['activeProjects'] = $this->contar(
            'eiprojectks',
            $filtros,
            fn (Builder $q) => $q->whereNotNull('finicial')->whereNull('ffinal')
        );

        // Planes de evaluación cerrados.
        $stats['completedEvaluations'] = $this->contar(
            'eievaluationks',
            $filtros,
            fn (Builder $q) => $q->whereNotNull('ffinal')
        );

        return $stats;
    }

    /**
     * Detalle de los 6 indicadores con su máximo configurable.
     *
     * Se separa de {@see getEducationStats()} porque la vista necesita el
     * CONJUNTO (contador, título, máximo) y pedirlo al servicio evitaría que la
     * BladeODY repita `config('inicial.max_number.…')` en seis sitios.
     *
     * @return array<int, array{clave: string, titulo: string, conteo: int, maximo: ?int, porcentaje: ?int}>
     */
    public function indicadores(?int $profesorId = null, ?int $gradoId = null, ?int $seccionId = null): array
    {
        $stats = $this->getEducationStats($profesorId, $gradoId, $seccionId);

        return array_map(function (string $entidad) use ($stats): array {
            $conteo = (int) ($stats[$entidad] ?? 0);
            $maximo = config('inicial.max_number.'.$entidad);

            return [
                'clave' => $entidad,
                'titulo' => RegistroInicial::titulo($entidad),
                'conteo' => $conteo,
                // `null` mientras nadie configure el máximo: la vista DEBE
                // entonces mostrar el dato absoluto y no un porcentaje.
                'maximo' => is_numeric($maximo) ? (int) $maximo : null,
                'porcentaje' => is_numeric($maximo) && (int) $maximo > 0
                    ? (int) min(100, round($conteo / (int) $maximo * 100))
                    : null,
            ];
        }, self::DOCUMENTOS);
    }

    /**
     * @param  array<string, int>  $filtros
     */
    private function contar(string $entidad, array $filtros, ?callable $extra = null): int
    {
        if ($entidad === 'eifinalks') {
            return $this->contarInformesFinales($filtros);
        }

        /** @var class-string $modelo */
        $modelo = RegistroInicial::modelo($entidad);

        $query = $modelo::query();

        foreach ($filtros as $columna => $valor) {
            $query->where($columna, $valor);
        }

        if ($extra) {
            $extra($query);
        }

        return $query->count();
    }

    /**
     * Los informes finales no tienen `profesor_id` ni `grado_id`: los heredan de
     * la carga académica, así que el filtro viaja por `pevaluacion`.
     *
     * @param  array<string, int>  $filtros
     */
    private function contarInformesFinales(array $filtros): int
    {
        if ($filtros === []) {
            return Eifinalk::count();
        }

        $query = Eifinalk::query()->whereHas('pevaluacion', function ($pevaluacion) use ($filtros) {
            foreach ($filtros as $columna => $valor) {
                // El grado del informe final vive en el pensum de la carga, y la
                // sección en la propia carga: son rutas distintas aunque ambas
                // se filtren con el mismo `grado_id`/`seccion_id` de la vista.
                if ($columna === 'grado_id') {
                    $pevaluacion->whereHas('pensum', fn ($pensum) => $pensum->where('grado_id', $valor));
                } else {
                    $pevaluacion->where($columna, $valor);
                }
            }
        });

        return $query->count();
    }
}
