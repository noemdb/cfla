<?php

use App\Http\Controllers\Administracion\Email\Collection\sendCongratulationController;

// INI congratulations

    $schedule->call(function () {
        $jobSend = new sendCongratulationController();
        $jobSend->sendCongratulations();
    })->runInBackground()->monthlyOn(6, '18:00');

// FIN congratulations

?>
