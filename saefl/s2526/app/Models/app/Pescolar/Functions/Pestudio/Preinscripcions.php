<?php
namespace App\Models\app\Pescolar\Functions\Pestudio;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Preinscripcions {

    public function preinscripcions()
    {
        $preinscripcions = Pestudio::select('pestudios.name',DB::raw('count(estudiants.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('preinscripcions', 'grados.id', '=', 'preinscripcions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->wherenull('preinscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        // dd($preinscripcions);
        return $preinscripcions;
    }

    public function pre_varones()
    {
        $preinscripcions = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('preinscripcions', 'grados.id', '=', 'preinscripcions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Masculino')
            ->wherenull('preinscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return $preinscripcions;
    }

    public function pre_hembras()
    {
        $preinscripcions = Pestudio::select('pestudios.name',DB::raw('count(pestudios.id) as value'))
            ->join('grados', 'grados.pestudio_id', '=', 'pestudios.id')
            ->join('preinscripcions', 'grados.id', '=', 'preinscripcions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('pestudios.id', '=', $this->id)
            ->where('pestudios.status_active','true')
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Femenino')
            ->wherenull('preinscripcions.deleted_at')
            ->groupby('pestudios.id')
            ->first();
        return $preinscripcions;
    }

}
