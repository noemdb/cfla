<?php

namespace App\Livewire\Forms;

use App\Models\app\Admon\Banco;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaymentForm extends Form
{
    #[Validate]
    public $ci_representant,$representant_id,$type_pay,$method_pay_id,$number_i_pay,$phone,$date_transaction,$ammount,$observations,$comment,$status_approved,$status_apply,$banco_emisor_1,$phone_1,$banco_id_1,$method_pay_id_1,$number_i_pay_1,$date_transaction_1,$ammount_1,$observation_1,$image_1;
    public $image;
    
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
        'image_1' => 'nullable|string',
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
        'image_1'=>'Imagen',
    ];

    protected $messages = [
        'ammount_1.numeric' => 'El campo Monto debe ser un número con decimales separados por punto).',
    ];

}

