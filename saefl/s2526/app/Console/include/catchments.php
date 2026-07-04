<?php

use App\Http\Controllers\Administracion\Email\Catchment\CatchmentSendNotificationsController;
use Carbon\Carbon;

// INI CatchmentsendMailReminderNotice

    // $schedule->call(function () {
    //     App\Http\Controllers\Admin\FixDB\CatchmentFixDB::importCatchment();
    // })->runInBackground()->everyTwoMinutes();


    // $schedule->call(function () {
    //     App\Http\Controllers\Admin\FixDB\CatchmentFixDB::importCatchmentTwo();
    // })->runInBackground()->everyFiveMinutes();

    
    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMailReminderNotice();
    // })->runInBackground()->weeklyOn(2, '8:15');
    

    // /*
    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMailInterviewReprogrammer();
    // })->runInBackground()->weeklyOn(5, '9:15');
    // */

    // /*
    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMailInterviewReprogrammerFaseOne();
    // })->runInBackground()->weeklyOn(5, '10:15');
    // */

    // /*
    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMailCatchmentAccepted();
    // })->runInBackground()->weeklyOn(5, '10:15');
    // */

    
    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMailNoticeRememberFirst();
    // })->dailyAt('10:00')->skip(function () {
    //     return now()->toDateString() !== '2025-04-04';
    // });

    // $schedule->call(function () {
    //     $jobSend = new CatchmentSendNotificationsController();
    //     $jobSend->sendMetaNotifications();
    // })->dailyAt('16:30')->skip(function () {
    //     return now()->toDateString() !== '2025-03-31';
    // });
    

// FIN CatchmentsendMailReminderNotice

?>
