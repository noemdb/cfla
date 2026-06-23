<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningbwsummary;
use App\Models\app\Inicial\Eiplanningbwstrategy;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EiplanningbwkComponent extends Component
{
    use EiplanningbwkValidateTrait, WithPagination;

    public Eiplanningbwk $eiplanningbwk;
    public Eiplanningbwsummary $eiplanningbwsummary;


    // Properties
    public $eiplanningbwk_id, $eiplanningbwsummary_id, $profesor_id, $lapso_id;
    
    // Mode states
    public $activeTab = 'list';
    public $showModal = false;
    public $modalType = '';
    public $editingId = null;
    
    // Lists for dropdowns
    public $list_comment, $list_comment_summary, $list_comment_strategy, $list_moment;
    public $list_grado, $list_seccion, $list_pevaluacion, $list_lapso, $list_eiprojectk;
    
    // Search and filters
    public $search = '';
    public $filterGrado = '';
    public $filterSeccion = '';

    protected $paginationTheme = 'bootstrap-4';

    // Agregar después de las propiedades existentes
    public $strategies = [];
    public $activeDay = 'lunes';
    public $activeMoment = 'Recibimiento';

    // Agregar en el método initializeLists()
    private function initializeLists()
    {
        $this->list_comment = Eiplanningbwk::COLUMN_COMMENTS;
        $this->list_comment_summary = Eiplanningbwsummary::COLUMN_COMMENTS;
        $this->list_comment_strategy = Eiplanningbwstrategy::COLUMN_COMMENTS;
        $this->list_moment = Eiplanningbwstrategy::LIST_MOMENT;
        
        $profesor = User::findOrFail(Auth::id())->profesor;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::list_lapso();
        $this->list_eiprojectk = Eiprojectk::getForProfesorIdList($this->profesor_id);
    }

    public function updatedEiplanningbwkGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id', $value)->orderBy('name')->pluck('name', 'id') : collect();
        $this->eiplanningbwk->seccion_id = null;
    }

    public function updatedLapsoId($lapso_id)
    {
        $eiplanningbwk = Eiplanningbwk::find($this->eiplanningbwk_id);
        $this->list_pevaluacion = ($eiplanningbwk) ? $eiplanningbwk->getPevaluacionsList($this->profesor_id, $lapso_id) : collect();
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

        $this->moments = Eiplanningbwstrategy::LIST_MOMENT;
        $this->weekDays = Eiplanningbwstrategy::WEEK_DAYS;

        $this->initializeLists();
        $this->resetModels();
    }

    public function render()
    {
        $query = Eiplanningbwk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion', 'eiprojectk']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('diagnostico', 'like', '%' . $this->search . '%')
                  ->orWhere('observacion', 'like', '%' . $this->search . '%');
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

        $eiplanningbwks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inicial.eiplanningbwk-component',[
            'eiplanningbwks' => $eiplanningbwks,
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
                    $this->loadPlan($id);
                    break;
                case 'view':
                    // Just set the ID for the view modal
                    $this->editingId = $id;
                    break;
                case 'summary':
                    $this->loadPlanForSummary($id);
                    break;
                case 'edit-summary':
                    $this->loadSummary($id);
                    break;
                case 'edit-strategy':
                    $this->loadStrategy($id);
                    break;
                case 'strategy':
                    $this->loadPlanForStrategy($id);
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
    private function loadPlan($id)
    {
        $this->eiplanningbwk = Eiplanningbwk::findOrFail($id);
        $this->eiplanningbwk_id = $this->eiplanningbwk->id;
        $grado_id = $this->eiplanningbwk->grado_id;
        $this->list_seccion = Seccion::active("true")->where('grado_id', $grado_id)->orderBy('name')->pluck('name', 'id');
    }

    private function loadPlanForSummary($id)
    {
        $eiplanningbwk = Eiplanningbwk::findOrFail($id);
        $this->eiplanningbwk_id = $eiplanningbwk->id;
        $this->list_pevaluacion = $eiplanningbwk->getPevaluacionsList($this->profesor_id);
        $this->resetModelSummary();
    }

    private function loadSummary($id = null)
    {
        // Si no se proporciona ID, es una creación nueva
        if (!$id) {
            $this->resetModelSummary();
            return;
        }

        // Intentar cargar el resumen existente
        $summary = Eiplanningbwsummary::find($id);
        
        if ($summary) {
            // Editar resumen existente
            $this->eiplanningbwsummary = $summary;
            $this->eiplanningbwsummary_id = $summary->id;
            $eiplanningbwk = $summary->eiplanningbwk;
            $this->eiplanningbwk_id = $eiplanningbwk->id;
            $this->list_pevaluacion = $eiplanningbwk->getPevaluacionsList($this->profesor_id);
        } else {
            // El resumen no existe, crear uno nuevo
            $this->resetModelSummary();
            // Si tenemos un plan activo, mantenerlo
            if ($this->eiplanningbwk_id) {
                $eiplanningbwk = Eiplanningbwk::find($this->eiplanningbwk_id);
                if ($eiplanningbwk) {
                    $this->list_pevaluacion = $eiplanningbwk->getPevaluacionsList($this->profesor_id);
                }
            }
        }
    }

    // CRUD Operations
    public function save()
    {
        $this->eiplanningbwk->profesor_id = $this->profesor_id;

        $this->validate([
            'eiplanningbwk.profesor_id' => 'required|integer',
            'eiplanningbwk.grado_id' => 'required|integer',
            'eiplanningbwk.seccion_id' => 'required|integer',
            'eiplanningbwk.eiprojectk_id' => 'nullable|integer',
            'eiplanningbwk.finicial' => 'required|date',
            'eiplanningbwk.ffinal' => 'required|date|after_or_equal:eiplanningbwk.finicial',
            'eiplanningbwk.tiempo_ejecucion' => 'required|integer|min:1',
            'eiplanningbwk.diagnostico' => 'required|string|min:10',
            'eiplanningbwk.observacion' => 'nullable|string',
        ]);

        try {
            $this->eiplanningbwk->save();
            $this->closeModal();
            $this->showSuccessAlert('Plan quincenal guardado exitosamente');
            $this->resetModels();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar el plan: ' . $e->getMessage());
        }
    }

    public function saveSummary()
    {
        // Validar que tenemos un plan válido
        if (!$this->eiplanningbwk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        $this->eiplanningbwsummary->eiplanningbwk_id = $this->eiplanningbwk_id;

        $this->validate([
            'eiplanningbwsummary.eiplanningbwk_id' => 'required|integer',
            'eiplanningbwsummary.pevaluacion_id' => 'required|integer',
            'eiplanningbwsummary.componente' => 'required|string',
            'eiplanningbwsummary.objetivo' => 'required|string',
            'eiplanningbwsummary.aprendizaje_esperado' => 'required|string',
            'eiplanningbwsummary.indicadores' => 'required|string',
            'eiplanningbwsummary.linea_investigacion' => 'nullable|string',
            'eiplanningbwsummary.enfasis_curriculares' => 'nullable|string',
            'eiplanningbwsummary.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eiplanningbwsummary->order = $this->eiplanningbwsummary->order ?: null;
            $this->eiplanningbwsummary->save();

            $this->showSuccessAlert('Resumen guardado exitosamente');

            $this->openModal('summary',$this->eiplanningbwsummary->eiplanningbwk_id); //dd($this->eiplanningbwsummary->id);

        } catch (Exception $e) {
            $this->showErrorAlert('Error al guardar el resumen: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $eiplanningbwk = Eiplanningbwk::findOrFail($id);
            $eiplanningbwk->delete();
            $this->showSuccessAlert('Plan eliminado exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar el plan: ' . $e->getMessage());
        }
    }

    public function deleteSummary($id)
    {
        try {
            $eiplanningbwsummary = Eiplanningbwsummary::findOrFail($id);
            $eiplanningbwsummary->delete();
            $this->showSuccessAlert('Resumen eliminado exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar el resumen: ' . $e->getMessage());
        }
    }

    private function resetModels()
    {
        $this->eiplanningbwk = new Eiplanningbwk();
        $this->resetModelSummary();
        $this->resetModelStrategies();
    }

    private function resetModelSummary()
    {
        $this->eiplanningbwsummary = new Eiplanningbwsummary();
        $this->eiplanningbwsummary_id = null;
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

    // Agregar nuevos métodos para gestión de estrategias
    private function loadPlanForStrategy($id)
    {
        $eiplanningbwk = Eiplanningbwk::findOrFail($id);
        $this->eiplanningbwk_id = $eiplanningbwk->id;
        $this->loadStrategiesForPlan($id);
    }

    private function loadStrategiesForPlan($planId)
    {
        $this->strategies = [];

        foreach ($this->weekDays as $day => $dayName) {
            $this->strategies[$day] = [];

            foreach ($this->moments as $moment => $momentName) {
                // Buscar estrategia existente para este día y momento
                $existingStrategy = \App\Models\app\Inicial\Eiplanningbwstrategy::where('eiplanningbwk_id', $planId)
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
                    // Crear estructura vacía para nuevo registro
                    $this->strategies[$day][$moment] = [
                        'id' => null,
                        'estrategia' => '',
                        'order' => null,
                    ];
                }
            }
        }
    }

    // Métodos para navegación
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

    // Métodos para guardar estrategias
    public function saveCurrentStrategy()
    {
        if (!$this->eiplanningbwk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        $currentStrategy = $this->strategies[$this->activeDay][$this->activeMoment];

        if (empty($currentStrategy['estrategia'])) {
            $this->showErrorAlert('Debe completar la estrategia antes de guardar');
            return;
        }

        try {
            if ($currentStrategy['id']) {
                $strategy = \App\Models\app\Inicial\Eiplanningbwstrategy::find($currentStrategy['id']);
            } else {
                $strategy = new \App\Models\app\Inicial\Eiplanningbwstrategy();
                $strategy->eiplanningbwk_id = $this->eiplanningbwk_id;
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
        if (!$this->eiplanningbwk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
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
                            $strategy = \App\Models\app\Inicial\Eiplanningbwstrategy::find($strategyData['id']);
                        } else {
                            $strategy = new \App\Models\app\Inicial\Eiplanningbwstrategy();
                            $strategy->eiplanningbwk_id = $this->eiplanningbwk_id;
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
                $strategy = \App\Models\app\Inicial\Eiplanningbwstrategy::findOrFail($this->strategies[$day][$moment]['id']);
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
}
