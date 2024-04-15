<?php

namespace App\Livewire\App\Catchment;

use App\Models\app\Academy\Enrollment;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Oinstitucion;
use Livewire\Component;

class IndexComponent extends Component
{

    public $catchment;

    public $ci;
    public $modalAssistent, $simpleModal, $modalSearch, $modalStart, $modalEmpty;
    public $list_comment,$list_grado, $list_oinstitucions,$list_blood_type,$list_laterality,$list_relationship,$list_profession,$list_sports_potential,$list_coexistence;

    public function setStart()
    {
        $this->modalSearch = true;
        $this->modalStart = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
    }

    public function mount()
    {
        $this->ci = '32446229';
        $this->list_comment = Enrollment::COLUMN_COMMENTS;
        $this->list_grado = Grado::list_inscripcion_grado();
        $this->list_oinstitucions = Oinstitucion::list_oinstitucions();
        $this->list_blood_type = Enrollment::list_blood_type();
        $this->list_laterality = Enrollment::list_laterality();
        $this->list_relationship = Enrollment::list_relationship();
        $this->list_profession = Enrollment::list_profession();
        $this->list_sports_potential = Enrollment::list_sports_potential();
        $this->list_coexistence = Enrollment::list_coexistence();
    }


    public function render()
    {
        return view('livewire.app.catchment.index-component');
    }
}
