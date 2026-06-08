<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;

use Exception;

use GuzzleHttp\Exception\ClientException;

use Carbon\Carbon;

use GuzzleHttp\Client;

trait RequestTrait
{
	private function getClient()
	{
		return new Client([
            'base_uri' => env('API_BTN_PAY_ENDPOINT'),
            'timeout'  => 50.0, //default 2.0 (milisecond)
            'verify' => false,
        ]);
	}

    private function getTransactionReportService()
    {
        $client = $this->getClient(); //dd($client);

        if (empty($this->access_token)) {
            $this->getTokenAccess($client);
        }

        try {
            $urlRequest = $this->getUrlTransactionReportService();
            $query = [
                'authorization' => $this->access_token, //authorization
                'begin' => "2023-03-01T01:00:00", //begin AAAA-MM-DDTHH:MM:SS (formato 24 horas)
                'end' => "2023-03-31T01:00:00", //end AAAA-MM-DDTHH:MM:SS (formato 24 horas)
                'time_zone' => "America/Caracas", //time_zone America/Caracas
                'status_id' => "pay", //status_id Valor = “pay”
                'affiliation' => "10000009", //affiliation Número de afiliación
                'limit' => '100', //limit Limite de registros en resultado
                'offset' => '1', //offset Desplazamiento en lista de resultados
            ]; //dd($query);
            $response = $client->get(
            $urlRequest,[
                'headers' => [  'Authorization' => 'Bearer ' . $this->access_token ],
                'query' => $query,
                ]
            ); //dd($response);
            $status = $response->getStatusCode(); //dd();
            if ($status == 200) {
                $data = json_decode($response->getBody()); //dd($data);
                if (!empty($data->results)) {
                    $this->status_transaction_report = true;
                    $this->transaction_reports = $data->results; //dd($this->commission_amount);
                }
                $this->validateOnly('status_transaction_report');
            } else {
                $this->setErrorsApiCode($response);
                $this->resetTransactionReport();
                $this->validateOnly('status_transaction_report');
            }

        } catch (ClientException $e) {
            $this->exception_errors = $e;
            $response = $e->getResponse();
            $this->exception_errors = $response->getReasonPhrase();
            $this->setErrorsException($this->exception_errors); dd($e,$response,$this->exception_errors);
            $this->resetTransactionReport();
            $this->validateOnly('status_transaction_report');
        }

    }

    public function sendTokenBankRequest()
    {
        $client = $this->getClient();

        try {
            if ($this->card_type_holder == "TDD") {
                $urlRequest = $this->getUrlRequestTokenService(); //dd($urlRequest);
                $post_arr = [
                    'headers' => [  'Authorization' => 'Bearer ' . $this->access_token ],
                    'json' => [ "rif" => $this->holder_id_con ],
                    'query' => [ 'bank_code' => $this->bank_code ]
                ]; //dd($urlRequest,$post_arr);
                $response = $client->post(
                    $urlRequest,
                    $post_arr
                );
            }

            if ($this->card_type_holder == "TDC") {
                $urlRequest = $this->getUrlRequestTokenServiceCrediCard();
                $post_arr = [
                    'headers' => [  'Authorization' => 'Bearer ' . $this->access_token ],
                    'json' => [
                        'card_number' => $this->card_number,
                        'expiration_month' => $this->expiration_month,
                        'expiration_year' => $this->expiration_year,
                        'holder_name' => $this->holder_name,
                        'holder_id_doc' => $this->holder_id_doc,
                        'holder_id' => $this->holder_id,
                        'account_type' => $this->account_type,
                        'pin' => $this->getPinEncrip($this->card_pin),
                        'cvc' => $this->cvc,
                        'bank_card_validation' => $this->bank_card_validation,
                    ]
                ];

                $response = $client->post(
                    $urlRequest,
                    $post_arr,
                );
            }

            $status = $response->getStatusCode();
            if ($status == 200) {
                $this->status_send_token_bank = true;
                $this->validateOnly('status_send_token_bank');
                return $response;
            } else {
                $data = json_decode($response->getBody()); //dd($data);
                $this->setErrorsApiCode($response);
                $this->resetTokenBank();
                $this->validateOnly('status_send_token_bank');
            }

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $this->setErrorsException($this->exception_errors);
            $this->exception_errors = $response->getReasonPhrase();

            $data = json_decode($response->getBody());
            $this->createTransaction($data);

            $this->resetTokenBank();
            $this->validateOnly('status_send_token_bank');
        }

    }

