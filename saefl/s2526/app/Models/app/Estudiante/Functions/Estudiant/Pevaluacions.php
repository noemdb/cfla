<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\DB;

trait Pevaluacions
{
    public function getSubAreas($lapso_id=1)
    {
        $grupo_estables = GrupoEstable::select('grupo_estables.*')
            ->join('pevaluacions', 'grupo_estables.id', '=', 'pevaluacions.grupo_estable_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            
            ->Where('estudiants.id',$this->id)
            ->Where('pevaluacions.lapso_id',$lapso_id)

            ->where('seccions.status_active','true' )
            ->whereNull('seccions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->groupBy('grupo_estables.id')
            ->get();
        return $grupo_estables;
    }

    public function getNotaPevaluacion($pevaluacion_id,$decimal=2)
    {
        $nota = null;

        $boletin = Boletin::select('boletins.*',
            DB::raw('count(evaluacions.id) as count_evaluacion'),
            DB::raw('sum(boletins.nota) as sum_nota'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')

            ->Where('boletins.estudiant_id',$this->id)
            ->Where('pevaluacions.id',$pevaluacion_id)

            ->whereNotNull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->groupby('boletins.estudiant_id')
            ->first();

        if ($boletin) {
            $ajuste = $this->getAjuste($pevaluacion_id);
            $nota = round(($boletin->sum_nota/$boletin->count_evaluacion),$decimal) + $ajuste;
        }

        return $nota;
    }

    //trait Boletins
    public function getPevaluacionsLapso($lapso_id=null)
    {
        $lapso = Lapso::find($lapso_id); $lapso_id = ($lapso) ? $lapso->id : null ;

        $pevaluacions =
        //DB::table('pevaluacions')
        Pevaluacion::select('pevaluacions.*','asignaturas.enable_academic_index','asignaturas.name as asignaturas_name')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

        ->Where('estudiants.id',$this->id)

        ->wherenull('pensums.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('inscripcions.deleted_at')
        ->wherenull('estudiants.deleted_at')

        ->groupby('pevaluacions.id');

        $pevaluacions = ($lapso_id) ? $pevaluacions->Where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions;

        $pevaluacions = $pevaluacions->get();

        return $pevaluacions;

    }

    public function getPevaluacionsAttribute()
    {
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

        ->Where('estudiants.id',$this->id)

        ->wherenull('pensums.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('inscripcions.deleted_at')
        ->wherenull('estudiants.deleted_at')

        ->groupby('pevaluacions.id')
        ->get();
        return $pevaluacions;
    }

}
