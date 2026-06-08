<?php

namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\DB;

trait Promedios
{

    public function getPromedioFinalRound($decimal=2)
    {
        $lapsos = Lapso::all();
        $promedio = null;
        $promedio_lapso = null;
        $sum_promedio_lapso = null;
        $count_lapso = null ;
        foreach ($lapsos as $lapso) {
            $promedio_lapso = $this->getPromedioLapsoRound($lapso->id,$decimal) ;
            if ($promedio_lapso > 0) {
                $sum_promedio_lapso = $sum_promedio_lapso + $promedio_lapso;
                $count_lapso ++;
            }
        }
        $promedio = ($count_lapso) ?  round( ( $sum_promedio_lapso / $count_lapso ), $decimal ) : null ;
        return  $promedio;
    }

    public function getPromedioFinalAlternative($decimal=2)
    {
        $lapsos = Lapso::all();

        $promedio = null;
        $promedio_lapso = null;
        $sum_promedio_lapso = null;
        $count_lapso = null ;

        foreach ($lapsos as $lapso) {
            $promedio_lapso = $this->getPromedioLapsoRound($lapso->id,$decimal) ;
            if ($promedio_lapso > 0) {
                $sum_promedio_lapso = $sum_promedio_lapso + $promedio_lapso;
                $count_lapso ++;
            }
        }
        $promedio = ($count_lapso) ?  round( ( $sum_promedio_lapso / $count_lapso ), $decimal ) : null ;
        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioFinalLapsoId($lapso_id,$decimal=2)
    {
        $lapsos = Lapso::all();

        $promedio = null;
        $promedio_lapso = null;
        $sum_promedio_lapso = null;
        $count_lapso = null ;

        foreach ($lapsos as $lapso) {
            if ($lapso->id <= $lapso_id) {
                $promedio_lapso = $this->getPromedioLapsoRoundForPensums($lapso->id,$decimal) ;
                $sum_promedio_lapso = $sum_promedio_lapso + $promedio_lapso;
                $count_lapso ++;
            }
        }
        $promedio = ($count_lapso) ?  round( ( $sum_promedio_lapso / $count_lapso ), $decimal ) : null ;
        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioLapsoRound($lapso_id=null,$decimal=2) // no es preciso
    {

        $pevaluacions = $this->getPevaluacionsLapso($lapso_id); //dd($this);

        $arr = [];

        $sum_nota = null;
        $count_pevaluacion = null;

        foreach ($pevaluacions as $pevaluacion) {

            $nota = $this->getNota($lapso_id,$pevaluacion->pensum_id);

            if ($nota && $pevaluacion->enable_academic_index == 'true') {
                $sum_nota += $nota;
                $count_pevaluacion++;
                $arr [$pevaluacion->pensum_id] = $nota;
            }

        }

        $promedio_round = (!empty($count_pevaluacion)) ? round(( $sum_nota / $count_pevaluacion ), $decimal): null;

        return  $promedio_round ;

    }

    public function getPromedioLapsoRoundForPensums($lapso_id=null,$decimal=2)
    {

        $pensums = $this->grado->pensums;
        $sum_nota = 0;
        $count_pensum = 0;
        $name = null;

        foreach ($pensums as $pensum) {

            $enable_academic_index = $pensum->asignatura->enable_academic_index;

            if ($enable_academic_index == 'true') {

                $promedio_pensum = $this->getNota($lapso_id,$pensum->id);

                if($promedio_pensum){
                    $sum_nota = $sum_nota + $promedio_pensum;
                    $count_pensum ++;
                }

            }

        }

        $promedio = ($count_pensum) ?  round( ( $sum_nota / $count_pensum ), $decimal ) : null ;

        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioFinal($decimal=0)
    {

        $pensums = ($this->grado) ? $this->grado->pensums : collect();
        $sum_nota = 0;
        $count_pensum = 0;
        $name = null;

        foreach ($pensums as $pensum) {

            $enable_academic_index = $pensum->asignatura->enable_academic_index;

            if ($enable_academic_index == 'true') {

                $promedio_pensum = $this->getNotaFinal($pensum->id);

                if($promedio_pensum){
                    $sum_nota = $sum_nota + $promedio_pensum;
                    $count_pensum ++;
                }

            }

        }

       //if($this->ci_estudiant=='33802914') dd($count_pensum,$sum_nota,$pensums->pluck('id'));

        $promedio = ($count_pensum) ?  round( ( $sum_nota / $count_pensum ), $decimal ) : null ;

        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioFinalLapso($decimal=0)
    {

        $lapsos = Lapso::all();
        $sum_nota = null;
        $count_lapso = null;
        $arr_promedios = Array();

        foreach ($lapsos as $lapso) {

            $promedio_lapso = $this->getPromedioLapsoRound($lapso->id,$decimal);

            $arr_promedios[]=$promedio_lapso;

            if($promedio_lapso){
                $sum_nota += $promedio_lapso;
                $count_lapso ++;
            }
        }

        $promedio = ($count_lapso) ?  round( ( $sum_nota / $count_lapso ), $decimal ) : null ;

        //if($this->ci_estudiant=='11608519180') dd($sum_nota,$arr_promedios);

        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioParcialLapso($lapso_id,$decimal=2)
    {

        $lapsos = Lapso::all();
        $sum_nota = null;
        $count_lapso = null;

        foreach ($lapsos as $lapso) {

            $promedio_lapso = $this->getPromedioLapsoRound($lapso->id,$decimal);

            if($lapso->id <= $lapso_id && $promedio_lapso){
                $sum_nota += $promedio_lapso;
                $count_lapso ++;
            }
        }

        $promedio = ($count_lapso) ?  round( ( $sum_nota / $count_lapso ), $decimal ) : null ;

        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioAttribute()
    {
        $lapsos = Lapso::all();
        $sum_nota = null;
        $count_lapso = null;
        foreach ($lapsos as $lapso) {
            $promedio_lapso = $this->getPromedioLapsoRound($lapso->id,0);
            if($promedio_lapso){
                $sum_nota += $promedio_lapso;
                $count_lapso ++;
            }
        }
        $promedio = ($count_lapso) ?  round( ( $sum_nota / $count_lapso ), 2 ) : null ;
        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getPromedioLapso($lapso_id,$decimal=2)
    {
        $boletin = DB::table('estudiants')
            ->select(
                DB::raw('count(boletins.id) as count_boletins'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('boletins', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')

            ->Where('estudiants.id',$this->id)
            ->Where('pevaluacions.lapso_id',$lapso_id)

            ->whereNotNull('boletins.nota')
            ->wherenull('boletins.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')

            ->groupby('estudiants.id')
            ->first();

            $ajuste_lapso = $this->getAjusteLapso($lapso_id);

            $promedio = (!empty($boletin)) ?  round( ( $boletin->sum_nota / $boletin->count_boletins ), $decimal ) + $ajuste_lapso : null ;

            $str = 'sum_nota: '.$boletin->sum_nota .' - count_boletins: '.$boletin->count_boletins. ' - ajuste_lapso:'.$ajuste_lapso .' - nota:'.$promedio;

        return  (!empty($promedio)) ? $promedio : null;
    }

    public function getNotaFinalAttribute()
    {
        $baremo = new Baremo();
        $pestudio = $this->pestudio;
        $promedio = $this->promedio;
        $nota = round($promedio,0);

        $nota_final = ($pestudio->status_baremo=="true") ? $baremo->getLiteral($pestudio->id,$nota) : $nota ;

        return $nota_final;
        // return $pestudio->id;

    }

    public function getPosicionSeccionLapso($lapso_id)
    {
        $posicion = null;
        $seccion = $this->seccion;
        if ($seccion) {
            $estudiants = $seccion->getEstudiantPosicionPromedioLapso($lapso_id);
            foreach ($estudiants as $index => $estudiant) { //dd($estudiants,$index);
                $posicion++;
                if ($estudiant['estudiant_id'] == $this->id) break;
            }
        }
        return $posicion;
    }

    public function getPosicionGradoLapso($lapso_id)
    {
        $posicion = 0;
        $grado = $this->grado;
        if ($grado) {
            $estudiants = $grado->getEstudiantPosicionPromedioLapso($lapso_id);
            foreach ($estudiants as $index => $estudiant) { //dd($estudiants,$index);
                $posicion++;
                if ($estudiant['estudiant_id'] == $this->id) break;
            }
        }
        return $posicion;
    }

    public function getPosicionSeccionAttribute()
    {
        $posicion = null;
        $seccion = $this->seccion;
        if ($seccion) {
            $estudiants = $seccion->estudiants_posicion_promedio;
            foreach ($estudiants as $index => $estudiant) { //dd($estudiants,$index);
                $posicion++;
                if ($estudiant['estudiant_id'] == $this->id) break;
            }
        }
        return $posicion;
    }

}
