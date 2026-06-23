<?php

namespace App\Http\Livewire\General\Catchment;

use App\Http\Controllers\General\Email\CatchmentController;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use Livewire\Component;

class RegisterComponent extends Component
{
    use RulesTrait;

    public $step=1,$limit=4;
    public $token,$status_token;
    public Catchment $catchment;
    public $list_grado,$list_comment,$list_oinstitucions;
    public $status_register,$status_institucion_not_found;
    public $institucion;

    public function save()
    {       
        $this->validate(); //dd($this->validate());

        $this->catchment->status_active = false;

        $this->catchment->save();
        
        $this->status_register = true;        
        $this->status_institucion_not_found = true;        

        $jobSend = new CatchmentController();
        $dataEmail = $jobSend->messegesSendRegister($this->catchment->id);

		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente. Se ha enviado el cronograma de actividades a la dirección de correo: '.$this->catchment->email;
		$this->showSwal($title,$html);
    }

    public function mount($token)
    {
        $this->step = 1;
        $this->token = $token;
        $this->catchment = ($token) ? Catchment::where('token',$token)->where('status_active',true)->first() : null; //dd($this->catchment);
        $this->token = ($this->catchment) ? $this->catchment->token : null ;
        $this->status_token = ($this->catchment) ? true : false ;
        $this->list_oinstitucions = Oinstitucion::list_oinstitucions(true);
        $this->list_comment = Catchment::COLUMN_COMMENTS;
        $this->list_grado = Grado::list_pestudio_grado_inscripcion(); //dd($this->list_grado);
        $this->institucion = Institucion::select('institucions.name', 'institucions.code', 'institucions.address')->first();

        $this->catchment->status_foreign = false;
        $this->catchment->status_siblings_college = false;
        $this->catchment->date_birth = "2015-01-01";
    }

    public function render()
    {
        return view('livewire.general.catchment.register-component');
    }

    public function next($step)
    {
        $this->validateStep($step);
        $step++;
        $this->step = ($step<=$this->limit) ? $step : 1 ;
    }

    public function back($step)
    {
        $step--;
        $this->step = ($step<=1) ? 1 : $step ;
    }

    public function validateStep($step)
    {
        switch ($step) {
            case '2':
                $this->validateOnly('catchment.firstname');
                $this->validateOnly('catchment.lastname');
                $this->validateOnly('catchment.grade');
                $this->setGroupId();
                $this->validateOnly('catchment.group_id');
                $this->validateOnly('catchment.date_birth');
                $this->validateOnly('catchment.status_foreign');
                $this->validateOnly('catchment.country_foreign');
                $this->validateOnly('catchment.status_siblings_college');
                $this->validateOnly('catchment.brothers');
                $this->validateOnly('catchment.gender');
                $this->validateOnly('catchment.origin');
                break;
            case '3':
                $this->validateOnly('catchment.reason_interest');
                break;
            default: break;
        }
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
            'icon' => 'success',
            'allowOutsideClick' => false,
        ]);
    }

    public function updatedCatchmentGrade ($value)
    {
        $catchment = Catchment::where('token',$this->token)->first();
        $group_id = ($catchment) ? $catchment->getGroupIdEnable($value) : null; 
        $this->catchment->group_id = $group_id;
        $this->validateOnly('catchment.group_id');
    }

    public function setGroupId ()
    {
        $catchment = Catchment::where('token',$this->token)->first();
        $group_id = $catchment->getGroupIdEnable($this->catchment->grade); 
        $this->catchment->group_id = $group_id;
    }
    
}
