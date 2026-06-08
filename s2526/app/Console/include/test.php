<?php



use App\Http\Controllers\Administracion\Email\CollectionScheduleController;
use App\Models\app\Cobranzas\CollCalendar;
use App\Models\app\Cobranzas\CollPolitical;
use Carbon\Carbon;

$now = Carbon::now();
$date = $now->format('Y-m-d');
$time = $now->format('h:i:s');
$coll_calendar = CollCalendar::active(true)->where('date',$date)->where('date',$date)->where('time','>=',$time)->orderBy('time','asc')->first(); dd($coll_calendar);
if ($coll_calendar) {
    $coll_political = $coll_calendar->coll_political;
    if ($coll_political) {
        $date_time = $coll_calendar->date_time;
        $day = ($date_time) ? $date_time->format('d') : 1;
        $time = ($date_time) ? $date_time->format('h:i') : 1;

        $schedule->call(function () use ($coll_political,$coll_calendar) {
            $jobSend = new CollectionScheduleController();
            $jobSend->bacthCollectionSendSchedule($coll_political->id,$coll_calendar->name);
        })->runInBackground()->monthlyOn($day, $time);
    }
}

?>