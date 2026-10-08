<?php

namespace App\Services\Inicial;

use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eievaluationp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Asistente de importación de PLANES DE EVALUACIÓN desde s2526.
 *
 * Como el semanal pero con dos diferencias propias del documento:
 *  · la cabecera trae `lapso_id`: el plan importado se ancla al LAPSO EN CURSO
 *    (no se copia el lapso viejo);
 *  · las hijas son POSICIONES (`eievaluationps`), con `pevaluacion_id` a
 *    remapear (misma asignatura+sección) o a omitir con conteo. No hay
 *    estrategias ni revisiones.
 */
class ImportadorEievaluationk extends ImportadorDocumento
{
    protected function tablaCabecera(): string
    {
        return 'eievaluationks';
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

        $planes = DB::connection($this->origen)->table('eievaluationks as p')
            ->select('p.id', 'p.grado_id', 'p.seccion_id', 'p.finicial', 'p.ffinal', 'p.observaciones', 'p.profesor_id', 'pr.name as prof_name', 'pr.lastname as prof_lastname', 'g.name as grado', 's.name as seccion')
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

        $areasPorPlan = DB::connection($this->origen)->table('eievaluationps as rs')
            ->select('rs.eievaluationk_id', 'a.id as asignatura_id', 'a.name as asignatura')
            ->join('pevaluacions as p', 'p.id', '=', 'rs.pevaluacion_id')
            ->join('pensums as pens', 'pens.id', '=', 'p.pensum_id')
            ->join('asignaturas as a', 'a.id', '=', 'pens.asignatura_id')
            ->whereIn('rs.eievaluationk_id', $ids)->distinct()->get()->groupBy('eievaluationk_id');

        $posicionesPorPlan = DB::connection($this->origen)->table('eievaluationps')
            ->selectRaw('eievaluationk_id, COUNT(*) as n')
            ->whereIn('eievaluationk_id', $ids)->groupBy('eievaluationk_id')
            ->pluck('n', 'eievaluationk_id');

        return $planes->map(function ($p) use ($posicionesPorPlan, $areasPorPlan) {
            $areas = ($areasPorPlan->get($p->id) ?? collect())->sortBy('asignatura')->values();

            return [
                'id' => (int) $p->id,
                'profesor_origen_id' => $p->profesor_id !== null ? (int) $p->profesor_id : null,
                'profesor_origen' => trim(($p->prof_lastname ?? '').' '.($p->prof_name ?? '')) ?: '—',
                'grado' => $p->grado ?? '—',
                'seccion' => $p->seccion ?? '—',
                'finicial' => $p->finicial,
                'ffinal' => $p->ffinal,
                'diagnostico' => $p->observaciones,
                'estrategias' => 0,
                'resumenes' => (int) ($posicionesPorPlan->get($p->id) ?? 0),
                'asignatura_ids' => $areas->pluck('asignatura_id')->map(fn ($v) => (int) $v)->all(),
                'asignaturas' => $areas->pluck('asignatura')->all(),
            ];
        })->values();
    }

    protected function modeloCabecera(): string
    {
        return Eievaluationk::class;
    }

    protected function campoDiagnostico(): string
    {
        return 'observaciones';
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

    protected function crearCabecera(array $carga, object $legacy, int $profesorId): Eievaluationk
    {
        return Eievaluationk::create([
            'profesor_id' => $profesorId,
            'grado_id' => $carga['grado_id'],
            // El lapso viejo no se copia: el plan nace en el lapso en curso.
            'lapso_id' => \App\Models\app\Academy\Lapso::current()?->id,
            'seccion_id' => $carga['seccion_id'],
            'finicial' => $legacy->finicial,
            'ffinal' => $legacy->ffinal,
            'observaciones' => $legacy->observaciones,
            'recomendacion' => null,
            'asistencia' => $legacy->asistencia,
            'observacion' => $legacy->observacion,
        ]);
    }

    protected function copiarHijasSinPevaluacion(object $nuevo, int $legacyId): array
    {
        return ['estrategias' => 0];
    }

    protected function copiarHijasConPevaluacion(object $nuevo, int $legacyId, array $carga, int $profesorId): array
    {
        $ok = 0;
        $omitidos = 0;

        foreach (DB::connection($this->origen)->table('eievaluationps')
            ->where('eievaluationk_id', $legacyId)->orderBy('id')->get() as $r) {
            $destino = $this->pevaluacionVigenteParaResumen($r->pevaluacion_id, $carga, $profesorId);

            if (! $destino) {
                $omitidos++;

                continue;
            }

            Eievaluationp::create([
                'eievaluationk_id' => $nuevo->id,
                'pevaluacion_id' => $destino,
                'fecha' => $r->fecha,
                'nombre_ninos' => $r->nombre_ninos,
                'aprendizaje_alcanzado' => $r->aprendizaje_alcanzado,
                'componente' => $r->componente,
                'indicadores' => $r->indicadores,
                'instrumento' => $r->instrumento,
                'observacion' => $r->observacion,
                'order' => $r->order,
            ]);
            $ok++;
        }

        return ['resumenes_ok' => $ok, 'resumenes_omitidos' => $omitidos];
    }
}
