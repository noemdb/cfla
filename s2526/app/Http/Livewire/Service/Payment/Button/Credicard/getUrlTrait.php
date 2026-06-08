<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

trait getUrlTrait
{


    private function getUrlTransactionReportService()
    {
        $client_id = env('API_BTN_PAY_CLIENT_ID');
        $client_secret = env('API_BTN_PAY_CLIENT_SECRET');
        $query = "/payment/transaction_report";
        $urlRequest = env('API_BTN_PAY_ENDPOINT').$query; //dd($urlRequest);
        return $urlRequest;

    }

    private function getUrlLotService()
    {
        $client_id = env('API_BTN_PAY_CLIENT_ID');
        $client_secret = env('API_BTN_PAY_CLIENT_SECRET');
        $query = "/payment/lot_report";
        $urlRequest = env('API_BTN_PAY_ENDPOINT').$query; //dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlAuthorizatioService()
    {
        $client_id = env('API_BTN_PAY_CLIENT_ID');
        $client_secret = env('API_BTN_PAY_CLIENT_SECRET');
        $query = "/oauth/authorize?grant_type=client_credentials&client_id=".$client_id."&client_secret=".$client_secret;
        $urlRequest = env('API_BTN_PAY_ENDPOINT').$query; //dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlInformationService()
    {
        $query = "/payment/bank_card_info?country=VE";
        $urlRequest = env('API_BTN_PAY_ENDPOINT').$query;// dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlRequestTokenService()
    {
        $query = "/payment/bank_card/send_token";
        $urlRequest = env('API_BTN_PAY_ENDPOINT'). $query;// dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlRequestTokenServiceCrediCard()
    {
        $query = "/payment/send_token_with_card";
        $urlRequest = env('API_BTN_PAY_ENDPOINT'). $query;// dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlCardHolderCommissionService()
    {
        $query = "/payment/card_holder_commission";
        $urlRequest = env('API_BTN_PAY_ENDPOINT'). $query;// dd($urlRequest);
        return $urlRequest;
    }

    private function getUrlPaymentService()
    {
        $query = "/payment";
        $urlRequest = env('API_BTN_PAY_ENDPOINT').$query;// dd($urlRequest);
        return $urlRequest;
    }

}

?>
