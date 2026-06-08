<?php

namespace App\Http\Livewire\Movile\Director\Catchment;

use App\Models\app\Enrollment\Catchment;
use Livewire\Component;

class IndexComponent extends Component
{
    public $arr,$catchments,$gradoIdTotals,$appointmentDays,$interviewsDays,$dailyHourlyTotals,$pestudioIdTotals,$institutionOriginTotals;

    public function mount()
    {
        $this->catchments = Catchment::all()->where('status_active',true)->sortBy('created_at');
        $this->gradoIdTotals = Catchment::gradoIdTotals(); //dd($gradoIdTotals);
        $this->dailyHourlyTotals = Catchment::dailyHourlyTotals(); //dd($dailyHourlyTotals);
        $this->pestudioIdTotals = Catchment::pestudioIdTotals(); //dd($pestudioIdTotals);
        $this->institutionOriginTotals = Catchment::institutionOriginTotals(); //dd($institutionOriginTotals);
        $this->appointmentDays = Catchment::getCountByAppointmentDayAsArray(); //dd($this->gradoIdTotals,$this->appointmentDays);
        $this->interviewsDays = Catchment::getCountByInterviewCreationDate(); //dd($this->interviewsDays);
        $this->arr = ['primary','secondary','success','info','warning','danger','dark'];
    }

    public function render()
    {
        return view('livewire.movile.director.catchment.index-component');
    }
}
