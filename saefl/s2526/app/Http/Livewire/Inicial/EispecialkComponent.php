<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eispecialact;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Inicial\Eispecialstrategy;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EispecialkComponent extends Component
{
    use WithPagination, EispecialkValidateTrait;

    public Eispecialk $eispecialk;
    public Eispecialact $eispecialact;

    // Properties
    public $eispecialk_id, $eispecialact_id, $profesor_id, $lapso_id;
    
    // Mode states
    public $activeTab = 'list';
    public $showModal = false;
    public $modalType = '';
    public $editingId = null;
    
    // Lists for dropdowns
    public $list_comment, $list_comment_activities;
    public $list_grado, $list_seccion, $list_pevaluacion, $list_lapso;
    
    // Search and filters
    public $search = '';
    public $filterGrado = '';
    public $filterSeccion = '';

    protected $paginationTheme = 'bootstrap-4';

    public $strategies = [];
    public $activeDay = 'lunes';
    public $activeMoment = 'Recibimiento';

    public function updatedEispecialkGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id', $value)->orderBy('name')->pluck('name', 'id') : collect();
        $this->eispecialk->seccion_id = null;
    }

    public function updatedLapsoId($lapso_id)
    {
        $eispecialk = Eispecialk::find($this->eispecialk_id);
        $this->list_pevaluacion = ($eispecialk) ? $eispecialk->getPevaluacionsList($this->profesor_id, $lapso_id) : collect();
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
        $query = Eispecialk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion']);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('justificacion', 'like', '%' . $this->search . '%')
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

        $eispecialks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inicial.eispecialk-component', [
            'eispecialks' => $eispecialks
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
                    $this->editingId = $id;
                    break;
                case 'activity':
                    $this->loadPlanForActivity($id);
                    break;
                case 'strategy':
                    $this->loadPlanForStrategy($id);
                    break;
                case 'edit-activity':
                    $this->loadActivity($id);
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
        $this->eispecialk = Eispecialk::findOrFail($id);
        $this->eispecialk_id = $this->eispecialk->id;
        $grado_id = $this->eispecialk->grado_id;
        $this->list_seccion = Seccion::active("true")->where('grado_id', $grado_id)->orderBy('name')->pluck('name', 'id');
    }

    private function loadPlanForActivity($id)
    {
        $eispecialk = Eispecialk::findOrFail($id);
        $this->eispecialk_id = $eispecialk->id;
        $this->list_pevaluacion = $eispecialk->getPevaluacionsList($this->profesor_id);
        $this->resetModelActivity();
    }

    private function loadActivity($id = null)
    {
        if (!$id) {
            $this->resetModelActivity();
            return;
        }

        $activity = Eispecialact::find($id);
        
        if ($activity) {
            $this->eispecialact = $activity;
            $this->eispecialact_id = $activity->id;
            $eispecialk = $activity->eispecialk;
            $this->eispecialk_id = $eispecialk->id;
            $this->list_pevaluacion = $eispecialk->getPevaluacionsList($this->profesor_id);
        } else {
            $this->resetModelActivity();
        }
    }

    // CRUD Operations
    public function save()
    {
        $this->eispecialk->profesor_id = $this->profesor_id;

        $this->validate([
            'eispecialk.profesor_id' => 'required|integer',
            'eispecialk.grado_id' => 'required|integer',
            'eispecialk.seccion_id' => 'required|integer',
            'eispecialk.finicial' => 'required|date',
            'eispecialk.ffinal' => 'required|date|after_or_equal:eispecialk.finicial',
            'eispecialk.tiempo_ejecucion' => 'required|integer|min:1',
            'eispecialk.justificacion' => 'required|string|min:10',
            'eispecialk.observacion' => 'nullable|string',
        ]);

        try {
            $this->eispecialk->save();
            $this->closeModal();
            $this->showSuccessAlert('Plan especial guardado exitosamente');
            $this->resetModels();
        } catch (Exception $e) {
            $this->showErrorAlert('Error al guardar el plan: ' . $e->getMessage());
        }
    }

    public function saveActivity()
    {
        if (!$this->eispecialk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        $this->eispecialact->eispecialk_id = $this->eispecialk_id;

        $this->validate([
            'eispecialact.eispecialk_id' => 'required|integer',
            'eispecialact.pevaluacion_id' => 'required|integer',
            'eispecialact.componente' => 'required|string',
            'eispecialact.objetivo' => 'required|string',
            'eispecialact.aprendizaje_esperado' => 'required|string',
            'eispecialact.indicadores' => 'required|string',
            'eispecialact.linea_investigacion' => 'nullable|string',
            'eispecialact.enfasis_curriculares' => 'nullable|string',
            'eispecialact.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eispecialact->order = $this->eispecialact->order ?: null;
            $this->eispecialact->save();
            
            $this->showSuccessAlert('Actividad guardada exitosamente');
            $this->openModal('activity', $this->eispecialact->eispecialk_id);

        } catch (Exception $e) {
            $this->showErrorAlert('Error al guardar la actividad: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $eispecialk = Eispecialk::findOrFail($id);
            $eispecialk->delete();
            $this->showSuccessAlert('Plan eliminado exitosamente');
        } catch (Exception $e) {
            $this->showErrorAlert('Error al eliminar el plan: ' . $e->getMessage());
        }
    }

    public function deleteActivity($id)
    {
        try {
            $eispecialact = Eispecialact::findOrFail($id);
            $eispecialact->delete();
            $this->showSuccessAlert('Actividad eliminada exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar la actividad: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function initializeLists()
    {
        $this->list_comment = Eispecialk::COLUMN_COMMENTS;
        $this->list_comment_activities = Eispecialact::COLUMN_COMMENTS;
        $this->list_comment_strategy = Eispecialstrategy::COLUMN_COMMENTS;
        $this->list_moment = Eispecialstrategy::LIST_MOMENT;

        $this->moments = Eispecialstrategy::LIST_MOMENT;
        $this->weekDays = Eispecialstrategy::WEEK_DAYS;
        
        $profesor = User::findOrFail(Auth::id())->profesor;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::list_lapso();
    }

    private function resetModelActivity()
    {
        $this->eispecialact = new Eispecialact();
        $this->eispecialact_id = null;
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
        $eispecialk = Eispecialk::findOrFail($id);
        $this->eispecialk_id = $eispecialk->id;
        $this->loadStrategiesForPlan($id);
    }

    private function loadStrategiesForPlan($planId)
    {
        $this->strategies = [];

        foreach ($this->weekDays as $day => $dayName) {
            $this->strategies[$day] = [];

            foreach ($this->moments as $moment => $momentName) {
                $existingStrategy = Eispecialstrategy::where('eispecialk_id', $planId)
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

    // Métodos para navegación (copiar los mismos métodos de los componentes anteriores)
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
        if (!$this->eispecialk_id) {
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
                $strategy = Eispecialstrategy::find($currentStrategy['id']);
            } else {
                $strategy = new Eispecialstrategy();
                $strategy->eispecialk_id = $this->eispecialk_id;
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
        if (!$this->eispecialk_id) {
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
                            $strategy = Eispecialstrategy::find($strategyData['id']);
                        } else {
                            $strategy = new Eispecialstrategy();
                            $strategy->eispecialk_id = $this->eispecialk_id;
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
                $strategy = Eispecialstrategy::findOrFail($this->strategies[$day][$moment]['id']);
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

    // Agregar en resetModels()
    private function resetModels()
    {
        $this->eispecialk = new Eispecialk();
        $this->resetModelActivity();
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
