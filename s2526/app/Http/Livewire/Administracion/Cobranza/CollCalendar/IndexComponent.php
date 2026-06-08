<?php

namespace App\Http\Livewire\Administracion\Cobranza\CollCalendar;

use App\Models\app\Cobranzas\CollCalendar;
use App\Models\app\Cobranzas\CollPolitical;
use Livewire\Component;

use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    

    // public CollCalendar $calendar;
    // public $calendars;

    public $calendar_id;
    public $coll_political_id;
    public $name;
    public $description;
    public $date;
    public $time;
    public $timestamp;
    public $status_active;
    public $status_email;
    public $status_whatsapp;

    public $modeIndex = true;
    public $modeCreate = false;
    public $modeEdit = false;

    public $list_coll_politicals;

    public $sortField = 'date';
    public $sortDirection = 'desc';
    public $search = null;

    protected $listeners = [
        'openModal' => 'showModal',
    ];

    public function mount()
    {
        $this->list_coll_politicals = CollPolitical::list_coll_politicals(true);
    }

    public function modeIndex()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
    }

    public function create()
    {
        $this->modeIndex = false;
        $this->modeCreate = true;
        $this->modeEdit = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = true;

        $collCalendar = CollCalendar::findOrFail($id);
        $this->calendar_id = $collCalendar->id;
        $this->coll_political_id = $collCalendar->coll_political_id;
        $this->name = $collCalendar->name;
        $this->description = $collCalendar->description;
        $this->date = $collCalendar->date;
        $this->time = $collCalendar->time;
        $this->status_active = $collCalendar->status_active;
$this->status_email = $collCalendar->status_email;

        $this->status_whatsapp = $collCalendar->status_whatsapp;
    }

    public function rules()
    {
        return [
            'coll_political_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'timestamp' => 'required|integer',
            'status_active' => 'required|boolean',
            'status_email' => 'nullable',
            'status_whatsapp' => 'nullable',
        ];
    }

    public function sortBy($field)
    {
        $this->sortField = $field;
        $this->sortDirection = ($this->sortDirection=='asc') ? 'desc' : 'asc' ;
    }

    public function updatedSortDirection($value)
    {
        $this->sortDirection = $value;
    }

    public function read()
    {
        return CollCalendar::query()->where('description','LIKE','%'.$this->search.'%')->orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    public function render()
    {
        return view('livewire.administracion.cobranza.coll-calendar.index-component', [
            'calendars' => $this->read(),
        ]);
    }

    public function store()
    {
        $validatedData = $this->validate([
            'coll_political_id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'date' => 'required',
            'time' => 'required',
            'status_active' => 'required',
            'status_email' => 'boolean|nullable',
            'status_whatsapp' => 'boolean|nullable',
        ]);
        CollCalendar::create($validatedData);
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'CollCalendar created successfully!',
        ]);
        $this->resetForm();
        $this->modeIndex();
        session()->flash('message', 'Registro guardado con éxito.');
    }

    public function update($id)
    {
        $collCalendar = CollCalendar::findOrFail($id);
        $validatedData = $this->validate([
            'coll_political_id' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'date' => 'required',
            'time' => 'required',
            'status_active' => 'required',
            'status_email' => 'boolean',
            'status_whatsapp' => 'boolean|nullable',
        ]);
        $collCalendar->update($validatedData);
        $this->resetForm();
        $this->modeIndex();
        session()->flash('message', 'Registro actualizado con éxito.');
    }

    public function delete($id)
    {
        $collCalendar = CollCalendar::findOrFail($id);
        $collCalendar->delete();
        session()->flash('message', 'Registro eliminado con éxito.');
    }

    public function resetForm()
    {
        $this->calendar_id = null;
        $this->coll_political_id = null;
        $this->name = null;
        $this->description = null;
        $this->date = null;
        $this->time = null;
        $this->status_active = null;
        $this->status_email = null;
        $this->status_whatsapp = null;
    }

}
