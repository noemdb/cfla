<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitWorker;

use Livewire\Component;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\sys\Rol;
use App\User;

class UpdateComponent extends Component
{
    public $list_comment_user,$list_assit_schedule;
    public $work_id;
    public $assit_schedule,$assit_schedule_select_id,$assit_schedule_id,$workers,$rol_id;
    public $editModeAssitWorker=false;

    protected $listeners = ['updateAssitScheduleWorker'];

    protected $rules = [
        'assit_schedule_id' => 'required|integer'
    ];

    public function updateAssitScheduleWorker()
    {
        $this->workers = AssitSchedule::getWorkers($this->assit_schedule_select_id);
        $this->list_assit_schedule = AssitSchedule::list_assit_schedule();
    }

    public function mount($id)
    {
        $this->assit_schedule_id = $id;
        $assit_schedule = AssitSchedule::find($id);
        if ($assit_schedule) {
            $this->assit_schedule = $assit_schedule;
            $this->assit_schedule_select_id = $assit_schedule->id;
            $this->workers = $assit_schedule->workers;
        }
        $this->list_comment_user = User::COLUMN_COMMENTS;
        $this->list_assit_schedule = AssitSchedule::list_assit_schedule();
    }

    public function render()
    {
        $this->updateAssitScheduleWorker();
        return view('livewire.administracion.assist-control.assit-worker.update-component');
    }

    public function edit($id)
    {
        $rol = Rol::find($id);
        if ($rol) {
            $this->rol_id = $rol->id;
            $this->assit_schedule_id = $rol->assit_schedule_id;
            $this->work_id = $rol->user->work_id;
            $this->editModeAssitWorkerOn();
        }
    }

    public function update()
    {
        $this->validate();

        if ($this->rol_id) {

            $rol = Rol::find($this->rol_id);
            $arr = ['assit_schedule_id' => $this->assit_schedule_id];
            $rol->update($arr);
            $this->editModeAssitWorkerOff();
            $this->updateAssitScheduleWorker();
            session()->flash('operp_ok', 'Guardado!!!.');
        }
    }

    public function editModeAssitWorkerOn()
    {
        $this->editModeAssitWorker = true;
    }
    public function editModeAssitWorkerOff()
    {
        $this->editModeAssitWorker = false;
    }
}
