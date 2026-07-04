<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Functions\Administrativas\AdministrativaMeta;

class Administrativa extends Model
{
    use AdministrativaMeta;

    // protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = [
        'estudiant_id', 'user_id', 'planpago_id','observations'
    ];

    protected $date = [ 'created_at','updated_at' ];

    /*INI relaciones entre modelos*/
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }

    public function planpago()
    {
        return $this->belongsTo('App\Models\app\Planpago','planpago_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public static function std_siaca_ciadm()
    {//Cantidad de estudiantes con incripción administrativa y sin inscripción académica

        // $estudiants = Estudiant::join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        //     ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
        //     ->leftJoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        //     ->leftJoin('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
        //     ->where('planpagos.status_inscription_affects', '=', 'true') 
        //     ->where('planpagos.status_active', '=', 'true')
        //     ->whereNotIn('estudiants.id', function($query) {
        //         $query->select('inscripcions.estudiant_id')
        //             ->from('inscripcions')
        //             ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
        //             ->where('seccions.status_active', '=', 'true');
        //     })
        //     ->get(); dd($estudiants);


        $estudiants = Estudiant::query()
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        // ->whereNotNull('administrativas.estudiant_id')
        ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id') 
        ->where('planpagos.status_inscription_affects', 'true')
        ->where('planpagos.status_active', 'true')
        ->whereDoesntHave('inscripcion', function($query) {
            $query->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                    ->where('seccions.status_active', 'true');
            })

        ->groupby('estudiants.id')

        ->get(); //dd($estudiants);

        return (empty($estudiants)) ? 0 : $estudiants;
    }

    public static function std_ciaca_siadm()
    {//Cantidad estudiante(s) con inscripción académica y sin inscripción administrativa
        $estudiants =
            Estudiant::select('estudiants.*',DB::raw('count(inscripcions.id) as value'))
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->leftJoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->leftJoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where( function($query) {
                $query->whereNull('planpagos.id')
                ->orWhere('planpagos.status_inscription_affects','false');
            })
            ->groupby('inscripcions.id')
            ->get();

             //dd($estudiants);

        return (empty($estudiants)) ? 0 : $estudiants;
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
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
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
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
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
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
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
      return (isset($arr_total)) ? $arr_total : 0;
    }

}
