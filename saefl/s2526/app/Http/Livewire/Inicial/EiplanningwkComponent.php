<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class EiplanningwkComponent extends Component
{
    use EiplanningwkValidateTrait, WithPagination;

    public Eiplanningwk $eiplanningwk;
    public Eiplanningwsummary $eiplanningwsummary;

    // Cambio: Array de estrategias por día y momento
    public $strategies = [];
    public $activeDay = 'lunes'; // Día activo en las pestañas
    public $activeMoment = 'Recibimiento'; // Momento activo

    // Properties
    public $eiplanningwk_id, $eiplanningwsummary_id, $profesor_id, $lapso_id;

    // Mode states
    public $activeTab = 'list';
    public $showModal = false;
    public $modalType = '';
    public $editingId = null;

    // Lists for dropdowns
    public $list_comment, $list_comment_summary, $list_comment_strategy, $list_moment;
    public $list_grado, $list_seccion, $list_pevaluacion, $list_lapso, $list_eiprojectk;

    // Días de la semana
    public $weekDays = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes'
    ];

    // Momentos de la rutina diaria (orden específico)
    public $moments = [
        'Recibimiento' => 'Recibimiento',
        'Momento Cívico' => 'Momento Cívico',
        'Aseo-Desayuno-Aseo' => 'Aseo-Desayuno-Aseo',
        'Periodo: Planificación' => 'Periodo: Planificación',
        'Periodo: Trabajo Libre' => 'Periodo: Trabajo Libre',
        'Periodo: Orden y limpieza' => 'Periodo: Orden y limpieza',
        'Periodo: Intercambio y Recuento' => 'Periodo: Intercambio y Recuento',
        'Periodo: Trabajos en Pequeños Grupos' => 'Periodo: Trabajos en Pequeños Grupos',
        'Periodo: Actividades Colectivas' => 'Periodo: Actividades Colectivas',
        'Periodo: Despedida' => 'Periodo: Despedida',
    ];

    // Search and filters
    public $search = '';
    public $filterGrado = '';
    public $filterSeccion = '';
    protected $paginationTheme = 'bootstrap-4';

    public function updatedEiplanningwkGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id', $value)->orderBy('name')->pluck('name', 'id') : collect();
        $this->eiplanningwk->seccion_id = null;
    }

    public function updatedLapsoId($lapso_id)
    {
        $eiplanningwk = Eiplanningwk::find($this->eiplanningwk_id);
        $this->list_pevaluacion = ($eiplanningwk) ? $eiplanningwk->getPevaluacionsList($this->profesor_id, $lapso_id) : collect();
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

    // Método para cambiar de pestaña (día)
    public function setActiveDay($day)
    {
        $this->activeDay = $day;
        // Resetear al primer momento cuando cambiamos de día
        $this->activeMoment = array_key_first($this->moments);
    }

    // Método para cambiar de momento
    public function setActiveMoment($moment)
    {
        $this->activeMoment = $moment;
    }

    // Método para avanzar al siguiente momento
    public function nextMoment()
    {
        $moments = array_keys($this->moments);
        $currentIndex = array_search($this->activeMoment, $moments);

        if ($currentIndex < count($moments) - 1) {
            $this->activeMoment = $moments[$currentIndex + 1];
        }
    }

    // Método para retroceder al momento anterior
    public function previousMoment()
    {
        $moments = array_keys($this->moments);
        $currentIndex = array_search($this->activeMoment, $moments);

        if ($currentIndex > 0) {
            $this->activeMoment = $moments[$currentIndex - 1];
        }
    }

    // Método para obtener el progreso de un día
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
        $query = Eiplanningwk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion', 'eiprojectk']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
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

        $eiplanningwks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inicial.eiplanningwk-component', [
            'eiplanningwks' => $eiplanningwks
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
                case 'summary':
                    $this->loadPlanForSummary($id);
                    break;
                case 'strategy':
                    $this->loadPlanForStrategy($id);
                    break;
                case 'edit-summary':
                    $this->loadSummary($id);
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
        $this->activeDay = 'lunes'; // Reset active day
        $this->activeMoment = 'Recibimiento'; // Reset active moment
        $this->resetValidation();
    }

    // Data Loading Methods
    private function loadPlan($id)
    {
        $this->eiplanningwk = Eiplanningwk::findOrFail($id);
        $this->eiplanningwk_id = $this->eiplanningwk->id;
        $grado_id = $this->eiplanningwk->grado_id;
        $this->list_seccion = Seccion::active("true")->where('grado_id', $grado_id)->orderBy('name')->pluck('name', 'id');
    }

    private function loadPlanForSummary($id)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);
        $this->eiplanningwk_id = $eiplanningwk->id;
        $this->list_pevaluacion = $eiplanningwk->getPevaluacionsList($this->profesor_id);
        $this->resetModelSummary();
    }

    private function loadPlanForStrategy($id)
    {
        $eiplanningwk = Eiplanningwk::findOrFail($id);
        $this->eiplanningwk_id = $eiplanningwk->id;
        $this->loadStrategiesForPlan($id);
    }

    // Método actualizado para cargar las estrategias existentes
    private function loadStrategiesForPlan($planId)
    {
        $this->strategies = [];

        foreach ($this->weekDays as $day => $dayName) {
            $this->strategies[$day] = [];

            foreach ($this->moments as $moment => $momentName) {
                // Buscar estrategia existente para este día y momento
                $existingStrategy = Eiplanningwstrategy::where('eiplanningwk_id', $planId)
                    ->where('day_of_week', $day)
                    ->where('momento_rutina_diaria', $moment)
                    ->first();

                if ($existingStrategy) {
                    $this->strategies[$day][$moment] = [
                        'id' => $existingStrategy->id,
                        'estrategia' => $existingStrategy->lunes, // Campo principal para la estrategia
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

    private function loadSummary($id = null)
    {
        if (!$id) {
            $this->resetModelSummary();
            return;
        }

        $summary = Eiplanningwsummary::find($id);

        if ($summary) {
            $this->eiplanningwsummary = $summary;
            $this->eiplanningwsummary_id = $summary->id;
            $eiplanningwk = $summary->eiplanningwk;
            $this->eiplanningwk_id = $eiplanningwk->id;
            $this->list_pevaluacion = $eiplanningwk->getPevaluacionsList($this->profesor_id);
        } else {
            $this->resetModelSummary();
            if ($this->eiplanningwk_id) {
                $eiplanningwk = Eiplanningwk::find($this->eiplanningwk_id);
                if ($eiplanningwk) {
                    $this->list_pevaluacion = $eiplanningwk->getPevaluacionsList($this->profesor_id);
                }
            }
        }
    }

    // CRUD Operations
    public function save()
    {
        $this->eiplanningwk->profesor_id = $this->profesor_id;
        $this->validate([
            'eiplanningwk.profesor_id' => 'required|integer',
            'eiplanningwk.grado_id' => 'required|integer',
            'eiplanningwk.seccion_id' => 'required|integer',
            'eiplanningwk.eiprojectk_id' => 'nullable|integer',
            'eiplanningwk.finicial' => 'required|date',
            'eiplanningwk.ffinal' => 'required|date|after_or_equal:eiplanningwk.finicial',
            'eiplanningwk.tiempo_ejecucion' => 'required|integer|min:1',
            'eiplanningwk.diagnostico' => 'required|string|min:10',
            'eiplanningwk.observacion' => 'nullable|string',
        ]);

        try {
            $this->eiplanningwk->save();
            $this->closeModal();
            $this->showSuccessAlert('Plan semanal guardado exitosamente');
            $this->resetModels();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar el plan: ' . $e->getMessage());
        }
    }

    public function saveSummary()
    {
        if (!$this->eiplanningwk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        $this->eiplanningwsummary->eiplanningwk_id = $this->eiplanningwk_id;
        $this->validate([
            'eiplanningwsummary.eiplanningwk_id' => 'required|integer',
            'eiplanningwsummary.pevaluacion_id' => 'required|integer',
            'eiplanningwsummary.componente' => 'required|string',
            'eiplanningwsummary.objetivo' => 'required|string',
            'eiplanningwsummary.aprendizaje_esperado' => 'required|string',
            'eiplanningwsummary.indicadores' => 'required|string',
            'eiplanningwsummary.linea_investigacion' => 'nullable|string',
            'eiplanningwsummary.enfasis_curriculares' => 'nullable|string',
            'eiplanningwsummary.order' => 'nullable|integer|min:1',
        ]);

        try {
            $this->eiplanningwsummary->order = $this->eiplanningwsummary->order ?: null;
            $this->eiplanningwsummary->save();

            $this->showSuccessAlert('Resumen guardado exitosamente');
            $this->openModal('summary', $this->eiplanningwsummary->eiplanningwk_id);
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar el resumen: ' . $e->getMessage());
        }
    }

    // Método actualizado para guardar todas las estrategias
    public function saveStrategies()
    {
        if (!$this->eiplanningwk_id) {
            $this->showErrorAlert('Error: No se ha seleccionado un plan válido');
            return;
        }

        // Validar que al menos una estrategia tenga datos
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
                    // Solo procesar si tiene datos
                    if (!empty($strategyData['estrategia'])) {

                        if ($strategyData['id']) {
                            // Actualizar estrategia existente
                            $strategy = Eiplanningwstrategy::find($strategyData['id']);
                        } else {
                            // Crear nueva estrategia
                            $strategy = new Eiplanningwstrategy();
                            $strategy->eiplanningwk_id = $this->eiplanningwk_id;
                            $strategy->day_of_week = $day;
                            $strategy->momento_rutina_diaria = $moment;
                        }

                        $strategy->lunes = $strategyData['estrategia']; // Campo principal
                        $strategy->order = $strategyData['order'] ?: null;

                        // Limpiar otros días para mantener compatibilidad
                        $strategy->martes = null;
                        $strategy->miercoles = null;
                        $strategy->jueves = null;
                        $strategy->viernes = null;

                        $strategy->save();

                        // Actualizar el ID en el array
                        $this->strategies[$day][$moment]['id'] = $strategy->id;
                    }
                }
            }

            $this->showSuccessAlert('Estrategias guardadas exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar las estrategias: ' . $e->getMessage());
        }
    }

    // Método para guardar solo la estrategia actual
    public function saveCurrentStrategy()
    {
        if (!$this->eiplanningwk_id) {
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
                // Actualizar estrategia existente
                $strategy = Eiplanningwstrategy::find($currentStrategy['id']);
            } else {
                // Crear nueva estrategia
                $strategy = new Eiplanningwstrategy();
                $strategy->eiplanningwk_id = $this->eiplanningwk_id;
                $strategy->day_of_week = $this->activeDay;
                $strategy->momento_rutina_diaria = $this->activeMoment;
            }

            $strategy->lunes = $currentStrategy['estrategia'];
            $strategy->order = $currentStrategy['order'] ?: null;

            // Limpiar otros días
            $strategy->martes = null;
            $strategy->miercoles = null;
            $strategy->jueves = null;
            $strategy->viernes = null;

            $strategy->save();

            // Actualizar el ID en el array
            $this->strategies[$this->activeDay][$this->activeMoment]['id'] = $strategy->id;

            $this->showSuccessAlert('Estrategia guardada exitosamente');

            // Avanzar automáticamente al siguiente momento
            $this->nextMoment();
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al guardar la estrategia: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $eiplanningwk = Eiplanningwk::findOrFail($id);
            $eiplanningwk->delete();
            $this->showSuccessAlert('Plan eliminado exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar el plan: ' . $e->getMessage());
        }
    }

    public function deleteSummary($id)
    {
        try {
            $eiplanningwsummary = Eiplanningwsummary::findOrFail($id);
            $eiplanningwsummary->delete();
            $this->showSuccessAlert('Resumen eliminado exitosamente');
        } catch (\Exception $e) {
            $this->showErrorAlert('Error al eliminar el resumen: ' . $e->getMessage());
        }
    }

    public function deleteStrategy($day, $moment)
    {
        try {
            if (isset($this->strategies[$day][$moment]['id']) && $this->strategies[$day][$moment]['id']) {
                $strategy = Eiplanningwstrategy::findOrFail($this->strategies[$day][$moment]['id']);
                $strategy->delete();

                // Limpiar el array
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

    // Helper Methods
    private function initializeLists()
    {
        $this->list_comment = Eiplanningwk::COLUMN_COMMENTS;
        $this->list_comment_summary = Eiplanningwsummary::COLUMN_COMMENTS;
        $this->list_comment_strategy = Eiplanningwstrategy::COLUMN_COMMENTS;
        $this->list_moment = Eiplanningwstrategy::LIST_MOMENT;

        $profesor = User::findOrFail(Auth::id())->profesor;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::list_lapso();
        $this->list_eiprojectk = Eiprojectk::getForProfesorIdList($this->profesor_id);
    }

    private function resetModels()
    {
        $this->eiplanningwk = new Eiplanningwk();
        $this->resetModelSummary();
        $this->resetModelStrategies();
    }

    private function resetModelSummary()
    {
        $this->eiplanningwsummary = new Eiplanningwsummary();
        $this->eiplanningwsummary_id = null;
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
}
