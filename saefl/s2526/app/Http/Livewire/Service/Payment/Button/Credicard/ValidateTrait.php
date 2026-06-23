<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

trait ValidateTrait
{
    protected $rules = [
        'name_representant' => 'required|string',
        'ci_representant' => 'required|exists:representants|numeric|between:100000,999999999',
        'card_number' => 'required|numeric|between:100000000000000,9999999999999999999',
        'type_ci' => 'required|max:1',
        'cvc' => 'required|max:3',
        'ammount_pay' => 'required|numeric|gt:0',
        'account_type' => 'required',
        'card_pin' => 'required|max:6',
        'card_type' => 'required',
        'date_expiration' => 'required|date_format:m/y',
        'holder_name'=>'required',
        'holder_id_doc'=>'required',
        'holder_id'=>'required',
        'token_bank'=>'required',
        ////////////////////////////////////////
        'access_token'=>'required',
        'bank_code'=>'required',
        'status_send_token_bank'=>'required|accepted',
        'ammount_holder_commission'=>'required',
        'status_payment_success'=>'accepted',
        'credicard_trace'=>'required',
        'credicard_created_at'=>'required',
        'status_estudiants_formaly'=>'accepted',
    ];

    protected $messages = [
        'ci_representant.required' => 'El campo :attribute es requerido.',
        'ci_representant.exists' => 'La CI del representante no se encuentra.',
        'type_ci.required' => 'El campo :attribute es requerido.',
        'type_ci.max' => 'El campo :attribute es requerido.',
        'card_number.between' => 'Debe tener entre 15 y 19 números.',
        ////////////////////////////////////////
        'access_token.required' => 'La conexión con Credicard falló, (900901)',
        'bank_code.required' => 'Fallo en los datos de la tarjeta, (900901)',
        'status_send_token_bank.accepted' => 'Falló el envío de la solicitud de código de autorización del banco',
        'ammount_holder_commission.required' => 'Falló la solicitud de obtensión del monto de la comisión por uso del servicio',
        'status_payment_success.accepted' => 'Falló el procesamiento del pago',
        'credicard_trace.required' => 'No se encuentra el número de referencia',
        'credicard_created_at.required' => 'No se encuentra la fecha de registro',
        'status_estudiants_formaly.accepted' => 'No se encontraron representados',
    ];

    protected $validationAttributes = [
        'ci_representant' => 'CI del representante',
        'ci_representant' => 'CI del representante',
        'type_ci' => 'Tipo',
        'ammount_pay' => 'Monto a pagar',
        'card_type' => 'Tipo de tarjeta',
        'card_number' => 'Nùmero de Tarjeta',
        'card_pin' => 'Clave de la tarjeta',
        'holder_name'=>'Nombre del Titular',
        'cvc'=>'CVC',
        'holder_id_doc'=>'Tipo de documento',
        'holder_id'=>'N. de RIF/CI',
        'holder_name'=>'Nombre del Titular',
        'token_bank'=>'Código de autorización del banco',
        // 'access_token'=>'La conexiòn con Credicard falló',

    ];

    public function validatedForStep()
    {
        $this->resetErrorBag();

        switch ($this->step) {
            case '1':
                $this->validateOnly("type_ci");
                $this->validateOnly("ci_representant");
                break;

            case '2':
                $this->validateOnly("ci_representant");
                $this->validateOnly("ammount_pay");
                break;

            case '3':
                $this->validateOnly("card_number");
                $this->validateOnly("cvc");
                $this->validateOnly("card_type");
                $this->validateOnly("date_expiration");
                $this->validateOnly("account_type");
                $this->validateOnly("card_pin");

                $this->validateOnly("holder_id_doc");
                $this->validateOnly("holder_type");
                $this->validateOnly("holder_id");
                $this->validateOnly("holder_name");
                break;

            case '4':
                // $this->validateOnly("token_bank");
                break;

            default:
                # code...
                break;
        }

    }

    public function validatedPayment()
    {
        $this->validateOnly('status_payment_success');
    }

    private function getBankCardValidation ($bank_code)
    {
        $data = null;
        switch ($bank_code) {
            case '0172': // BANCAMIGA
                $data = [
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;

            case '0169': // MI BANCO
                $data = [
                    "phone" => "4143454265",
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;

            case '0168': // BANCRECER
                $data = [
                    "phone" => "4143454265",
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;

            case '0177': // BANFANB
                $data = [
                    "phone" => "4143454265",
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;


            case '0163': // BANCO DEL TESORO
                $data = [
                    "phone" => "4143454265",
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;

            case '0114': // BANCARIBE
                $data = [
                    "phone" => "4143454265",
                    "rif"=> "V000011111",
                    "token"=> "123456"
                ];
                break;
        }
    }
}

?>
