<?php
namespace App\Models\app\Pescolar\Functions\Grado;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

trait Indicators {

    public function goal_notas_load($lapso_id=null)
    {
        $lapso = ($lapso_id) ? Lapso::findOrFail($lapso_id) : null ;

        $seccions = Seccion::select('seccions.id',DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')

            ->where('grados.id',$this->id)

            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('grados.deleted_at')

            ->groupBy('seccions.id');

        $seccions = ($lapso) ? $seccions->where('pevaluacions.lapso_id',$lapso->id) : $seccions  ;

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = $seccion->getEstudiants($lapso_id);
            if ($estudiants->isNotEmpty()) {
                $count = $estudiants->count();
                $total = $total + $count * $seccion->count_evaluacions;
            }
            //if($lapso_id==1 && $seccion->id==19) dd($seccions,$estudiants,$total);
        }

        return ($total) ? $total : 0;
    }

    public function real_notas_load($lapso_id=null)
    {
        $boletins = Boletin::select(DB::raw('count(boletins.id) as count'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('estudiants', 'estudiants.id', '=','boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')

            ->where('grados.id',$this->id)
            ->where('estudiants.status_active','true')

            // ->whereNotNull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')

            ->groupby('grados.id');

        if (!empty($lapso_id)) {
            $lapso = Lapso::findOrFail($lapso_id);
            $boletins = $boletins->where('pevaluacions.lapso_id',$lapso->id)
                    // ->whereDate('inscripcions.created_at', '>=', $lapso->finicial)
                    ->whereDate('inscripcions.created_at', '<=', $lapso->ffinal);
        }

        $boletins = $boletins->first();

        return ($boletins) ? $boletins->count : 0;
    }

}
