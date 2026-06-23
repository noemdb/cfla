<?php

namespace App\Http\Livewire\Administracion\Bienestar;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Bienestar\StudentRecord\StudentRecordTrait;

class IndexComponent extends Component
{
    use WithPagination;
    use StudentRecordTrait;
    public StudentRecord $student_record;
    protected $paginationTheme = 'bootstrap';

    public $student_record_id,$estudiant_id,$estudiant;

    public $search = '',$paginate=10; //'name',teacher,cource
    public $student_records,$estudiant_selected,$student_recordt_id;

    public $modeIndex,$modeEdit,$modeCreate;

    public $list_comment;

    public $status_last,$status_first,$saveInto;

    public $student_records_list,$topics_list,$pevaluations_list,$list_potencial;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->list_comment = StudentRecord::COLUMN_COMMENTS;
        $this->list_potencial = StudentRecord::LIST_POTENCIAL;
    }

    public function render()
    {
        $search = $this->search;
        $estudiants = Estudiant::select('estudiants.*')->WidthInscripcion();

        $estudiants = (!empty($search)) ? Estudiant::orwhere(
            function($query) use ($search) {
                $query->orWhere('estudiants.lastname','like', '%'.$search.'%')
                    ->orWhere('estudiants.name','like','%'.$search.'%')
                    ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                    ;
            })
            : $estudiants;

        $estudiants = $estudiants->paginate($this->paginate);

        return view('livewire.administracion.bienestar.index-component', [
            'estudiants' => $estudiants,
        ]);
    }

    public function create($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id; //dd($this->estudiant_id);
        $this->student_record = new StudentRecord; //dd($this->student_record);
        $this->student_record->estudiant_id = $estudiant->id;
        $this->modeIndex = false;
        $this->modeCreate = true;
        $this->modeEdit = false;
    }

    public function edit($id)
    {
        $student_record = StudentRecord::findOrfail($id);
        $estudiant = Estudiant::findOrfail($student_record->estudiant_id);

        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->student_record = $student_record;
        $this->student_recordt_id = $student_record->id;

        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = true;
    }

    public function save()
    {
        //dd($this->student_record);

        $this->validate();
        $this->student_record->estudiant_id = $this->estudiant_id;
        $this->student_record->save(); //dd($this->student_record);
        // $this->alert('success', 'Los datos fueron almacenados satisfactoriamente!');
        $this->close();
        session()->flash('operp_ok', 'Guardado!!!.');
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->student_record = new StudentRecord;
        $this->estudiant_id = null;
        $this->estudiant_selected = new Estudiant;
    }
}
