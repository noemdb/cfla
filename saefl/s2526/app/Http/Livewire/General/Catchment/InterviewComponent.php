<?php

namespace App\Http\Livewire\General\Catchment;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use App\Http\Controllers\General\Email\CatchmentController;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class InterviewComponent extends Component
{
    use InterviewTrait;

    public Catchment $catchment;
    public CatchmentInterview $catchment_interview;

    public $list_comment,$list_comment_catchment,$list_catchment,$list_grado,$list_monthly_income,$list_religions;
    public $institucion,$interview_id;
    public $status_found,$status_consult,$status_save,$status_load_interview;
    public $modeIndex,$modeCreate;
    public $catchments,$catchment_count,$representant_name,$interviews_count,$representant_ci;
    public $currentStep=1;

    public function nextStep()
    {
        if ($this->currentStep < 7) {
            try {
                $this->dataValidate($this->currentStep);
                $this->currentStep++;
                $this->resetErrorBag(); // Limpiar errores al pasar de paso
            } catch (ValidationException $e) {
                $title = 'Error';
                $html = 'Por favor, complete todos los campos requeridos';
                $this->showSwal($title,$html,'error');
                throw $e;
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function dataValidate($currentStep)
    {
        switch ($currentStep) {
            case '1':
                $this->validate([
                    'catchment_interview.catchment_id' => 'required',
                    'catchment_interview.identification_number' => 'required',
                    'catchment_interview.full_name' => 'required',
                    'catchment_interview.age' => 'required|numeric|min:1',
                    'catchment_interview.relationship' => 'required',
                    'catchment_interview.phone_numbers' => 'required',
                    'catchment_interview.email' => 'required|email',
                    'catchment_interview.email_alternate' => 'required|email',
                    'catchment_interview.profession_occupation' => 'required|string',
                ]);
                break;

            case '2':
                $this->validate([
                    'catchment_interview.student_full_name' => 'required|string',
                    'catchment_interview.date_of_birth' => 'required|date',
                    'catchment_interview.student_age' => 'required|integer',
                    'catchment_interview.grade_year_aspiring' => 'required|integer',
                    'catchment_interview.has_siblings' => 'nullable|boolean',
                    'catchment_interview.sibling_name' => 'nullable|string',
                    'catchment_interview.sibling_grade_section' => 'nullable|integer',
                    'catchment_interview.tutor_teacher_name' => 'nullable|string',
                    'catchment_interview.tutor_teacher_phone' => 'nullable|string',
                ]);
                break;
            case '3':
                $this->validate([
                    'catchment_interview.living_with' => 'required|string',
                    'catchment_interview.other_person_origin' => 'nullable|string',
                    'catchment_interview.reason_for_living_with_other' => 'nullable|string',
                    'catchment_interview.num_family_group_members' => 'required|integer',
                    'catchment_interview.num_people_financially_dependent' => 'required|integer',
                    'catchment_interview.person_responsible_attending' => 'required|string',
                    'catchment_interview.place_person_responsible_attending' => 'required|string',
                    'catchment_interview.position_person_responsible_attending' => 'nullable|string',
                ]);
                break;
            case '4':
                $this->validate([
                    'catchment_interview.monthly_income' => 'required|string',
                    'catchment_interview.num_people_contributing' => 'required|integer',
                    'catchment_interview.income_source' => 'required|string',
                    'catchment_interview.able_to_pay_dollars' => 'nullable|boolean',
                    'catchment_interview.able_to_pay_bolivars' => 'nullable|boolean',
                    'catchment_interview.has_payment_responsible' => 'nullable|boolean',
                    'catchment_interview.person_guarantor_name_phone' => 'nullable|string',
                ]);
                break;
            case '5':
                $this->validate([
                    'catchment_interview.knowledge_of_school' => 'nullable|string',
                    'catchment_interview.studied_at_school' => 'nullable|boolean',
                    'catchment_interview.year_of_graduation' => 'nullable|integer',
                    'catchment_interview.academic_director' => 'nullable|string',
                    'catchment_interview.school_director' => 'nullable|string',
                    'catchment_interview.teachers_worked_at_school' => 'nullable|string',
                    'catchment_interview.reason_for_choosing_institution' => 'required|string',
                    'catchment_interview.recommendation_from_school' => 'nullable|boolean',
                    'catchment_interview.recommender_name' => 'nullable|string',
                    'catchment_interview.recommender_affinity' => 'nullable|string',
                    'catchment_interview.recommender_phone' => 'nullable|string',
                     'catchment_interview.family_member_studied_worked_at_school' => 'nullable|boolean',
                ]);
                break;
            case '6':
                $this->validate([
                    'catchment_interview.agreement_to_code_of_conduct' => 'required|boolean',
                    'catchment_interview.respect_communication_channels' => 'nullable|boolean',
                    'catchment_interview.ensure_compliance_with_school_activities' => 'nullable|boolean',
                    'catchment_interview.comply_with_school_uniform' => 'nullable|boolean',
                    'catchment_interview.respect_authorities_directives' => 'nullable|boolean',
                    'catchment_interview.pay_installments_on_time' => 'nullable|boolean',
                    'catchment_interview.respect_parent_assembly_agreements' => 'nullable|boolean',
                    'catchment_interview.pay_overdue_installments' => 'nullable|boolean',
                ]);
                break;
            case '7':
                $this->validate([
                    'catchment_interview.religion' => 'nullable|string',
                    'catchment_interview.awareness_of_catholic_school_affiliation' => 'nullable|boolean',
                    'catchment_interview.agreement_to_participate_in_catholic_activities' => 'nullable|boolean',
                    'catchment_interview.justification_for_not_participating_in_catholic_activities' => 'nullable|string',
                    'catchment_interview.agreement_to_catholic_formation' => 'nullable|boolean',
                ]);
                break;

            default:
                # code...
                break;
        }
    }

    public function mount()
    {
        $this->list_comment = CatchmentInterview::COLUMN_COMMENTS;
        $this->list_monthly_income = CatchmentInterview::list_monthly_income();
        $this->list_religions = CatchmentInterview::list_religions();
        $this->list_comment_catchment = Catchment::COLUMN_COMMENTS;
        $this->list_grado = Grado::list_pestudio_grado_inscripcion(); //dd($this->list_grado);

        $this->status_found = false;
        $this->status_consult = false;
        // $this->status_save = false;
        $this->interviews_count = 0;
        $this->catchment_count = 0;

        $this->modeIndex = true;
        $this->modeCreate = false;

        $this->catchment = New Catchment;
        $this->catchment_interview = New CatchmentInterview;
        $this->list_catchment = collect();
    }

    public function render()
    {
        return view('livewire.general.catchment.interview-component');
    }

    public function search()
    {
        $this->status_consult = true;
        $this->status_save = false;
        $this->status_load_interview = false;
        $this->representant_ci = $this->catchment->representant_ci;

        // Obtener todos los censos del representante
        $allCatchments = Catchment::with(['grado', 'catchmentInterviews'])
            // ->where('representant_ci','like', ''.$this->catchment->representant_ci)
            ->where('representant_ci','like', '%'.$this->catchment->representant_ci.'%')
            ->get();

        $this->catchment_count = $allCatchments->count();
        $this->representant_name = $allCatchments->isNotEmpty()
            ? $allCatchments->first()->full_name_representant
            : null;

        // Obtener censos sin entrevistas
        $catchmentsWithoutInterviews = $allCatchments->filter(function ($catchment) {
            return $catchment->catchmentInterviews->isEmpty();
        });

        // Obtener conteo de entrevistas existentes
        $this->interviews_count = CatchmentInterview::where(
            'identification_number', $this->catchment->representant_ci
        )->count();

        if ($catchmentsWithoutInterviews->isNotEmpty()) {
            $this->list_catchment = $catchmentsWithoutInterviews->mapWithKeys(function ($item) {
                return [$item->id => $item->full_name_grade];
            });
            $this->status_found = true;
        } else {
            $this->list_catchment = collect();
            $this->status_found = false;
        }
    }

    public function create()
    {
        $this->modeIndex = false;
        $this->modeCreate = true;

        $this->loadData();
    }

    public function loadData()
    {
        $catchment = Catchment::find($this->catchment_interview->catchment_id);

        if ($catchment) {
            $this->catchment_interview->full_name = $catchment->full_name_representant ;
            $this->catchment_interview->identification_number = $catchment->representant_ci ;
            $this->catchment_interview->age = $catchment->representant_age ;
            $this->catchment_interview->relationship = $catchment->relationship ;
            $this->catchment_interview->phone_numbers = $catchment->representant_phone ;
            $this->catchment_interview->email = $catchment->email ;

            $this->catchment_interview->student_full_name = $catchment->full_name ;
            $this->catchment_interview->date_of_birth = $catchment->date_birth ;
            $this->catchment_interview->student_age = ($catchment->age) ? Carbon::parse($catchment->date_birth)->age : null ;
            $this->catchment_interview->grade_year_aspiring = ($catchment->grado) ? $catchment->grado->id : null ;
        }

        $catchment_interview = CatchmentInterview::where('identification_number',$this->catchment->representant_ci)->orderBy('created_at','desc')->first(); //dd($catchment_interview);
        if ($catchment_interview) {
            $this->status_load_interview = true;
            $interview_arr = $catchment_interview->toArray(); //dd($interview_arr);
            foreach ($interview_arr as $field => $value) { //dd($field,$value);
                $this->catchment_interview->$field = (empty($this->catchment_interview->$field)) ? $value : $this->catchment_interview->$field ;
            }
        } else {
            $this->status_load_interview = false;
        }

    }

    public function save()
    {
        $this->validate();

        $this->catchment_interview->save();

        $savedEmail = $this->catchment_interview->email;
        $savedInterviewId = $this->catchment_interview->id;
        $savedCatchmentId = $this->catchment_interview->catchment_id;

        $this->status_save = true;
        $this->interview_id = $savedInterviewId;

        $title = '¡Excelente, buen trabajo!';
        $html = 'Operación realizada exitosamente. Se ha enviado la información a la dirección de correo: '.$savedEmail;
        $this->showSwal($title, $html);

        $jobSend = new CatchmentController();
        $jobSend->messegesInterview($savedInterviewId);

        // Reset únicamente del modelo
        $this->catchment_interview = new CatchmentInterview();

        // conservar referencia del censo si se necesita
        $this->catchment_interview->catchment_id = $savedCatchmentId;

        // reiniciar wizard
        $this->currentStep = 1;
        $this->mount();

        // limpiar errores de validación
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedCatchmentInterviewCatchmentId($value)
    {
        //dd($value);
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>60000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'icon' => $icon,
            'allowOutsideClick' => false,
        ]);
    }

}
