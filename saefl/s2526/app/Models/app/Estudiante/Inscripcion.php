<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiant;

use App\Models\app\Estudiante\Functions\Inscripcions\InscripcionMeta;

class Inscripcion extends Model
{
  use InscripcionMeta;

    protected $fillable = [
      'tipo_id','seccion_id','estudiant_id','escolaridad_id','programacion_id','grupo_estable_id','observations'
    ];

    /*INI relaciones entre modelos*/
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant','estudiant_id');
    }

    public function seccion()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Seccion');
    }

    public function tinscripcion()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Tinscripcion','tipo_id');
    }
    public function escolaridad()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Escolaridad');
    }
    public function programacion()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Programacion');
    }
    public function grupo_estable()
    {
        return $this->belongsTo('App\Models\app\Estudiante\GrupoEstable');
    }

    public function getStatusDeleteAttribute()
    {
        $boletins = Boletin::select('boletins.*')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('inscripcions.id',$this->id)
            ->get();

        return ($boletins->IsEmpty()) ? true : false;
    }

    public function getFullNameAttribute()
    {
        $seccion = (!empty($this->seccion->id)) ? $this->seccion->name : null ;
        $grado = (!empty($this->seccion->grado->id)) ? $this->seccion->grado->name : null ;
        return "{$grado} {$seccion}";
    }

    public static function std_ciaca_siadm()
    {//Cantidad de estudiantes con incripción administrativa y sin inscripción académica
        $estudiant =
            Estudiant::select('estudiants.*',DB::raw('count(inscripcions.id) as value'))
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->leftJoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->leftJoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('seccions.status_active','true')
            // ->whereNull('administrativas.id')
            ->where('estudiants.status_active','true')
            ->where( function($query) {
              $query->whereNull('planpagos.id')
              ->orWhere('planpagos.status_inscription_affects','false');
            })
            ->groupby('inscripcions.id')
            ->get();

            // dd($estudiant);

        return (empty($estudiant)) ? 0 : $estudiant;
    }

    public function getInscripcionAdministrativaTestAttribute ()
    {//prueba de estudiantes con incripción administrativa y sin inscripción académica

        $estudiant =
            Inscripcion::select('estudiants.*')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->Join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->where('inscripcions.id',$this->id)
            ->where('estudiants.status_active','true')
            ->first();

            // dd($estudiant);

        // return $estudiant ;
        return ( empty( $estudiant->id ) ) ? false : true;
    }

    public function scopeName($query, $arr_dat){
        //añade condicion para el username
        if(trim($arr_dat['search'])!=""){
            $search = ($arr_dat['search']=="&ALL") ? "" : $arr_dat['search'];
            $query->where('estudiants.name', 'like', "%".$search."%")
                    ->orWhere('estudiants.lastname', 'like', "%".$search."%")
                    ->orWhere('estudiants.ci_estudiant', 'like', "%".$search."%")
                    ->orWhere('estudiants.representant_id', 'like', "%".$search."%");
        }
        return $query;
    }

    public static function getPECodeID($limit=10)
    {
        $inscripcions = Pestudio::select('pestudios.code','pestudios.name','pestudios.id',DB::raw('count(pestudios.code) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->groupby('pestudios.id')
            ->get()
            ->take($limit);

        return ($inscripcions) ? $inscripcions : 0;
    }

    public static function getCountGenderTotal($arr_id, $gender)
    {
      //INI array con los totales de las tasks
      foreach ($arr_id as $key => $value) {
        $inscripcions = Pestudio::select('pestudios.code','pestudios.id',DB::raw('count(pestudios.code) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pestudios.id',$value)
            ->where('estudiants.gender','like','%'.$gender.'%')
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->get();
        if( $inscripcions->count()>0){
            $arr_total[] = $inscripcions->first()->value;
        }
      }
      //FIN array con los totales de las tasks

      return (isset($arr_total)) ? $arr_total : 0;
    }
    public static function getGRNameID($limit=10)
    {
        $inscripcions = Pestudio::select('grados.name','grados.id',DB::raw('count(grados.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->orderby('grados.id','asc')
            ->get()
            ->take($limit);

        return ($inscripcions) ? $inscripcions : 0;
    }

    public static function getCountGRTotal($arr_id, $gender)
    {
      //INI array con los totales de las tasks
      foreach ($arr_id as $key => $value) {
        $inscripcions = Pestudio::select('grados.name','grados.id',DB::raw('count(grados.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('grados.id',$value)
            ->where('estudiants.gender','like','%'.$gender.'%')
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('grados.id')
            ->get();
        if( $inscripcions->count()>0){
            $arr_total[] = $inscripcions->first()->value;
        }
      }
      //FIN array con los totales de las tasks

      return (isset($arr_total)) ? $arr_total : 0;
    }

    public static function print_header($num_estuden,$pestudio_name,$pestudio_code,$grado_name,$seccion_name,$total_std)
    {
        // dd('$num_estuden,$pestudio_name,$pestudio_code,$grado_name,$seccion_name,$total_std',$num_estuden,$pestudio_name,$pestudio_code,$grado_name,$seccion_name,$total_std);

        $return =


        '
        <tr>
          <th rowspan="4" style=" width:120px;height:60px;vertical-align: top">
            <img width="120px" height="30px" class="card-img-top" src="'.asset('images/avatar/gob_ve.png').'">
          </th>
          <th rowspan="4" style=" width:70px;height:60px;vertical-align: top">
              <img width="70px" height="30px" class="card-img-top" src="'.asset('images/avatar/corazon_venezolano.png').'"></th>
          <th style="font-size:14px">MATRICULA INICIAL</th>
        </tr>
        <tr>
          <td><b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Régimen Regular) Código del Formato: RR-DEA-01-03 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></b></td>
        </tr>
        <tr>
          <td><b> I. Año Escolar:</b> <u>&nbsp;&nbsp;&nbsp;&nbsp; 2019-2020 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
        </tr>
        <tr>
          <td><b>Mes y Año de la Matricula:</b> <u>&nbsp;&nbsp;&nbsp; SEPTIEMBRE-2019 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
        </tr>

        <tr>
          <td colspan="3">



          <table class="table table-striped table-sm" style="font-size:6.5px" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <th colspan="10">II. Datos del Plantel</th>
          </tr>
          <tr>
            <th colspan="1">Cód. de Plantel:</th>
            <td class="uline">S0427D2211</td>
            <th>Nombre:</th>
            <td colspan="5" class="uline">UNIDAD EDUCATIVA COLEGIO FRAY LUIS AMIGÓ</td>
            <th>Dtto. Esc.:</th>
            <td class="uline">*</td>
          </tr>
          <tr>
            <th>Dirección:</th>
            <td colspan="7" class="uline">AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA</td>
            <th>Teléfono:</th>
            <td colspan="1" class="uline" >0254-2310816 / 2343820</td>
          </tr>
          <tr>
            <th>Municipio:</th>
            <td colspan="3" class="uline">SAN FELIPE</td>
            <th>Ent. Federal:</th>
            <td colspan="3" class="uline">YARACUY</td>
            <th>Zona Educativa:</th>
            <td class="uline">YARACUY</td>
          </tr>
          <tr>
            <th colspan="3">III Identificación del Curso:</th>
            <th colspan="1">Plan de Estudio:</th>
            <td colspan="6" class="uline">'.$pestudio_name.'</td>
          </tr>
          <tr>
            <th>Código:</th>
            <td colspan="2" class="uline">'.$pestudio_code.'</td>
            <th colspan="1">Mención:</th>
            <td colspan="6" class="uline">******</td>
          </tr>
          <tr>
            <th colspan="1">Grado o Año:</th>
            <td  colspan="1" class="uline">'.$grado_name.'</td>
            <th colspan="1">Sección:</th>
            <td  colspan="1" class="uline">'.$seccion_name.'</td>

            <th colspan="4">Número de estudiantes de la sección:</th>
            <td class="uline">'.$total_std.'</td>
          </tr>
          <tr>
            <th colspan="3">Número de estudiantes de esta página:</th>
            <td colspan="1" class="uline">'.$num_estuden.'</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th colspan="10">IV. Datos de Identificación de Estudiantes</th>
          </tr>
        </table>


          </td>
        </tr>
      </table>

    <div class="page">
        <small class="text-muted pt-2 pb-0" style="padding-top:1rem"><b>'.$grado_name.' Sección '.$seccion_name.'</b></small><br>

        <table class="table table-striped table-sm" style="font-size:7px" cellpadding="0" cellspacing="0" border="1">

            <tbody>
                <tr >
                    <th rowspan="2" style="text-align:center; vertical-align:middle">N°</th>
                    <th rowspan="2" style="text-align:center; vertical-align:middle">Cédula de Identidad</th>
                    <th rowspan="2" style="text-align:center; vertical-align:middle">Apellidos</th>
                    <th rowspan="2" style="text-align:center; vertical-align:middle">Nombres</th>
                    <th rowspan="2" style="text-align:center; vertical-align:middle">Sexo</th>
                    <th colspan="3" style="text-align:center; vertical-align:middle">Fecha de Nac</th>
                    <th colspan="4" style="text-align:center; vertical-align:middle">Escolaridad</th>
                </tr>
                <tr>
                    <th>Dia</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>RG</th>
                    <th>RP</th>
                    <th>MP</th>
                    <th>DI</th>
                </tr>


    ';
        //dd($return);
        return $return;
    }

}
