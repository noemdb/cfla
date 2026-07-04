<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitSchedules;

use App\Models\app\Assistcontrol\AssitSchedule;
use Livewire\Component;

class FormComponent extends Component
{
    public $list_comment_assit_schedule;
    public $name,$number_turn,$description,$observations,$frecuency,$status;
    public $assit_schedule,$assit_schedule_id;
    public $editModeAssitSchedule=null,$createModeAssitSchedule=null;

    protected $rules = [
        'name' => 'required|string|min:6',
        'number_turn' => 'required|integer|max:10',
        'description' => 'required|string',
        'frecuency' => 'required|string',
        'status' => 'required|string',
    ];

    public function mount($id=null)
    {
        $this->setAssitSchedule($id);
        $this->list_comment_assit_schedule = AssitSchedule::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-schedules.form-component');
    }

    public function editModeAssitScheduleOn($id=null)
    {
        $this->setAssitSchedule($id);
        $this->editModeAssitSchedule = true;
        $this->createModeAssitSchedule = false;
    }
    public function editModeAssitScheduleOff()
    {
        $this->editModeAssitSchedule = false;
        $this->createModeAssitSchedule = false;
    }

    public function createModeAssitScheduleOn()
    {
        $this->createModeAssitSchedule = true;
        $this->editModeAssitSchedule = false;
        $this->inputReset();
    }
    public function createModeAssitScheduleOff()
    {
        $this->createModeAssitSchedule = false;
        $this->editModeAssitSchedule = false;
    }

    public function setAssitSchedule($id=null)
    {
        $assit_schedule = AssitSchedule::find($id);
        if ($assit_schedule) {
            $this->assit_schedule_id = $id;
            $this->assit_schedule = $assit_schedule;
            $this->id = $assit_schedule->id;
            $this->name = $assit_schedule->name;
            $this->number_turn = $assit_schedule->number_turn;
            $this->description = $assit_schedule->description;
            $this->observations = $assit_schedule->observations;
            $this->frecuency = $assit_schedule->frecuency;
            $this->status = $assit_schedule->status;
        }
    }

    public function saveAssitSchedule()
    {
        $this->validate();

        if ($this->editModeAssitSchedule) {
            $assit_schedule = AssitSchedule::find($this->assit_schedule_id);
            if ($assit_schedule) {
                $arr = [
                    'name' => $this->name,
                    'number_turn' => $this->number_turn,
                    'description' => $this->description,
                    'observations' => $this->observations,
                    'frecuency' => $this->frecuency,
                    'status' => $this->status,
                ];
                $assit_schedule->update($arr);
                $this->editModeAssitScheduleOff();
                $this->emit('mainSaveSchedule',$this->assit_schedule_id);
                session()->flash('operp_ok', 'Guardado!!!.');
            }
        }

        if ($this->createModeAssitSchedule) {
            $arr = [
                'name' => $this->name,
                'number_turn' => $this->number_turn,
                'description' => $this->description,
                'observations' => $this->observations,
                'frecuency' => $this->frecuency,
                'status' => $this->status,
            ];
            $newAssitSchedule = AssitSchedule::create($arr);
            $this->inputReset();
            $this->emit('mainSaveSchedule',$newAssitSchedule->id);
            session()->flash('operp_ok', 'Guardado!!!.');
        }
    }

    public function inputReset()
    {
        $this->name = null;
        $this->number_turn = null;
        $this->description = null;
        $this->observations = null;
        $this->frecuency = null;
        $this->status = null;
    }
}
