<?php
namespace App\Models\app\Pescolar\Functions\Pestudio;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Inscripcions {

    public function a_inscritos()
    {
        $administrativas = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();

        return ($administrativas) ? $administrativas : 0;
    }

    public function a_varones()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function a_hembras()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }
    public function a_retirados()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->Where('retiros.tipo', '=', 'admon')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function inscritos()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            //->where('planpagos.status_inscription_affects','true')
            //->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function retirados()
    {
        $inscripcions = Estudiant::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->leftjoin('grados', 'seccions.grado_id', '=', 'grados.id')
            ->leftjoin('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->Where('retiros.tipo', '=', 'control')
            ->Where('pestudios.id', '=', $this->id)
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function inschistories()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inschistories', 'seccions.id', '=', 'inschistories.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inschistories.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->Where('pestudios.id', '=', $this->id)
            ->Where('grados.id', '<>', 11)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->whereNull('inscripcions.id')
            ->groupby('pestudios.id')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function varones()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            //->where('planpagos.status_inscription_affects','true')
            //->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->where('estudiants.gender','Masculino')
            ->first();
        return ($inscripcions) ? $inscripcions : 0; //->where('estudiants.gender','Masculino')
    }

    public function hembras()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            //->where('planpagos.status_inscription_affects','true')
            //->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->where('estudiants.gender','Femenino')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    public function others()
    {
        $inscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            //->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            //->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            //->where('planpagos.status_inscription_affects','true')
            //->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('inscripcions.deleted_at')
            ->groupby('pestudios.id')
            // ->where('estudiants.gender','<>','Femenino')
            ->wherenull('estudiants.gender')
            ->first();
        return ($inscripcions) ? $inscripcions : 0;
    }

    ////////////////////////////////////////////////////
    public function getRetirados()
    {
        $retirados = Estudiant::select('pestudios.*')
            ->join('retiros', 'estudiants.id', '=', 'retiros.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->leftjoin('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->leftjoin('grados', 'seccions.grado_id', '=', 'grados.id')
            ->leftjoin('pestudios', 'grados.pestudio_id', '=', 'pestudios.id')
            ->Where('retiros.tipo', '=', 'control')
            ->Where('pestudios.id', '=', $this->id)
            ->get();
        return $retirados;
    }

}
