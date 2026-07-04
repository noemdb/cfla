<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitWeek;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Assistcontrol\AssitWeek;
use Livewire\Component;

class FormComponent extends Component
{
    //'name','assit_schedule_id','number_week'
    public $assit_week_id,$name,$assit_schedule_id,$number_week;
    public $assit_schedule;
    public $editModeAssitWeek=false,$createModeAssitWeek=false;
    public $list_comment_assit_week;

    protected $rules = [
        // 'assit_schedule_id' => 'required|integer',
        'name' => 'required|string|min:4',
        'number_week' => 'required|integer'
    ];

    public function mount($id)
    {
        $this->setAssitWeek($id);
        $this->list_comment_assit_week = AssitWeek::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-week.form-component');
    }

    public function saveAssitWeek()
    {
        $this->validate();

        $arr = [
            'assit_schedule_id' => $this->assit_schedule_id,
            'name' => $this->name,
            'number_week' => $this->number_week
        ];

        if ($this->editModeAssitWeek) {
            $assit_schedule = AssitWeek::find($this->assit_week_id);
            if ($assit_schedule) {
                $assit_schedule->update($arr);
                $this->setMode('editOFF');
                $this->emit('mainSaveWeek',$this->assit_week_id);
                session()->flash('operp_ok', 'Guardado!!!.');
            }
        }

        if ($this->createModeAssitWeek) {
            $arr[] = ['assit_schedule_id' => $this->assit_schedule_id]; //dd($arr);
            $new = AssitWeek::create($arr);
            $this->setAssitWeek($this->assit_week_id);
            $this->setMode('createOFF');
            $this->emit('mainSaveWeek',$this->assit_week_id);
            session()->flash('operp_ok', 'Guardado!!!.');
        }
    }


    //editON,editOFF,createON,createOFF
    public function setMode($mode,$id=null)
    {
        switch ($mode) {
            case 'editON':
                $this->setAssitWeek($id);
                $this->editModeAssitWeek = true; $this->createModeAssitWeek = false;
                break;
            case 'editOFF': $this->editModeAssitWeek = false; break;

            case 'createON':
                $this->setAssitWeek($id);
                $this->editModeAssitWeek = false; $this->createModeAssitWeek = true;
                break;
            case 'createOFF': $this->createModeAssitWeek = false; break;

            default:
                $editModeAssitWeek = false;
                $createModeAssitWeek = false;
                break;
        }
    }

    public function setAssitWeek($id)
    {
        $assit_week = AssitWeek::find($id);
        if ($assit_week) {
            $this->assit_week_id = $id;
            $this->assit_schedule_id = $assit_week->assit_schedule_id;
            $this->name = $assit_week->name;
            $this->number_week = $assit_week->number_week;
        } else {
            $this->inputReset();
        }
    }

    public function inputReset()
    {
        // $this->assit_schedule_id = null;
        $this->name = null;
        $this->number_week = null;
    }
}
