<?php

namespace App\Http\Livewire\Welcome\Payment\Report;

use App\Http\Controllers\Email\SendPaymentController;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Payment;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithFileUploads;

class ListComponent extends Component
{
    use ValidateTrait;
    use WithFileUploads;

    protected $listeners = ['showSwal'];

    public int $currentStep = 0;
    public int $totalSteps = 3;
    public $ci_representan;
    public $list_comment,$banco_list,$banco_emisor_list,$method_pay_list,$type_pay_list;
    public $status_save = false;
    public $banco_name,$toDate;
    public $image;

    public function updatedBancoId1($value)
    {
        $banco = Banco::find($this->banco_id_1);
        $this->banco_name = ($banco) ? $banco->name : null ;
    }

    public function render()
    {
        return view('livewire.welcome.payment.report.list-component');
    }

    public function mount()
    {
        $this->banco_emisor_list = Payment::LIST_BANK_EMISOR; //dd($this->banco_emisor_list);
        $this->banco_list = Banco::list_public_bancos();
        $this->method_pay_list = MetodoPago::method_pay_list();
        $this->list_comment = Payment::COLUMN_COMMENTS;
        $this->type_pay_list = Payment::LIST_TYPE_PAY;
        $this->toDate = Date::now()->format('d F Y');

        $this->status_save = false;
        $this->currentStep = 0;

        // $this->loadTest();
    }

    public function other()
    {
        $this->number_i_pay_1 = null;
        $this->image_1 = null;
        $this->image = null;
        $this->status_save = false;
        $this->currentStep = 1;
    }

    public function save()
    {
        $this->validate();

        $this->uploadImage();

        $representant = Representant::where('ci_representant',$this->ci_representant)->first();

        $inputs = [
            'ci_representant'=>$this->ci_representant,
            'name_representant'=>$representant->name,
            'ci_representant'=>$this->ci_representant,
            'type_pay'=>$this->type_pay,
            'phone'=>$this->phone,
            'comment'=>$this->comment,
            'phone_1'=>$this->phone_1,
            'number_i_pay_1'=>$this->number_i_pay_1,
            'banco_id_1'=>$this->banco_id_1,
            'banco_emisor_1'=>$this->banco_emisor_1,
            'method_pay_id_1'=>$this->method_pay_id_1,
            'date_transaction_1'=>$this->date_transaction_1,
            'ammount_1'=>$this->ammount_1,
            'observation_1'=>$this->observation_1,
            'image_1'=>$this->image_1,
        ]; //dd($inputs);

        $payment = Payment::create( $inputs ); //dd($inputs,$payment);

        $inputs['id'] = $payment->id; //dd($inputs);
        $inputs['representant_name'] = $representant->name; //dd($inputs);
        $inputs['number_i_pay'] = $this->number_i_pay_1; //dd($inputs);
        $inputs['ammount'] = $this->ammount_1; //dd($inputs);
        $inputs['type_pay'] = $this->type_pay; //dd($inputs);
        $inputs['date'] = Carbon::now()->format('d-m-Y h:i'); //dd($inputs);

        $mail = New SendPaymentController;
        $mail->collectionSend($representant->id,$inputs);

        $this->status_save = true;
        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente, se enviará un ticket de registro y seguimiento a su correo electrónico';
        $icon = 'success';
        $this->showSwal($title,$html,$icon);
    }

    public function uploadImage()
    {
        //dd($this->image);
        if ($this->image) {
            $this->image_1 = 'storage/payment/'.$this->image->store('images','payment');
        }
    }

    public function goStep($step)
    {
        $this->validatedForStep($step);
        $this->currentStep = $step;
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
