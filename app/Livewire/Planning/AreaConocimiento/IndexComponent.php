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

    // Paso 2 — vista y listado
    public $viewMode = 'available';   // 'available' | 'assigned'  (Disponibles / Adscritas)
    public $groupBy = 'materia';      // 'materia' | 'none'
    public $visibleCount = 24;        // "cargar más"

    const MATERIA_ORDER = ['sky', 'emerald', 'amber', 'indigo', 'orange', 'purple', 'rose', 'teal', 'slate'];

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';
    public $filter_peducativo = '';
    public $paginate = 20;

    // Confirm delete
    public $confirmDeleteId = null;
    public $confirmDeleteCampoId = null;

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
            ->withCount('campo_conocimientos');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('code_sm', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter_pestudio) {
            $query->where('pestudio_id', $this->filter_pestudio);
        }

        if ($this->filter_peducativo) {
            $query->where('peducativo_id', $this->filter_peducativo);
        }

        $area_conocimientos = $query->orderBy('order')
            ->orderBy('name')
            ->paginate($this->paginate);

        return view('livewire.planning.area-conocimiento.index-component', [
            'area_conocimientos' => $area_conocimientos,
        ]);
    }

    // ─── PAGINATION & FILTERS ────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterPeducativo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    // ─── CRUD AreaConocimiento ───────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->area_id = null;
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $area = AreaConocimiento::findOrFail($id);
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

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
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

    // ─── CAMPO CONOCIMIENTO (modal 95%) ──────────────────────────

    public function openCampoManager($areaId)
    {
        $area = AreaConocimiento::withCount('campo_conocimientos')->findOrFail($areaId);
        $this->campoAreaId = $area->id;
        $this->campoAreaName = $area->name;
        $this->resetCampoForm();
        $this->resetWizard();
        // Pre-filtrar por el plan de estudio del área para facilitar la selección.
        if ($area->pestudio_id) {
            $this->wizardFilterPestudio = $area->pestudio_id;
            $this->updatedWizardFilterPestudio($area->pestudio_id);
        }
        $this->modeCampo = true;
    }

    public function closeCampoManager()
    {
        $this->modeCampo = false;
        $this->campoAreaId = null;
        $this->campoAreaName = '';
        $this->resetCampoForm();
        $this->resetWizard();
    }

    // ─── WIZARD 2-STEP (CampoConocimiento) ───────────────────────

    public function resetWizard()
    {
        $this->wizardStep = 1;
        $this->wizardFilterPestudio = '';
        $this->wizardFilterGrado = '';
        $this->wizardSearch = '';
        $this->selectedSubjects = [];
        $this->viewMode = 'available';
        $this->groupBy = 'materia';
        $this->visibleCount = 24;
        // Restore full grados list
        $this->gradosList = Grado::where('status_active', 'true')
            ->orderBy('code_sm')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();
    }

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
    public function getAvailableSubjectsProperty()
    {
        if (! $this->campoAreaId) return collect();

        // IDs of subjects already assigned to this area
        $assignedIds = CampoConocimiento::where('area_conocimiento_id', $this->campoAreaId)
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
     * Computed: asignaturas disponibles agrupadas por materia (color) y
     * limitadas por visibleCount ("cargar más"). Si groupBy === 'none',
     * devuelve un único grupo sin encabezado.
     */
    public function getAvailableGroupsProperty()
    {
        $subjects = $this->availableSubjects->take($this->visibleCount);

        if ($this->groupBy === 'none') {
            return collect([[
                'key'   => null,
                'label' => null,
                'items' => $subjects,
            ]]);
        }

        return $subjects
            ->groupBy(fn ($a) => Asignatura::colorKey($a->name))
            ->map(fn ($items, $key) => [
                'key'   => $key,
                'label' => $this->materiaLabel($key),
                'items' => $items,
            ])
            ->sortBy(fn ($group) => $this->materiaOrderIndex($group['key']))
            ->values();
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

    protected function materiaOrderIndex(?string $key): int
    {
        $index = array_search($key, self::MATERIA_ORDER, true);
        return $index === false ? 99 : $index;
    }

    protected function materiaLabel(string $key): string
    {
        return match ($key) {
            'sky'     => 'Matemáticas',
            'emerald' => 'Lengua y Castellano',
            'amber'   => 'Ciencias Naturales',
            'indigo'  => 'Inglés / Idiomas',
            'orange'  => 'Física / Deporte',
            'purple'  => 'Arte / Música',
            'rose'    => 'Formación / Religión',
            'teal'    => 'Tecnología',
            'slate'   => 'Otras',
            default   => 'Otras',
        };
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
        $areaPestudioId = AreaConocimiento::where('id', $this->campoAreaId)->value('pestudio_id');

        $resolved = CampoConocimiento::resolvePensumId($asignaturaId, $areaPestudioId);
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
     * Select all currently available subjects (con su pensum por defecto).
     */
    public function selectAllAvailable()
    {
        $this->selectedSubjects = $this->availableSubjects->mapWithKeys(function ($asig) {
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
        $this->campoEditingId = null;
    }

    public function editCampo($id)
    {
        $campo = CampoConocimiento::findOrFail($id);
        $this->campoEditingId = $campo->id;
        $this->campo_asignatura_id = $campo->asignatura_id;
        $this->campo_observations = $campo->observations;
    }

    public function saveCampo()
    {
        $this->validate($this->rulesCampo);

        $data = [
            'area_conocimiento_id' => $this->campoAreaId,
            'asignatura_id'        => $this->campo_asignatura_id,
            'pensum_id'            => CampoConocimiento::resolvePensumId(
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
        return CampoConocimiento::with('asignatura')
            ->where('area_conocimiento_id', $this->campoAreaId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
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
