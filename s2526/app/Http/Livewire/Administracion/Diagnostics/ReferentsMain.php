<?php

namespace App\Http\Livewire\Administracion\Diagnostics;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagIndicator;
use App\Models\app\Pescolar\Pensum;
use Illuminate\Support\Facades\Auth;

class ReferentsMain extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    // ============ PROPIEDADES GENERALES ============
    public $search = '';
    public $filterActive = 'all';
    public $selectedReferentId = null;
    public $selectedCompetencyId = null;
    // public $detailFilterPestudioId = ''; // Eliminado por refactorización
    public $detailFilterGradoId = ''; // Nuevo filtro de grado para el modal
    public $detailFilterPensumId = ''; // Nuevo filtro de pensum para el modal

    // ============ PROPIEDADES PARA REFERENTES ============
    public $referent_id;
    public $referent_pestudio_id; // Nuevo campo para versionado
    public $referent_name;
    public $referent_code;
    public $referent_version;
    public $referent_description;
    public $referent_active = true;

    // ============ PROPIEDADES PARA COMPETENCIAS ============
    public $competency_id;
    public $competency_referent_id;
    public $competency_pensum_id;
    public $competency_name;
    public $competency_description;

    // Filtros para la vista de competencias
    public $compFilterGrado = '';
    public $compFilterPensum = '';
    public $compFilterGradosList = [];
    public $compFilterPensumsList = [];

    // ============ PROPIEDADES PARA INDICADORES ============
    public $indicator_id;
    public $indicator_competency_id;
    public $indicator_code;
    public $indicator_description;
    public $indicator_expected_level = 3;

    // ============ MODALES ============
    public $isReferentModalOpen = false;
    public $isCompetencyModalOpen = false;
    public $isIndicatorModalOpen = false;
    public $isReferentDetailOpen = false;
    public $isCompetencyDetailOpen = false;

    // ============ ESTADOS ============
    public $viewMode = 'referents'; // 'referents', 'competencies', 'indicators'

    // ============ LISTAS DESPLEGABLES ============
    //public $referents = [];
    public $pensums = [];
    // public $competencies = []; // Eliminado para evitar conflictos con paginación

    public $currentReferent = null;
    public $currentCompetency = null;

    // ============ INICIALIZACIÓN ============
    public function mount()
    {
        $this->loadPensums();
        $this->loadReferentPestudios(); // Cargar pestudios para el select de referentes
        // $this->loadReferents(); // Legacy
    }

    public $referentPestudios = []; // Lista para el select

    private function loadReferentPestudios()
    {
        // Cargar pestudios activos para asociarlos a los referentes
        $this->referentPestudios = \App\Models\app\Pescolar\Pestudio::where('status_active', true)
            ->orderBy('name')
            ->get();
    }

    private function loadPensums()
    {
        $this->pensums = Pensum::where('status_active', true)
            ->with(['grado', 'asignatura'])
            ->get()
            ->map(function ($pensum) {
                // Agregar full_name como propiedad para ordenar
                $pensum->sort_key = strtolower($pensum->full_name);
                return $pensum;
            })
            ->sortBy('sort_key')
            ->values();
    }

    // private function loadReferents() {} // Legacy
    // private function loadCompetencies($referentId = null) {} // Legacy eliminado

    // ============ RENDER PRINCIPAL ============
    public function render()
    {
        // Consulta base para referentes
        // Consulta base para referentes
        $query = DiagReferent::with('pestudio')
            ->withCount(['competencies' => function ($q) {
                $q->withCount('indicators');
            }]);

        // Filtros para referentes
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterActive !== 'all') {
            $isActive = $this->filterActive === 'active'; // Esto devuelve true/false
            $query->where('active', $isActive); // Ahora compara booleano con booleano
        }

        // Siempre paginar los referentes
        $referents = $query->orderBy('name')
            ->paginate(10);

        // Inicializar variables para competencias e indicadores
        // Inicializar variables para competencias e indicadores
        $competencies = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $indicators = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        // Solo paginar competencias si estamos en ese modo
        // Solo paginar competencias si estamos en ese modo
        if ($this->viewMode === 'competencies' && $this->selectedReferentId) {
            $compQuery = DiagCompetency::where('referent_id', $this->selectedReferentId)
                ->with(['pensum.grado', 'indicators'])
                ->withCount('indicators');

            if ($this->compFilterGrado) {
                $compQuery->whereHas('pensum', function ($q) {
                    $q->where('grado_id', $this->compFilterGrado);
                });
            }

            if ($this->compFilterPensum) {
                $compQuery->where('pensum_id', $this->compFilterPensum);
            }

            $competencies = $compQuery->orderBy('name')->paginate(10);
        }

        // Solo paginar indicadores si estamos en ese modo
        if ($this->viewMode === 'indicators' && $this->selectedCompetencyId) {
            $indicators = DiagIndicator::where('competency_id', $this->selectedCompetencyId)
                ->with('competency')
                ->orderBy('code')
                ->paginate(10);
        }

        if (!$this->currentReferent && $this->selectedReferentId) {
            $this->currentReferent = DiagReferent::find($this->selectedReferentId);
        }

        if (!$this->currentCompetency && $this->selectedCompetencyId) {
            $this->currentCompetency = DiagCompetency::find($this->selectedCompetencyId);
        }

        return view('livewire.administracion.diagnostics.referents-main', compact(
            'referents',
            'competencies',
            'indicators'
        ));
    }

    // En el componente, agrega este método:
    public function isPaginated($variable)
    {
        return $variable instanceof \Illuminate\Pagination\LengthAwarePaginator;
    }

    // ============ MÉTODOS PARA REFERENTES ============
    public function createReferent()
    {
        $this->resetReferentForm();
        $this->isReferentModalOpen = true;
    }

    public function editReferent($id)
    {
        $referent = DiagReferent::findOrFail($id);

        $this->referent_id = $referent->id;
        $this->referent_pestudio_id = $referent->pestudio_id;
        $this->referent_name = $referent->name;
        $this->referent_code = $referent->code;
        $this->referent_version = $referent->version;
        $this->referent_description = $referent->description;
        $this->referent_active = $referent->active;

        $this->isReferentModalOpen = true;
    }

    public function showReferentDetail($id)
    {
        $this->selectedReferentId = $id;
        $this->currentReferent = DiagReferent::find($id); // Guardar el objeto
        $this->viewMode = 'competencies';
        $this->currentReferent = DiagReferent::find($id); // Guardar el objeto
        $this->viewMode = 'competencies';
        $this->loadCompetencyLists($id); // Cargar listas para filtros
        $this->resetPage(); // Resetear paginación al entrar
        // $this->loadCompetencies($id); // Eliminado
    }

    public function backToReferents()
    {
        $this->viewMode = 'referents';
        $this->selectedReferentId = null;
        $this->selectedCompetencyId = null;
        $this->currentReferent = null;
        $this->currentCompetency = null;
    }

    public function openDetailModal($id)
    {
        // Cargar relaciones profundas: Competencias -> Pensum -> (Grado, Pestudio)
        // También cargar indicadores
        $this->currentReferent = DiagReferent::with([
            'pestudio', // Cargar el plan de estudio del referente
            'competencies.pensum.grado',
            'competencies.pensum.pestudio', // Mantener por si acaso, aunque el referente ahora manda
            'competencies.indicators'
        ])->find($id);

        // $this->detailFilterPestudioId = ''; // Eliminado
        $this->detailFilterGradoId = '';    // Resetear filtro de grado al abrir
        $this->detailFilterPensumId = '';   // Resetear filtro de pensum al abrir
        $this->isReferentDetailOpen = true;
        $this->dispatchBrowserEvent('open-referent-detail-modal');
    }

    // Propiedad computada para obtener las competencias filtradas para el modal
    public function getDetailCompetenciesProperty()
    {
        if (!$this->currentReferent) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
        }

        $query = DiagCompetency::query()
            ->where('referent_id', $this->currentReferent->id)
            ->with(['pensum.grado', 'indicators']); // Eager load necesario

        if ($this->detailFilterPensumId) {
            $query->where('pensum_id', $this->detailFilterPensumId);
        }

        if ($this->detailFilterGradoId) {
            $query->whereHas('pensum', function ($q) {
                $q->where('grado_id', $this->detailFilterGradoId);
            });
        }

        return $query->orderBy('name')->paginate(5, ['*'], 'detailPage');
    }

    public function updatingDetailFilterGradoId()
    {
        $this->resetPage('detailPage');
    }

    public function updatingDetailFilterPensumId()
    {
        $this->resetPage('detailPage');
    }

    // Propiedad getDetailPestudiosProperty ELIMINADA

    // Propiedad computada para obtener la lista de Grados disponibles en las competencias filtradas (o totales)
    public function getDetailGradosProperty()
    {
        if (!$this->currentReferent) return collect();

        // Obtenemos los grados de las competencias actuales 
        // Ya no filtramos por pestudio porque el referente es único para un pestudio

        $competencies = $this->currentReferent->competencies;

        // No aplicamos filtros aquí para mostrar todos los grados disponibles

        return $competencies
            ->pluck('pensum.grado')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    // Propiedad para obtener la lista de Pensums (Asignaturas) disponibles en las competencias
    public function getDetailPensumsProperty()
    {
        if (!$this->currentReferent) return collect();

        $competencies = $this->currentReferent->competencies;

        // Si hay filtro de grado, mostrar solo asignaturas de ese grado
        if ($this->detailFilterGradoId) {
            $competencies = $competencies->filter(function ($competency) {
                return optional(optional($competency->pensum)->grado)->id == $this->detailFilterGradoId;
            });
        }

        // Si hay filtro de pensum y queremos que el dropdown solo muestre el seleccionado?
        // No, el dropdown debe mostrar todos los posibles para permitir cambiar.
        // Pero quizas filtrados por grado si hay grado seleccionado.

        return $competencies
            ->pluck('pensum')
            ->filter()
            ->unique('id')
            ->sortBy('fullname') // Asumiendo que pensum tiene fullname o name
            ->values();
    }

    public function loadCompetencyLists($referentId)
    {
        // Cargar listas para los filtros de competencias basados en el referido activo
        // Obtenemos todos los pensums asociados a las competencias de este referente
        $referent = DiagReferent::with('competencies.pensum.grado')->find($referentId);

        if ($referent) {
            $pensums = $referent->competencies->pluck('pensum')->filter()->unique('id');

            $this->compFilterPensumsList = $pensums->sortBy('fullname')->map(function ($p) {
                return [
                    'id' => $p->id,
                    'fullname' => $p->fullname
                ];
            })->values()->all();

            $this->compFilterGradosList = $pensums->pluck('grado')->filter()->unique('id')->sortBy('name')->map(function ($g) {
                return [
                    'id' => $g->id,
                    'name' => $g->name
                ];
            })->values()->all();
        } else {
            $this->compFilterPensumsList = [];
            $this->compFilterGradosList = [];
        }

        $this->compFilterGrado = '';
        $this->compFilterPensum = '';
    }

    public function updatingCompFilterGrado()
    {
        $this->resetPage();
    }

    public function updatingCompFilterPensum()
    {
        $this->resetPage();
    }

    public function resetCompFilters()
    {
        $this->compFilterGrado = '';
        $this->compFilterPensum = '';
        $this->resetPage();
    }

    public function closeDetailModal()
    {
        $this->isReferentDetailOpen = false;
        $this->dispatchBrowserEvent('close-referent-detail-modal');
        // Optional: Reset currentReferent if you don't want to keep it in memory, 
        // but keeping it might be fine for re-opening.
        // $this->currentReferent = null; 
    }

    public function saveReferent()
    {
        $this->validate([
            'referent_pestudio_id' => 'required|exists:pestudios,id',
            'referent_name' => 'required|string|max:255',
            'referent_code' => 'required|string|max:100|unique:diag_referents,code,' . $this->referent_id . ',id,version,' . $this->referent_version,
            'referent_version' => 'required|string|max:20',
            'referent_description' => 'nullable|string',
        ]);

        // REGLA DE VERSIONADO: Solo 1 referente activo por Pestudio
        if ($this->referent_active) {
            $existingActive = DiagReferent::where('pestudio_id', $this->referent_pestudio_id)
                ->where('active', true)
                ->where('id', '!=', $this->referent_id)
                ->first();

            if ($existingActive) {
                // Opción A: Bloquear y avisar
                // Opción A: Bloquear y avisar
                $this->addError('referent_active', "Ya existe un referente ACTIVO para este Plan de Estudio ({$existingActive->version}). Desactívelo primero.");
                return;

                // Opción B: Desactivar automáticamente el anterior (Descomentar si se prefiere)
                // $existingActive->update(['active' => false]);
            }
        }

        DiagReferent::updateOrCreate(
            ['id' => $this->referent_id],
            [
                'pestudio_id' => $this->referent_pestudio_id,
                'name' => $this->referent_name,
                'code' => $this->referent_code,
                'version' => $this->referent_version,
                'description' => $this->referent_description,
                'active' => $this->referent_active,
            ]
        );

        // session()->flash('message', '✅ Referente guardado exitosamente.');

        // SweetAlert de éxito
        $this->dispatchBrowserEvent('swal', [
            'title' => $this->referent_id ? '¡Actualizado!' : '¡Creado Exitosamente!',
            'text' => $this->referent_id
                ? 'El referente normativo ha sido actualizado correctamente.'
                : 'El referente normativo ha sido creado exitosamente.',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);


        $this->closeReferentModal();
        $this->loadReferents();
    }

    public function toggleReferentActive($id)
    {
        $referent = DiagReferent::findOrFail($id);
        $newStatus = !$referent->active;

        // VERIFICACIÓN: Si vamos a activar, verificar que no haya otro activo para el mismo pestudio
        if ($newStatus) {
            $existingActive = DiagReferent::where('pestudio_id', $referent->pestudio_id)
                ->where('active', true)
                ->where('id', '!=', $referent->id)
                ->first();

            if ($existingActive) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'No se puede activar',
                    'text' => "Ya existe un referente ACTIVO para este Plan de Estudio ({$existingActive->version}). Desactívelo primero.",
                    'icon' => 'error',
                    'confirmButtonText' => 'Entendido'
                ]);
                return;
            }
        }

        $referent->active = $newStatus;
        $referent->save();

        // Mensaje apropiado
        $message = $referent->active
            ? '✅ Referente activado correctamente.'
            : '⚠️ Referente desactivado. Ya no estará disponible para nuevos diagnósticos.';

        // session()->flash('message', $message);

        // SweetAlert con cambio de estado
        $this->dispatchBrowserEvent('swal', [
            'title' => $newStatus ? '¡Activado!' : '¡Desactivado!',
            'text' => $newStatus
                ? 'El referente ha sido activado y estará disponible para nuevos diagnósticos.'
                : 'El referente ha sido desactivado. No estará disponible para nuevos diagnósticos.',
            'icon' => $newStatus ? 'success' : 'warning',
            'confirmButtonText' => 'Aceptar'
        ]);

        // ✅ Opcional: Si el filtro está activo y cambiamos el estado, 
        // el item podría desaparecer de la vista actual
        if ($this->filterActive === 'active' && !$referent->active) {
            // El referente se desactivó pero estamos viendo solo activos
            // Podemos mantenerlo en la vista o refrescar
        } elseif ($this->filterActive === 'inactive' && $referent->active) {
            // El referente se activó pero estamos viendo solo inactivos
        }
    }

    public function deleteReferent($id)
    {
        $referent = DiagReferent::findOrFail($id);

        if ($referent->competencies()->count() > 0) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error al eliminar referente.',
                'text' => 'No se puede eliminar un referente que tiene competencias asociadas.',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);

            return;
        }

        // SweetAlert de confirmación para eliminar
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => '¿Eliminar Referente?',
            'text' => 'Esta acción eliminará permanentemente el referente: ' . $referent->name,
            'icon' => 'warning',
            'id' => $id,
            'method' => 'confirmDeleteReferent'
        ]);
    }

    // Método para confirmar eliminación después del SweetAlert
    public function confirmDeleteReferent($id)
    {
        $referent = DiagReferent::findOrFail($id);
        $referentName = $referent->name;
        $referent->delete();

        // SweetAlert de éxito después de eliminar
        $this->dispatchBrowserEvent('swal', [
            'title' => '🗑️ Eliminado',
            'text' => "El referente '{$referentName}' ha sido eliminado correctamente.",
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);
    }

    public function closeReferentModal()
    {
        $this->isReferentModalOpen = false;
        $this->resetReferentForm();
    }

    private function resetReferentForm()
    {
        $this->referent_id = null;
        $this->referent_pestudio_id = null;
        $this->referent_name = '';
        $this->referent_code = '';
        $this->referent_version = '1.0';
        $this->referent_description = '';
        $this->referent_active = true;
    }

    // ============ MÉTODOS PARA COMPETENCIAS ============
    public function createCompetency()
    {
        $this->resetCompetencyForm();
        $this->competency_referent_id = $this->selectedReferentId;
        $this->isCompetencyModalOpen = true;
    }

    public function editCompetency($id)
    {
        $competency = DiagCompetency::findOrFail($id);

        $this->competency_id = $competency->id;
        $this->competency_referent_id = $competency->referent_id;
        $this->competency_pensum_id = $competency->pensum_id;
        $this->competency_name = $competency->name;
        $this->competency_description = $competency->description;

        $this->isCompetencyModalOpen = true;
    }

    public function showCompetencyDetail($id)
    {
        $this->selectedCompetencyId = $id;
        $this->currentCompetency = DiagCompetency::find($id); // Guardar el objeto
        $this->viewMode = 'indicators';
    }

    public function backToCompetencies()
    {
        $this->viewMode = 'competencies';
        $this->selectedCompetencyId = null;
        $this->currentCompetency = null;
    }

    public function saveCompetency()
    {
        $this->validate([
            'competency_referent_id' => 'required|exists:diag_referents,id',
            'competency_pensum_id' => 'nullable|exists:pensums,id',
            'competency_name' => 'required|string|max:255',
            'competency_description' => 'nullable|string',
        ]);

        DiagCompetency::updateOrCreate(
            ['id' => $this->competency_id],
            [
                'referent_id' => $this->competency_referent_id,
                'pensum_id' => $this->competency_pensum_id,
                'name' => $this->competency_name,
                'description' => $this->competency_description,
            ]
        );

        // session()->flash('message', '✅ Competencia guardada exitosamente.');

        // SweetAlert para competencia
        $this->dispatchBrowserEvent('swal', [
            'title' => $this->competency_id ? '¡Actualizada!' : '¡Creada Exitosamente!',
            'text' => $this->competency_id
                ? 'La competencia ha sido actualizada correctamente.'
                : 'La competencia ha sido creada exitosamente.',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);

        $this->closeCompetencyModal();
        // $this->loadCompetencies($this->competency_referent_id); // Eliminado
    }

    public function deleteCompetency($id)
    {
        $competency = DiagCompetency::findOrFail($id);

        if ($competency->indicators()->count() > 0) {
            session()->flash('error', '❌ No se puede eliminar una competencia que tiene indicadores asociados.');
            return;
        }

        $competency->delete();
        // session()->flash('message', '🗑️ Competencia eliminada.');

        // SweetAlert de confirmación para competencia
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => '¿Eliminar Competencia?',
            'text' => 'Esta acción eliminará permanentemente la competencia: ' . $competency->name,
            'icon' => 'warning',
            'id' => $id,
            'method' => 'confirmDeleteCompetency'
        ]);
    }

    public function confirmDeleteCompetency($id)
    {
        $competency = DiagCompetency::findOrFail($id);
        $competencyName = $competency->name;
        $competency->delete();

        $this->dispatchBrowserEvent('swal', [
            'title' => '🗑️ Eliminada',
            'text' => "La competencia '{$competencyName}' ha sido eliminada correctamente.",
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);
    }

    public function closeCompetencyModal()
    {
        $this->isCompetencyModalOpen = false;
        $this->resetCompetencyForm();
    }

    private function resetCompetencyForm()
    {
        $this->competency_id = null;
        $this->competency_referent_id = null;
        $this->competency_pensum_id = null;
        $this->competency_name = '';
        $this->competency_description = '';
    }

    // ============ MÉTODOS PARA INDICADORES ============
    public function createIndicator()
    {
        $this->resetIndicatorForm();
        $this->indicator_competency_id = $this->selectedCompetencyId;
        $this->isIndicatorModalOpen = true;
    }

    public function editIndicator($id)
    {
        $indicator = DiagIndicator::findOrFail($id);

        $this->indicator_id = $indicator->id;
        $this->indicator_competency_id = $indicator->competency_id;
        $this->indicator_code = $indicator->code;
        $this->indicator_description = $indicator->description;
        $this->indicator_expected_level = $indicator->expected_level;

        $this->isIndicatorModalOpen = true;
    }

    public function saveIndicator()
    {
        $this->validate([
            'indicator_competency_id' => 'required|exists:diag_competencies,id',
            'indicator_code' => 'required|string|max:50|unique:diag_indicators,code,' . $this->indicator_id . ',id,competency_id,' . $this->indicator_competency_id,
            'indicator_description' => 'required|string',
            'indicator_expected_level' => 'required|in:1,2,3,4',
        ]);

        DiagIndicator::updateOrCreate(
            ['id' => $this->indicator_id],
            [
                'competency_id' => $this->indicator_competency_id,
                'code' => $this->indicator_code,
                'description' => $this->indicator_description,
                'expected_level' => $this->indicator_expected_level,
            ]
        );

        // session()->flash('message', '✅ Indicador guardado exitosamente.');

        // SweetAlert para indicador
        $this->dispatchBrowserEvent('swal', [
            'title' => $this->indicator_id ? '¡Actualizado!' : '¡Creado Exitosamente!',
            'text' => $this->indicator_id
                ? 'El indicador ha sido actualizado correctamente.'
                : 'El indicador ha sido creado exitosamente.',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);

        $this->closeIndicatorModal();
    }

    public function deleteIndicator($id)
    {
        $indicator = DiagIndicator::findOrFail($id);
        $indicator->delete();

        // session()->flash('message', '🗑️ Indicador eliminado.');

        // SweetAlert de confirmación directa para indicador
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => '¿Eliminar Indicador?',
            'text' => 'Esta acción eliminará permanentemente el indicador: ' . $indicator->code,
            'icon' => 'warning',
            'id' => $id,
            'method' => 'confirmDeleteIndicator'
        ]);
    }

    public function confirmDeleteIndicator($id)
    {
        $indicator = DiagIndicator::findOrFail($id);
        $indicatorCode = $indicator->code;
        $indicator->delete();

        $this->dispatchBrowserEvent('swal', [
            'title' => '🗑️ Eliminado',
            'text' => "El indicador '{$indicatorCode}' ha sido eliminado correctamente.",
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ]);
    }

    public function closeIndicatorModal()
    {
        $this->isIndicatorModalOpen = false;
        $this->resetIndicatorForm();
    }

    private function resetIndicatorForm()
    {
        $this->indicator_id = null;
        $this->indicator_competency_id = null;
        $this->indicator_code = '';
        $this->indicator_description = '';
        $this->indicator_expected_level = 3;
    }

    // ============ MÉTODOS AUXILIARES ============
    public function getLevelLabel($level)
    {
        $labels = [
            1 => ['label' => 'Insuficiente', 'class' => 'danger'],
            2 => ['label' => 'En desarrollo', 'class' => 'warning'],
            3 => ['label' => 'Satisfactorio', 'class' => 'info'],
            4 => ['label' => 'Avanzado', 'class' => 'success'],
        ];

        return $labels[$level] ?? ['label' => 'No definido', 'class' => 'secondary'];
    }

    public function showValidationError($field, $message)
    {
        $this->dispatchBrowserEvent('swal:error', [
            'title' => 'Error de validación',
            'text' => "Campo '{$field}': {$message}",
        ]);
    }
}
