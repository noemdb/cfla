<?php

namespace App\Http\Livewire\General\Enrollment;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\StudentRecord;
use App\Models\app\Estudiante\Representant;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Grado;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    use RulesTrait;
    public Enrollment $enrollment;
    public $token;
    public $representant,$estudiants_formaly,$estudiants_enrollments,$estudiants_enrollments_in,$estudiant,$estudiant_index,$status_save;

    public $status_representant;

    public $list_comment,$list_grado,$list_country_birth,$list_relationship,$list_oinstitucions,$list_potencial;

    public $step,$limit=5;

    protected $listeners = ['EstudiantFocus'];

    public function goToBack()
    {
        $this->validateStep($this->step);
        $this->step = ($this->step == 1) ? 1 : ($this->step - 1);
    }
    public function goToNext()
    {
        $this->validateStep($this->step);
        $this->step = ($this->step == $this->limit) ? $this->limit : ($this->step + 1);
    }

    public function save()
    {
        // dd($this->enrollment);
        $this->validate();
        // dd($this->enrollment);
        $this->enrollment->save();
        $this->enrollment = New Enrollment;
		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente. Puedes continuar.';
		$this->showSwal($title,$html);
        $this->emit('EstudiantFocus');
        $this->step = 1;
        $this->loadRepresentant($this->token);
        // session()->flash('operp_ok', 'Guardado!!!.');
        $this->status_save = true;
    }

    public function EstudiantFocus()
    {
        //
    }

    public function loadRepresentant($token)
    {
        $this->status_save = false;
        $representant = Representant::where('ci_representant',$token)->first(); //dd($representant);
        if ($representant) {
            $this->representant = $representant;
            $this->status_representant = true;
            $this->estudiants_formaly = Estudiant::select('estudiants.*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'prosecucions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')

            ->where('representants.ci_representant',$token)
            ->get()
            ; //dd($estudiants_formaly);

            $this->estudiants_enrollments = $representant->estudiants_enrollments; 
            $this->estudiants_enrollments_in = $representant->estudiants_enrollments_in; //dd($this->estudiants_enrollments_in);
            if ($this->estudiants_enrollments->isNotEmpty()) {
                $this->estudiant = $this->estudiants_enrollments->first();
                $this->estudiant_index = $this->estudiants_enrollments_in->count() + 1;
                $this->loadEstudiant();
            } else {
                $this->estudiant = null;
            }

        } else {
            $this->representant = null;
            $this->status_representant = false;
            $this->estudiant = null;
            $this->estudiants_formaly = collect(New Estudiant);
            $this->estudiants_enrollments = collect(New Estudiant);
        }
    }

    public function mount($token)
    {
        $this->token = $token;
        $this->status_save = false;
        $this->loadRepresentant($this->token);
        $this->step = 1;

        $this->list_comment = Enrollment::COLUMN_COMMENTS;
        $this->list_potencial = Enrollment::LIST_POTENCIAL;
        $this->list_grado = Grado::list_pestudio_grado();
		$this->list_country_birth = Estudiant::list_country_birth();
		$this->list_relationship = ['Madre'=>'Madre','Padre'=>'Padre','Hermano(a)'=>'Hermano(a)','Abuelo(a)'=>'Abuelo(a)','Otro'=>'Otro'];
		$this->list_oinstitucions = Oinstitucion::list_oinstitucions()->take(1);
        // $this->enrollment = New Enrollment;
    }

    public function loadEstudiant()
    {
        $this->enrollment = New Enrollment;
        if ($this->estudiant) {
            $this->enrollment->ci_estudiant = $this->estudiant->ci_estudiant;
            $this->enrollment->name = $this->estudiant->name;
            $this->enrollment->lastname = $this->estudiant->lastname;
            $this->enrollment->gender = $this->estudiant->gender;
            $this->enrollment->date_birth = $this->estudiant->date_birth;
            $this->enrollment->city_birth = $this->estudiant->city_birth;
            $this->enrollment->town_hall_birth = $this->estudiant->town_hall_birth;
            $this->enrollment->state_birth = $this->estudiant->state_birth;
            $this->enrollment->country_birth = $this->estudiant->country_birth;
            $this->enrollment->dir_address = $this->estudiant->dir_address;
            $this->enrollment->phone = $this->estudiant->phone;


            $estudiant = $this->estudiant;
            $grado = $estudiant->grado;
            $grado_next = ($grado) ? $estudiant->getGradoNext($grado->id) : null;
            $this->enrollment->grado_id = ($grado_next) ? $grado_next->id : null;

            if ($this->representant) {
                $this->enrollment->ci_representant = $this->representant->ci_representant;
                $this->enrollment->name_representant = $this->representant->name;
                $this->enrollment->phone_representant = $this->representant->cellphone;
                $this->enrollment->email_representant = $this->representant->email;
            }
        }

    }

    public function render()
    {
        return view('livewire.general.enrollment.index-component');
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
}
