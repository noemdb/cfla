<?php

namespace App\Http\Livewire\Administracion\Enrollment;
// livewire.administracion.enrollment.edit-component

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\Representant;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Grado;
use Livewire\Component;

class EditComponent extends Component
{
    use RulesTrait;
    public Enrollment $enrollment;
    public $token;
    public $representant,$estudiants_formaly,$estudiants_enrollments,$estudiants_enrollments_in,$estudiant,$estudiant_index;

    public $status_representant;

    public $list_comment,$list_grado,$list_country_birth,$list_relationship,$list_oinstitucions,$list_potencial,$list_blood_type;

    public $step,$limit=5;

    public function goToBack()
    {
        $this->step = ($this->step == 1) ? 1 : ($this->step - 1);
    }
    public function goToNext()
    {
        $this->validateStep($this->step);
        $this->step = ($this->step == $this->limit) ? $this->limit : ($this->step + 1);
    }

    public function save()
    {
        $this->validate();
        $this->enrollment->save();
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente. Puedes continuar.';
		$this->showSwal($title,$html);
        $this->step = 1;
        session()->flash('operp_ok', 'Guardado!!!.');
        return redirect()->route('administracion.enrollments.crud');
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'type' => 'warning',
        ]);
    }

    public function loadRepresentant($token)
    {
        $representant = Representant::where('ci_representant',$token)->first(); //dd($representant);
        if ($representant) {
            $this->representant = $representant;
            $this->status_representant = true;
            $this->estudiant = true;
        }
    }

    public function loadEstudiante($token)
    {
        $estudiant = Estudiant::where('ci_estudiant',$token)->first(); //dd($representant);
        if ($estudiant) {
            $this->estudiant = $estudiant;
        }
    }

    public function mount(Enrollment $enrollment)
    {
        $this->enrollment = Enrollment::findOrFail($enrollment->id);
        $this->token = $this->enrollment->ci_representant;
        $this->loadRepresentant($this->enrollment->ci_representant);
        $this->loadEstudiante($this->enrollment->ci_estudiant);
        $this->step = 1;

        $this->list_comment = Enrollment::COLUMN_COMMENTS;
        $this->list_potencial = Enrollment::LIST_POTENCIAL;
        $this->list_blood_type = Enrollment::list_blood_type();
        $this->list_grado = Grado::list_pestudio_grado();
		$this->list_country_birth = Estudiant::list_country_birth();
		$this->list_relationship = ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
		$this->list_oinstitucions = ['U. E. COLEGIO FRAY LUIS AMIGÓ'=>'U. E. COLEGIO FRAY LUIS AMIGÓ'];
        // $this->enrollment = New Enrollment;
    }

    public function render()
    {
        return view('livewire.administracion.enrollment.edit-component');
    }

    public function validateStep($step)
    {
        switch ($step) {
            case '1':
                $this->validateOnly('enrollment.ci_estudiant');
                $this->validateOnly('enrollment.name');
                $this->validateOnly('enrollment.lastname');
                $this->validateOnly('enrollment.gender');
                $this->validateOnly('enrollment.date_birth');
                $this->validateOnly('enrollment.town_hall_birth');
                $this->validateOnly('enrollment.state_birth');
                $this->validateOnly('enrollment.country_birth');
                $this->validateOnly('enrollment.dir_address');
                $this->validateOnly('enrollment.grado_id');
                $this->validateOnly('enrollment.institution');
                break;
            case '2':
                $this->validateOnly('enrollment.ci_representant');
                $this->validateOnly('enrollment.name_representant');
                $this->validateOnly('enrollment.relationship');
                $this->validateOnly('enrollment.phone_representant');
                $this->validateOnly('enrollment.email_representant');
                $this->validateOnly('enrollment.profession_representant');
                $this->validateOnly('enrollment.recommended_by');
                break;
            case '3':
                $this->validateOnly('enrollment.coexistence');
                $this->validateOnly('enrollment.status_transport_private_vehicle');
                $this->validateOnly('enrollment.status_transport_public_vehicle');
                $this->validateOnly('enrollment.status_transport_walking');
                $this->validateOnly('enrollment.status_transport_other');
                $this->validateOnly('enrollment.transport_other');
                $this->validateOnly('enrollment.status_vaccination_schedule');
                $this->validateOnly('enrollment.status_sports_potential');
                $this->validateOnly('enrollment.sports_potential');
                $this->validateOnly('enrollment.place_where_he_practices');
                $this->validateOnly('enrollment.blood_type');
                $this->validateOnly('enrollment.weight');
                $this->validateOnly('enrollment.height');
                $this->validateOnly('enrollment.status_brother');
                $this->validateOnly('enrollment.order_born');

               $this->validateOnly('enrollment.laterality');

                break;
            case '4':
                $this->validateOnly('enrollment.status_illness_cardiovascular');
                $this->validateOnly('enrollment.status_illness_cancer');
                $this->validateOnly('enrollment.status_illness_lupus');
                $this->validateOnly('enrollment.status_illness_diabetes');
                $this->validateOnly('enrollment.status_illness_renal_problems');
                $this->validateOnly('enrollment.status_illness_overweight');
                $this->validateOnly('enrollment.status_illness_other');
                $this->validateOnly('enrollment.illness_other');
                $this->validateOnly('enrollment.status_conditions_intellectual_disability');
                $this->validateOnly('enrollment.status_conditions_motor_disability');
                $this->validateOnly('enrollment.status_conditions_visual_disability');
                $this->validateOnly('enrollment.status_conditions_hearing_impairment');
                $this->validateOnly('enrollment.status_conditions_outstanding_attitudes');
                $this->validateOnly('enrollment.status_conditions_autism');
                $this->validateOnly('enrollment.status_conditions_other');
                $this->validateOnly('enrollment.conditions_other');
                $this->validateOnly('enrollment.status_treated_by_specialist');
                $this->validateOnly('enrollment.specialist');
                break;

            case '5':
                $this->validateOnly('enrollment.mother_name');
                $this->validateOnly('enrollment.mother_lastname');
                $this->validateOnly('enrollment.mother_ci');
                $this->validateOnly('enrollment.mother_profession');
                $this->validateOnly('enrollment.mother_phones');
                $this->validateOnly('enrollment.father_name');
                $this->validateOnly('enrollment.father_lastname');
                $this->validateOnly('enrollment.father_ci');
                $this->validateOnly('enrollment.father_profession');
                $this->validateOnly('enrollment.father_phones');
                break;

            default: break;
        }
    }
}
//grado_next
/*
5
mother_name
mother_lastname
mother_ci
mother_profession
mother_phones
father_name
father_lastname
father_ci
father_profession
father_phones

4
status_illness_cardiovascular
status_illness_cancer
status_illness_lupus
status_illness_diabetes
status_illness_renal_problems
status_illness_overweight
status_illness_other
illness_other
status_conditions_intellectual_disability
status_conditions_motor_disability
status_conditions_visual_disability
status_conditions_hearing_impairment
status_conditions_outstanding_attitudes
status_conditions_autism
status_conditions_other
conditions_other
status_treated_by_specialist
specialist

step 1
ci_estudiant
name
lastname
gender
date_birth
town_hall_birth
state_birth
country_birth
dir_address
grado_id
institution

2
ci_representant
name_representant
relationship
phone_representant
email_representant

3
coexistence
status_transport_private_vehicle
status_transport_public_vehicle
status_transport_walking
status_transport_other
transport_other
status_vaccination_schedule
status_sports_potential
sports_potential
place_where_he_practices

*/
