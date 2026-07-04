<?php

namespace App\Http\Livewire\Welcome\Payment\Report;

use Carbon\Carbon;

use Illuminate\Support\Str;

trait ValidateTrait
{
    public $ci_representant,$banco_emisor_1,$phone_1,$banco_id_1,$method_pay_id_1,$number_i_pay_1,$date_transaction_1,$ammount_1,$observation_1,$image_1,$type_pay,$phone,$comment;

    protected $rules = [
        'ci_representant' => 'required|exists:representants,ci_representant',
        'type_pay' => 'required|string',
        'phone' => 'nullable|string',
        'comment' => 'nullable|string',
        'phone_1' => 'nullable|string',
        'number_i_pay_1' => 'required|string|unique:ingresos,number_i_pay|unique:payments,number_i_pay_1',
        'banco_id_1' => 'required|integer',
        'banco_emisor_1' => 'required|string',
        'method_pay_id_1' => 'required|integer',
        'date_transaction_1' => 'required|date',
        'ammount_1' => 'required|numeric',
        'observation_1' => 'nullable|string',
        'image' => 'image|max:1024|nullable',
    ];
    protected $validationAttributes = [
        'ci_representant'=>'CI del representante',
        'type_pay'=>'Tipo de pago',
        'banco_emisor_1'=>'Banco emisor',
        'phone_1'=>'Teléf. emisor del Pago Móvil',
        'banco_id_1'=>'Banco receptor',
        'method_pay_id_1'=>'Método de Pago',
        'number_i_pay_1'=>'Núm. de referencia',
        'date_transaction_1'=>'Fecha de la transacción',
        'ammount_1'=>'Monto',
        'observation_1'=>'',
        'image'=>'Imagen',
    ];

    protected $messages = [
        'ammount_1.numeric' => 'El campo Monto debe ser un número con decimales separados por punto).',
    ];

    public function validatedForStep($step)
    {
        $this->resetErrorBag();

        switch ($step) {
            case '0':
                $this->clear();
                break;
            case '2':
                $this->validateOnly("ci_representant");
                $this->validateOnly("type_pay");
                $this->validateOnly("phone");
                $this->validateOnly("comment");
                $this->validateOnly("number_i_pay_1");
                $this->validateOnly("phone_1");
                $this->validateOnly("banco_id_1");
                $this->validateOnly("banco_emisor_1");
                $this->validateOnly("method_pay_id_1");
                $this->validateOnly("date_transaction_1");
                $this->validateOnly("ammount_1");
                $this->validateOnly("observation_1");
                $this->validateOnly("image");
                break;
            case '3':
                $this->validateOnly("number_i_pay_1");
                $this->validateOnly("phone_1");
                $this->validateOnly("banco_id_1");
                $this->validateOnly("banco_emisor_1");
                $this->validateOnly("method_pay_id_1");
                $this->validateOnly("date_transaction_1");
                $this->validateOnly("ammount_1");
                $this->validateOnly("observation_1");
                $this->validateOnly("image");
                break;
        }

    }

    public function loadTest()
    {
        $this->ci_representant = '16088226';
        $this->banco_emisor_1 = 'BANCO EXTERIOR';
        $this->phone_1 = '123456789';
        $this->banco_id_1 = 2;
        $this->banco_name = 'BANCARIBE';
        $this->method_pay_id_1 = 2;
        $this->number_i_pay_1 = Str::random(20);
        $this->date_transaction_1 = Carbon::now()->format('Y-m-d');
        $this->ammount_1 = 100;
        $this->observation_1 = '1ra cuota del mes en curso';
        $this->image_1 = null;
        $this->type_pay = 'Mensualidad actual';
        $this->phone = '123456789';
        $this->comment = 'Mensualidad actual';
    }

    public function clear()
    {
        $this->ci_representant =null;
        $this->banco_emisor_1 =null;
        $this->phone_1 =null;
        $this->banco_id_1 =null;
        $this->banco_name =null;
        $this->method_pay_id_1 =null;
        $this->number_i_pay_1 =null;
        $this->date_transaction_1 =null;
        $this->ammount_1 =null;
        $this->observation_1 =null;
        $this->image_1 =null;
        $this->type_pay =null;
        $this->phone =null;
        $this->comment =null;
        $this->status_save = false;
        $this->currentStep = 0;
    }

}

/*
,$banco_emisor_1,$phone_1,$banco_id_1,$method_pay_id_1,$number_i_pay_1,$date_transaction_1,$ammount_1,$observation_1,$image_1


banco_emisor_1
phone_1
banco_id_1
method_pay_id_1
number_i_pay_1
date_transaction_1
ammount_1
observation_1
image_1
*/
