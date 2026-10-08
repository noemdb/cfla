<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eiprojectkstrategy;
use App\Models\app\Inicial\Eiprojectreview;
use App\Models\app\Inicial\Eiprojectsummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Asistente de importación de PROYECTOS DE AULA desde s2526.
 *
 * Como el semanal/quincenal pero con una hija más sin pevaluación (reviews):
 * estrategias + revisiones se copian tal cual; resúmenes se remapean a la
 * pevaluación vigente (misma asignatura+sección) o se omiten con conteo.
 */
class ImportadorEiprojectk extends ImportadorDocumento
{
    protected function tablaCabecera(): string
    {
        return 'eiprojectks';
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function candidatos(int $profesorId): Collection
    {
        $cargas = $this->cargasVigentes($profesorId);

        if ($cargas->isEmpty()) {
            return collect();
        }

        $pares = $cargas->map(fn ($c) => $c['grado_id'].'-'.$c['seccion_id'])->unique()->all();

        $planes = DB::connection($this->origen)->table('eiprojectks as p')
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

        $estrategiasPorPlan = DB::connection($this->origen)->table('eiprojectkstrategies')
            ->selectRaw('eiprojectk_id, COUNT(*) as n')
            ->whereIn('eiprojectk_id', $ids)->groupBy('eiprojectk_id')
            ->pluck('n', 'eiprojectk_id');

        $areasPorPlan = DB::connection($this->origen)->table('eiprojectsummaries as rs')
            ->select('rs.eiprojectk_id', 'a.id as asignatura_id', 'a.name as asignatura')
            ->join('pevaluacions as p', 'p.id', '=', 'rs.pevaluacion_id')
            ->join('pensums as pens', 'pens.id', '=', 'p.pensum_id')
            ->join('asignaturas as a', 'a.id', '=', 'pens.asignatura_id')
            ->whereIn('rs.eiprojectk_id', $ids)->distinct()->get()->groupBy('eiprojectk_id');

        $resumenesPorPlan = DB::connection($this->origen)->table('eiprojectsummaries')
            ->selectRaw('eiprojectk_id, COUNT(*) as n')
            ->whereIn('eiprojectk_id', $ids)->groupBy('eiprojectk_id')
            ->pluck('n', 'eiprojectk_id');

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
        return Eiprojectk::class;
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

    protected function crearCabecera(array $carga, object $legacy, int $profesorId): Eiprojectk
    {
        return Eiprojectk::create([
            'profesor_id' => $profesorId,
            'grado_id' => $carga['grado_id'],
            'seccion_id' => $carga['seccion_id'],
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

        foreach (DB::connection($this->origen)->table('eiprojectkstrategies')
            ->where('eiprojectk_id', $legacyId)->orderBy('id')->get() as $e) {
            Eiprojectkstrategy::create([
                'eiprojectk_id' => $nuevo->id,
                'day_of_week' => $e->day_of_week,
                'momento_rutina_diaria' => $e->momento_rutina_diaria,
                'lunes' => $e->lunes,
                'martes' => $e->martes,
                'miercoles' => $e->miercoles,
                'jueves' => $e->jueves,
                'viernes' => $e->viernes,
                'order' => $e->order,
                'description' => $e->description,
            ]);
            $estrategias++;
        }

        $revisiones = 0;

        foreach (DB::connection($this->origen)->table('eiprojectreviews')
            ->where('eiprojectk_id', $legacyId)->orderBy('id')->get() as $r) {
            Eiprojectreview::create([
                'eiprojectk_id' => $nuevo->id,
                'posibles_temas_interes' => $r->posibles_temas_interes,
                'eleccion_tema_nombre' => $r->eleccion_tema_nombre,
                'que_sabe' => $r->que_sabe,
                'que_desean_aprender' => $r->que_desean_aprender,
                'que_necesitamos' => $r->que_necesitamos,
                'quienes_nos_pueden_apoyar' => $r->quienes_nos_pueden_apoyar,
                'order' => $r->order,
                'estrategias' => $r->estrategias,
            ]);
            $revisiones++;
        }

        return ['estrategias' => $estrategias, 'revisiones' => $revisiones];
    }

    protected function copiarHijasConPevaluacion(object $nuevo, int $legacyId, array $carga, int $profesorId): array
    {
        $ok = 0;
        $omitidos = 0;

        foreach (DB::connection($this->origen)->table('eiprojectsummaries')
            ->where('eiprojectk_id', $legacyId)->orderBy('id')->get() as $r) {
            $destino = $this->pevaluacionVigenteParaResumen($r->pevaluacion_id, $carga, $profesorId);

            if (! $destino) {
                $omitidos++;

                continue;
            }

            Eiprojectsummary::create([
                'eiprojectk_id' => $nuevo->id,
                'pevaluacion_id' => $destino,
                'componente' => $r->componente,
                'objetivo' => $r->objetivo,
                'aprendizaje_esperado' => $r->aprendizaje_esperado,
                'indicadores' => $r->indicadores,
                'linea_investigacion' => $r->linea_investigacion,
                'enfasis_curriculares' => $r->enfasis_curriculares,
                'order' => $r->order,
                'estrategias' => $r->estrategias,
            ]);
            $ok++;
        }

        return ['resumenes_ok' => $ok, 'resumenes_omitidos' => $omitidos];
    }
}
