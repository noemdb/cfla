<?php

namespace App\Livewire\App\Enrollment;

use App\Models\app\Academy\Census;
use App\Models\app\Academy\Enrollment;
use App\Livewire\Forms\EnrollmentForm;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Oinstitucion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Learner\Estudiant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class IndexComponent extends Component
{
    use Actions;
    use WithFileUploads;
    #[Validate('image|max:1024')] // 1MB Max
    public $image;

    public Census $census;
    public EnrollmentForm $enrollment;

    public $ci;
    public $step = 0, $limit = 9;
    public $modalAssistent, $simpleModal, $modalSearch, $modalStart, $modalEmpty;
    public $list_comment;
    public $ci_estudiant;
    public $list_grado, $list_oinstitucions,$list_blood_type,$list_laterality,$list_relationship,$list_profession,$list_sports_potential,$list_coexistence;
    public $status_enrollment_exists;

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
        return view('livewire.app.enrollment.index-component');
    }

    public function search()
    {
        $this->resetValidation();
        $census = Census::where('ci_estudiant', $this->ci)->first(); 
        $enrollment = Enrollment::where('ci_estudiant', $this->ci)->first();
        $this->status_enrollment_exists = ($enrollment) ? true : false ;
        if ($census) {
            $this->census = $census;
            $this->step = 1;
            $this->enrollment->fill($census->toArray());
            $this->modalAssistent = true;
            $this->modalSearch = false;
            $this->modalStart = false;
            $this->modalEmpty = false;
        } else {
            
            $this->modalEmpty = true;
            $this->modalSearch = false;
            $this->modalStart = false;
            $this->modalAssistent = false;
            //dd('$this->modalEmpty',$this->modalEmpty,'$this->modalSearch',$this->modalSearch,'$this->modalStart',$this->modalStart,'$this->modalAssistent',$this->modalAssistent);
        }        
    }

    public function getValidate($step)
    {
        $this->resetValidation();       
        switch ($step) {
            case '1':
                $this->validateOnly('enrollment.ci_estudiant');
                $this->validateOnly('enrollment.lastname');
                $this->validateOnly('enrollment.name');
                $this->validateOnly('enrollment.gender');
                $this->validateOnly('enrollment.date_birth');
                $this->validateOnly('enrollment.town_hall_birth');
                $this->validateOnly('enrollment.state_birth');
                $this->validateOnly('enrollment.country_birth');
                $this->validateOnly('enrollment.dir_address');
                $this->validateOnly('enrollment.grado_id');
                $this->validateOnly('enrollment.pestudio_id');
                $this->validateOnly('enrollment.institution');
                $this->validateOnly('enrollment.pending_matter');
                $this->next($step); //dd($this->enrollment);         
                break;
            case '2':
                $this->validateOnly('enrollment.age');
                $this->validateOnly('enrollment.blood_type');
                $this->validateOnly('enrollment.weight');
                $this->validateOnly('enrollment.height');
                $this->validateOnly('enrollment.laterality');
                $this->validateOnly('enrollment.order_born');
                $this->validateOnly('enrollment.group_family');
                $this->validateOnly('enrollment.status_brother');
                $this->next($step);
                break;
            case '3':
                $this->validateOnly('enrollment.ci_representant');
                $this->validateOnly('enrollment.name_representant');
                $this->validateOnly('enrollment.relationship');
                $this->validateOnly('enrollment.profession_representant');
                $this->validateOnly('enrollment.phone_representant');
                $this->validateOnly('enrollment.email_representant');
                $this->validateOnly('enrollment.recommended_by');
                $this->next($step);
                break;
            case '4':
                $this->validateOnly('enrollment.coexistence');
                $this->validateOnly('enrollment.status_transport_private_vehicle');
                $this->validateOnly('enrollment.status_transport_public_vehicle');
                $this->validateOnly('enrollment.status_transport_walking');
                $this->validateOnly('enrollment.status_transport_other');
                $this->validateOnly('enrollment.transport_other');
                $this->next($step);
                break;
            case '5':
                $this->validateOnly('enrollment.status_vaccination_schedule');
                $this->validateOnly('enrollment.status_sports_potential');
                $this->validateOnly('enrollment.sports_potential');
                $this->validateOnly('enrollment.place_where_he_practices');
                $this->next($step);
                break;
            case '6':
                $this->validateOnly('enrollment.status_illness_cardiovascular');
                $this->validateOnly('enrollment.status_illness_cancer');
                $this->validateOnly('enrollment.status_illness_lupus');
                $this->validateOnly('enrollment.status_illness_diabetes');
                $this->validateOnly('enrollment.status_illness_renal_problems');
                $this->validateOnly('enrollment.status_illness_overweight');
                $this->validateOnly('enrollment.status_illness_other');
                $this->validateOnly('enrollment.illness_other');
                $this->next($step);
                break;
            case '7':
                $this->validateOnly('enrollment.status_conditions_intellectual_disability');
                $this->validateOnly('enrollment.status_conditions_motor_disability');
                $this->validateOnly('enrollment.status_conditions_visual_disability');
                $this->validateOnly('enrollment.status_conditions_hearing_impairment');
                $this->validateOnly('enrollment.status_conditions_outstanding_attitudes');
                $this->validateOnly('enrollment.status_conditions_autism');
                $this->validateOnly('enrollment.status_conditions_other');
                $this->validateOnly('enrollment.conditions_other');
                $this->next($step);
                break;
            case '8':
                $this->validateOnly('enrollment.status_treated_by_specialist');
                $this->validateOnly('enrollment.specialist');
                $this->validateOnly('enrollment.status_take_medication');
                $this->validateOnly('enrollment.medication');
                $this->next($step);
                break;
            case '9':
                $this->validateOnly('enrollment.mother_name');
                $this->validateOnly('enrollment.mother_lastname');
                $this->validateOnly('enrollment.mother_ci');
                $this->validateOnly('enrollment.mother_profession');
                $this->validateOnly('enrollment.mother_phones');
                $this->validateOnly('enrollment.mother_address');
                $this->validateOnly('enrollment.father_name');
                $this->validateOnly('enrollment.father_lastname');
                $this->validateOnly('enrollment.father_ci');
                $this->validateOnly('enrollment.father_profession');
                $this->validateOnly('enrollment.father_phones');
                $this->validateOnly('enrollment.father_address');
                $this->next($step);
                break;
        }
    }

    public function next($step)
    {
        $this->step = ($step < $this->limit) ? $step + 1 : $this->limit;
    }

    public function back($step)
    {
        $this->step = ($step > 1) ? $step - 1 : 1;
    }

    public function updatedEnrollmentGradoId($grado_id)
    {
        $grado = Grado::find($grado_id);
        $this->enrollment->pestudio_id = ($grado) ? $grado->pestudio_id : 1 ;
    }

    public function save()
    {
        $this->enrollment->user_id = Auth::id();
        $this->enrollment->photo = $this->upLoadImage($this->image);
        $this->validate();
        $enrollment = Enrollment::create($this->enrollment->all());

        if ($enrollment) {
            $title = "Datos guardados";
            $description = "Toda la información ha sido guardada éxitosamente!";
            $icon = "success";
        } else {
            $title = "No se han guardado los datos";
            $description = "Ocurrieron errores";
            $icon = "warning";
        }        

        $this->notification()->send([
            'title'       => $title,
            'description' => $description,
            'icon'        => $icon
        ]);

        $this->modalStart = true;
        $this->modalEmpty = false;
        $this->modalSearch = false;
        $this->modalAssistent = false;
    }

    public function upLoadImage($image)
    {
        $url = ($image) ? $image->store('images','enrollments') : null; //dd($url);
        return ($url) ? 'storage/enrollments/'.$url : null;
    }

}

