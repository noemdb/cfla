<?php

namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinRevision;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Profesor\Pevaluacion;
use Illuminate\Support\Facades\DB;

trait Notas
{
    public function getGoalEvaluacionsPensumLapso($pensum_id=null,$lapso_id=null)
    {
        $evaluacions = $this->getEvaluacionsPensumLapso($pensum_id,$lapso_id);

        return ($evaluacions->isNotEmpty()) ? $evaluacions->count() : null;
    }
    public function getRealEvaluacionsPensumLapso($pensum_id=null,$lapso_id=null)
    {
        $evaluacions = $this->getEvaluacionsPensumLapsoBoletin($pensum_id,$lapso_id);

        return ($evaluacions->isNotEmpty()) ? $evaluacions->count() : null;
    }

    public function getNotaEvaluacion($evaluacion_id)
    {
        $boletin = Boletin::select('boletins.*')
        ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
        ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
        ->where('boletins.estudiant_id',$this->id)
        ->where('boletins.evaluacion_id',$evaluacion_id)
        ->whereNotNull('boletins.nota')
        ->wherenull('evaluacions.deleted_at')
        ->orderBy('boletins.created_at')
        ->first();

        return ( $boletin ) ? $boletin->nota:null;
    }



    public function getNota($lapso_id,$pensum_id,$decimal=0,$ajuste=true)
    {
        $seccion = $this->inscripcion->seccion;

        $boletin = Boletin::select('boletins.*',
            DB::raw('count(evaluacions.id) as count_evaluacion'),
            DB::raw('sum(boletins.nota) as sum_nota'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')

            ->Where('boletins.estudiant_id',$this->id)
            ->Where('pevaluacions.lapso_id',$lapso_id)
            ->Where('pensums.id',$pensum_id)
            ->Where('pevaluacions.seccion_id',$seccion->id)

            //->whereNotNull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->groupby('boletins.estudiant_id')
            ->first(); //dd('notas');

        $nota = null;
        if ($boletin) {
            $pevaluacion = Pevaluacion::where('seccion_id',$seccion->id)->where('lapso_id',$lapso_id)->where('pensum_id',$pensum_id)->first();
            $ajuste = ($ajuste) ? $this->getAjuste($pevaluacion->id) : 0;
            $nota = round(( $boletin->sum_nota / $boletin->count_evaluacion ),$decimal) + $ajuste;
            $nota = ($nota>=20) ? 20 : $nota ;
        }

        return $nota;
    }

    public function getNotaRevisionStatus($pensum_id)
    {
        return ($this->getNotaRevisions($pensum_id)->isNotEmpty()) ? true : false ;
    }

    public function getNotaRevisions($pensum_id)
    {
        $boletin_revisions = $this->boletin_revisions->where('pensum_id',$pensum_id);
        return $boletin_revisions;
    }

    // public function getNotaFinalRevision($pensum_id, $decimal = 0, $status_literal = true)
    // {
    //     $pensum                = Pensum::find($pensum_id);
    //     $pestudio              = $pensum->pestudio;
    //     $enable_academic_index = $pensum->asignatura->enable_academic_index;

    //     $nota       = 0;
    //     $sum_nota   = 0;
    //     $count_nota = 0;

    //     $boletin_revisions = $this->getNotaRevisions($pensum_id);

    //     foreach ($boletin_revisions as $boletin_revision) {
    //         $count_nota++;
    //         $nota = is_numeric($boletin_revision?->nota) ?? 0;
    //         $sum_nota += $nota ?? 0;
    //     }

    //     $nota = ($count_nota) ? round(($sum_nota / $count_nota), $decimal) : null;

    //     if ($enable_academic_index == "false" && $status_literal) {
    //         $nota = Baremo::getLiteral($pestudio->id, $nota);
    //     }

    //     return $nota;
    // }

    public function getNotaFinalRevision($pensum_id, $decimal = 0, $status_literal = true, $lapso_id = null)
    {
        $pensum                = Pensum::find($pensum_id);
        $pestudio              = $pensum->pestudio;
        $enable_academic_index = $pensum->asignatura->enable_academic_index;
        $nota       = 0;
        $sum_nota   = 0;
        $count_nota = 0;
        $boletin_revisions = $this->getNotaRevisions($pensum_id);
        foreach ($boletin_revisions as $boletin_revision) {
            $count_nota++;
            $nota = is_numeric($boletin_revision?->nota) ?? 0;
            $sum_nota += $nota ?? 0;
        }
        $nota = ($count_nota) ? round(($sum_nota / $count_nota), $decimal) : null;
        if ($enable_academic_index == "false" && $status_literal) {
            $nota = Baremo::getLiteral($pestudio->id, $nota, $lapso_id);  // ← CON lapso_id
        }
        return $nota;
    }

    // public function getNotaFinal($pensum_id,$decimal=0,$status_literal=true, $lapso_id=null)
    // {
    //     $lapsos = Lapso::when($lapso_id, function ($query, $lapso_id) {
    //         return $query->where('id', '<=', $lapso_id);
    //     })->get();            
    //     $pensum = Pensum::find($pensum_id);
    //     $pestudio = $pensum->pestudio;
    //     $enable_academic_index = $pensum->asignatura->enable_academic_index;
    //     $seccion = $this->seccion;
    //     $sum_nota = null;
    //     $count_nota = null;
    //     foreach ($lapsos as $lapso) {
    //         $getNota = $this->getNota($lapso->id,$pensum_id);
    //         $pevaluacion = Pevaluacion::where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->where('pensum_id',$pensum_id)->first();
    //         if (isset($getNota) && $pevaluacion) {
    //             $count_nota++;
    //             $sum_nota += $getNota;
    //         }
    //     }
    //     $nota = ($count_nota) ? round( ( $sum_nota / $count_nota ) ,$decimal) : 0;
    //     if ($enable_academic_index == "false" && $status_literal) {
    //         $nota = Baremo::getLiteral($pestudio->id,$nota) ;
    //     }
    //     return $nota;
    // }

    public function getNotaFinal($pensum_id,$decimal=0,$status_literal=true, $lapso_id=null)
    {
        $lapsos = Lapso::when($lapso_id, function ($query, $lapso_id) {
            return $query->where('id', '<=', $lapso_id);
        })->get();
        $pensum = Pensum::find($pensum_id);
        $pestudio = $pensum->pestudio;
        $enable_academic_index = $pensum->asignatura->enable_academic_index;
        $seccion = $this->seccion;
        $sum_nota = null;
        $count_nota = null;
        foreach ($lapsos as $lapso) {
            $getNota = $this->getNota($lapso->id,$pensum_id);
            $pevaluacion = Pevaluacion::where('seccion_id',$seccion->id)->where('lapso_id',$lapso->id)->where('pensum_id',$pensum_id)->first();
            if (isset($getNota) && $pevaluacion) {
                $count_nota++;
                $sum_nota += $getNota;
            }
        }
        $nota = ($count_nota) ? round( ( $sum_nota / $count_nota ) ,$decimal) : 0;
        if ($enable_academic_index == "false" && $status_literal) {
            $nota = Baremo::getLiteral($pestudio->id, $nota, $lapso_id);  // ← CON lapso_id
        }
        return $nota;
    }

    public function getNotaPensumLapso($pensum_id,$lapso_id,$decimal=2)
    {
        $evaluacions = $this->getEvaluacionsPensumLapso($pensum_id,$lapso_id);
        $arr = array();
        $nota = null;

        $sum_nota = 0;
        $count_nota = 0;
        foreach ($evaluacions as $evaluacion) {
            $nota = $this->getNotaEvaluacion($evaluacion->id);
            $arr[] = $nota;
            if (isset($nota)) {
                $sum_nota += $nota;
            }
            $count_nota++;
        }

        $nota = (!empty($evaluacions->count())) ? round ($sum_nota / $count_nota,$decimal):null;

        $seccion = $this->inscripcion->seccion;
        $pevaluacion = Pevaluacion::where('seccion_id',$seccion->id)->where('lapso_id',$lapso_id)->where('pensum_id',$pensum_id)->first();
        $ajuste = ($pevaluacion) ? $this->getAjuste($pevaluacion->id) : null;

        $nota = $nota + $ajuste ;

        return $nota;
    }

    public function getPromedioFinalPensum($pensum_id,$decimal=0)
    {
        $lapsos = Lapso::all() ;

        $sum_nota = null;
        $count_nota = null;

        foreach ($lapsos as $lapso) {

            $getNota = $this->getNota($lapso->id,$pensum_id,$decimal);

            if (isset($getNota)) {
                $count_nota++;
                $sum_nota += $getNota;
            }

        }

        $nota = ($count_nota) ? round( ( $sum_nota / $count_nota ) ,$decimal) : null;

        return $nota;
    }

    public function getNotaFinalLapso($lapso_id,$decimal=0,$status_literal=true,$ajuste=true)
    {
        $notas = 0;
        $count = 0;
        $arr = [];
        $pensums = $this->pensums;
        foreach ($pensums as $pensum) {
            $enable_academic_index = $pensum->asignatura->enable_academic_index;
            $nota = $this->getNota($lapso_id,$pensum->id,$decimal,$ajuste);
            if (isset($nota) && $enable_academic_index=='true') {
                $count++;
                $notas = $notas + $nota;
                $arr[$pensum->asignatura->code] = $nota;
            }
        }
        $promedio = ($count > 0) ? ($notas / $count): 0 ;
        return round($promedio,$decimal) ;

    }

}
