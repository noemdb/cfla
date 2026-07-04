<?php



use App\Http\Controllers\Administracion\Email\CollectionScheduleController;
use App\Models\app\Cobranzas\CollCalendar;
use App\Models\app\Cobranzas\CollPolitical;
use Carbon\Carbon;

//INI payment Monthly
    $schedule->call(function () {
        $coll_political = CollPolitical::collPoliticalActive();
        if ($coll_political) {
            $jobSend = new CollectionScheduleController();
            $jobSend->bacthCollectionSendSchedule($coll_political->id,'1RA');
        }
    })->runInBackground()->monthlyOn(6, '10:00');

    $schedule->call(function () {
        $coll_political = CollPolitical::collPoliticalActive();
        if ($coll_political) {
            $jobSend = new CollectionScheduleController();
            $jobSend->bacthCollectionSendSchedule($coll_political->id,'2DA');
        }
    })->runInBackground()->monthlyOn(16, '15:00');

    $schedule->call(function () {
        $coll_political = CollPolitical::collPoliticalActive();
        if ($coll_political) {
            $jobSend = new CollectionScheduleController();
            $jobSend->bacthCollectionSendSchedule($coll_political->id,'3RA');
        }
    })->runInBackground()->monthlyOn(26, '10:00');
//FIN payment Monthly
?>
