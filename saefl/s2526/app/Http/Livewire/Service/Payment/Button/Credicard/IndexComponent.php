<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;

use Carbon\Carbon;

use Illuminate\Support\Str;

use Exception;
use Livewire\Component;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use App\Models\app\Estudiante\Representant;

use App\Http\Livewire\Service\Payment\Button\Credicard\ValidateTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\StepperTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\PaymentTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\setDataTestTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\getUrlTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\FunctionTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\UpdatedTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\RequestTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\RegistroPagoTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\ErrorsApiTrait;
use App\Http\Livewire\Service\Payment\Button\Credicard\VariblesTrait;
use App\Models\app\Services\Sequence;
// use Seffeng\LaravelRSA\Facades\RSA;
// use Seffeng\LaravelRSA\Exceptions\RSAException;

// use Seffeng\Cryptlib\Crypt;

// use Seffeng\LaravelRSA\Facades\RSA;

// use phpseclib\Crypt\RSA;

use Seffeng\LaravelRSA\Facades\RSA;


class IndexComponent extends Component
{
    use ValidateTrait;
    use StepperTrait;
    use PaymentTrait;
    use setDataTestTrait;
    use getUrlTrait;
    use FunctionTrait;
    use UpdatedTrait;
    use RequestTrait;
    use RegistroPagoTrait;
    use ErrorsApiTrait;

    use VariblesTrait;

    public function mount()
    {
        // $this->token = Str::random(20);
        $this->token = Carbon::now()->timestamp;
        $this->step = 1;
        $this->modeAssistent = true;
        $this->modeConnected = false;
        $this->modeSendTokenBank = false;
        $this->exception_errors = null;
        $this->modeTerms = false;
        $this->modeTest = false;
        // $this->modeTest = (env('APP_ENV')=='local') ? true : false;

        $this->currency = 'VED';
        $this->bank_type = 'CCR';
        $this->product_name = 'PAY_BUTTON';

        $this->card_pin_type = "password";
        $this->cvc_type = "password";

        $this->totalPay = true;

        $this->expires_in = null;
        $this->access_token = null;

        $this->status_send_token_bank = false;
        $this->status_payment_success = false;
        $this->status_payment_error = false;
        $this->status_request_send_token_bank = false;

        $this->cuentaxpagar_name = null;
        $this->abono_ingreso_ammount = null;
        $this->abono_exchange_ammount = null;

        // $this->loadTDDExterior();
        // $this->loadTDDVenezuela();
        // $this->loadTDCMercantil();
        // $this->loadTDDTesoro();
        // $this->loadTDDBancaribe();

        $this->setExchangeRateAmmount();

        // if ($this->modeTest) {
        //     $this->setDataTest();
        //     $this->ci_representant = '18365060';
        // }

        $this->resetResult();

    }

    public function modeTermsToggle()
    {
        $this->modeTerms = ! $this->modeTerms;
    }


    public function render()
    {
        return view('livewire.service.payment.button.credicard.index-component');
    }

    public function goStep($step)
    {
        $this->saveState($step);

        $this->setExchangeRateAmmount();

        $this->setRepresentant($this->ci_representant);

        $this->validatedForStep();

        switch ($step) {
            case '1': $this->goHome(); break;
            case '2': $this->goTabSetAmmountPay($step); break;
            case '3': $this->goTabSetDataCard($step); break;
            case '4': $this->goConnecting($step); break;
            case '5': $this->goPayment($step); break;
            case '6': $this->paymentProcess($step); break;
        }
    }

