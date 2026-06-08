<?php

namespace App\Http\Livewire\General\Catchment;

use App\Http\Controllers\General\Email\CatchmentController;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Institucion;
use Livewire\Component;

class IndexComponent extends Component
{
    public Catchment $catchment;
    public $token,$list_comment;
    public $institucion;
    public $status_start=false,$status_intern=true;

    protected $rules = [
        'catchment.representant_name' => 'required|string',
        'catchment.representant_lastname' => 'required|string',
        'catchment.representant_date_birth' => 'required|date',
        'catchment.representant_ci' => 'required|string',
        'catchment.email' => 'email|required',
        'catchment.relationship' => 'required|string',
        'catchment.representant_phone' => 'required',
        'catchment.occupation' => 'nullable|string',
        'catchment.educational_level' => 'nullable|string',
        'catchment.educational_level' => 'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'catchment.representant_name' => $this->list_comment['representant_name'],
            'catchment.representant_lastname' => $this->list_comment['representant_lastname'],
            'catchment.representant_date_birth' => $this->list_comment['representant_date_birth'],
            'catchment.representant_ci' => $this->list_comment['representant_ci'],
            'catchment.email' => $this->list_comment['email'],
            'catchment.relationship' => $this->list_comment['relationship'],
            'catchment.representant_phone' => $this->list_comment['representant_phone'],
            'catchment.occupation' => $this->list_comment['occupation'],
            'catchment.educational_level' => $this->list_comment['educational_level'],
            'catchment.educational_level' => $this->list_comment['educational_level'],
        ];
    }

    public function mount($status_start=false,$status_intern=true)
    {
        $this->status_start = $status_start;                
        $this->status_intern = $status_intern;                

        $this->catchment = new Catchment;
        $this->list_comment = Catchment::COLUMN_COMMENTS;
        $this->institucion = Institucion::select('institucions.name', 'institucions.code', 'institucions.address')->first();
        
        $this->catchment->representant_date_birth="1995-05-24";

        // $this->status_start = true;
        // $this->test();
    }

    public function test()
    {
        $this->catchment->representant_name="Manuel";
        $this->catchment->representant_lastname="Perez";
        $this->catchment->representant_ci="12389234";
        $this->catchment->email="este_es_mi_email@gmail.com";
        $this->catchment->relationship="Padre";
        $this->catchment->representant_phone="0123456789";
        $this->catchment->occupation="profesional";
        $this->catchment->educational_level="universidad";
        $this->catchment->representant_date_birth="1995-05-24";
    }

    public function render()
    {
        $this->institucion = Institucion::select('institucions.name', 'institucions.code', 'institucions.address')->first();
        return view('livewire.general.catchment.index-component');
    }

    public function save()
    {
        $this->validate();
        $this->token = substr(str_replace(['+', '/', '=', '&'], '', password_hash(bin2hex(random_bytes(45)), PASSWORD_BCRYPT)), 0, 64);
        $arr = [
            'token'                   => $this->token,
            'representant_name'       => $this->catchment->representant_name,
            'representant_lastname'   => $this->catchment->representant_lastname,
            'representant_date_birth' => $this->catchment->representant_date_birth,
            'representant_ci'         => $this->catchment->representant_ci,
            'email'                   => $this->catchment->email,
            'occupation'              => $this->catchment->occupation,
            'relationship'            => $this->catchment->relationship,
            'educational_level'       => $this->catchment->educational_level,
            'representant_phone'      => $this->catchment->representant_phone,
        ]; //dd($arr);
        $this->catchment = Catchment::create($arr);

        $jobSend = new CatchmentController();
        $dataEmail = $jobSend->messegesSend($this->catchment->id);

        $this->catchment = New Catchment;
		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente, el primer paso ha sido completado. En la dirección de correo ingresada, tendrá las instrucciones siguientes.';
		$this->showSwal($title,$html);
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

    public function start()
    {
        $this->status_start=true;
    }

    public function restart()
    {
        $this->status_start = true;
        $this->catchment = new Catchment;
        $this->token = null;
        $this->resetValidation();
    }

}
