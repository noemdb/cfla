<?php

namespace App\Http\Livewire\Evaluacion\Peducativo;

use App\Models\app\Pescolar\Peducativo;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $search = '';
    public $perPage = 10;
    public $editingId = null;
    
    // Fields for editing
    public $max_number_eiplanningwks;
    public $max_number_eiplanningbwks;
    public $max_number_eiprojectks;
    public $max_number_eispecialks;
    public $max_number_eievaluationks;
    public $max_number_eifinalks;

    protected $rules = [
        'max_number_eiplanningwks' => 'required|integer|min:0|max:999',
        'max_number_eiplanningbwks' => 'required|integer|min:0|max:999',
        'max_number_eiprojectks' => 'required|integer|min:0|max:999',
        'max_number_eispecialks' => 'required|integer|min:0|max:999',
        'max_number_eievaluationks' => 'required|integer|min:0|max:999',
        'max_number_eifinalks' => 'required|integer|min:0|max:999',
    ];

    protected $validationAttributes = [
        'max_number_eiplanningwks' => 'Planes Semanales',
        'max_number_eiplanningbwks' => 'Planes Quincenales',
        'max_number_eiprojectks' => 'Proyectos',
        'max_number_eispecialks' => 'Planes Especiales',
        'max_number_eievaluationks' => 'Evaluaciones',
        'max_number_eifinalks' => 'Informes Pedagógicos',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $peducativo = Peducativo::findOrFail($id);
        
        $this->editingId = $id;
        $this->max_number_eiplanningwks = $peducativo->max_number_eiplanningwks;
        $this->max_number_eiplanningbwks = $peducativo->max_number_eiplanningbwks;
        $this->max_number_eiprojectks = $peducativo->max_number_eiprojectks;
        $this->max_number_eispecialks = $peducativo->max_number_eispecialks;
        $this->max_number_eievaluationks = $peducativo->max_number_eievaluationks;
        $this->max_number_eifinalks = $peducativo->max_number_eifinalks;
    }

    public function cancelEdit()
    {
        $this->resetEditingFields();
    }

    public function update()
    {
        $this->validate();

        try {
            $peducativo = Peducativo::findOrFail($this->editingId);
            
            $peducativo->update([
                'max_number_eiplanningwks' => $this->max_number_eiplanningwks,
                'max_number_eiplanningbwks' => $this->max_number_eiplanningbwks,
                'max_number_eiprojectks' => $this->max_number_eiprojectks,
                'max_number_eispecialks' => $this->max_number_eispecialks,
                'max_number_eievaluationks' => $this->max_number_eievaluationks,
                'max_number_eifinalks' => $this->max_number_eifinalks,
            ]);

            $this->resetEditingFields();

            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => 'Período educativo actualizado correctamente.',
                'icon' => 'success',
                'timer' => 3000,
                'showConfirmButton' => false,
            ]);

        } catch (Exception $e) {
            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Error!',
                'text' => 'Ocurrió un error al actualizar el período educativo.',
                'icon' => 'error',
                'timer' => 3000,
                'showConfirmButton' => false,
            ]);
        }
    }

    private function resetEditingFields()
    {
        $this->editingId = null;
        $this->max_number_eiplanningwks = null;
        $this->max_number_eiplanningbwks = null;
        $this->max_number_eiprojectks = null;
        $this->max_number_eispecialks = null;
        $this->max_number_eievaluationks = null;
        $this->max_number_eifinalks = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $user_id = Auth::id();
        $peducativos = Peducativo::select('peducativos.*')
            ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })
            ->where('peducativos.status_active','true')
            ->where('peducativos.show_quantitative_indicators','false')

            ->where(
            function($query) use ($user_id) {
                $query->orWhere('peducativos.manager_id',$user_id)
                    ->orWhere('peducativos.assistant_id',$user_id)
                    ->orWhere('peducativos.deputy_id',$user_id)
                    ;
            })
            ->paginate($this->perPage)
        ; //dd($peducativos);

        return view('livewire.evaluacion.peducativo.index-component', compact('peducativos'));
    }
}