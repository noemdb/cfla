<?php

namespace App\Livewire\Planning\AreaConocimiento;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Peducativo;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // Modal modes
    public $modeForm = false;
    public $modeCampo = false;      // 95% dialog for campo_conocimientos

    // Editing flag
    public $isEditing = false;
    public $area_id;

    // ─── Form AreaConocimiento ────────────────────────────────────
    public $peducativo_id, $pestudio_id, $leader_id;
    public $name, $code, $code_sm, $description, $observations;
    public $order = 1;
    public $enable_academic_index = 'true';

    // ─── CampoConocimiento ────────────────────────────────────────
    public $campoAreaId = null;          // area_conocimiento_id for current campo management
    public $campoAreaName = '';          // display name in dialog header
    public $campo_asignatura_id;
    public $campo_observations;
    public $campoEditingId = null;       // editing a specific campo

    // Select lists
    public $pestudios = [];
    public $peducativos = [];
    public $usuarios = [];
    public $asignaturasList = [];        // for campo asignatura selection
    public $gradosList = [];             // for wizard grado filter

    // Wizard step
    public $wizardStep = 1;
    public $wizardFilterPestudio = '';
    public $wizardFilterGrado = '';
    public $wizardSearch = '';
    // Selección del wizard: mapa [asignatura_id => pensum_id] para poder elegir
    // el pensum (grado/plan) a adscribir cuando una asignatura tiene varios.
    public $selectedSubjects = [];
    // Wizard de creación (modal modeForm): paso 1 = datos del área,
    // paso 2 = seleccionar campoConocimiento.
    public $creatingArea = false;
    public $createStep = 1;
    // Mostrar solo pensums NO asociados a ninguna área de conocimiento.
    public $wizardOnlyUnassigned = false;

    // Paso 2 — vista y listado
    public $viewMode = 'available';   // 'available' | 'assigned'  (Disponibles / Adscritas)
    public $visibleCount = 24;        // "cargar más"

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';
    public $filter_peducativo = '';
    public $paginate = 20;
    // Orden y filtros extra del listado principal
    public $sortBy = 'order';            // 'order' | 'name' | 'count_desc' | 'count_asc'
    public $filter_leader = '';          // '' | 'none' | user_id
    public $filter_adscripcion = '';     // '' | 'with' | 'empty' | 'pending'
    // Buscador y pensum en la vista Adscritas / edición de adscripción
    public $campoSearch = '';
    public $campo_pensum_id = null;
    // Memoria de filtros del wizard por área (open → close → open)
    public $wizardMemory = [];

    // Confirm delete (área, vía x-dialog WireUI)
    public $confirmDeleteId = null;
    public $confirmDeleteName = '';
    public $confirmDeleteCampoId = null;

    // Confirm clone (área + campo_conocimientos) — vía x-dialog WireUI
    public $confirmCloneId = null;
    public $confirmCloneName = '';

    protected $rules = [
        'pestudio_id'   => 'required|integer|exists:pestudios,id',
        'name'           => 'required|string|max:255',
        'code'           => 'required|string|max:20',
        'code_sm'        => 'required|string|max:10',
        'description'    => 'nullable|string|max:500',
        'observations'   => 'nullable|string|max:500',
        'order'          => 'required|integer|min:1|max:50',
        'enable_academic_index' => 'required|in:true,false',
        'peducativo_id'  => 'nullable|integer|exists:peducativos,id',
        'leader_id'      => 'nullable|integer|exists:users,id',
    ];

    protected $rulesCampo = [
        'campo_asignatura_id' => 'required|integer|exists:asignaturas,id',
        'campo_observations'   => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->peducativos = Peducativo::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->usuarios = User::orderBy('username')
            ->get()
            ->pluck('username', 'id')
            ->toArray();

        // Flat list for individual assignment (fallback)
        $this->asignaturasList = Asignatura::orderBy('code')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        // Grados activos for wizard filter
        $this->gradosList = Grado::where('status_active', 'true')
            ->orderBy('code_sm')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function render()
    {
        $query = AreaConocimiento::with(['pestudio', 'peducativo', 'leader'])
            ->withCount([
                'campo_conocimientos',
                'campo_conocimientos as campos_sin_pensum_count' => fn ($q) => $q->whereNull('pensum_id'),
            ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('code_sm', 'like', "%{$this->search}%")
                  ->orWhereHas('leader', function ($l) {
                      $l->where('username', 'like', "%{$this->search}%")
                        ->orWhereHas('profile', function ($p) {
                            $p->where('firstname', 'like', "%{$this->search}%")
                              ->orWhere('lastname', 'like', "%{$this->search}%");
                        });
                  });
            });
        }

        if ($this->filter_pestudio) {
            $query->where('pestudio_id', $this->filter_pestudio);
        }

        if ($this->filter_peducativo) {
            $query->where('peducativo_id', $this->filter_peducativo);
        }

        if ($this->filter_leader === 'none') {
            $query->whereNull('leader_id');
        } elseif ($this->filter_leader !== '') {
            $query->where('leader_id', $this->filter_leader);
        }

        if ($this->filter_adscripcion === 'empty') {
            $query->whereDoesntHave('campo_conocimientos');
        } elseif ($this->filter_adscripcion === 'with') {
            $query->whereHas('campo_conocimientos');
        } elseif ($this->filter_adscripcion === 'pending') {
            $query->whereHas('campo_conocimientos', fn ($q) => $q->whereNull('pensum_id'));
        }

        match ($this->sortBy) {
            'name' => $query->orderBy('name'),
            'count_desc' => $query->orderByDesc('campo_conocimientos_count')->orderBy('order'),
            'count_asc' => $query->orderBy('campo_conocimientos_count')->orderBy('order'),
            default => $query->orderBy('order')->orderBy('name'),
        };

        $area_conocimientos = $query->paginate($this->paginate);

        $lideres = User::whereIn('id', AreaConocimiento::whereNotNull('leader_id')->distinct()->pluck('leader_id'))
            ->orderBy('username')
            ->pluck('username', 'id');

        return view('livewire.planning.area-conocimiento.index-component', [
            'area_conocimientos' => $area_conocimientos,
            'lideres' => $lideres,
        ]);
    }

    // ─── PAGINATION & FILTERS ────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterLeader() { $this->resetPage(); }
    public function updatingFilterAdscripcion() { $this->resetPage(); }
    public function updatingSortBy() { $this->resetPage(); }
    public function updatingFilterPeducativo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    // ─── CRUD AreaConocimiento ───────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->area_id = null;
        // Wizard de creación: paso 1 (datos) + paso 2 (campoConocimiento).
        $this->creatingArea = true;
        $this->createStep = 1;
        $this->resetWizardState();
        $this->modeForm = true;
    }

    public function closeForm()
    {
        $this->modeForm = false;
        $this->creatingArea = false;
        $this->createStep = 1;
        $this->resetForm();
        $this->resetWizardState();
    }

    /**
     * Avanza del paso 1 (datos) al paso 2 (adscribir campoConocimiento):
     * valida el área y pre-filtra por su plan de estudio.
     */
    public function nextCreateStep()
    {
        $this->validate();

        $this->createStep = 2;
        $this->wizardStep = 1;
        $this->viewMode = 'available';
        $this->visibleCount = 24;
        $this->selectedSubjects = [];
        if ($this->pestudio_id) {
            $this->wizardFilterPestudio = $this->pestudio_id;
            $this->updatedWizardFilterPestudio($this->pestudio_id);
        }
    }

    public function prevCreateStep()
    {
        $this->createStep = 1;
    }

    /**
     * Guarda el área nueva junto con los campoConocimiento seleccionados
     * (mapa asignatura_id => pensum_id).
     */
    public function saveNewArea()
    {
        $this->validate();

        if (empty($this->selectedSubjects)) {
            $this->notification()->error(
                title: 'Sin selección',
                description: 'Selecciona al menos una asignatura para adscribir al área nueva.'
            );
            return;
        }

        $area = null;
        \Illuminate\Support\Facades\DB::transaction(function () use (&$area) {
            $area = AreaConocimiento::create([
                'peducativo_id' => $this->peducativo_id ?: null,
                'pestudio_id'   => $this->pestudio_id,
                'leader_id'     => $this->leader_id ?: null,
                'name'          => $this->name,
                'code'          => $this->code,
                'code_sm'       => $this->code_sm,
                'description'   => $this->description,
                'observations'  => $this->observations,
                'order'         => $this->order,
                'enable_academic_index' => $this->enable_academic_index ?: 'false',
            ]);

            $order = 0;
            foreach ($this->selectedSubjects as $asignaturaId => $pensumId) {
                CampoConocimiento::create([
                    'area_conocimiento_id' => $area->id,
                    'asignatura_id'        => (int) $asignaturaId,
                    'pensum_id'            => $pensumId ?: null,
                    'order'                => ++$order,
                ]);
            }
        });

        $count = count($this->selectedSubjects);
        $this->closeForm();
        $this->notification()->success(
            title: 'Área Creada',
            description: "El área «{$area->name}» se creó con {$count} asignatura(s) adscrita(s)."
        );
    }

    /**
     * Activa/desactiva el filtro "solo pensums sin área".
     */
    public function toggleOnlyUnassigned()
    {
        $this->wizardOnlyUnassigned = ! $this->wizardOnlyUnassigned;
        $this->visibleCount = 24;
    }

    /**
     * Resetea el estado del wizard de selección (usado al abrir/cerrar flujos).
     */
    protected function resetWizardState()
    {
        $this->wizardStep = 1;
        $this->wizardFilterPestudio = '';
        $this->wizardFilterGrado = '';
        $this->wizardSearch = '';
        $this->selectedSubjects = [];
        $this->viewMode = 'available';
        $this->wizardOnlyUnassigned = false;
        $this->visibleCount = 24;
        $this->gradosList = Grado::where('status_active', 'true')
            ->orderBy('code_sm')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function edit($id)
    {
        $area = AreaConocimiento::findOrFail($id);
        $this->creatingArea = false;
        $this->createStep = 1;
        $this->area_id = $area->id;
        $this->peducativo_id = $area->peducativo_id;
        $this->pestudio_id = $area->pestudio_id;
        $this->leader_id = $area->leader_id;
        $this->name = $area->name;
        $this->code = $area->code;
        $this->code_sm = $area->code_sm;
        $this->description = $area->description;
        $this->observations = $area->observations;
        $this->order = $area->order;
        $this->enable_academic_index = $area->enable_academic_index;
        $this->isEditing = true;
        $this->modeForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'peducativo_id' => $this->peducativo_id ?: null,
            'pestudio_id'   => $this->pestudio_id,
            'leader_id'     => $this->leader_id ?: null,
            'name'          => $this->name,
            'code'          => $this->code,
            'code_sm'       => $this->code_sm,
            'description'   => $this->description,
            'observations'  => $this->observations,
            'order'         => $this->order,
            'enable_academic_index' => $this->enable_academic_index ?: 'false',
        ];

        if ($this->isEditing) {
            $area = AreaConocimiento::findOrFail($this->area_id);
            $area->update($data);
            $this->notification()->success(
                title: 'Área Actualizada',
                description: 'El área de conocimiento se actualizó correctamente.'
            );
        } else {
            AreaConocimiento::create($data);
            $this->notification()->success(
                title: 'Área Creada',
                description: 'El área de conocimiento se creó correctamente.'
            );
        }

        $this->modeForm = false;
        $this->resetForm();
    }

    // ─── DELETE ──────────────────────────────────────────────────

    /**
     * Abre el diálogo de confirmación (x-dialog WireUI). El cierre visual lo
     * hace el navegador con `close()`; aquí solo se fija el estado.
     */
    public function confirmDelete($id)
    {
        $area = AreaConocimiento::findOrFail($id);
        $this->confirmDeleteId = $area->id;
        $this->confirmDeleteName = (string) $area->name;

        $this->dialog()->id('area-delete')->show([
            'icon' => 'warning',
            'close' => false,
        ]);
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
        $this->confirmDeleteName = '';
    }

    public function destroy()
    {
        $area = AreaConocimiento::withCount('campo_conocimientos')->findOrFail($this->confirmDeleteId);

        if ($area->campo_conocimientos_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El área tiene {$area->campo_conocimientos_count} asignatura(s) adscrita(s). Elimínelas primero."
            );
            $this->cancelDelete();
            return;
        }

        $area->delete();
        $this->cancelDelete();
        $this->notification()->success(
            title: 'Área Eliminada',
            description: 'El área de conocimiento se eliminó correctamente.'
        );
    }

    // ─── CLONE (área + campo_conocimientos) ───────────────────────

    /**
     * Abre el diálogo de confirmación (x-dialog WireUI). El cierre visual lo
     * hace el navegador con `close()`; aquí solo se fija el estado.
     */
    public function confirmClone($id)
    {
        $area = AreaConocimiento::findOrFail($id);
        $this->confirmCloneId = $area->id;
        $this->confirmCloneName = (string) $area->name;

        $this->dialog()->id('area-clone')->show([
            'icon' => 'question',
            'close' => false,
        ]);
    }

    public function cancelClone()
    {
        $this->confirmCloneId = null;
        $this->confirmCloneName = '';
    }

    public function cloneArea()
    {
        $source = AreaConocimiento::with('campo_conocimientos')->findOrFail($this->confirmCloneId);

        $newArea = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($source, &$newArea) {
            $newArea = AreaConocimiento::create([
                'peducativo_id' => $source->peducativo_id,
                'pestudio_id'   => $source->pestudio_id,
                'leader_id'     => $source->leader_id,
                'name'          => \Illuminate\Support\Str::limit($source->name.' (Copia)', 255, ''),
                'code'          => $this->uniqueCloneCode('code', (string) $source->code, 20),
                'code_sm'       => $this->uniqueCloneCode('code_sm', (string) $source->code_sm, 10),
                'description'   => $source->description,
                'observations'  => $source->observations,
                'order'         => $source->order,
                'enable_academic_index' => $source->enable_academic_index,
            ]);

            foreach ($source->campo_conocimientos as $campo) {
                CampoConocimiento::create([
                    'area_conocimiento_id' => $newArea->id,
                    'asignatura_id'        => $campo->asignatura_id,
                    'pensum_id'            => $campo->pensum_id,
                    'observations'         => $campo->observations,
                    'order'                => $campo->order,
                ]);
            }
        });

        $copied = $source->campo_conocimientos->count();
        $this->cancelClone();
        $this->notification()->success(
            title: 'Área Clonada',
            description: "Se creó «{$newArea->name}» con {$copied} asignatura(s) adscrita(s)."
        );
    }

    /**
     * Genera un código único para el clon dentro del límite de la columna
     * (code: 20, code_sm: 10), con sufijo -C1, -C2, …
     */
    protected function uniqueCloneCode(string $column, string $base, int $max): string
    {
        $base = trim($base) !== '' ? trim($base) : 'AREA';
        $suffix = 0;
        do {
            $suffix++;
            $tag = '-C'.$suffix;
            $code = \Illuminate\Support\Str::limit($base, $max - strlen($tag), '').$tag;
        } while (AreaConocimiento::where($column, $code)->exists());

        return $code;
    }

    // ─── CAMPO CONOCIMIENTO (modal 95%) ──────────────────────────

    public function openCampoManager($areaId)
    {
        $area = AreaConocimiento::withCount('campo_conocimientos')->findOrFail($areaId);
        $this->campoAreaId = $area->id;
        $this->campoAreaName = $area->name;
        $this->resetCampoForm();
        // Restaura la memoria de filtros de esta área si existe; si no,
        // pre-filtra por el plan de estudio del área.
        if (isset($this->wizardMemory[$area->id])) {
            $mem = $this->wizardMemory[$area->id];
            $this->wizardStep = 1;
            $this->viewMode = 'available';
            $this->visibleCount = 24;
            $this->selectedSubjects = [];
            $this->wizardFilterPestudio = $mem['pestudio'] ?? '';
            $this->updatedWizardFilterPestudio($this->wizardFilterPestudio);
            $this->wizardFilterGrado = $mem['grado'] ?? '';
            $this->wizardSearch = $mem['search'] ?? '';
            $this->wizardOnlyUnassigned = (bool) ($mem['unassigned'] ?? false);
        } else {
            $this->resetWizardState();
            if ($area->pestudio_id) {
                $this->wizardFilterPestudio = $area->pestudio_id;
                $this->updatedWizardFilterPestudio($area->pestudio_id);
            }
        }
        $this->modeCampo = true;
    }

    public function closeCampoManager()
    {
        // Guarda la memoria de filtros por área antes de limpiar.
        if ($this->campoAreaId) {
            $this->wizardMemory[$this->campoAreaId] = [
                'pestudio' => $this->wizardFilterPestudio,
                'grado' => $this->wizardFilterGrado,
                'search' => $this->wizardSearch,
                'unassigned' => $this->wizardOnlyUnassigned,
            ];
        }
        $this->modeCampo = false;
        $this->campoAreaId = null;
        $this->campoAreaName = '';
        $this->resetCampoForm();
        $this->resetWizardState();
    }

    // ─── WIZARD 2-STEP (CampoConocimiento) ───────────────────────

    /**
     * When wizard pestudio changes, reload grados list filtered by that pestudio.
     */
    public function updatedWizardFilterPestudio($value)
    {
        $this->wizardFilterGrado = ''; // reset grado selection
        $this->visibleCount = 24;
        $query = Grado::where('status_active', 'true');
        if ($value) {
            $query->where('pestudio_id', $value);
        }
        $this->gradosList = $query->orderBy('code_sm')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function updatedWizardFilterGrado($value)
    {
        $this->visibleCount = 24;
    }

    public function updatedWizardSearch($value)
    {
        $this->visibleCount = 24;
    }

    public function updatedWizardOnlyUnassigned($value)
    {
        $this->visibleCount = 24;
    }

    public function nextStepWizard()
    {
        // No validation needed — filters are optional; just advance
        $this->wizardStep = 2;
        $this->selectedSubjects = [];
    }

    public function prevStepWizard()
    {
        $this->wizardStep = 1;
        $this->wizardSearch = '';
        $this->selectedSubjects = [];
    }

    /**
     * Computed: available subjects (not yet assigned to this area),
     * optionally filtered by grado (via pensum) and search text.
     */
    /**
     * IDs de pensums ya asociados a alguna área (campo_conocimientos.pensum_id).
     */
    protected function associatedPensumIds(): array
    {
        return CampoConocimiento::whereNotNull('pensum_id')
            ->pluck('pensum_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    public function getAvailableSubjectsProperty()
    {
        // En modo creación aún no hay área: todo está disponible.
        if (! $this->campoAreaId && ! $this->creatingArea) return collect();

        // IDs of subjects already assigned to this area
        $assignedIds = $this->creatingArea
            ? []
            : CampoConocimiento::where('area_conocimiento_id', $this->campoAreaId)
                ->pluck('asignatura_id')
                ->toArray();

        $query = Asignatura::whereNotIn('id', $assignedIds);

        // Filter by plan de estudio (directo o vía pensum, que es la asociación
        // real asignatura ↔ pestudio en este modelo de datos).
        if ($this->wizardFilterPestudio) {
            $query->where(function ($q) {
                $q->where('pestudio_id', $this->wizardFilterPestudio)
                  ->orWhereHas('pensums', function ($p) {
                      $p->where('pestudio_id', $this->wizardFilterPestudio)
                        ->where('pensums.status_active', true)
                        ->whereHas('grado', fn ($g) => $g->where('grados.status_active', 'true'));
                  });
            });
        }

        // Filter by grado via pensum relationship (solo pensums activos con grado activo)
        if ($this->wizardFilterGrado) {
            $query->whereHas('pensums', function ($q) {
                $q->where('grado_id', $this->wizardFilterGrado)
                  ->where('pensums.status_active', true)
                  ->whereHas('grado', fn ($g) => $g->where('grados.status_active', 'true'));
            });
        }

        // Search by name or code
        if ($this->wizardSearch) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->wizardSearch}%")
                  ->orWhere('code', 'like', "%{$this->wizardSearch}%");
            });
        }

        // Solo asignaturas con al menos un pensum activo cuyo grado esté activo
        $query->whereHas('pensums', function ($q) {
            $q->where('pensums.status_active', true)
              ->whereHas('grado', fn ($g) => $g->where('grados.status_active', 'true'));
        });

        // Opción "solo pensums sin área": la asignatura debe tener al menos un
        // pensum (activo, grado activo) no asociado a ninguna área.
        if ($this->wizardOnlyUnassigned) {
            $query->whereHas('pensums', function ($q) {
                $q->where('pensums.status_active', true)
                  ->whereHas('grado', fn ($g) => $g->where('grados.status_active', 'true'))
                  ->whereNotIn('pensums.id', CampoConocimiento::whereNotNull('pensum_id')->select('pensum_id'));
            });
        }

        // Precarga de pensums activos con grado activo (con grado, pestudio y secciones)
        $query->with([
            'pensums' => function ($q) {
                $q->where('pensums.status_active', true)
                  ->whereHas('grado', fn ($g) => $g->where('grados.status_active', 'true'))
                  ->orderBy('pensums.grado_id')
                  ->with([
                      'pestudio',
                      'grado' => function ($g) {
                          $g->with([
                              'pestudio',
                              'seccions' => function ($s) {
                                  $s->where('seccions.status_active', 'true')
                                    ->orderBy('seccions.name');
                              },
                          ]);
                      },
                  ]);
            },
        ]);

        return $query->orderBy('name')
            ->get();
    }

    /**
     * Restantes sin mostrar (para el botón "cargar más").
     */
    public function getRemainingSubjectsCountProperty(): int
    {
        return max(0, $this->availableSubjects->count() - $this->visibleCount);
    }

    public function loadMore()
    {
        $this->visibleCount += 24;
    }

    public function materiaColorClass(?string $key): string
    {
        return match ($key) {
            'sky'     => 'bg-sky-400',
            'emerald' => 'bg-emerald-400',
            'amber'   => 'bg-amber-400',
            'indigo'  => 'bg-indigo-400',
            'purple'  => 'bg-purple-400',
            'orange'  => 'bg-orange-400',
            'rose'    => 'bg-rose-400',
            'teal'    => 'bg-teal-400',
            default   => 'bg-slate-400',
        };
    }

    /**
     * Toggle a subject in the selection map. Al seleccionar se guarda un
     * pensum por defecto (resuelto o el primero de la lista); el usuario
     * puede cambiarlo con selectPensum().
     */
    public function toggleSubject($id)
    {
        $id = (int) $id;

        if (array_key_exists($id, $this->selectedSubjects)) {
            unset($this->selectedSubjects[$id]);
            return;
        }

        $this->selectedSubjects[$id] = $this->defaultPensumId($id);
    }

    /**
     * Cambia el pensum adscrito de una asignatura ya seleccionada.
     */
    public function selectPensum($asignaturaId, $pensumId)
    {
        $asignaturaId = (int) $asignaturaId;
        $pensumId = $pensumId ? (int) $pensumId : null;

        if (! array_key_exists($asignaturaId, $this->selectedSubjects)) {
            return;
        }

        $this->selectedSubjects[$asignaturaId] = $pensumId;
    }

    /**
     * Pensum por defecto para una asignatura: el resuelto por la cadena
     * pevaluacion → pensum → asignatura; si no, el primer pensum activo
     * (priorizando el pestudio del área).
     */
    protected function defaultPensumId(int $asignaturaId): ?int
    {
        $areaPestudioId = $this->creatingArea
            ? ($this->pestudio_id ?: null)
            : AreaConocimiento::where('id', $this->campoAreaId)->value('pestudio_id');

        $resolved = CampoConocimiento::resolvePensumId($asignaturaId, $areaPestudioId);

        // Con "solo sin área": preferir un pensum no asociado a ninguna área.
        if ($this->wizardOnlyUnassigned) {
            $associated = $this->associatedPensumIds();
            if ($resolved !== null && ! in_array($resolved, $associated, true)) {
                return $resolved;
            }

            $unassigned = \App\Models\app\Academy\Pensum::where('asignatura_id', $asignaturaId)
                ->where('status_active', true)
                ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
                ->whereNotIn('id', $associated);
            if ($areaPestudioId) {
                $byPestudio = (clone $unassigned)->where('pestudio_id', $areaPestudioId)->orderBy('grado_id')->value('id');
                if ($byPestudio) {
                    return (int) $byPestudio;
                }
            }

            return $unassigned->orderBy('grado_id')->value('id') ?: null;
        }

        if ($resolved !== null) {
            return $resolved;
        }

        $pensums = \App\Models\app\Academy\Pensum::where('asignatura_id', $asignaturaId)
            ->where('status_active', true)
            ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'));
        if ($areaPestudioId) {
            $pensums->where('pestudio_id', $areaPestudioId);
        }

        return $pensums->orderBy('grado_id')->value('id') ?: null;
    }

    /**
     * Select all currently VISIBLE available subjects (con su pensum por
     * defecto). Solo las cargadas en pantalla ("Cargar más" para ver el resto).
     */
    public function selectAllAvailable()
    {
        $this->selectedSubjects = $this->availableSubjects->take($this->visibleCount)->mapWithKeys(function ($asig) {
            return [(int) $asig->id => $this->defaultPensumId((int) $asig->id)];
        })->toArray();
    }

    /**
     * Deselect all subjects.
     */
    public function deselectAll()
    {
        $this->selectedSubjects = [];
    }

    /**
     * Batch-assign selected subjects as CampoConocimiento records, guardando
     * en cada una el pensum elegido.
     */
    public function assignSelectedSubjects()
    {
        // En modo creación el guardado lo hace saveNewArea(); aquí solo manager.
        if ($this->creatingArea || ! $this->campoAreaId) {
            return;
        }

        if (empty($this->selectedSubjects)) {
            $this->notification()->error(
                title: 'Sin selección',
                description: 'Selecciona al menos una asignatura para adscribir.'
            );
            return;
        }

        // Asignaturas ya adscritas al área (para evitar duplicados).
        $assignedIds = CampoConocimiento::where('area_conocimiento_id', $this->campoAreaId)
            ->pluck('asignatura_id')
            ->toArray();

        $count = 0;

        // Orden inicial: continúa después del último registro del área.
        $nextOrder = (int) CampoConocimiento::where('area_conocimiento_id', $this->campoAreaId)
            ->max('order');

        foreach ($this->selectedSubjects as $asignaturaId => $pensumId) {
            $asignaturaId = (int) $asignaturaId;

            if (in_array($asignaturaId, $assignedIds, true)) {
                continue;
            }

            CampoConocimiento::create([
                'area_conocimiento_id' => $this->campoAreaId,
                'asignatura_id'        => $asignaturaId,
                'pensum_id'            => $pensumId ?: null,
                'order'                => ++$nextOrder,
            ]);
            $count++;
        }

        if ($count > 0) {
            $this->notification()->success(
                title: 'Asignaturas Adscritas',
                description: "{$count} asignatura(s) se adscribieron al área correctamente."
            );
        } else {
            $this->notification()->info(
                title: 'Sin cambios',
                description: 'Las asignaturas seleccionadas ya estaban adscritas.'
            );
        }

        $this->selectedSubjects = [];
    }

    public function resetCampoForm()
    {
        $this->campo_asignatura_id = null;
        $this->campo_observations = null;
        $this->campo_pensum_id = null;
        $this->campoEditingId = null;
    }

    public function editCampo($id)
    {
        $campo = CampoConocimiento::findOrFail($id);
        $this->campoEditingId = $campo->id;
        $this->campo_asignatura_id = $campo->asignatura_id;
        $this->campo_pensum_id = $campo->pensum_id;
        $this->campo_observations = $campo->observations;
    }

    /**
     * Pensums activos (grado activo) de la asignatura en edición, para el
     * selector de pensum del formulario de adscripción.
     */
    public function getCampoEditPensumsProperty()
    {
        if (! $this->campo_asignatura_id) return collect();

        return \App\Models\app\Academy\Pensum::where('asignatura_id', $this->campo_asignatura_id)
            ->where('status_active', true)
            ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
            ->with(['grado', 'pestudio'])
            ->orderBy('grado_id')
            ->get();
    }

    public function saveCampo()
    {
        $this->validate($this->rulesCampo);

        $data = [
            'area_conocimiento_id' => $this->campoAreaId,
            'asignatura_id'        => $this->campo_asignatura_id,
            'pensum_id'            => $this->campo_pensum_id ?: CampoConocimiento::resolvePensumId(
                (int) $this->campo_asignatura_id,
                AreaConocimiento::where('id', $this->campoAreaId)->value('pestudio_id'),
            ),
            'observations'         => $this->campo_observations,
        ];

        if ($this->campoEditingId) {
            $campo = CampoConocimiento::findOrFail($this->campoEditingId);
            $campo->update($data);
            $this->notification()->success(
                title: 'Adscripción Actualizada',
                description: 'La asignatura se actualizó en el área de conocimiento.'
            );
        } else {
            CampoConocimiento::create($data);
            $this->notification()->success(
                title: 'Asignatura Adscrita',
                description: 'La asignatura se adscribió al área de conocimiento.'
            );
        }

        $this->resetCampoForm();
    }

    public function confirmDeleteCampo($id)
    {
        $this->confirmDeleteCampoId = $id;
    }

    public function cancelDeleteCampo()
    {
        $this->confirmDeleteCampoId = null;
    }

    public function destroyCampo()
    {
        $campo = CampoConocimiento::findOrFail($this->confirmDeleteCampoId);
        $campo->delete();
        $this->cancelDeleteCampo();
        $this->notification()->success(
            title: 'Adscripción Eliminada',
            description: 'La asignatura se desadscribió del área de conocimiento.'
        );
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'peducativo_id', 'pestudio_id', 'leader_id',
            'name', 'code', 'code_sm', 'description', 'observations',
        ]);
        $this->order = 1;
        $this->enable_academic_index = 'true';
    }

    public function getCampoConocimientosProperty()
    {
        if (! $this->campoAreaId) return collect();
        $query = CampoConocimiento::with(['asignatura', 'pensum.grado', 'pensum.pestudio'])
            ->where('area_conocimiento_id', $this->campoAreaId);

        if ($this->campoSearch) {
            $s = $this->campoSearch;
            $query->where(function ($q) use ($s) {
                $q->whereHas('asignatura', fn ($a) => $a
                        ->where('name', 'like', "%{$s}%")
                        ->orWhere('code', 'like', "%{$s}%"))
                  ->orWhereHas('pensum.grado', fn ($g) => $g->where('name', 'like', "%{$s}%"));
            });
        }

        return $query->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Total de pensums (activos, grado activo) sin área en todo el sistema,
     * para el contador del filtro "solo pensums sin área".
     */
    public function getUnassignedPensumCountProperty(): int
    {
        return \App\Models\app\Academy\Pensum::where('status_active', true)
            ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
            ->whereNotIn('id', CampoConocimiento::whereNotNull('pensum_id')->select('pensum_id'))
            ->count();
    }

    /**
     * Persiste el nuevo orden de las adscripciones (drag & drop).
     */
    public function reorderCampo($orderedIds)
    {
        foreach (array_values($orderedIds) as $index => $id) {
            CampoConocimiento::where('id', (int) $id)
                ->where('area_conocimiento_id', $this->campoAreaId)
                ->update(['order' => $index + 1]);
        }
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
