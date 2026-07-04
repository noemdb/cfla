<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Preinscripcion extends Model
{
    protected $fillable = [
        'tipo_id','grado_id','seccion_id','estudiant_id','escolaridad_id','programacion_id','grupo_estable_id','observations'
    ];

    /*INI relaciones entre modelos*/
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant','estudiant_id');
    }
    public function grado()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Grado');
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

    public function fullPreinscripcion()
    {
        $grado_name = ($this->grado) ? $this->grado->name:null ;
        $grupo_estable_code = ($this->grupo_estable) ? $this->grupo_estable->code:null ;

        $preinscripcion = ($grupo_estable_code) ? $grado_name.' - '.$grupo_estable_code:$grado_name ;

        return $preinscripcion;
    }
    public function getGradoName()
    {
        return ($this->grado) ? $this->grado->name:null;
    }

    public function getGrupoEtableCode()
    {
        return ($this->grupo_estable) ? $this->grupo_estable->code:null;
    }

    public static function getCountGRTotal($arr_id, $gender)
    {
      //INI array con los totales de las tasks
        foreach ($arr_id as $key => $value) {
            $preinscripcions = Grado::select('grados.name','grados.id',DB::raw('count(grados.id) as value'))
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                ->join('preinscripcions', 'preinscripcions.grado_id', '=', 'grados.id')
                ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
                ->where('grados.id',$value)
                ->where('estudiants.gender','like','%'.$gender.'%')
                ->where('pestudios.status_active','true')
                ->where('grados.status_active','true')
                ->where('estudiants.status_active','true')
                ->groupby('grados.id')
                ->get();
            if( $preinscripcions->count()>0){
                $arr_total[] = $preinscripcions->first()->value;
            }
        }
      //FIN array con los totales de las tasks

        return (isset($arr_total)) ? $arr_total : 0;
    }

    public static function getCountGenderTotal($arr_id, $gender)
    {
      //INI array con los totales de las tasks
        foreach ($arr_id as $key => $value) {
            $inscripcions = Pestudio::select('pestudios.code','pestudios.id',DB::raw('count(pestudios.code) as value'))
                ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
                ->join('preinscripcions', 'preinscripcions.grado_id', '=', 'grados.id')
                ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
                ->where('pestudios.id',$value)
                ->where('estudiants.gender','like','%'.$gender.'%')
                ->where('pestudios.status_active','true')
                ->where('grados.status_active','true')
                ->where('estudiants.status_active','true')
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
        $preinscripcions = Grado::select('grados.name','grados.id',DB::raw('count(grados.id) as value'))
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('preinscripcions', 'preinscripcions.grado_id', '=', 'grados.id')
            ->where('grados.status_active','true')
            ->where('pestudios.status_active','true')
            ->groupby('grados.id')
            ->orderby('grados.id','asc')
            ->get()
            ->take($limit);

        return ($preinscripcions) ? $preinscripcions : 0;
    }

    public static function getPECodeID($limit=10)
    {
        $preinscripcions = Pestudio::select('pestudios.code','pestudios.name','pestudios.id',DB::raw('count(pestudios.code) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('preinscripcions', 'preinscripcions.grado_id', '=', 'grados.id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->groupby('pestudios.id')
            ->get()
            ->take($limit);

        return ($preinscripcions) ? $preinscripcions : 0;
    }

    
}
