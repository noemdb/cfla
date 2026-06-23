<?php

namespace App\Http\Livewire\Administracion\AssistControl\AssitDay;

use Livewire\Component;

class FormComponent extends Component
{
    public $assit_week_id;
    public $name, $number_day;
    public $list_comment_assit_day;

    protected $rules = [
        'name' => 'required|string',
        'number_day' => 'required|integer|min:1|max:7'
    ];

    public function mount($assit_week_id = null)
    {
        $this->assit_week_id = $assit_week_id;
        $this->list_comment_assit_day = \App\Models\app\Assistcontrol\AssitDay::COLUMN_COMMENTS;
    }

    public function render()
    {
        return view('livewire.administracion.assist-control.assit-day.form-component');
    }

    public function save()
    {
        $this->validate();

        \App\Models\app\Assistcontrol\AssitDay::create([
            'assit_week_id' => $this->assit_week_id,
            'name' => $this->name,
            'number_day' => $this->number_day
        ]);

        $this->reset(['name', 'number_day']);
        $this->emit('mainSaveDay'); // Notify parent/listeners
        session()->flash('operp_ok', 'Día guardado exitosamente.');
    }
}
