<?php

namespace App\Http\Livewire\Service\Payment\Button\Credicard;
// app/Http/Livewire/Service/Payment/Button/Credicard/ValidateTrait.php

use Carbon\Carbon;
use Illuminate\Support\Str;

trait setDataTestTrait
{

    public function loadTDDExterior()
    {
        $this->ci_representant = '18365060';
        $this->card_number = "6275340000017704603";
        $this->type_ci = "V";
        $this->cvc = "643";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "08";
        $this->expiration_year = "19";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1140";
        $this->card_type = "TDD";
        $this->card_type_holder = "TDD";

        $this->holder_name = "NOE DOMINGUEZ";
        $this->holder_id_doc = "CI";
        $this->holder_id = "14608133";
        $this->holder_id_con = "V014608133";
        // $this->token_bank = "123456";
    }

    public function loadTDDVenezuela()
    {
        $this->ci_representant = '18365060';
        $this->card_number = "5899415558727288";
        $this->type_ci = "V";
        $this->cvc = "643";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "08";
        $this->expiration_year = "20";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "AHORRO";
        $this->card_pin = "1140";
        $this->card_type = "TDD";
        $this->card_type_holder = "TDD";

        $this->holder_name = "NOE DOMINGUEZ";
        $this->holder_id_doc = "CI";
        $this->holder_id = "14608133";
        $this->holder_id_con = "V014608133";
        $this->modeSendTokenBank = false;
        // $this->token_bank = "123456";
    }

    public function loadTDCMercantil()
    {
        $this->ci_representant = '18365060';
        $this->card_number = "5412474303451007";
        $this->type_ci = "V";
        $this->cvc = "103";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "08";
        $this->expiration_year = "24";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1640";
        $this->card_type = "TDC";
        $this->card_type_holder = "TDC";

        $this->holder_name = "NOE DOMINGUEZ";
        $this->holder_id_doc = "CI";
        $this->holder_id = "14608133";
        $this->holder_id_con = "V014608133";
        // $this->token_bank = "123456";
    }

    public function loadTDDBancaribe()
    {
        // $this->ci_representant = '18365060';
        $this->card_number = "6036440003198818312";
        $this->type_ci = "V";
        $this->cvc = "773";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "01";
        $this->expiration_year = "20";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1640";
        $this->card_type = "TDD";
        $this->card_type_holder = "TDD";

        $this->holder_name = "NOE DOMINGUEZ";
        $this->holder_id_doc = "CI";
        $this->holder_id = "14608133";
        $this->holder_id_con = "V014608133";
        $this->token_bank = "91543383";
    }

    public function loadTDDTesoro()
    {
        $this->ci_representant = '18365060';
        $this->card_number = "6394890001004978997";
        $this->type_ci = "V";
        $this->cvc = "877";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "12";
        $this->expiration_year = "19";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1440";
        $this->card_type = "TDD";
        $this->card_type_holder = "TDD";

        $this->holder_name = "NOE DOMINGUEZ";
        $this->holder_id_doc = "CI";
        $this->holder_id = "14608133";
        $this->holder_id_con = "V014608133";
        $this->holder_type = "V";
        // $this->token_bank = "123456";
    }

    public function setDataTest ()
    {
        // $this->loadTDD();
        // $this->loadTDC();
        $this->loadTDDTesoro();

        $this->card_type_payment = ($this->card_type=="TDC") ? "CREDIT" : "DEBIT";
        $this->card_type_holder = ($this->card_type=="TDC") ? "TDC" : "TDD";

        $this->getResponse();

    }

    public function loadTDD()
    {
        $this->card_number = "5859480000000146871";
        $this->type_ci = "V";
        $this->cvc = "941";
        $this->ammount_pay = 1;
        $this->expiration_month = "06";
        $this->expiration_year = "24";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1234";
        $this->card_type = "TDD";
        $this->holder_name = "Ariana Dominguez";
        $this->holder_id_doc = "CI";
        $this->holder_id = "16673906";
        $this->holder_id_con = "V014608133";
        $this->holder_type = "V";
        $this->token_bank = "123456";
        $this->status_send_token_bank = true;
        $this->modeSendTokenBank = false;
    }

    public function loadTDC()
    {
        $this->card_number = "4222610122997125";
        $this->type_ci = "V";
        $this->cvc = "808";
        $this->ammount_pay = 1;
        // $this->date_expiration = "12/24";
        $this->expiration_month = "12";
        $this->expiration_year = "24";
        $this->date_expiration = $this->expiration_month."/".$this->expiration_year;
        $this->account_type = "CORRIENTE";
        $this->card_pin = "1234";
        $this->card_type = "TDC";

        $this->holder_name = "Ariana Dominguez";
        $this->holder_id_doc = "CI";
        $this->holder_id = "16673906";
        $this->holder_id_con = "V014608133";
        $this->holder_type = "V";
        $this->token_bank = "123456";
        $this->modeSendTokenBank = false;

        // $this->bank_card_validation = [
        //     "CI"=> "V004000004",
        //     "token"=> "123456"
        // ];
    }

    public function getResponse()
    {
        $file = file_get_contents(__DIR__.'/responsePro.json');
        $json = json_decode($file);
        $json->amount = $this->ammount_pay;
        $json->amount_formatted = 'Bs. '. number_format($this->ammount_pay, 2, '.', '');
        $json->amount_before_commission = $this->ammount_pay;
        $json->credicard->approval = Str::random(20);
        $json->info->created_at = Carbon::now();
        $json->credicard->datetime = Carbon::now();

        //dd($json);
        return $json;
    }

    public function getResponseInfoCard()
    {
        $path = ($this->card_type == "TDD") ? "/info_card.json" : '/info_card_tdc.json' ; //dd($path);
        $file = file_get_contents(__DIR__.$path); //dd($file);
        $json = json_decode($file); //dd($json);
        return $json;
    }

    public function getResponseCommission()
    {
        $path = '/commission.json' ; //dd($path);
        $file = file_get_contents(__DIR__.$path); //dd($file);
        $json = json_decode($file); //dd($json);
        return $json;

        /*
            {#2529 ▼
            +"amount": 1.0
            +"amount_formatted": "Bs. 1,00"
            }
        */
    }

    public function getResponseToken()
    {
        $path = '/token.json' ; //dd($path);
        $file = file_get_contents(__DIR__.$path); //dd($file);
        $json = json_decode($file); //dd($json);
        return $json;
    }

}

?>
