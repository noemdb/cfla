<?php

//SetExchangeRate

use App\Http\Controllers\Admin\Email\SetExchangeRateController;
use App\Http\Controllers\Restapi\Exchange\CapriceController;

$schedule->call(function () {
    $caprice = new CapriceController();
    $exchange_rate = $caprice->setExchangeRateTodateCFLA();
    if ($exchange_rate) {
        $jobSend = new SetExchangeRateController();
        $jobSend->messegesSend($exchange_rate);
    }
})->runInBackground()->daily();

?>
