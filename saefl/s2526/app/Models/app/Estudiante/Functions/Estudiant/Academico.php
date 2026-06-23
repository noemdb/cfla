<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\DB;

trait Academico
{

    public static function getMunicipios($state="YARACUY",$operator="=")
    {
        $municipios =
            DB::table('estudiants')
                ->select('estudiants.town_hall_birth','estudiants.state_birth')
                // ->selectRaw('count(town_hall_birth) as count_town_hall_birth')
                ->selectRaw('count(estudiants.id) as count_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                ->GroupBy('town_hall_birth')
                ->OrderBy('count_id','desc')
                // ->WhereNotNull('town_hall_birth')
                ->where('estudiants.town_hall_birth','<>','NULL')
                ->where('estudiants.state_birth','<>','NULL')
                ;

        $municipios = (!empty($state)) ? $municipios->Where('state_birth',$operator,$state) : $municipios ;

        $municipios = $municipios->get();

        return $municipios ;

    }

    public static function getMunicipiosValues($town_hall_birth,$state="YARACUY",$operator="=")
    {
        $pestudios_values =
            DB::table('estudiants')
                ->select('estudiants.town_hall_birth')
                ->selectRaw('count(estudiants.id) as count_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                // ->where('estudiants.town_hall_birth',$town_hall_birth)
                ->where('estudiants.state_birth','<>','NULL')
                ->where('estudiants.town_hall_birth','<>','NULL')
                // ->WhereNotNull('town_hall_birth')
                ->GroupBy('pestudios.id')
                ->OrderBy('pestudios.id');

        $pestudios_values = (!empty($town_hall_birth)) ? $pestudios_values->Where('town_hall_birth',$town_hall_birth) : $pestudios_values ;

        $pestudios_values = (!empty($state)) ? $pestudios_values->Where('state_birth',$operator,$state) : $pestudios_values ;

        $pestudios_values = $pestudios_values->get()->pluck('count_id')->toArray();

        return $pestudios_values ;
    }

    public function getSeccionAttribute()
    {
        if (! empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (! empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                return $seccion;
            }
        }
    }

    public function getGradoAttribute()
    {
        ///$grado = new Grado();
        if (! empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (! empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                if (! empty($seccion->grado)) {
                    $grado = $seccion->grado;
                }
            }
        }
        return ($grado) ? $grado : null;
    }

    public function getFullInscripcionAttribute()
    {
        $full_inscripcion = null;
        if (! empty($this->inscripcion)) {
            $inscripcion = $this->inscripcion;
            if (! empty($inscripcion->seccion)) {
                $seccion = $inscripcion->seccion;
                if (! empty($seccion->grado)) {
                    $grado = $seccion->grado;
                    $full_inscripcion = "{$grado->name} {$seccion->name}";
                }
            }
        }
        return $full_inscripcion;
    }

    public function GetIA($pestudio_id)
    {
        $notas = Hnota::select('hnotas.*')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('historico_notas.pestudio_id',$pestudio_id)
            ->where('hnotas.estudiant_id',$this->id)
            ->where('asignaturas.enable_academic_index','true' )
            ->WhereNotNull('hnotas.valor')
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->get(); //dd($this);
        $ia = ($notas->count() > 0) ? round(($notas->sum('valor')/$notas->count()),2) :null;
        return $ia;
    }
    public function GetAllInstitucions($pestudio_id)
    {
        $oinstitucions = Oinstitucion::select('oinstitucions.*')
            ->join('hnotas', 'oinstitucions.id', '=', 'hnotas.institucion_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->where('historico_notas.pestudio_id',$pestudio_id)
            ->where('hnotas.estudiant_id',$this->id)
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('historico_notas.deleted_at')
            ->OrderBy('pensums.grado_id')
            // ->OrderBy('oinstitucions.name')
            ->groupby('oinstitucions.id')
            ->get();
        return $oinstitucions;
    }

    public function GetAllHNotas($pestudio_id,$enable_academic_index = 'true')
    {
        $notas = Hnota::select('hnotas.*')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('historico_notas.pestudio_id',$pestudio_id)
            ->where('hnotas.estudiant_id', $this->id)
            ->where('asignaturas.enable_academic_index',$enable_academic_index )
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->get();
        return $notas;
    }

    public function GetHNotas($grado_id,$enable_academic_index = 'true')
    {
        $notas = Hnota::select('hnotas.*')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('hnotas.estudiant_id',$this->id)
            ->where('pensums.grado_id',$grado_id)
            ->where('asignaturas.enable_academic_index',$enable_academic_index )
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->OrderBy('asignaturas.order')
            ->get();
        return $notas;
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

        return ( !empty( $boletin->count() ) ) ? $boletin->nota:null;
    }

    public function getNotaPensumLapso($pensum_id,$lapso_id,$decimal=2)
    {
        $evaluacions = $this->getEvaluacionsPensumLapso($pensum_id,$lapso_id);

        $sum_nota = 0;
        foreach ($evaluacions as $evaluacion) {
            $sum_nota += $this->getNotaEvaluacion($evaluacion->id);
        }

        return (!empty($evaluacions->count())) ? round ($sum_nota / $evaluacions->count(),$decimal):null;
    }

    public function getEvaluacionsPensumLapso($pensum_id,$lapso_id)
    {
        $seccion = $this->seccion;
        $evaluacions = Evaluacion::select('evaluacions.*')
        ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->where('pevaluacions.seccion_id',$seccion->id)
        ->where('pevaluacions.lapso_id',$lapso_id)
        ->where('pensums.id',$pensum_id)

        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('pensums.deleted_at')
        ->get();

        return ($evaluacions->isNotEmpty()) ? $evaluacions : collect();
    }

    public function getAjuste($pevaluacion_id)
    {
        $boletin_ajuste =
            BoletinAjuste::where('estudiant_id',$this->id)
                ->where('pevaluacion_id',$pevaluacion_id)
                ->first();
        return ($boletin_ajuste) ? $boletin_ajuste->ajuste : null;
    }

    public function getNota($lapso_id,$pensum_id,$decimal=2)
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
            ->Where('pevaluacions.seccion_id',$seccion->id)
            ->Where('pensums.id',$pensum_id)

            ->whereNotNull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')

            ->groupby('boletins.estudiant_id')
            ->first();

        $nota = null;
        if (!empty($boletin->count())) {
            $pevaluacion = Pevaluacion::where('seccion_id',$seccion->id)->where('lapso_id',$lapso_id)->where('pensum_id',$pensum_id)->first();
            $ajuste = $this->getAjuste($pevaluacion->id);
            $nota = round(($boletin->sum_nota/$boletin->count_evaluacion),$decimal) + $ajuste;
        }

        return $nota;
    }

    public function getPevaluacionsLapso($lapso_id)
    {
        $pevaluacions = DB::table('pevaluacions')
        ->select('pevaluacions.*','asignaturas.enable_academic_index')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
        ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

        ->Where('estudiants.id',$this->id)
        ->Where('pevaluacions.lapso_id',$lapso_id)

        // ->wherenull('pensums.deleted_at')
        // ->wherenull('grados.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('inscripcions.deleted_at')
        ->wherenull('estudiants.deleted_at')

        ->groupby('pevaluacions.id')

        ->get();

        return $pevaluacions;

    }

    public function getPromedioLapsoRound($lapso_id,$decimal=2)
    {
        $pevaluacions = $this->getPevaluacionsLapso($lapso_id); //dd($this);
        $sum_nota = null;
        $str = null;
        $count_pevaluacion = null;

        foreach ($pevaluacions as $pevaluacion) {

            $nota = $this->getNota($lapso_id,$pevaluacion->pensum_id);

            if ($nota && $pevaluacion->enable_academic_index == 'true') {
                $sum_nota += $nota;
                $count_pevaluacion++;
            }
        }

        $promedio_round = ($count_pevaluacion) ? round(( $sum_nota / $count_pevaluacion ), $decimal): null;

        return  $promedio_round ;
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

    public function getAjusteLapso($lapso_id)
    {
        $boletin_ajuste = DB::table('estudiants')
            ->select(
                DB::raw('sum(boletin_ajustes.ajuste) as sum_ajuste')
            )
            ->join('boletin_ajustes', 'estudiants.id', '=', 'boletin_ajustes.estudiant_id')
            ->Where('estudiants.id',$this->id)
            ->groupby('estudiants.id')
            ->first();

        return  (!empty($boletin_ajuste)) ? $boletin_ajuste->sum_ajuste : null;
    }

    public function getEnableInscriptionAttribute()
    {
        $id = $this->id;
        $planpago_id = ( !empty($this->administrativa->planpago_id) ) ? $this->administrativa->planpago_id : '0' ;
        $cta_x_pagars =
            Cuentaxpagar::where('planpago_id',$planpago_id)
                ->Where('cuentaxpagars.status_inscription','true')
                ->Where('cuentaxpagars.type','GENERAL')
                ->orWhere(function($q) use ($id){
                    $q->where('cuentaxpagars.type','INDIVIDUAL')
                    ->where('cuentaxpagars.estudiant_id',$id);
                })
                ->get();
        $total=0;
        foreach ($cta_x_pagars as $cta_x_pagar) {
            $total = $total + $cta_x_pagar->TotalMontoConceptosXPagar($this->id);
        }
        return ($total==0) ? true:false;
    }

    public function getPromedioAttribute()
    {
        $lapsos = Lapso::all();
        $sum_nota = null;
        $count_lapso = null;

        foreach ($lapsos as $lapso) {

            $promedio = $this->getPromedioLapsoRound($lapso->id,0);

            if($promedio){
                $sum_nota += $promedio;
                $count_lapso ++;
            }
        }

        $promedio = ($count_lapso) ?  round( ( $sum_nota / $count_lapso ), 2 ) : null ;

        return  (!empty($promedio)) ? $promedio : null;
    }

    // public function getLiteralAttribute()
    // {
    //     $promedio = $this->promedio;
    //     $pestudio = $this->pestudio;

    //     dd($this);

    //     $baremo = ( $promedio ) ? Baremo::Where('minimo','<',$promedio)->Where('maxima','>=',$promedio)->where('pestudio_id',$pestudio->id)->first() : null;

    //     return ($baremo) ? $baremo->literal : null;
    // }

    public function getGradoPromocionAttribute()
    {
        $grado_id = $this->grado->id;

        $grado = Grado::find($grado_id + 1);

        return ($grado) ? $grado : null;
    }

    public function getProfesorGuiaAttribute()
    {
        $profesor = Profesor::select('profesors.*')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.id',$this->id)
            ->groupBy('profesors.id')
            ->OrderBy('profesor_guias.created_at')
            ->first();

        return (!empty($profesor)) ? $profesor:null;
    }

}