    private function getCardHolderCommissionService()
    {
        $client = $this->getClient(); //dd($client);

        try {
            $response = collect();
            // if (false) {
            if (! $this->modeTest) {
                $urlRequest = $this->getUrlCardHolderCommissionService();
                $query = [
                    'authorization' => $this->access_token,
                    'card_number' => $this->card_number,
                    'card_type' => $this->card_type_holder,
                    'currency' => $this->currency,
                    'amount' => $this->ammount_pay,
                    'bank_type' => $this->bank_type,
                    'product_name' => $this->product_name,
                ];
                $response = $client->get(
                $urlRequest,[
                    'headers' => [  'Authorization' => 'Bearer ' . $this->access_token ],
                    'query' => $query,
                    ]
                );
                $data = json_decode($response->getBody()); //dd($data);
                $status = $response->getStatusCode();
            } else {
                $data = $this->getResponseCommission();
                $status = '200';
            }

            if ($status == 200) {
                if (!empty($data->commission_amount)) {
                    $this->commission_amount = $data->commission_amount; //dd($this->commission_amount);
                    $this->commission_type = ($data->commission_config_type=="PERCENTAGE") ? 'Porcentaje' : 'Monto';
                    $this->ammount_holder_commission = $data->commission_config_amount;
                } else {
                    $this->commission_amount = 0;
                    $this->commission_type = "Porcentaje";
                    $this->ammount_holder_commission = 0;
                }
                $this->validateOnly('ammount_holder_commission');
            } else {
                $this->setErrorsApiCode($response);
                $this->resetAmmountHolderCommission();
                $this->validateOnly('ammount_holder_commission');
            }

        } catch (ClientException $e) {
            $this->exception_errors = $e->getMessage();
            $response = $e->getResponse();
            $this->exception_errors = $response->getReasonPhrase();
            $this->setErrorsException($this->exception_errors);

            $data = json_decode($response->getBody());
            $this->createTransaction($data);

            $this->resetAmmountHolderCommission();
            $this->validateOnly('ammount_holder_commission');
        }

    }

    private function getCardInfo()
    {
        $client = $this->getClient(); //dd($client);
        $urlRequest = $this->getUrlInformationService();
        try {
            if (! $this->modeTest) {
                $response = $client->post(
                $urlRequest,[
                    'headers' => [ 'Authorization' => 'Bearer ' . $this->access_token ],
                    'body' => $this->card_number,
                    ]
                );
                $status = $response->getStatusCode();
                $data = json_decode($response->getBody()); //dd($data);
            } else {
                $data = $this->getResponseInfoCard();
                $status = '200';
            }

            if ($status == 200) {

                $this->bank_code = $data->bank_info->code;
                $this->bank_thumbnail = $data->bank_info->thumbnail;
                $this->card_emitter_thumbnail = $data->financial_card_emitter->thumbnail;
                $this->card_emitter_name = $data->financial_card_emitter->name;

                switch ($this->card_type) {
                    case 'TDD': $this->modeSendTokenBank = $data->bank_info->bank_card_validation->TDD ; break;
                    case 'TDC': $this->modeSendTokenBank = $data->bank_info->bank_card_validation->TDC ; break;
                    default: $this->modeSendTokenBank = true; break;
                } //dd($data->bank_info,$this->modeSendTokenBank);

                if ($this->bank_code == "0114") {
                    $this->status_send_token_bank = true;
                }

                $this->validateOnly('bank_code');
                return $data;
            } else {
                $this->setErrorsApiCode($response);
                $this->resetCardInfo();
                $this->validateOnly('bank_code');
            }

        } catch (ClientException $e) {
            $this->exception_errors = $e->getMessage();
            $response = $e->getResponse();
            $this->exception_errors = $response->getReasonPhrase();
            $this->setErrorsException($this->exception_errors);
            $this->resetCardInfo();
            $this->validateOnly('bank_code');
        }

    }

    private function getTokenAccess(Client $client)
    {
        $urlRequest = $this->getUrlAuthorizatioService(); //dd($urlRequest);
        try {
            $data = collect();
            $response = collect();

            if (! $this->modeTest) {
                $response = $client->post($urlRequest);
                $status = $response->getStatusCode();
                $data = json_decode($response->getBody());
            } else {
                $status = 200;
                $data = $this->getResponseToken();
            } //dd($data);

            if ($status == 200) {
                $this->expires_in = $data->expires_in;
                $this->access_token = $data->access_token;
                $this->date_expires = Carbon::now()->addSeconds($this->expires_in);
                $this->validateOnly('access_token');
                return $response;
            } else {
                $this->resetTokenAccess();
                $this->setErrorsApiCode($status);
                $this->validateOnly('access_token');
            }
        } catch (ClientException $e) {
            $this->exception_errors = $e; //dd($e);
            $response = $e->getResponse();
            $this->exception_errors = $response->getReasonPhrase();
            $this->setErrorsException($this->exception_errors);
            $this->resetTokenAccess();
            $this->validateOnly('access_token');
        }
    }

    private function resetTokenAccess()
    {
        $this->expires_in = null;
        $this->access_token = null;
        $this->date_expires = null;
        $this->step = 3;
    }

    private function resetCardInfo()
    {
        $this->bank_code = null;
        $this->bank_thumbnail = null;
        $this->card_emitter_thumbnail = null;
        $this->step = 3;
    }

    private function resetTokenBank()
    {
        $this->status_send_token_bank = false;
        $this->step = 4;
    }

    private function resetAmmountHolderCommission()
    {
        $this->commission_amount = null;
        $this->ammount_holder_commission = null;
        $this->step = 3;
    }


    private function resetPayment()
    {
        $this->status_payment_success = false;
        $this->step = 4; //dd($this->step);
        $this->resetResult();
    }

    private function resetTransactionReport()
    {
        $this->status_transaction_report = false;
    }
}

?>
