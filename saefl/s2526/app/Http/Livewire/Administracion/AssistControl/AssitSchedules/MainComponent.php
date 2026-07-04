<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\Models\app\Assistcontrol\AssitSchedule;
use Illuminate\Http\Request;
use Livewire\Component;

class MainComponent extends Component
{
    public $assit_schedules;
    public $selected_schedule_id = null;
    public $createMode = false;
    public $list_comment_assit_schedule;

    protected $listeners = ['mainSaveSchedule', 'selectSchedule', 'createSchedule'];

    public function mount(Request $request)
    {
        $this->loadSchedules();
        $this->list_comment_assit_schedule = AssitSchedule::COLUMN_COMMENTS;
        
        // Select the first schedule by default if available
        if ($this->assit_schedules->isNotEmpty()) {
            $this->selected_schedule_id = $this->assit_schedules->first()->id;
        }
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-schedules.main-component');
    }

    public function loadSchedules()
    {
        $this->assit_schedules = AssitSchedule::all();
    }

    public function selectSchedule($id)
    {
        $this->selected_schedule_id = $id;
        $this->createMode = false;
    }

    public function createSchedule()
    {
        $this->createMode = true;
        $this->selected_schedule_id = null;
    }

    public function mainSaveSchedule($id = null)
    {
        $this->loadSchedules();
        if ($id) {
            $this->selectSchedule($id);
        }
        // $this->emit('saveScheduleNavBar', $id); // No longer needed with new architecture
        // $this->emit('saveScheduleContent', $id); // No longer needed with new architecture
    }
}