    private function saveState($step)
    {
        if ($this->ci_representant) {
            $arr = [
                'token' => $this->token,
                'ci_representant' => $this->ci_representant,
                'step' => $this->step,
                'card_number' => $this->card_number,
                'type_ci' => $this->type_ci,
                'cvc' => $this->cvc,
                'ammount_pay' => $this->ammount_pay,
                'expiration_month' => $this->expiration_month,
                'expiration_year' => $this->expiration_year,
                'date_expiration' => $this->date_expiration,
                'account_type' => $this->account_type,
                'card_pin' => $this->card_pin,
                'card_type' => $this->card_type,
                'card_type_holder' => $this->card_type_holder,
                'holder_name' => $this->holder_name,
                'holder_id_doc' => $this->holder_id_doc,
                'holder_id' => $this->holder_id,
                'access_token' => $this->access_token,
                'date_expires' => $this->date_expires,
                'token_bank' => $this->token_bank,
                'ammount_pay' => $this->ammount_pay,
                'ammount_pay_exchange' => $this->ammount_pay_exchange,
                'exchange_ammount' => $this->exchange_ammount,
            ];

            Sequence::create($arr);
        }
    }

    private function paymentProcess()
    {
        $this->step = 6;

        $response = null;

        $client = $this->getClient(); //dd($client);

        $urlRequest = $this->getUrlPaymentService(); //dd($urlRequest);

        try {
            if ($this->modeTest == false) {
                $arr = [
                    'headers' => [  'Authorization' => 'Bearer ' . $this->access_token ],
                    'query' => [
                        'payment_type' => 'CARD_PAYMENT',
                    ],
                    'json' => $this->setBody(),
                ]; //dd($arr);
                $response = $client->post(
                    $urlRequest,
                    $arr,
                );
                $data = json_decode($response->getBody()); //dd($data);
                $status = $response->getStatusCode();
            } else {
                $data = $this->getResponse();
                $status = '200';
            }

            switch ($status) {
                case '200':
                    if (!empty($data->credicard->success)) {
                        $result = $this->registroPago($data);
                        $this->credicard_message = $data->credicard->message;
                        $this->credicard_amount_formatted = $data->amount_formatted;
                        $this->credicard_datetime = $data->credicard->datetime;
                        $this->credicard_datetime = Carbon::parse(substr($data->credicard->datetime,0,19));
                        $this->credicard_approval = $data->credicard->approval;
                        $this->loadResult($data);
                        session()->flash('messengeCredicarSuccess', '<b>CREDICARD</b> aprobó su transacción exitosamente.');
                    }

                    $this->validatedPayment();
                    break;
                case '202':
                    $this->status_payment_success = false;
                    $this->setErrorsApiCode($response);
                    $this->resetPayment();
                    $this->validatedPayment();
                    break;
                default: $this->validatedPayment(); break;
            }

            $this->createTransaction($data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $jsonBody = json_decode( (string) $response->getBody() ); //dd($jsonBody);
            $this->setErrorsApiCode($response);

            $data = json_decode($response->getBody());
            $this->createTransaction($data);

            $this->resetPayment();
            $this->validatedPayment();
        }
    }

    private function loadResult($data)
    {
        $this->result_bank_name = $data->collect_method->bank_collector->bank_name;
        $this->result_bank_rif = $data->collect_method->bank_collector->bank_rif;
        $this->result_card_emitter_name = $data->payment_method->payment_card->card_emitter->name;
        $this->result_affiliate_num = $data->credicard->affiliate;
        $this->result_lot_number = $data->credicard->lot;
        $this->result_amount_formatted = $data->amount_formatted;
        $this->result_collector_id = $data->collector->id_doc;
        $this->result_collector_name = $data->collector->name;
        $this->result_terminal_num = $data->credicard->terminal;
        $this->result_approval_num = $data->credicard->approval;
        $this->result_sequence_num = $data->credicard->sequence;
        $this->result_account_numbe = $data->payment_method->payment_card->masked_account_number;
        $this->result_trace_num = $data->credicard->trace;
    }

    private function resetResult()
    {
        $this->result_bank_name = null;
        $this->result_bank_rif = null;
        $this->result_card_emitter_name = null;
        $this->result_affiliate_num = null;
        $this->result_lot_number = null;
        $this->result_amount_formatted = null;
        $this->result_collector_name = null;
        $this->result_terminal_num = null;
        $this->result_approval_num = null;
        $this->result_sequence_num = null;
        $this->result_account_numbe = null;
        $this->result_trace_num = null;
        $this->status_send_token_bank = false;
        $this->token_bank = null;
    }
}
