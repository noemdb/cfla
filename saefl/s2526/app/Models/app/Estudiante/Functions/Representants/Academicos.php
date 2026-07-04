<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;

use Carbon\Carbon;

trait Academicos
{
    public function getAddressAttribute()
    {
        $estudiant = Estudiant::select('estudiants.*')
        ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
        // ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        // ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->where('representants.id',$this->id)
        // ->where('estudiants.status_active', 'true')
        // ->where('representants.status_active', 'true')
        ->whereNotNull('estudiants.dir_address')
        ->first(); //dd($estudiant);

        return ($estudiant) ? $estudiant->dir_address : null;
    }

    public function getProfesorsAttribute()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.representant_id',$this->id)
            ->where('estudiants.status_active','true')
            ->whereNull('estudiants.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('inscripcions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('profesors.id')
            ->get();
        return $profesors;
    }

    public function getPestudiosAttribute()
    {
        $pestudios = Pestudio::select('pestudios.*')
            ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('estudiants.representant_id',$this->id)
            ->where('estudiants.status_active','true')
            ->whereNull('estudiants.deleted_at')
            ->groupBy('pestudios.id')
            ->get();
        return $pestudios;
    }
}
