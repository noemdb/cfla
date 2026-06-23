<?php

namespace App\Services;

use App\Models\app\Inicial\{
    Eiplanningwk, 
    Eiplanningbwk, 
    Eiprojectk, 
    Eispecialk, 
    Eievaluationk, 
    Eifinalk
};
use Carbon\Carbon;

class EducationStatsService
{
    public function getEducationStats($profesor_id = null, $grado_id = null): array
    {
        $stats = [];
        
        // Planes semanales
        $eiplanningwks = Eiplanningwk::query();
        if ($grado_id) $eiplanningwks->where('grado_id', $grado_id);
        if ($profesor_id) $eiplanningwks->where('profesor_id', $profesor_id);
        $stats['eiplanningwks'] = $eiplanningwks->count();

        // Planes quincenales
        $eiplanningbwks = Eiplanningbwk::query();
        if ($grado_id) $eiplanningbwks->where('grado_id', $grado_id);
        if ($profesor_id) $eiplanningbwks->where('profesor_id', $profesor_id);
        $stats['eiplanningbwks'] = $eiplanningbwks->count();

        // Proyectos
        $eiprojectks = Eiprojectk::query();
        if ($grado_id) $eiprojectks->where('grado_id', $grado_id);
        if ($profesor_id) $eiprojectks->where('profesor_id', $profesor_id);
        $stats['eiprojectks'] = $eiprojectks->count();

        // Planes especiales
        $eispecialks = Eispecialk::query();
        if ($grado_id) $eispecialks->where('grado_id', $grado_id);
        if ($profesor_id) $eispecialks->where('profesor_id', $profesor_id);
        $stats['eispecialks'] = $eispecialks->count();

        // Evaluaciones
        $eievaluationks = Eievaluationk::query();
        if ($grado_id) $eievaluationks->where('grado_id', $grado_id);
        if ($profesor_id) $eievaluationks->where('profesor_id', $profesor_id);
        $stats['eievaluationks'] = $eievaluationks->count();

        // Informes finales
        $eifinalks = Eifinalk::query();
        if ($grado_id || $profesor_id) {
            $eifinalks->whereHas('pevaluacion', function($query) use ($grado_id, $profesor_id) {
                if ($grado_id) {
                    $query->whereHas('pensum', function($q) use ($grado_id) {
                        $q->where('grado_id', $grado_id);
                    });
                }
                if ($profesor_id) {
                    $query->where('profesor_id', $profesor_id);
                }
            });
        }
        $stats['eifinalks'] = $eifinalks->count();

        // Estadísticas calculadas
        $stats['totalRecords'] = array_sum([
            $stats['eiplanningwks'],
            $stats['eiplanningbwks'],
            $stats['eiprojectks'],
            $stats['eispecialks'],
            $stats['eievaluationks'],
            $stats['eifinalks']
        ]);

        // Proyectos activos (con fecha inicial pero sin fecha final)
        $activeProjectsQuery = Eiprojectk::whereNotNull('finicial')->whereNull('ffinal');
        if ($grado_id) $activeProjectsQuery->where('grado_id', $grado_id);
        if ($profesor_id) $activeProjectsQuery->where('profesor_id', $profesor_id);
        $stats['activeProjects'] = $activeProjectsQuery->count();

        // Evaluaciones completadas (con fecha final)
        $completedEvaluationsQuery = Eievaluationk::whereNotNull('ffinal');
        if ($grado_id) $completedEvaluationsQuery->where('grado_id', $grado_id);
        if ($profesor_id) $completedEvaluationsQuery->where('profesor_id', $profesor_id);
        $stats['completedEvaluations'] = $completedEvaluationsQuery->count();

        return $stats;
    }

    public function getStatsAsJson($profesor_id = null, $grado_id = null): string
    {
        return json_encode($this->getEducationStats($profesor_id, $grado_id));
    }
}