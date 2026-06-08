<?php

namespace App\Models\sys\Functions\User;

use App\Models\app\Assistcontrol\AssitAttendance;
use App\User;
use Carbon\Carbon;

trait AssitAttendances
{

    public function getAssitAttendances($date = null)
    {
        $assit_attendances = AssitAttendance::where('work_id', $this->work_id)->orderBy('timestamp', 'asc');
        $assit_attendances = ($date) ? $assit_attendances->whereDate('assit_attendances.date', $date) : $assit_attendances;
        $assit_attendances = $assit_attendances->get(); //dd($date,$assit_attendances);
        return $assit_attendances;
    }

    public static function getWorkersSchedule($date = null, $assit_schedule_id = null)
    {
        $workers = User::select('users.*', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->join('assit_schedules', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
            ->orderBy('rols.id', 'desc')
            ->groupBy('users.id')
            // ->groupBy('users.work_id')
        ;
        $workers = ($date) ? $workers->whereDate('assit_attendances.date', $date) : $workers;

        if ($assit_schedule_id) {
            $fecha = Carbon::now();
            $workers = $workers
                ->WhereDate('rols.ffinal', '>=', $fecha)
                ->WhereDate('rols.finicial', '<=', $fecha)
                ->where('rols.assit_schedule_id', $assit_schedule_id);
        }

        $workers = $workers->get(); //dd($date,$area,$workers);
        return $workers;
    }

    public static function getWorkersAttendance($date = null, $area = null)
    {
        $workers = User::select('users.*', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->groupBy('users.work_id');
        $workers = ($date) ? $workers->whereDate('assit_attendances.date', $date) : $workers;
        if ($area) {
            $fecha = Carbon::now();
            $workers = $workers->join('rols', 'users.id', '=', 'rols.user_id')->WhereDate('rols.ffinal', '>=', $fecha)->WhereDate('rols.finicial', '<=', $fecha)->where('rols.area', $area);
        }

        $workers = $workers->get(); //dd($date,$area,$workers);
        return $workers;
    }

    public static function getAsisstAttendance($finicial = null, $ffinal = null)
    {
        $users = User::select('users.*', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->groupBy('users.work_id');
        $users = ($finicial) ? $users->whereDate('assit_attendances.date', '>=', $finicial) : $users;
        $users = ($ffinal) ? $users->whereDate('assit_attendances.date', '<=', $ffinal) : $users;

        $users = $users->get();

        return $users;
    }

    public static function getCargoWorkers($date = null, $cargo_id = null)
    {
        $workers = User::select('users.*', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->groupBy('users.work_id');
        $workers = ($date) ? $workers->whereDate('assit_attendances.date', $date) : $workers;
        if ($cargo_id) {
            $fecha = Carbon::now();
            $workers = $workers->join('rols', 'users.id', '=', 'rols.user_id')->WhereDate('rols.ffinal', '>=', $fecha)->WhereDate('rols.finicial', '<=', $fecha)->where('rols.cargo_id', $cargo_id);
        }

        $workers = $workers->get(); //dd($date,$area,$workers);
        return $workers;
    }

    public static function getWorkersManageSchedule($date = null, $cargo_id = null, $assit_schedule_id = null, $manager_id = null)
    {
        $fecha = Carbon::now();

        $workers = User::select('users.*', 'assit_attendances.id as assit_attendance_id', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->join('profesors', 'users.id', '=', 'profesors.user_id')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->groupBy('users.id')
            ->orderBy('assit_attendances.timestamp', 'asc');
        if ($date) {
            $workers->whereDate('assit_attendances.date', $date);
            $workers->WhereDate('rols.ffinal', '>=', $fecha);
            $workers->WhereDate('rols.finicial', '<=', $fecha);
        }

        $workers = ($manager_id) ? $workers->where('pestudios.manager_id', $manager_id) : $workers;
        $workers = ($cargo_id) ? $workers->where('rols.cargo_id', $cargo_id) : $workers;
        $workers = ($assit_schedule_id) ? $workers->where('rols.assit_schedule_id', $assit_schedule_id) : $workers;


        $workers = $workers->get();

        //dd($workers,$date,$cargo_id,$manager_id);
        return $workers;
    }

    public static function getWorkersCargoSchedule($date = null, $cargo_id = null, $assit_schedule_id = null)
    {
        $fecha = Carbon::now();

        $workers = User::select('users.*', 'assit_attendances.id as assit_attendance_id', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code', 'rols.area')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            // ->join('assit_schedules', 'assit_schedules.id', '=', 'rols.assit_schedule_id')

            ->where('rols.status_schedule', true)

            // ->groupBy('users.id')
            ->groupBy('users.work_id')
            ->orderBy('rols.area', 'asc') // Order by Operational Unit (Area)
            ->orderBy('users.worker_order', 'asc') // Then by worker order
            ->orderBy('assit_attendances.timestamp', 'asc');
        if ($date) {
            $workers->whereDate('assit_attendances.date', $date);
            $workers->WhereDate('rols.ffinal', '>=', $fecha);
            $workers->WhereDate('rols.finicial', '<=', $fecha);
        }

        $workers = ($cargo_id) ? $workers->where('rols.cargo_id', $cargo_id) : $workers;
        $workers = ($assit_schedule_id) ? $workers->where('rols.assit_schedule_id', $assit_schedule_id) : $workers;


        $workers = $workers->get();

        //dd($workers,$date,$cargo_id);
        return $workers;
    }

    public static function getWorkersCargoScheduleRange($dates, $cargo_id = null, $assit_schedule_id = null)
    {
        $fecha = Carbon::now();

        $list_date = null;

        foreach ($dates as $date) {
            $list_date[] = $date->format('Y-m-d');
        } // dd($list_date);

        $workers = User::select('users.*', 'assit_attendances.id as assit_attendance_id', 'assit_attendances.user', 'assit_attendances.work_id', 'assit_attendances.card_id', 'assit_attendances.date', 'assit_attendances.time', 'assit_attendances.timestamp', 'assit_attendances.in_out', 'assit_attendances.event_code')
            ->join('assit_attendances', 'users.work_id', '=', 'assit_attendances.work_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            // ->join('assit_schedules', 'assit_schedules.id', '=', 'rols.assit_schedule_id')
            ->where('rols.status_schedule', true)

            ->groupBy('users.id')
            // ->orderBy('assit_attendances.timestamp','asc')
            ->orderBy('users.worker_order', 'asc');
        // if ($date) {
        $workers->whereIn('assit_attendances.date', $list_date);
        $workers->WhereDate('rols.ffinal', '>=', $fecha);
        $workers->WhereDate('rols.finicial', '<=', $fecha);
        // }

        $workers = ($cargo_id) ? $workers->where('rols.cargo_id', $cargo_id) : $workers;
        $workers = ($assit_schedule_id) ? $workers->where('rols.assit_schedule_id', $assit_schedule_id) : $workers;


        $workers = $workers->get();

        //dd($workers,$date,$cargo_id);
        return $workers;
    }

    public static function getWorkersCargoScheduleRangeAsuntes($dates, $cargo_id = null, $assit_schedule_id = null)
    {
        $fecha = Carbon::now();

        $list_date = array();

        foreach ($dates as $date) {
            $list_date[] = $date->format('Y-m-d');
        } // dd($list_date);

        $finicial = $list_date[0];
        $ffinal = end($list_date); //dd($finicial,$ffinal);
        $list_work_id = array();

        $assit_attendances = AssitAttendance::select('assit_attendances.*')
            ->WhereDate('assit_attendances.date', '>=', $finicial)
            ->WhereDate('assit_attendances.date', '<=', $ffinal)
            ->get(); //dd($assit_attendances);
        foreach ($assit_attendances as $assit_attendance) {
            $list_work_id[] = $assit_attendance->work_id;
        }

        $users = User::select('users.*')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->where('rols.status_schedule', true)
            ->WhereDate('rols.ffinal', '>=', $fecha)
            ->WhereDate('rols.finicial', '<=', $fecha)
            ->whereNotIn('users.work_id', $list_work_id); //dd($users);

        $users = ($cargo_id) ? $users->where('rols.cargo_id', $cargo_id) : $users;
        $users = ($assit_schedule_id) ? $users->where('rols.assit_schedule_id', $assit_schedule_id) : $users;

        $users = $users->get();

        //dd($assit_attendances,$list_work_id ,$users);

        //dd("workers",$workers,'cargo_id',$cargo_id,'assit_schedule_id',$assit_schedule_id,'dates',$dates);
        return $users;
    }

    public static function getWorkers()
    {
        $workers = User::select('users.*')
            ->whereNotNull('users.work_id')
            ->get();
        return $workers;
    }
}
