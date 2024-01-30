<?php

namespace App\Livewire\App\Payment;

use App\Livewire\Forms\PaymentForm;
use App\Models\app\Admon\Banco;
use App\Models\app\Admon\MetodoPago;
use App\Models\app\Admon\Payment;
use App\Models\app\Learner\Representant;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use WireUi\Traits\Actions;

class IndexComponent extends Component
{
    use Actions;

    use WithFileUploads;
    #[Validate('image|max:1024')] // 1MB Max
    public $image;

    public $ci;
    public $step = 0, $limit = 3;
    public $modalStart,$modalSearch,$modalAssistent,$modalEmpty;
    public Representant $representant;
    public PaymentForm $payment;
    public $list_comment,$list_bank,$method_pay_list,$type_pay_list,$banco_emisor_list;
    public $toDate,$banco;

    public function loadTest()
    {
        $this->payment->ci_representant = '14608133';
        $this->payment->type_pay = 'Mensualidad actual';
        $this->payment->phone = '12345678';
        $this->payment->comment = '#####################';
        $this->payment->phone_1 = '12345678';
        $this->payment->number_i_pay_1 = rand(1000000,100000000);
        $this->payment->banco_id_1 = 2;
        $this->payment->banco_emisor_1 = 'BANCO DE VENEZUELA';
        $this->payment->method_pay_id_1 = 3;
        $this->payment->date_transaction_1 = '2024-01-25';
        $this->payment->ammount_1 = '10000';
        $this->payment->observation_1 = '#################';
        
    }

    public function upLoadImage($image)
    {
        $url = ($image) ? $image->store('images','payments') : null; //dd($url);
        return ($url) ? 'storage/payment/'.$url : null;
    }

    public function save()
    {
        $this->payment->image_1 = $this->upLoadImage($this->image);

        $payment = Payment::create($this->payment->all());

        if ($payment) {
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

        $this->modalAssistent = false;

    }    

    public function mount()
    {
        $this->ci = '14608133';
        $this->modalStart = true;

        $this->banco_emisor_list = Payment::LIST_BANK_EMISOR; //dd($this->banco_emisor_list);
        $this->list_comment = Payment::COLUMN_COMMENTS;
        $this->list_bank = Banco::list_public_bancos();
        $this->method_pay_list = MetodoPago::method_pay_list();
        $this->type_pay_list = Payment::LIST_TYPE_PAY;
        $this->toDate = Carbon::now()->format('d F Y');

        $this->loadTest();
    }

    public function render()
    {
        return view('livewire.app.payment.index-component');
    }

    public function search()
    {
        $this->resetValidation();
        $representant = Representant::where('ci_representant', $this->ci)->first(); //dd($representant);
        if ($representant) {
            $this->representant = $representant;
            $this->step = 1;
            $this->payment->ci_representant = $representant->ci_representant;
            $this->payment->representant_id = $representant->representant_id;
            $this->modalAssistent = true;
        } else {
            $this->modalEmpty = true;
            $this->modalStart = true;
        }
        $this->modalSearch = false;
        // $this->modalStart = false;        
    }

    public function setStart()
    {
        $this->modalSearch = true;
        $this->modalStart = false;
        $this->modalAssistent = false;
        $this->modalEmpty = false;
    }

    public function validatedForStep($step)
    {
        $this->resetErrorBag();

        switch ($step) {
            case '1':

                $this->validateOnly("payment.ci_representant");
                $this->validateOnly("payment.type_pay");
                $this->validateOnly("payment.phone");
                $this->validateOnly("payment.comment");
                $this->next($step);
                break;
            case '2':
                $this->validateOnly("payment.number_i_pay_1");
                $this->validateOnly("payment.phone_1");
                $this->validateOnly("payment.banco_id_1");
                $this->validateOnly("payment.banco_emisor_1");
                $this->validateOnly("payment.method_pay_id_1");
                $this->validateOnly("payment.date_transaction_1");
                $this->validateOnly("payment.ammount_1");
                $this->validateOnly("payment.observation_1");
                $this->validateOnly("payment.image_1");
                $this->next($step);
                break;
            case '3':
                $this->validate();
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
}
