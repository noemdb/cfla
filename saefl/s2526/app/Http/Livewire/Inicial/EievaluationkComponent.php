<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eievaluationp;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EievaluationkComponent extends Component
{
    use EievaluationkValidateTrait, WithPagination;

    public Eievaluationk $eievaluationk;
    public Eievaluationp $eievaluationp;

    // Properties
    public $eievaluationk_id, $eievaluationp_id, $profesor_id, $lapso_id;
    
    // Mode states
    public $activeTab = 'list';
    public $showModal = false;
    public $modalType = '';
    public $editingId = null;
    
    // Lists for dropdowns
    public $list_comment, $list_comment_position, $list_lapso, $list_grado, $list_seccion, $list_pevaluacion;
    
    // Search and filters
    public $search = '';
    public $filterGrado = '';
    public $filterSeccion = '';
    public $filterLapso = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedEievaluationkGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id', $value)->orderBy('name')->pluck('name', 'id') : collect();
        $this->eievaluationk->seccion_id = null;
    }

    public function updatedLapsoId($lapso_id)
    {
        $eievaluationk = Eievaluationk::find($this->eievaluationk_id);
        $this->list_pevaluacion = ($eievaluationk) ? $eievaluationk->getPevaluacionsList($this->profesor_id, $lapso_id) : collect();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterGrado()
    {
        $this->resetPage();
        $this->filterSeccion = '';
        $this->list_seccion = ($this->filterGrado) ? 
            Seccion::active("true")->where('grado_id', $this->filterGrado)->orderBy('name')->pluck('name', 'id') : 
            collect();
    }

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $profesor = ($user->IsInicial()) ? $user->profesor : null;
        $this->profesor_id = ($profesor) ? $profesor->id : null;

        $this->initializeLists();
        $this->resetModels();
    }

    public function render()
    {
        $query = Eievaluationk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion', 'lapso', 'profesor']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('observaciones', 'like', '%' . $this->search . '%')
                  ->orWhere('recomendacion', 'like', '%' . $this->search . '%')
                  ->orWhere('asistencia', 'like', '%' . $this->search . '%');
            });
        }

        // Apply grade filter
        if ($this->filterGrado) {
            $query->where('grado_id', $this->filterGrado);
        }

        // Apply section filter
        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        // Apply lapso filter
        if ($this->filterLapso) {
            $query->where('lapso_id', $this->filterLapso);
        }

        $eievaluationks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inicial.eievaluationk-component',[
            'eievaluationks' => $eievaluationks
        ]);
    }

    // Modal Management
    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->editingId = $id;
        $this->showModal = true;

        try {
            switch ($type) {
                case 'create':
                    $this->resetModels();
                    break;
                case 'edit':
                    $this->loadEvaluation($id);
                    break;
                case 'view':
                    $this->editingId = $id;
                    break;
                case 'position':
                    $this->loadEvaluationForPosition($id);
                    break;
                case 'edit-position':
                    $this->loadPosition($id);
                    break;
            }
        } catch (\Exception $e) {
            $this->closeModal();
            $this->showErrorAlert('Error al cargar los datos: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
        $this->editingId = null;
        $this->resetValidation();
    }

    // Data Loading Methods
    private function loadEvaluation($id)
    {
        $this->eievaluationk = Eievaluationk::findOrFail($id);
        $this->eievaluationk_id = $this->eievaluationk->id;
        $grado_id = $this->eievaluationk->grado_id;
        $this->list_seccion = Seccion::active("true")->where('grado_id', $grado_id)->orderBy('name')->pluck('name', 'id');
    }

    private function loadEvaluationForPosition($id)
    {
        $eievaluationk = Eievaluationk::findOrFail($id);
        $this->eievaluationk_id = $eievaluationk->id;
        $this->list_pevaluacion = $eievaluationk->getPevaluacionsList($this->profesor_id);
        $this->resetModelPosition();
    }

    private function loadPosition($id = null)
    {
        if (!$id) {
            $this->resetModelPosition();
            return;
        }

        $position = Eievaluationp::find($id);
        
        if ($position) {
            $this->eievaluationp = $position;
            $this->eievaluationp_id = $position->id;
            $eievaluationk = $position->eievaluationk;
            $this->eievaluationk_id = $eievaluationk->id;
            $this->list_pevaluacion = $eievaluationk->getPevaluacionsList($this->profesor_id);
        } else {
            $this->resetModelPosition();
            if ($this->eievaluationk_id) {
                $eievaluationk = Eievaluationk::find($this->eievaluationk_id);
                if ($eievaluationk) {
                    $this->list_pevaluacion = $eievaluationk->getPevaluacionsList($this->profesor_id);
                }
            }
        }
    }

    // CRUD Operations
    public function save()
    {
        $this->eievaluationk->profesor_id = $this->profesor_id;

        $this->validate([
            'eievaluationk.profesor_id' => 'required|integer',
            'eievaluationk.grado_id' => 'required|integer',
            'eievaluationk.lapso_id' => 'required|integer',
            'eievaluationk.seccion_id' => 'required|integer',
            'eievaluationk.finicial' => 'required|date',
            'eievaluationk.ffinal' => 'required|date|after_or_equal:eievaluationk.finicial',
            'eievaluationk.observaciones' => 'required|string|min:10',
            'eievaluationk.recomendacion' => 'nullable|string',
            'eievaluationk.asistencia' => 'required|string',
        ]);

        try {
            $this->eievaluationk->save();
            $this->closeModal();
            $this->showSuccessAlert('Plan de evaluación guardado exitosamente');
            $this->resetModels();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar el plan: ' . $e->getMessage());
        }
    }

    public function savePosition()
    {
        if (!$this->eievaluationk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        $this->eievaluationp->eievaluationk_id = $this->eievaluationk_id;

        $this->validate([
            'eievaluationp.eievaluationk_id' => 'required|integer',
            'eievaluationp.pevaluacion_id' => 'required|integer',
            'eievaluationp.fecha' => 'nullable|string',
            'eievaluationp.nombre_ninos' => 'nullable|string',
            'eievaluationp.aprendizaje_alcanzado' => 'nullable|string',
            'eievaluationp.componente' => 'nullable|string',
            'eievaluationp.indicadores' => 'nullable|string',
            'eievaluationp.instrumento' => 'nullable|string',
            'eievaluationp.observacion' => 'nullable|string',
            'eievaluationp.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eievaluationp->order = $this->eievaluationp->order ?: null;
            $this->eievaluationp->save();
            
            $this->showSuccessAlert('Posición guardada exitosamente');
            $this->openModal('position', $this->eievaluationp->eievaluationk_id);

        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar la posición: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $eievaluationk = Eievaluationk::findOrFail($id);
            $eievaluationk->delete();
            $this->showSuccessAlert('Plan eliminado exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar el plan: ' . $e->getMessage());
        }
    }

    public function deletePosition($id)
    {
        try {
            $eievaluationp = Eievaluationp::findOrFail($id);
            $eievaluationp->delete();
            $this->showSuccessAlert('Posición eliminada exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar la posición: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function initializeLists()
    {
        $this->list_comment = Eievaluationk::COLUMN_COMMENTS;
        $this->list_comment_position = Eievaluationp::COLUMN_COMMENTS;
        
        $profesor = User::findOrFail(Auth::id())->profesor;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::list_lapso();
    }

    private function resetModels()
    {
        $this->eievaluationk = new Eievaluationk();
        $this->resetModelPosition();
    }

    private function resetModelPosition()
    {
        $this->eievaluationp = new Eievaluationp();
        $this->eievaluationp_id = null;
    }

    private function showSuccessAlert($message)
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => '¡Excelente!',
            'html' => $message,
            'timer' => 3000,
            'icon' => 'success',
        ]);
    }

    private function showErrorAlert($message)
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => '¡Error!',
            'html' => $message,
            'timer' => 5000,
            'icon' => 'error',
        ]);
    }
}
