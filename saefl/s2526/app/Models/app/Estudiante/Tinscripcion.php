<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;
// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;


class Tinscripcion extends Model
{
    protected $guarded = ['id','deleted_at','created_at','updated_at'];

    public function inscripcions()
    {
        return $this->hasMany('App\Models\app\Pescolar\Inscripcion');
    }

    public function inscritos()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }
    public function varones()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }
    public function hembras()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }

    public function others()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            //->where('planpagos.status_inscription_affects','true')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('estudiants.gender')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function a_inscritos()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }
    public function a_varones()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }
    public function a_hembras()
    {
        $inscripcions = Pestudio::select('tinscripcions.name',DB::raw('count(tinscripcions.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('tinscripcions', 'inscripcions.tipo_id', '=', 'tinscripcions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('tinscripcions.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('tinscripcions.id')
            ->first();
            // ->value;

        return ($inscripcions) ? $inscripcions : 0;
    }

}
