<?php

//SetExchangeRate

use App\Http\Controllers\Admin\Email\SetExchangeRateController;
use App\Http\Controllers\Restapi\Exchange\CapriceController;
use App\Http\Controllers\Restapi\Exchange\GoutteController;

$schedule->call(function () {
    $goutte = new GoutteController();
    $goutte = $goutte->setExchangeRateTodateCFLA();
    if ($goutte) {
        $jobSend = new SetExchangeRateController();
        $jobSend->messegesSend($goutte);
    }
})->runInBackground()->everyThirtyMinutes();

?>
