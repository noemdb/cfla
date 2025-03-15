<?php

namespace App\Livewire;

trait CatchmentUpdates
{
    public function updatedDayAppointment($value)
    {
        // $this->day_appointment = Carbon::createFromFormat("Y-m-d",$value)->addDay()->format('Y-m-d');
    }
}