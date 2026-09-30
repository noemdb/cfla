<?php

namespace App\Livewire\Planning\Activities;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Asignatura;
use App\Livewire\Concerns\InteractsWithLmsLessons;
use App\Models\User;
use App\Services\Planning\ActivityCopyService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions, InteractsWithLmsLessons;

    public $activity, $activity_id, $pevaluacion_id, $objetivo, $comments, $status;
    public $previewActivity;
    public $supplementActivity = null;
    public $pevaluacion, $observations, $pestudios;
    public $lapso_current;

    // Modal modes
    public $modeIndex = true;
    public $modeObservation = false;
    public $modeComments = false;
    public $modePreview = false;
    public $modeSupplement = false;
    public $modeCopy = false;

    // Filters
    public $pestudio_id, $grado_id, $seccion_id, $lapso_id, $profesor_id;
    public $status_activities, $search, $paginate = 10;
    public $filter_observations = false;
    public $filter_status = '';

    // Select lists
    public $list_pestudio, $list_grado, $list_seccion, $list_lapso;
    public $list_profesors, $list_pensum, $list_comment;

    // Tab data (lapsos collection for the Alpine.js tab navigation)
    public $tabsLapsos;

    // Leader context
    public $leader_id;

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->leader_id = $user->id;

        $this->close();
        $this->modeIndex = true;

        // Cargar listas para filtros
        $this->pestudios = Pestudio::whereHas('grados.pensums.pevaluacions')
            ->where('planning_module', true)
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get();
        $this->list_pestudio = $this->pestudios->pluck('name', 'id');
        // Estado anidado: sin pestudio seleccionado grado y sección desactivados
        $this->list_grado = collect();
        $this->list_seccion = collect();
        $this->list_pensum = collect();
        $this->setProfesorLists();

        $this->list_lapso = Lapso::select('name', 'id')->orderBy('name')->pluck('name', 'id');
        $this->tabsLapsos = Lapso::orderBy('name')->orderBy('id')->get();
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;

        // Lapso actual por defecto
        $this->lapso_current = Lapso::current();
        $this->lapso_id = $this->lapso_current->id ?? null;

        $this->paginate = 10;
    }

    public function render()
    {
        $filters = array_filter([
            'pestudio_id' => $this->pestudio_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'profesor_id' => $this->profesor_id,
            'status_activities' => $this->status_activities,
            'filter_observations' => $this->filter_observations ? true : null,
            'filter_status' => $this->filter_status ?: null,
        ], fn($v) => $v !== null && $v !== '');

        $pevaluacions = $this->getPevaluaciones($filters);

        // Compute the active tab index from the current lapso_id
        $activeTabIndex = 1;
        if ($this->tabsLapsos && $this->lapso_id) {
            $found = $this->tabsLapsos->search(fn($lapso) => $lapso->id == $this->lapso_id);
            if ($found !== false) {
                $activeTabIndex = $found + 1;
            }
        }

        return view('livewire.planning.activities.index-component', [
            'pevaluacions' => $pevaluacions,
            'activeTabIndex' => $activeTabIndex,
        ]);
    }

    // ─── FILTERS CASCADE ────────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_grado = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('order')
                ->pluck('name', 'id');
            $this->list_profesors = Profesor::list_profesors_pestudio($value);
        } else {
            $this->list_grado = collect();
            $this->setProfesorLists();
        }
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
        $this->list_pensum = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_seccion = Seccion::list_seccion_grado($value);
            $this->list_pensum = Pensum::where('grado_id', $value)
                ->whereHas('pevaluacions')
                ->with('asignatura')
                ->get()
                ->mapWithKeys(fn($p) => [$p->id => '[' . ($p->asignatura->code ?? '') . '] ' . ($p->asignatura->name ?? '')]);
        } else {
            $this->list_seccion = collect();
            $this->list_pensum = collect();
        }
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value) { $this->resetPage(); }

    public function updatedLapsoId($value) { $this->resetPage(); }

    public function selectLapso($id)
    {
        $this->lapso_id = $id;
        $this->resetPage();
    }

    public function updatedProfesorId($value) { $this->resetPage(); }

    public function updatedStatusActivities($value) { $this->resetPage(); }

    public function updatedFilterObservations($value) { $this->resetPage(); }

    public function updatedFilterStatus($value) { $this->resetPage(); }

    public function updatedPaginate($value) { $this->resetPage(); }

    public function updatingSearch() { $this->resetPage(); }

    // ─── DATA ───────────────────────────────────────────────────

    protected function getPevaluaciones(array $filters)
    {
        $query = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'grupoEstable',
        ])
        ->with('activities.lmsPublication')
        ->with('activities.lmsSections')
        ->with('activities.supplement')
        ->withCount([
            'activities',
            'activities as activities_revision_count' => fn($q) => $q->where('status', 0),
            'activities as activities_approved_count' => fn($q) => $q->where('status', 1),
            'activities as activities_lessons_count' => fn($q) => $q->whereHas('lmsPublication'),
        ])
        ->whereHas('pensum.pestudio', fn($q) => $q->where('planning_module', true))
        ->whereNull('pevaluacions.deleted_at');

        if (isset($filters['pestudio_id'])) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('id', $filters['pestudio_id']));
        }
        if (isset($filters['grado_id'])) {
            $query->whereHas('seccion.grado', fn($q) => $q->where('id', $filters['grado_id']));
        }
        if (isset($filters['seccion_id'])) {
            $query->where('seccion_id', $filters['seccion_id']);
        }
        if (isset($filters['lapso_id'])) {
            $query->where('lapso_id', $filters['lapso_id']);
        }
        if (isset($filters['profesor_id'])) {
            $query->where('profesor_id', $filters['profesor_id']);
        }
        if (isset($filters['status_activities'])) {
            if ($filters['status_activities'] === 'SI') {
                $query->having('activities_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO') {
                $query->having('activities_count', '=', 0);
            } elseif ($filters['status_activities'] === 'SI_LE') {
                $query->having('activities_lessons_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO_LE') {
                $query->having('activities_lessons_count', '=', 0);
            }
        }
        if (!empty($filters['filter_observations'])) {
            $query->whereNotNull('pevaluacions.observations')
                  ->where('pevaluacions.observations', '!=', '');
        }
        if (!empty($filters['filter_status'])) {
            if ($filters['filter_status'] === 'pending') {
                $query->whereHas('activities', fn($q) => $q->where('status', 0));
            } elseif ($filters['filter_status'] === 'approved') {
                $query->has('activities')
                      ->whereDoesntHave('activities', fn($q) => $q->where('status', 0));
            }
        }

        $query->orderBy('created_at', 'desc');

        if ((int) $this->paginate === 9999) {
            $all = $query->get();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $all,
                $all->count(),
                max($all->count(), 1),
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return $query->paginate($this->paginate);
    }

    public function setProfesorLists($value = null)
    {
        $profesors = Profesor::select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->where('profesors.status_active', true)
            ->orderBy('profesors.lastname');

        if ($value) {
            $profesors->where(function ($q) use ($value) {
                $q->where('profesors.name', 'like', "%{$value}%")
                  ->orWhere('profesors.lastname', 'like', "%{$value}%");
            });
        }

        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    // ─── MODAL OBSERVATIONS ─────────────────────────────────────

    public function createObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id);
        $this->pevaluacion_id = $id;
        $this->observations = $this->pevaluacion->observations;
        $this->close();
        $this->modeObservation = true;
    }

    public function saveObservation()
    {
        $this->validate([
            'observations' => 'required|min:5',
        ], [
            'observations.required' => 'Las observaciones del coordinador son obligatorias.',
            'observations.min' => 'Las observaciones deben tener al menos 5 caracteres.',
        ]);

        $this->pevaluacion->observations = $this->observations;
        $this->pevaluacion->save();
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->close();
        $this->modeIndex = true;

        $this->notification()->success(
            title: 'Observación Guardada',
            description: 'Las observaciones del plan de evaluación se actualizaron correctamente.'
        );
    }

    public function deleteObservation($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $pevaluacion->observations = null;
        $pevaluacion->save();

        $this->notification()->success(
            title: 'Observación Eliminada',
            description: 'Las observaciones del plan de evaluación se eliminaron correctamente.'
        );
    }

    // ─── DELETE ACTIVITY ────────────────────────────────────────

    public function deleteActivity($id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->status) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: 'Solo se pueden eliminar actividades en revisión.'
            );

            return;
        }

        try {
            $activity->delete();
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'No se pudo eliminar',
                description: 'La actividad tiene registros asociados que impiden su eliminación.'
            );

            return;
        }

        $this->close();
        $this->modeIndex = true;

        $this->notification()->success(
            title: 'Actividad Eliminada',
            description: 'La actividad se eliminó correctamente.'
        );
    }

    // ─── MODAL COMMENTS ─────────────────────────────────────────

    public function setModeComment($activitie_id)
    {
        $this->activity = Activity::findOrFail($activitie_id);
        $this->activity_id = $this->activity->id;
        $this->comments = $this->activity->comments;
        $this->status = $this->activity->status;
        $this->close();
        $this->modeComments = true;
    }

    public function saveComent()
    {
        $this->validate([
            'comments' => 'nullable|string|max:65535',
            'status' => 'required|boolean',
        ]);

        $this->activity->comments = $this->comments;
        $this->activity->status = $this->status;
        $this->activity->save();
        $this->activity = null;
        $this->activity_id = null;
        $this->close();
        $this->modeIndex = true;

        $this->notification()->success(
            title: 'Comentario Guardado',
            description: 'El comentario y estado de la actividad se actualizaron correctamente.'
        );
    }

    // ─── MODAL PREVIEW ─────────────────────────────────────────

    public function showPreview($activitie_id)
    {
        $this->previewActivity = Activity::with('achievements')->findOrFail($activitie_id);
        $this->close();
        $this->modePreview = true;
    }

    // ─── MODAL SUPLEMENTO (solo lectura) ──────────────────────────

    public function openSupplementModal($activityId)
    {
        $this->supplementActivity = Activity::with('supplement')->findOrFail($activityId);
        $this->close();
        $this->modeSupplement = true;
    }

    public function closeSupplementModal()
    {
        $this->supplementActivity = null;
        $this->close();
    }

    // ─── WIZARD: COPIAR ACTIVIDADES (activity:copy) ────────────

    public $copyStep = 1;

    public $copySource = '2';

    public $copyFromId = '';

    public $copyFromSearch = '';

    public $copyFromOptions = [];

    public $copyPestudioId = '';

    public $copyPestudioOptions = [];

    public $copyGradoId = '';

    public $copyGradoOptions = [];

    public $copyToId = '';

    public $copyToSearch = '';

    public $copyToOptions = [];

    public $copyToPestudioId = '';

    public $copyToPestudioOptions = [];

    public $copyToGradoId = '';

    public $copyToGradoOptions = [];

    public $copyPreview = null;

    public $copyPreviewError = '';

    public $copyConfirm = false;

    public $copyResult = null;

    public $copyRunning = false;

    public function openCopyWizard(?int $preselectToId = null)
    {
        $this->close();
        $this->modeCopy = true;
        $this->copyStep = 1;
        $this->copyPreview = null;
        $this->copyPreviewError = '';
        $this->copyConfirm = false;
        $this->copyResult = null;
        $this->copyRunning = false;
        if ($preselectToId) {
            $this->copyToId = (string) $preselectToId;
        }
        $service = app(ActivityCopyService::class);
        $this->refreshCopyFilterLists($service);
        $this->refreshCopyFromOptions($service);
        $this->refreshCopyToFilterLists($service);
        $this->refreshCopyToOptions($service);
    }

    public function updatedCopySource($value)
    {
        $this->copyFromId = '';
        $this->copyFromSearch = '';
        $this->copyPestudioId = '';
        $this->copyGradoId = '';
        $this->refreshCopyFilterLists(app(ActivityCopyService::class));
        $this->refreshCopyFromOptions(app(ActivityCopyService::class));
    }

    public function updatedCopyPestudioId($value)
    {
        $this->copyGradoId = '';
        $this->copyFromId = '';
        $service = app(ActivityCopyService::class);
        $this->refreshCopyFilterLists($service);
        $this->refreshCopyFromOptions($service);
    }

    public function updatedCopyGradoId($value)
    {
        $this->copyFromId = '';
        $this->refreshCopyFromOptions(app(ActivityCopyService::class));
    }

    public function updatedCopyFromSearch($value)
    {
        $this->refreshCopyFromOptions(app(ActivityCopyService::class));
    }

    public function updatedCopyToPestudioId($value)
    {
        $this->copyToGradoId = '';
        $this->copyToId = '';
        $service = app(ActivityCopyService::class);
        $this->refreshCopyToFilterLists($service);
        $this->refreshCopyToOptions($service);
    }

    public function updatedCopyToGradoId($value)
    {
        $this->copyToId = '';
        $this->refreshCopyToOptions(app(ActivityCopyService::class));
    }

    public function updatedCopyToSearch($value)
    {
        $this->refreshCopyToOptions(app(ActivityCopyService::class));
    }

    protected function refreshCopyToFilterLists(ActivityCopyService $service)
    {
        // El destino siempre vive en la conexión actual.
        $connection = $service->targetConnection();

        try {
            $this->copyToPestudioOptions = $service->listPestudios($connection)
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'label' => trim(($p->code ? "[{$p->code}] " : '').$p->name),
                    'description' => self::copyStatusDescription($p->status_active),
                ])
                ->all();
            $this->copyToGradoOptions = $service->listGrados($connection, $this->copyToPestudioId ?: null)
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'label' => $g->name,
                    'description' => trim(($g->pestudio?->name ?? '').' · '.(in_array($g->status_active, ['true', true, 1, '1'], true) ? 'Activo' : 'Inactivo'), ' ·'),
                ])
                ->all();
        } catch (\Throwable) {
            $this->copyToPestudioOptions = [];
            $this->copyToGradoOptions = [];
        }
    }

    protected function refreshCopyToOptions(ActivityCopyService $service)
    {
        try {
            $this->copyToOptions = $service->searchPevaluacions(
                $service->targetConnection(),
                (string) $this->copyToSearch,
                30,
                $this->copyToPestudioId ?: null,
                $this->copyToGradoId ?: null
            )
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'label' => '#'.$p->id.' · '.($p->pensum?->asignatura?->name ?? 'Sin asignatura'),
                    'description' => trim(($p->seccion?->grado?->name ?? '').' · Sec. '.($p->seccion?->name ?? '—').' · '.($p->profesor?->lastname ?? '').' '.($p->profesor?->name ?? '').' · '.($p->lapso?->name ?? '—').' · '.$p->activities_count.' acts.'),
                ])
                ->all();
        } catch (\Throwable) {
            $this->copyToOptions = [];
        }
    }

    /**
     * Descripción de estado para los selects del wizard de copia.
     * Marca textual ●/○ porque x-select.option no admite template propio;
     * los inactivos se siguen pudiendo elegir (copiar desde planes viejos
     * es un caso de uso válido), solo se diferencian visualmente.
     */
    protected static function copyStatusDescription(mixed $status): string
    {
        // status_active es string: 'true' = activo, 'false' = inactivo.
        // Comparación estricta: 'false' == true daría verdadero con ==.
        $active = in_array($status, ['true', true, 1, '1'], true);

        return $active ? '● Activo' : '○ Inactivo · solo como fuente';
    }

    protected function refreshCopyFilterLists(ActivityCopyService $service)
    {        $connection = $service->resolveSourceConnection((string) $this->copySource);

        if ($connection === null) {
            $this->copyPestudioOptions = [];
            $this->copyGradoOptions = [];

            return;
        }

        try {
            $this->copyPestudioOptions = $service->listPestudios($connection)
                ->map(fn ($p) => [
                    'id' => $p->id,
                    // Código + nombre: desambigua planes homónimos
                    // (p. ej. dos "EDUCACION MEDIA GENERAL" con distinto code).
                    'label' => trim(($p->code ? "[{$p->code}] " : '').$p->name),
                    'description' => self::copyStatusDescription($p->status_active),
                ])
                ->all();
            $this->copyGradoOptions = $service->listGrados($connection, $this->copyPestudioId ?: null)
                ->map(fn ($g) => [
                    'id' => $g->id,
                    'label' => $g->name,
                    'description' => trim(($g->pestudio?->name ?? '').' · '.(in_array($g->status_active, ['true', true, 1, '1'], true) ? 'Activo' : 'Inactivo'), ' ·'),
                ])
                ->all();
        } catch (\Throwable) {
            $this->copyPestudioOptions = [];
            $this->copyGradoOptions = [];
        }
    }

    protected function refreshCopyFromOptions(ActivityCopyService $service)
    {
        $connection = $service->resolveSourceConnection((string) $this->copySource);

        if ($connection === null) {
            $this->copyFromOptions = [];

            return;
        }

        try {
            $this->copyFromOptions = $service->searchPevaluacions(
                $connection,
                (string) $this->copyFromSearch,
                30,
                $this->copyPestudioId ?: null,
                $this->copyGradoId ?: null
            )
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'label' => '#'.$p->id.' · '.($p->pensum?->asignatura?->name ?? 'Sin asignatura'),
                    'description' => trim(($p->seccion?->grado?->name ?? '').' · Sec. '.($p->seccion?->name ?? '—').' · '.($p->profesor?->lastname ?? '').' '.($p->profesor?->name ?? '').' · '.($p->lapso?->name ?? '—').' · '.$p->activities_count.' acts.'),
                ])
                ->all();
        } catch (\Throwable) {
            $this->copyFromOptions = [];
        }
    }

    public function closeCopyWizard()
    {
        $this->copyPreview = null;
        $this->copyPreviewError = '';
        $this->copyConfirm = false;
        $this->copyResult = null;
        $this->copyRunning = false;
        $this->copyStep = 1;
        $this->close();
        $this->modeIndex = true;
    }

    public function copyGoToStep(int $step)
    {
        $step = max(1, min(4, $step));

        // Pasos 1 y 2: navegación libre. Paso 3: solo con vista previa
        // cargada. Paso 4: solo con resultado de copia.
        if ($step <= 2) {
            $this->copyStep = $step;
        } elseif ($step === 3 && $this->copyPreview !== null) {
            $this->copyStep = $step;
        } elseif ($step === 4 && $this->copyResult !== null) {
            $this->copyStep = $step;
        }
    }

    public function loadCopyPreview(ActivityCopyService $service)
    {
        $this->validate([
            'copySource' => 'required|in:1,2',
            'copyFromId' => 'required|integer|min:1',
            'copyToId' => 'required|integer|min:1|different:copyFromId',
        ], [
            'copyFromId.required' => 'Indica el ID de la Pevaluación origen.',
            'copyToId.required' => 'Indica el ID de la Pevaluación destino.',
            'copyToId.different' => 'El destino debe ser distinto al origen.',
        ]);

        $this->copyPreviewError = '';
        $this->copyPreview = null;
        $this->copyConfirm = false;
        $this->copyResult = null;

        try {
            $preview = $service->preview((int) $this->copyFromId, (int) $this->copyToId, (string) $this->copySource);

            $this->copyPreview = [
                'from_id' => $preview['from']->id,
                'from_name' => $preview['from']->full_name,
                'to_id' => $preview['to']->id,
                'to_name' => $preview['to']->full_name,
                'source' => $preview['source'],
                'sourceConnection' => $preview['sourceConnection'],
                'targetConnection' => $preview['targetConnection'],
                'toCopy' => $preview['toCopy']->map(fn ($a) => [
                    'id' => $a->id,
                    'topic' => $a->topic,
                    'thematic' => $a->thematic,
                    'finicial' => $a->finicial,
                    'ffinal' => $a->ffinal,
                    'achievements' => $a->achievements->count(),
                ])->values()->all(),
                'skipped' => $preview['skipped']->map(fn ($a) => [
                    'id' => $a->id,
                    'topic' => $a->topic,
                    'thematic' => $a->thematic,
                    'finicial' => $a->finicial,
                    'ffinal' => $a->ffinal,
                    'achievements' => $a->achievements->count(),
                ])->values()->all(),
                'achievementsToCopy' => $preview['achievementsToCopy'],
            ];
            $this->copyStep = 3;
        } catch (\InvalidArgumentException $e) {
            $this->copyPreviewError = $e->getMessage();
        } catch (\Throwable $e) {
            $this->copyPreviewError = 'No se pudo cargar la vista previa: '.$e->getMessage();
        }
    }

    public function runCopy(ActivityCopyService $service)
    {
        $this->validate([
            'copySource' => 'required|in:1,2',
            'copyFromId' => 'required|integer|min:1',
            'copyToId' => 'required|integer|min:1|different:copyFromId',
            'copyConfirm' => 'accepted',
        ], [
            'copyConfirm.accepted' => 'Debes confirmar que entiendes que no hay deshacer automático.',
        ]);

        if ($this->copyPreview === null) {
            $this->copyPreviewError = 'Primero genera la vista previa (paso 3).';

            return;
        }

        $this->copyRunning = true;

        try {
            $result = $service->copy((int) $this->copyFromId, (int) $this->copyToId, (string) $this->copySource);

            $this->copyResult = [
                'copiedActivities' => $result['copiedActivities'],
                'copiedAchievements' => $result['copiedAchievements'],
                'skippedActivities' => $result['skippedActivities'],
                'to_name' => $result['to']->full_name,
                'details' => $result['details'],
            ];
            $this->copyStep = 4;

            $this->notification()->success(
                title: 'Actividades copiadas',
                description: "{$result['copiedActivities']} actividades y {$result['copiedAchievements']} indicadores copiados. {$result['skippedActivities']} omitidas."
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'No se pudo copiar',
                description: $e->getMessage()
            );
        } finally {
            $this->copyRunning = false;
        }
    }

    // ─── MODE MANAGEMENT ────────────────────────────────────────

    public function close()
    {
        $this->modeIndex = false;
        $this->modeObservation = false;
        $this->modeComments = false;
        $this->modePreview = false;
        $this->modeSupplement = false;
        $this->modeCopy = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
