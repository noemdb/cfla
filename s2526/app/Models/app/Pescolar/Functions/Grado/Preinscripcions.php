<?php
namespace App\Models\app\Pescolar\Functions\Grado;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Preinscripcion;
use App\Models\app\Pescolar\Pestudio;

use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Preinscripcions {

    public function preinscritos()
    {
        $preinscripcions = Preinscripcion::select('grados.name',DB::raw('count(preinscripcions.id) as value'))
            ->join('grados', 'grados.id', '=', 'preinscripcions.grado_id')
            // ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('preinscripcions.grado_id', '=', $this->id)
            ->where('grados.status_active','true')
            // ->where('estudiants.status_active','true')
            // ->wherenull('preinscripcions.deleted_at')
            ->groupby('grados.id')
            ->first(); //dd($preinscripcions);
        return ($preinscripcions) ? $preinscripcions : 0;
    }

    public function pre_varones()
    {
        $preinscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('preinscripcions', 'grados.id', '=', 'preinscripcions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('grados.id', '=', $this->id)
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Masculino')
            // ->wherenull('preinscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
            // ->value;
        return ($preinscripcions) ? $preinscripcions : 0;
    }
    public function pre_hembras()
    {
        $preinscripcions = Grado::select('grados.name',DB::raw('count(grados.id) as value'))
            ->join('preinscripcions', 'grados.id', '=', 'preinscripcions.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->Where('grados.id', '=', $this->id)
            ->where('grados.status_active','true')
            ->where('estudiants.status_active','true')
            ->where('estudiants.gender','Femenino')
            // ->wherenull('preinscripcions.deleted_at')
            ->groupby('grados.id')
            ->first();
        return ($preinscripcions) ? $preinscripcions : 0;
    }

}
