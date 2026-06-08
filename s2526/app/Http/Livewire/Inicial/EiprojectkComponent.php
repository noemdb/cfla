<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eiprojectreview;
use App\Models\app\Inicial\Eiprojectsummary;
use App\Models\app\Inicial\Eiprojectkstrategy;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EiprojectkComponent extends Component
{
    use EiprojectkValidateTrait, WithPagination;

    public Eiprojectk $eiprojectk;
    public Eiprojectreview $eiprojectreview;
    public Eiprojectsummary $eiprojectsummary;

    // Properties
    public $eiprojectk_id, $eiprojectreview_id, $eiprojectsummary_id, $profesor_id, $lapso_id;

    // Mode states
    public $activeTab = 'list';
    public $showModal = false;
    public $modalType = '';
    public $editingId = null;

    // Lists for dropdowns
    public $list_comment, $list_comment_review, $list_comment_summary;
    public $list_grado, $list_seccion, $list_pevaluacion, $list_lapso;

    // Search and filters
    public $search = '';
    public $filterGrado = '';
    public $filterSeccion = '';

    protected $paginationTheme = 'bootstrap-4';

    // Agregar después de las propiedades existentes
    public $strategies = [];
    public $activeDay = 'lunes';
    public $activeMoment = 'Recibimiento';

    public function updatedEiprojectkGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id', $value)->orderBy('name')->pluck('name', 'id') : collect();
        $this->eiprojectk->seccion_id = null;
    }

    public function updatedLapsoId($lapso_id)
    {
        $eiprojectk = Eiprojectk::find($this->eiprojectk_id);
        $this->list_pevaluacion = ($eiprojectk) ? $eiprojectk->getPevaluacionsList($this->profesor_id, $lapso_id) : collect();
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

    private function initializeLists()
    {
        $this->list_comment = Eiprojectk::COLUMN_COMMENTS;
        $this->list_comment_review = Eiprojectreview::COLUMN_COMMENTS;
        $this->list_comment_summary = Eiprojectsummary::COLUMN_COMMENTS;
        $this->list_comment_strategy = Eiprojectkstrategy::COLUMN_COMMENTS;
        $this->list_moment = Eiprojectkstrategy::LIST_MOMENT;

        $this->moments = Eiprojectkstrategy::LIST_MOMENT;
        $this->weekDays = Eiprojectkstrategy::WEEK_DAYS;
        
        $profesor = User::findOrFail(Auth::id())->profesor;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::list_lapso();
    }

    public function render()
    {
        $query = Eiprojectk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('diagnostico', 'like', '%' . $this->search . '%');
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

        $eiprojectks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inicial.eiprojectk-component',[
            'eiprojectks' => $eiprojectks
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
                    $this->loadProject($id);
                    break;
                case 'view':
                    $this->editingId = $id;
                    break;
                case 'review':
                    $this->loadProjectForReview($id);
                    break;
                case 'summary':
                    $this->loadProjectForSummary($id);
                    break;
                case 'strategy':
                    $this->loadProjectForStrategy($id);
                    break;
                case 'edit-review':
                    $this->loadReview($id);
                    break;
                case 'edit-summary':
                    $this->loadSummary($id);
                    break;
            }
        } catch (Exception $e) {
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
    private function loadProject($id)
    {
        $this->eiprojectk = Eiprojectk::findOrFail($id);
        $this->eiprojectk_id = $this->eiprojectk->id;
        $grado_id = $this->eiprojectk->grado_id;
        $this->list_seccion = Seccion::active("true")->where('grado_id', $grado_id)->orderBy('name')->pluck('name', 'id');
    }

    private function loadProjectForReview($id)
    {
        $eiprojectk = Eiprojectk::findOrFail($id);
        $this->eiprojectk_id = $eiprojectk->id;
        $this->resetModelReview();
    }

    private function loadProjectForSummary($id)
    {
        $eiprojectk = Eiprojectk::findOrFail($id);
        $this->eiprojectk_id = $eiprojectk->id;
        $this->list_pevaluacion = $eiprojectk->getPevaluacionsList($this->profesor_id);
        $this->resetModelSummary();
    }

    private function loadReview($id = null)
    {
        if (!$id) {
            $this->resetModelReview();
            return;
        }

        $review = Eiprojectreview::find($id);

        if ($review) {
            $this->eiprojectreview = $review;
            $this->eiprojectreview_id = $review->id;
            $eiprojectk = $review->eiprojectk;
            $this->eiprojectk_id = $eiprojectk->id;
        } else {
            $this->resetModelReview();
            if ($this->eiprojectk_id) {
                $eiprojectk = Eiprojectk::find($this->eiprojectk_id);
            }
        }
    }

    private function loadSummary($id = null)
    {
        if (!$id) {
            $this->resetModelSummary();
            return;
        }

        $summary = Eiprojectsummary::find($id);

        if ($summary) {
            $this->eiprojectsummary = $summary;
            $this->eiprojectsummary_id = $summary->id;
            $eiprojectk = $summary->eiprojectk;
            $this->eiprojectk_id = $eiprojectk->id;
            $this->list_pevaluacion = $eiprojectk->getPevaluacionsList($this->profesor_id);
        } else {
            $this->resetModelSummary();
            if ($this->eiprojectk_id) {
                $eiprojectk = Eiprojectk::find($this->eiprojectk_id);
                if ($eiprojectk) {
                    $this->list_pevaluacion = $eiprojectk->getPevaluacionsList($this->profesor_id);
                }
            }
        }
    }

    // CRUD Operations
    public function save()
    {
        $this->eiprojectk->profesor_id = $this->profesor_id;

        $this->validate([
            'eiprojectk.profesor_id' => 'required|integer',
            'eiprojectk.grado_id' => 'required|integer',
            'eiprojectk.seccion_id' => 'required|integer',
            'eiprojectk.finicial' => 'required|date',
            'eiprojectk.ffinal' => 'required|date|after_or_equal:eiprojectk.finicial',
            'eiprojectk.tiempo_ejecucion' => 'required|integer|min:1',
            'eiprojectk.diagnostico' => 'required|string|min:10',
        ]);

        try {
            $this->eiprojectk->save();
            $this->closeModal();
            $this->showSuccessAlert('Proyecto guardado exitosamente');
            $this->resetModels();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar el proyecto: ' . $e->getMessage());
        }
    }

    public function saveReview()
    {
        if (!$this->eiprojectk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un proyecto válido');
            return;
        }

        $this->eiprojectreview->eiprojectk_id = $this->eiprojectk_id;

        $this->validate([
            'eiprojectreview.eiprojectk_id' => 'required|integer',
            'eiprojectreview.posibles_temas_interes' => 'required|string',
            'eiprojectreview.eleccion_tema_nombre' => 'required|string',
            'eiprojectreview.que_sabe' => 'required|string',
            'eiprojectreview.que_desean_aprender' => 'required|string',
            'eiprojectreview.que_necesitamos' => 'required|string',
            'eiprojectreview.quienes_nos_pueden_apoyar' => 'required|string',
            'eiprojectreview.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eiprojectreview->order = $this->eiprojectreview->order ?: null;
            $this->eiprojectreview->save();

            $this->showSuccessAlert('Revisión guardada exitosamente');
            $this->openModal('review', $this->eiprojectreview->eiprojectk_id);

        } catch (Exception $e) {
            $this->showErrorAlert('Error al guardar la revisión: ' . $e->getMessage());
        }
    }

    public function saveSummary()
    {
        if (!$this->eiprojectk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un proyecto válido');
            return;
        }

        $this->eiprojectsummary->eiprojectk_id = $this->eiprojectk_id;

        $this->validate([
            'eiprojectsummary.eiprojectk_id' => 'required|integer',
            'eiprojectsummary.pevaluacion_id' => 'required|integer',
            'eiprojectsummary.componente' => 'required|string',
            'eiprojectsummary.objetivo' => 'required|string',
            'eiprojectsummary.aprendizaje_esperado' => 'required|string',
            'eiprojectsummary.indicadores' => 'required|string',
            'eiprojectsummary.linea_investigacion' => 'nullable|string',
            'eiprojectsummary.enfasis_curriculares' => 'nullable|string',
            'eiprojectsummary.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eiprojectsummary->order = $this->eiprojectsummary->order ?: null;
            $this->eiprojectsummary->save();

            $this->showSuccessAlert('Resumen guardado exitosamente');
            $this->openModal('summary', $this->eiprojectsummary->eiprojectk_id);
        } catch (Exception $e) {
            $this->showErrorAlert('Error al guardar el resumen: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $eiprojectk = Eiprojectk::findOrFail($id);
            $eiprojectk->delete();
            $this->showSuccessAlert('Proyecto eliminado exitosamente');
        } catch (Exception $e) {
            $this->showErrorAlert('Error al eliminar el proyecto: ' . $e->getMessage());
        }
    }

    public function deleteReview($id)
    {
        try {
            $eiprojectreview = Eiprojectreview::findOrFail($id);
            $eiprojectreview->delete();
            $this->showSuccessAlert('Revisión eliminada exitosamente');
        } catch (Exception $e) {
            $this->showErrorAlert('Error al eliminar la revisión: ' . $e->getMessage());
        }
    }

    public function deleteSummary($id)
    {
        try {
            $eiprojectsummary = Eiprojectsummary::findOrFail($id);
            $eiprojectsummary->delete();
            $this->showSuccessAlert('Resumen eliminado exitosamente');
        } catch (Exception $e) {
            $this->showErrorAlert('Error al eliminar el resumen: ' . $e->getMessage());
        }
    }
    
    private function resetModelReview()
    {
        $this->eiprojectreview = new Eiprojectreview();
        $this->eiprojectreview_id = null;
    }

    private function resetModelSummary()
    {
        $this->eiprojectsummary = new Eiprojectsummary();
        $this->eiprojectsummary_id = null;
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


    private function loadProjectForStrategy($id)
    {
        $eiprojectk = Eiprojectk::findOrFail($id);
        $this->eiprojectk_id = $eiprojectk->id;
        $this->loadStrategiesForProject($id);
    }

    private function loadStrategiesForProject($projectId)
    {
        $this->strategies = [];

        foreach ($this->weekDays as $day => $dayName) {
            $this->strategies[$day] = [];

            foreach ($this->moments as $moment => $momentName) {
                $existingStrategy = \App\Models\app\Inicial\Eiprojectkstrategy::where('eiprojectk_id', $projectId)
                    ->where('day_of_week', $day)
                    ->where('momento_rutina_diaria', $moment)
                    ->first();

                if ($existingStrategy) {
                    $this->strategies[$day][$moment] = [
                        'id' => $existingStrategy->id,
                        'estrategia' => $existingStrategy->lunes,
                        'order' => $existingStrategy->order,
                    ];
                } else {
                    $this->strategies[$day][$moment] = [
                        'id' => null,
                        'estrategia' => '',
                        'order' => null,
                    ];
                }
            }
        }
    }

    // Métodos para navegación (copiar los mismos métodos de EiplanningbwkComponent)
    public function setActiveDay($day)
    {
        $this->activeDay = $day;
        $this->activeMoment = array_key_first($this->moments);
    }

    public function setActiveMoment($moment)
    {
        $this->activeMoment = $moment;
    }

    public function nextMoment()
    {
        $moments = array_keys($this->moments);
        $currentIndex = array_search($this->activeMoment, $moments);

        if ($currentIndex < count($moments) - 1) {
            $this->activeMoment = $moments[$currentIndex + 1];
        }
    }

    public function previousMoment()
    {
        $moments = array_keys($this->moments);
        $currentIndex = array_search($this->activeMoment, $moments);

        if ($currentIndex > 0) {
            $this->activeMoment = $moments[$currentIndex - 1];
        }
    }

    public function getDayProgress($day)
    {
        $completed = 0;
        $total = count($this->moments);

        foreach ($this->moments as $moment => $momentName) {
            if (!empty($this->strategies[$day][$moment]['estrategia'])) {
                $completed++;
            }
        }

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? ($completed / $total) * 100 : 0
        ];
    }

    // Métodos para guardar estrategias (copiar de EiplanningbwkComponent)
    public function saveCurrentStrategy()
    {
        if (!$this->eiprojectk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un proyecto válido');
            return;
        }

        $currentStrategy = $this->strategies[$this->activeDay][$this->activeMoment];

        if (empty($currentStrategy['estrategia'])) {
            $this->showErrorAlert('Debe completar la estrategia antes de guardar');
            return;
        }

        try {
            if ($currentStrategy['id']) {
                $strategy = \App\Models\app\Inicial\Eiprojectkstrategy::find($currentStrategy['id']);
            } else {
                $strategy = new \App\Models\app\Inicial\Eiprojectkstrategy();
                $strategy->eiprojectk_id = $this->eiprojectk_id;
                $strategy->day_of_week = $this->activeDay;
                $strategy->momento_rutina_diaria = $this->activeMoment;
            }

            $strategy->lunes = $currentStrategy['estrategia'];
            $strategy->order = $currentStrategy['order'] ?: null;
            $strategy->martes = null;
            $strategy->miercoles = null;
            $strategy->jueves = null;
            $strategy->viernes = null;

            $strategy->save();

            $this->strategies[$this->activeDay][$this->activeMoment]['id'] = $strategy->id;

            $this->showSuccessAlert('Estrategia guardada exitosamente');
            $this->nextMoment();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar la estrategia: ' . $e->getMessage());
        }
    }

    public function saveStrategies()
    {
        if (!$this->eiprojectk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un proyecto válido');
            return;
        }

        $hasData = false;
        foreach ($this->strategies as $day => $dayStrategies) {
            foreach ($dayStrategies as $moment => $strategyData) {
                if (!empty($strategyData['estrategia'])) {
                    $hasData = true;
                    break 2;
                }
            }
        }

        if (!$hasData) {
            $this->showErrorAlert('Debe completar al menos una estrategia');
            return;
        }

        try {
            foreach ($this->strategies as $day => $dayStrategies) {
                foreach ($dayStrategies as $moment => $strategyData) {
                    if (!empty($strategyData['estrategia'])) {
                        if ($strategyData['id']) {
                            $strategy = \App\Models\app\Inicial\Eiprojectkstrategy::find($strategyData['id']);
                        } else {
                            $strategy = new \App\Models\app\Inicial\Eiprojectkstrategy();
                            $strategy->eiprojectk_id = $this->eiprojectk_id;
                            $strategy->day_of_week = $day;
                            $strategy->momento_rutina_diaria = $moment;
                        }

                        $strategy->lunes = $strategyData['estrategia'];
                        $strategy->order = $strategyData['order'] ?: null;
                        $strategy->martes = null;
                        $strategy->miercoles = null;
                        $strategy->jueves = null;
                        $strategy->viernes = null;

                        $strategy->save();
                        $this->strategies[$day][$moment]['id'] = $strategy->id;
                    }
                }
            }

            $this->showSuccessAlert('Estrategias guardadas exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar las estrategias: ' . $e->getMessage());
        }
    }

    public function deleteStrategy($day, $moment)
    {
        try {
            if (isset($this->strategies[$day][$moment]['id']) && $this->strategies[$day][$moment]['id']) {
                $strategy = \App\Models\app\Inicial\Eiprojectkstrategy::findOrFail($this->strategies[$day][$moment]['id']);
                $strategy->delete();

                $this->strategies[$day][$moment] = [
                    'id' => null,
                    'estrategia' => '',
                    'order' => null,
                ];

                $this->showSuccessAlert('Estrategia eliminada exitosamente');
            }
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar la estrategia: ' . $e->getMessage());
        }
    }

    private function resetModels()
    {
        $this->eiprojectk = new Eiprojectk();
        $this->resetModelReview();
        $this->resetModelSummary();
        $this->resetModelStrategies();
    }

    private function resetModelStrategies()
    {
        $this->strategies = [];
        foreach ($this->weekDays as $day => $dayName) {
            $this->strategies[$day] = [];
            foreach ($this->moments as $moment => $momentName) {
                $this->strategies[$day][$moment] = [
                    'id' => null,
                    'estrategia' => '',
                    'order' => null,
                ];
            }
        }
    }

}
