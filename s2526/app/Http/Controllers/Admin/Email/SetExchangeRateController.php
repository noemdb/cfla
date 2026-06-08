<?php

namespace App\Http\Controllers\Admin\Email;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Restapi\Exchange\CapriceController;

use Jenssegers\Date\Date;
use Carbon\Carbon;

use App\Jobs\Email\Admin\ProcessNotifySetExchangeRate;
use App\Models\app\Planpago\ExchangeRate;

class SetExchangeRateController extends Controller
{

    public function testMessegesSend()
    {
        $caprice = new CapriceController();
        $exchange_rate = $caprice->setExchangeRateTodate();
        if ($exchange_rate) {
            $this->messegesSend($exchange_rate);
        }
    }

    public function messegesSend(ExchangeRate $exchange_rate)
    {
        $subject = 'Notificación - Admin';
        $time = Carbon::now();
        $datas = collect();
        $toDate = Date::now()->format('d F Y');
        $email =  env('MAIL_ADDRESS_ADMON');
        if (validate_email($email)) {
            $mailCCAddress = env('MAIL_CC_ADDRESS_ADMON', $email);
            $dataEmail = [
                'mailCCAddress' => $mailCCAddress,
                'subject' => $subject,
                'address' => $email,
                'exchange_rate' => $exchange_rate,
                'toDate' => $toDate,
                'view' => 'email.admin.set_exchange_rate',
            ];
            ProcessNotifySetExchangeRate::dispatch($dataEmail)->delay($time);
            $datas->push($dataEmail);
        }
        return $datas;
    }
}