/*

"mother_name" => null
"mother_lastname" => null
"mother_ci" => null
"mother_profession" => null
"mother_phones" => null
"mother_address" => null
"father_name" => null
"father_lastname" => null
"father_ci" => null
"father_profession" => null
"father_phones" => null
"father_address" => null

"status_treated_by_specialist" => null
"specialist" => null
"status_take_medication" => null
"medication" => null

{{--
"status_conditions_intellectual_disability" => null
"status_conditions_motor_disability" => null
"status_conditions_visual_disability" => null
"status_conditions_hearing_impairment" => null
"status_conditions_outstanding_attitudes" => null
"status_conditions_autism" => null
"status_conditions_other" => null
"conditions_other" => null
--}}

--------------------------------------
"status_illness_cardiovascular" => null
"status_illness_cancer" => null
"status_illness_lupus" => null
"status_illness_diabetes" => null
"status_illness_renal_problems" => null
"status_illness_overweight" => null
"status_illness_other" => null
"illness_other" => null

estudiant, step=1
"ci_estudiant" => "32446229"
"lastname" => "GOMEZ SANCHEZ"
"name" => "ANGELES TRINIDAD"
"gender" => "Femenino"
"date_birth" => "2008-04-11"
"town_hall_birth" => "INDEPENDENCIA"
"state_birth" => "YARACUY"
"country_birth" => "VENEZUELA"
"dir_address" => "URB. COLINAS DEL NORTE, AV 1 ENTRE CALLES 8 Y 10, CASA # 055, PRADOS DEL NORTE."
"grado_id" => "9"
"pestudio_id" => "1"
"institution" => "U.E.C. LOS ANGELES"
"pending_matter" => null
"literal" => "A"
"grupo_estable_id" => 0

--------------------------------------
"age" => 0
"blood_type" => "A"
"weight" => 0
"height" => 0
"laterality" => "IZQUIERDA"
"order_born" => "1"
"group_family" => 0
"status_brother" => "true"

--------------------------------------
"ci_representant" => "12079224"
"name_representant" => "ANA YOMAIRA SANCHEZ ORDOÑEZ"
"relationship" => "Madre"
"profession_representant" => ""
"phone_representant" => "04125081606"
"email_representant" => "anayso1306@gmail.com"
"recommended_by" => null

--------------------------------------
"coexistence" => null
"status_transport_private_vehicle" => null
"status_transport_public_vehicle" => null
"status_transport_walking" => null
"status_transport_other" => null
"transport_other" => null

"status_vaccination_schedule" => null
"status_sports_potential" => null
"sports_potential" => null
"place_where_he_practices" => null
*/