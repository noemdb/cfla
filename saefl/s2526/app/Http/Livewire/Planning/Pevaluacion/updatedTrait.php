<?php

namespace App\Http\Livewire\Planning\Pevaluacion;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Leader;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;

trait updatedTrait
{
    public function updatedGradoId($value)
    {
        if ($value) {
            $grado = Grado::find($value);
            if ($grado) {
                $this->grado_id = $grado->id;            
                $this->list_seccion = Seccion::list_seccion_grado($this->grado_id); //dd($this->list_seccion);
                $this->list_pensum = Leader::getPensumsForLeader($this->leader_id, $value)->pluck('asignatura_fullname','id');        
            }
        } else {
            $this->grado_id = null;
            $this->list_seccion = collect();
        }
        // $this->close();
    }

    public function updatedPestudioId($value)
    {
        $this->list_grado = ($value) ? Grado::list_pestudio_grado($value) : Array() ;
        $this->list_profesor = ($value) ? Profesor::list_profesors_pestudio($value) : Array() ;
        // $this->close();
    }

    public function updatedProfesorName($value)
    {
        $this->setProfesorLists($value);
        // $this->close();
    }

}
