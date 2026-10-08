<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Asistente de importación de PLANIFICACIONES SEMANALES desde s2526.
 *
 * Declara solo lo propio del documento (tabla/modelo de cabecera, hijas a
 * copiar y a remapear); el resto del flujo vive en {@see ImportadorDocumento}.
 */
class ImportadorEiplanningwk extends ImportadorDocumento
{
    protected function tablaCabecera(): string
    {
        return 'eiplanningwks';
    }

    /**
     * Planes legacy candidatos: los de s2526 cuyo (grado, sección) coincide con
     * alguna carga vigente del docente.
     *
     * Cada candidato trae su docente origen y sus ÁREAS (asignaturas de los
     * resúmenes, vía `pevaluacion → pensum → asignatura`): la cabecera no tiene
     * área propia. Las áreas se resuelven en consultas agrupadas (no N+1).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function candidatos(int $profesorId): Collection
    {
        $cargas = $this->cargasVigentes($profesorId);

        if ($cargas->isEmpty()) {
            return collect();
        }

        $pares = $cargas->map(fn ($c) => $c['grado_id'].'-'.$c['seccion_id'])->unique()->all();

        $planes = DB::connection($this->origen)->table('eiplanningwks as p')
            ->select('p.id', 'p.grado_id', 'p.seccion_id', 'p.finicial', 'p.ffinal', 'p.diagnostico', 'p.profesor_id', 'pr.name as prof_name', 'pr.lastname as prof_lastname', 'g.name as grado', 's.name as seccion')
            ->leftJoin('profesors as pr', 'pr.id', '=', 'p.profesor_id')
            ->leftJoin('grados as g', 'g.id', '=', 'p.grado_id')
            ->leftJoin('seccions as s', 's.id', '=', 'p.seccion_id')
            ->orderBy('p.finicial', 'desc')
            ->orderBy('p.id', 'desc')
            ->get()
            ->filter(fn ($p) => in_array($p->grado_id.'-'.$p->seccion_id, $pares, true));

        if ($planes->isEmpty()) {
            return collect();
        }

        $ids = $planes->pluck('id')->all();

        $estrategiasPorPlan = DB::connection($this->origen)->table('eiplanningwstrategies')
            ->selectRaw('eiplanningwk_id, COUNT(*) as n')
            ->whereIn('eiplanningwk_id', $ids)->groupBy('eiplanningwk_id')
            ->pluck('n', 'eiplanningwk_id');

        $areasPorPlan = DB::connection($this->origen)->table('eiplanningwsummaries as rs')
            ->select('rs.eiplanningwk_id', 'a.id as asignatura_id', 'a.name as asignatura')
            ->join('pevaluacions as p', 'p.id', '=', 'rs.pevaluacion_id')
            ->join('pensums as pens', 'pens.id', '=', 'p.pensum_id')
            ->join('asignaturas as a', 'a.id', '=', 'pens.asignatura_id')
            ->whereIn('rs.eiplanningwk_id', $ids)->distinct()->get()->groupBy('eiplanningwk_id');

        $resumenesPorPlan = DB::connection($this->origen)->table('eiplanningwsummaries')
            ->selectRaw('eiplanningwk_id, COUNT(*) as n')
            ->whereIn('eiplanningwk_id', $ids)->groupBy('eiplanningwk_id')
            ->pluck('n', 'eiplanningwk_id');

        return $planes->map(function ($p) use ($estrategiasPorPlan, $resumenesPorPlan, $areasPorPlan) {
            $areas = ($areasPorPlan->get($p->id) ?? collect())->sortBy('asignatura')->values();

            return [
                'id' => (int) $p->id,
                'profesor_origen_id' => $p->profesor_id !== null ? (int) $p->profesor_id : null,
                'profesor_origen' => trim(($p->prof_lastname ?? '').' '.($p->prof_name ?? '')) ?: '—',
                'grado' => $p->grado ?? '—',
                'seccion' => $p->seccion ?? '—',
                'finicial' => $p->finicial,
                'ffinal' => $p->ffinal,
                'diagnostico' => $p->diagnostico,
                'estrategias' => (int) ($estrategiasPorPlan->get($p->id) ?? 0),
                'resumenes' => (int) ($resumenesPorPlan->get($p->id) ?? 0),
                'asignatura_ids' => $areas->pluck('asignatura_id')->map(fn ($v) => (int) $v)->all(),
                'asignaturas' => $areas->pluck('asignatura')->all(),
            ];
        })->values();
    }

    protected function modeloCabecera(): string
    {
        return Eiplanningwk::class;
    }

    protected function campoDiagnostico(): string
    {
        return 'diagnostico';
    }

    protected function coincideConCarga(Collection $cargas, object $legacy): ?array
    {
        return $cargas->first(fn ($c) => $c['grado_id'] === (int) $legacy->grado_id
            && $c['seccion_id'] === (int) $legacy->seccion_id);
    }

    protected function motivoFueraDeCarga(): string
    {
        return 'Su grado/sección no coincide con ninguna carga vigente.';
    }

    protected function crearCabecera(array $carga, object $legacy, int $profesorId): Eiplanningwk
    {
        return Eiplanningwk::create([
            'profesor_id' => $profesorId,
            'grado_id' => $carga['grado_id'],
            'seccion_id' => $carga['seccion_id'],
            // El proyecto vinculado es del período viejo: sus ids no existen
            // aquí y copiarlo dejaría un FK colgando.
            'eiprojectk_id' => null,
            'finicial' => $legacy->finicial,
            'ffinal' => $legacy->ffinal,
            'tiempo_ejecucion' => $legacy->tiempo_ejecucion,
            'diagnostico' => $legacy->diagnostico,
            'observacion' => $legacy->observacion,
        ]);
    }

    protected function copiarHijasSinPevaluacion(object $nuevo, int $legacyId): array
    {
        $estrategias = 0;

        foreach (DB::connection($this->origen)->table('eiplanningwstrategies')
            ->where('eiplanningwk_id', $legacyId)->orderBy('id')->get() as $e) {
            Eiplanningwstrategy::create([
                'eiplanningwk_id' => $nuevo->id,
                'day_of_week' => $e->day_of_week,
                'momento_rutina_diaria' => $e->momento_rutina_diaria,
                'lunes' => $e->lunes,
                'martes' => $e->martes,
                'miercoles' => $e->miercoles,
                'jueves' => $e->jueves,
                'viernes' => $e->viernes,
                'order' => $e->order,
            ]);
            $estrategias++;
        }

        return ['estrategias' => $estrategias];
    }

    protected function copiarHijasConPevaluacion(object $nuevo, int $legacyId, array $carga, int $profesorId): array
    {
        $ok = 0;
        $omitidos = 0;

        foreach (DB::connection($this->origen)->table('eiplanningwsummaries')
            ->where('eiplanningwk_id', $legacyId)->orderBy('id')->get() as $r) {
            $destino = $this->pevaluacionVigenteParaResumen($r->pevaluacion_id, $carga, $profesorId);

            if (! $destino) {
                $omitidos++;

                continue;
            }

            Eiplanningwsummary::create([
                'eiplanningwk_id' => $nuevo->id,
                'pevaluacion_id' => $destino,
                'componente' => $r->componente,
                'objetivo' => $r->objetivo,
                'aprendizaje_esperado' => $r->aprendizaje_esperado,
                'indicadores' => $r->indicadores,
                'linea_investigacion' => $r->linea_investigacion,
                'enfasis_curriculares' => $r->enfasis_curriculares,
                'order' => $r->order,
            ]);
            $ok++;
        }

        return ['resumenes_ok' => $ok, 'resumenes_omitidos' => $omitidos];
    }
}
