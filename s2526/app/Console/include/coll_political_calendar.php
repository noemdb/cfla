<?php

use App\Http\Controllers\Administracion\Email\Collection\CollectionScheduleEmailRotationController;
use App\Http\Controllers\Administracion\Email\CollectionScheduleController;
use App\Http\Controllers\Administracion\Email\CollectionScheduleEmailJetController;
use App\Models\app\Cobranzas\CollCalendar;
use App\Models\app\Cobranzas\CollPolitical;
use Carbon\Carbon;

$now = Carbon::now();
$date = $now->format('Y-m-d');
$time = $now->format('H:i') . ':00';
$coll_calendar = CollCalendar::active(true)->where('date', $date)->where('time', $time)->orderBy('time', 'asc')->first(); //dd($coll_calendar);
if ($coll_calendar) {
    $coll_political = $coll_calendar->coll_political;
    if ($coll_political) {
        $id = $coll_political->id;
        $status_email = $coll_calendar->status_email;
        $status_whatsapp = $coll_calendar->status_whatsapp;
        $number = $coll_calendar->name;

        $jobSend = new CollectionScheduleController();
        $jobSend->bacthCollectionSendSchedule($coll_political->id, $coll_calendar->name, $status_whatsapp, $status_email);

        // $jobSend = new CollectionScheduleEmailJetController();
        // $jobSend->batchCollectionSendSchedule($coll_political->id, $coll_calendar->name, $status_whatsapp, $status_email);

        //$controller = new CollectionScheduleEmailRotationController();
        //$controller->batchCollectionSendSchedule($id, $number, $status_whatsapp, $status_email);
    }
}
