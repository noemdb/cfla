<?php

namespace App\Livewire\Leadership;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Services\Leadership\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class DiagnosticQuestionReview extends Component
{
    use WireUiActions, WithPagination;

    public string $search = '';

    public string $filterAreaId = '';

    public string $filterPensumId = '';

    public string $filterActive = '';

    public string $filterTipo = '';

    public string $filterDiagMain = '';

    public string $filterPestudioId = '';

    public string $filterGradoId = '';

    public string $filterProfesorId = '';

    public ?int $selectedId = null;

    public bool $showDetail = false;

    public bool $enrichedLoaded = false;

    // ─── Wizard de pregunta (edición + registro) ───────────────────
    public bool $showQuestionModal = false;

    public bool $isCreatingQuestion = false;

    public int $wizardStep = 1;

    public ?DiagQuestion $editingQuestion = null;

    public string $pregunta = '';

    public string $tipo_pregunta = 'multiple';

    public $orden = 1;

    public bool $activo = true;

    public array $options = [];

    public int $correct_option_index = 0;

    public $min_value = 1;

    public $max_value = 5;

    public $weighing = 1;

    public $difficulty = 'medium';

    public $pensum_id = null;

    public $diag_main_id = null;

    public string $expected_answer = '';

    public bool $taggingMath = false;

    public int $paginate = 10;

    // ─── Resultados por estudiante (réplica del tab Sesiones de profesores) ───
    public string $searchSessions = '';

    public string $filterDateFrom = '';

    public string $filterDateTo = '';

    public ?int $selectedSessionId = null;

    public bool $showSessionDetail = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAreaId' => ['except' => ''],
        'filterPensumId' => ['except' => ''],
        'filterActive' => ['except' => ''],
        'filterTipo' => ['except' => ''],
        'filterDiagMain' => ['except' => ''],
        'filterPestudioId' => ['except' => ''],
        'filterGradoId' => ['except' => ''],
        'filterProfesorId' => ['except' => ''],
        'searchSessions' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
    ];

    public function updatingPaginate(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAreaId(): void
    {
        $this->filterPensumId = '';
        $this->resetPage();
    }

    public function updatedFilterPestudioId(): void
    {
        $this->filterGradoId = '';
        $this->filterPensumId = '';
        $this->resetPage();
    }

    public function updatedFilterGradoId(): void
    {
        $this->filterPensumId = '';
        $this->resetPage();
    }

    public function updatedFilterProfesorId(): void
    {
        $this->filterPensumId = '';
        $this->resetPage();
    }

    public function updatingFilterPensumId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterActive(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDiagMain(): void
    {
        $this->resetPage();
        $this->resetPage('sessionsPage');
    }

    public function updatingSearchSessions(): void
    {
        $this->resetPage('sessionsPage');
    }

    public function updatingFilterDateFrom(): void
    {
        $this->resetPage('sessionsPage');
    }

    public function updatingFilterDateTo(): void
    {
        $this->resetPage('sessionsPage');
    }

    public function resetSessionFilters(): void
    {
        $this->searchSessions = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage('sessionsPage');
    }

    public function openSessionDetail(int $id): void
    {
        $this->selectedSessionId = $id;
        $this->showSessionDetail = true;
    }

    public function closeSessionDetail(): void
    {
        $this->showSessionDetail = false;
        $this->selectedSessionId = null;
    }

    public function loadEnriched(): void
    {
        $this->enrichedLoaded = true;
    }

    public function clearAllFilters(): void
    {
        $this->search = '';
        $this->filterAreaId = '';
        $this->filterPensumId = '';
        $this->filterTipo = '';
        $this->filterActive = '';
        $this->filterDiagMain = '';
        $this->filterPestudioId = '';
        $this->filterGradoId = '';
        $this->filterProfesorId = '';
        $this->resetPage();
        $this->resetSessionFilters();
    }

    public function toggleActivo(int $id): void
    {
        $q = $this->scopedQuery()->findOrFail($id);
        $this->assertCanReview($q);
        $q->activo = ! (bool) $q->activo;
        $q->save();
        $this->notification()->success(
            $q->activo ? 'Pregunta activada' : 'Pregunta desactivada',
            $q->activo ? 'La pregunta quedó activa para diagnóstico.' : 'La pregunta quedó inactiva.'
        );
    }

    public function openDetail(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    // ─── Wizard: registro de pregunta (solo pensums del ámbito) ───

    public function openCreateQuestionModal(): void
    {
        $scopeIds = $this->activeLeadershipPensumIds();

        if ($scopeIds->isEmpty()) {
            $this->notification()->warning(
                'Sin áreas asignadas',
                'No tienes pensums activos en tu ámbito para registrar preguntas.'
            );

            return;
        }

        $this->resetForm();
        $this->isCreatingQuestion = true;
        $this->wizardStep = 1;
        $this->showQuestionModal = true;
    }

    // ─── Wizard: edición de pregunta (sin crear) ───────────────────

    public function openQuestionModal(int $id): void
    {
        $this->resetForm();
        $this->isCreatingQuestion = false;
        $this->wizardStep = 1;

        $question = $this->scopedQuery()->with('options')->find($id);

        if (! $question) {
            $this->notification()->error('Pregunta no disponible', 'La pregunta no pertenece a tus áreas asignadas.');

            return;
        }

        $this->assertCanReview($question);
        $this->editingQuestion = $question;

        $this->pregunta = (string) $question->pregunta;
        $this->tipo_pregunta = $question->tipo_pregunta ?: 'multiple';
        $this->orden = $question->orden ?? 1;
        $this->activo = (bool) ($question->activo ?? true);
        $this->weighing = $question->weighing ?? 1;
        $this->difficulty = $question->difficulty ?? 'medium';
        $this->pensum_id = $question->pensum_id;
        $this->diag_main_id = $question->diag_main_id;

        if ($this->tipo_pregunta === 'multiple') {
            $this->options = $question->options->sortBy('orden')->map(function ($option, $index) {
                return [
                    'opcion' => $option->opcion,
                    'valor' => (int) ($option->valor ?? 0),
                    'orden' => $option->orden ?? $index + 1,
                ];
            })->values()->toArray();

            if (empty($this->options)) {
                $this->resetOptions();
            }

            $correctIndex = $question->options->search(fn ($option) => (int) ($option->valor ?? 0) > 0);
            $this->correct_option_index = $correctIndex !== false ? $correctIndex : 0;
        }

        $this->showQuestionModal = true;
    }

    public function nextStep(): void
    {
        $this->validateStep();
        if ($this->wizardStep < 3) {
            $this->wizardStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    public function goToStep($step): void
    {
        if ($step >= 1 && $step <= 3) {
            $this->wizardStep = $step;
        }
    }

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = ['opcion' => '', 'valor' => 0, 'orden' => count($this->options) + 1];
        }
    }

    public function removeOption($index): void
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    public function validateStep(): void
    {
        if ($this->wizardStep === 1) {
            $this->validate([
                'pensum_id' => ['required', 'exists:pensums,id'],
                'tipo_pregunta' => 'required|in:multiple,open,scale',
            ], [
                'pensum_id.required' => 'Debe seleccionar un área de formación.',
                'pensum_id.exists' => 'El área de formación seleccionada no es válida.',
                'tipo_pregunta.required' => 'Debe seleccionar un tipo de pregunta.',
                'tipo_pregunta.in' => 'El tipo de pregunta seleccionado no es válido.',
            ]);
        } elseif ($this->wizardStep === 2) {
            $rules = ['pregunta' => 'required|string|min:10|max:500'];
            $messages = [
                'pregunta.required' => 'El texto de la pregunta es obligatorio.',
                'pregunta.min' => 'La pregunta debe tener al menos 10 caracteres.',
                'pregunta.max' => 'La pregunta no puede exceder 500 caracteres.',
            ];

            if ($this->tipo_pregunta === 'multiple') {
                $rules['options'] = 'required|array|min:2|max:6';
                $rules['options.*.opcion'] = 'required|string|max:200';
                $rules['correct_option_index'] = 'required|integer|min:0|max:'.(count($this->options) - 1);
                $messages['options.required'] = 'Debe agregar al menos 2 opciones.';
                $messages['options.min'] = 'Debe tener al menos 2 opciones.';
                $messages['options.*.opcion.required'] = 'Todas las opciones deben tener texto.';
                $messages['correct_option_index.required'] = 'Debe seleccionar la opción correcta.';
            } elseif ($this->tipo_pregunta === 'scale') {
                $rules['min_value'] = 'required|integer|min:1|max:10';
                $rules['max_value'] = 'required|integer|min:2|max:10|gt:min_value';
                $messages['min_value.required'] = 'El valor mínimo es obligatorio.';
                $messages['max_value.required'] = 'El valor máximo es obligatorio.';
                $messages['max_value.gt'] = 'El valor máximo debe ser mayor al mínimo.';
            }

            $this->validate($rules, $messages);
        } elseif ($this->wizardStep === 3) {
            $this->validate([
                'orden' => 'nullable|integer|min:1',
                'weighing' => 'required|integer|min:1|max:5',
                'difficulty' => 'required|string|in:easy,medium,hard',
            ], [
                'weighing.required' => 'La ponderación es obligatoria.',
                'weighing.min' => 'La ponderación debe ser al menos 1.',
                'weighing.max' => 'La ponderación no puede ser mayor a 5.',
                'difficulty.required' => 'Debe seleccionar la dificultad.',
                'difficulty.in' => 'La dificultad seleccionada no es válida.',
            ]);
        }
    }

    public function saveQuestion(): void
    {
        $this->validateStep();

        if ($this->editingQuestion) {
            // Autorización edición: el área (pensum) debe pertenecer al scope del líder.
            $service = new LeadershipService(Auth::user());
            if (! Auth::user()->is_admin) {
                $service->assertCanAccessPensum((int) $this->pensum_id);
            }
        } elseif (! $this->activeLeadershipPensumIds()->contains((int) $this->pensum_id)) {
            // Registro: el pensum debe estar en el ámbito activo.
            // Aviso en vez de abort para no dejar el wizard en estado inconsistente.
            $this->notification()->error(
                'Área fuera de tu ámbito',
                'Solo puedes registrar preguntas en pensums activos de tus áreas asignadas.'
            );

            return;
        }

        try {
            DB::beginTransaction();

            if ($this->editingQuestion) {
                $question = $this->editingQuestion;
                $question->update([
                    'pregunta' => $this->pregunta,
                    'tipo_pregunta' => $this->tipo_pregunta,
                    'pensum_id' => $this->pensum_id,
                    'diag_main_id' => $this->diag_main_id ?: null,
                    'orden' => $this->orden,
                    'weighing' => $this->weighing,
                    'difficulty' => $this->difficulty,
                    'activo' => $this->activo,
                ]);
                $savedMessage = 'Pregunta actualizada';
            } else {
                $nextOrder = (int) (DiagQuestion::where('pensum_id', $this->pensum_id)->max('orden') ?? 0) + 1;

                $question = DiagQuestion::create([
                    'pregunta' => $this->pregunta,
                    'tipo_pregunta' => $this->tipo_pregunta,
                    'pensum_id' => $this->pensum_id,
                    'diag_main_id' => $this->diag_main_id ?: null,
                    'orden' => $this->orden ?: $nextOrder,
                    'weighing' => $this->weighing,
                    'difficulty' => $this->difficulty,
                    'activo' => $this->activo,
                ]);
                $savedMessage = 'Pregunta registrada';
            }

            if ($this->tipo_pregunta === 'multiple') {
                if ($this->editingQuestion) {
                    $question->options()->delete();
                }

                $optionsData = [];
                foreach ($this->options as $index => $option) {
                    // trim() !== '': '0' es una opción válida (empty('0') === true la descartaría).
                    if (trim((string) ($option['opcion'] ?? '')) !== '') {
                        $optionsData[] = [
                            'question_id' => $question->id,
                            'opcion' => $option['opcion'],
                            'valor' => $index == $this->correct_option_index ? 1 : 0,
                            'orden' => $option['orden'] ?? ($index + 1),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (! empty($optionsData)) {
                    DiagOption::insert($optionsData);
                }
            }

            DB::commit();

            $this->closeQuestionModal();
            $this->notification()->success($savedMessage, 'La pregunta se ha guardado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->notification()->error('Error', 'Ocurrió un error al guardar la pregunta: '.$e->getMessage());
        }
    }

    public function closeQuestionModal(): void
    {
        $this->showQuestionModal = false;
        $this->isCreatingQuestion = false;
        $this->wizardStep = 1;
        $this->resetForm();
    }

    /**
     * Etiqueta expresiones matemáticas con LaTeX (KaTeX) en el enunciado y
     * las opciones del wizard, usando la cadena de modelos math de OpenRouter.
     * Réplica del flujo "Etiquetar Not. Mat." del LessonWizard.
     */
    public function tagQuestionMath(): void
    {
        if (trim($this->pregunta) === '') {
            $this->notification()->warning(
                'Texto vacío',
                'Escribe primero el enunciado de la pregunta.'
            );

            return;
        }

        $this->taggingMath = true;

        try {
            $options = $this->tipo_pregunta === 'multiple'
                ? array_map(fn ($o) => (string) ($o['opcion'] ?? ''), $this->options)
                : [];

            $result = app(\App\Services\Diagnostic\QuestionMathTaggingService::class)
                ->tag($this->pregunta, $options);

            if (! ($result['success'] ?? false)) {
                $this->notification()->error(
                    'No se pudo etiquetar',
                    $result['error'] ?? 'La IA no devolvió un resultado válido.'
                );

                return;
            }

            $this->pregunta = $result['pregunta'];

            if ($this->tipo_pregunta === 'multiple') {
                foreach ($result['opciones'] as $i => $text) {
                    if (isset($this->options[$i])) {
                        $this->options[$i]['opcion'] = $text;
                    }
                }
            }

            $this->notification()->success(
                'Notación matemática',
                'Expresiones convertidas a LaTeX. Revisa la vista previa.'
            );
        } catch (\Throwable $e) {
            Log::error('Leadership math tagging failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            $this->notification()->error('Error inesperado', $e->getMessage());
        } finally {
            $this->taggingMath = false;
        }
    }

    private function resetOptions(): void
    {
        $this->options = [
            ['opcion' => '', 'valor' => 0, 'orden' => 1],
            ['opcion' => '', 'valor' => 0, 'orden' => 2],
        ];
    }

    private function resetForm(): void
    {
        $this->editingQuestion = null;
        $this->isCreatingQuestion = false;
        $this->pregunta = '';
        $this->tipo_pregunta = 'multiple';
        $this->orden = 1;
        $this->activo = true;
        $this->weighing = 1;
        $this->difficulty = 'medium';
        $this->pensum_id = null;
        $this->diag_main_id = null;
        $this->expected_answer = '';
        $this->min_value = 1;
        $this->max_value = 5;
        $this->correct_option_index = 0;
        $this->resetOptions();
    }

    private function scopedQuery()
    {
        $user = Auth::user();
        $query = DiagQuestion::query()->with(['pensum.asignatura', 'pensum.grado.pestudio', 'diagMain', 'competency', 'indicator', 'options']);

        // Strict is_leadership (raw, sin bypass admin) — cifras asociadas a pensumId vía AreaConocimiento.leader_id = userId
        $strictPensumIds = $this->getStrictLeadershipPensumIds($user);
        if ($strictPensumIds->isEmpty()) {
            return $query->whereRaw('1=0');
        }

        return $query->whereIn('pensum_id', $strictPensumIds);
    }

    private function getStrictLeadershipPensumIds($user): \Illuminate\Support\Collection
    {
        $areaIds = AreaConocimiento::where('leader_id', $user->id)->pluck('id');
        if ($areaIds->isEmpty()) {
            return collect();
        }

        return CampoConocimiento::whereIn('area_conocimiento_id', $areaIds)
            ->whereNotNull('pensum_id')
            ->pluck('pensum_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * Pensums del ámbito del líder que además están activos
     * (pensum.status_active = 1 y grado/pestudio.status_active = 'true').
     * Misma regla que el render usa para facetas y preguntas.
     */
    private function activeLeadershipPensumIds(): \Illuminate\Support\Collection
    {
        $strictIds = $this->getStrictLeadershipPensumIds(Auth::user());
        if ($strictIds->isEmpty()) {
            return collect();
        }

        return $strictIds->intersect(
            Pensum::whereIn('id', $strictIds)
                ->where('status_active', 1)
                ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
                ->whereHas('pestudio', fn ($q) => $q->where('status_active', 'true'))
                ->pluck('id')->map(fn ($id) => (int) $id)
        )->values();
    }

    private function assertCanReview(DiagQuestion $q): void
    {
        if (Auth::user()->is_admin) {
            return;
        }
        $service = new LeadershipService(Auth::user());
        if ($q->pensum_id) {
            $service->assertCanAccessPensum((int) $q->pensum_id);
        } else {
            abort(403, 'Pregunta sin pensum asociado.');
        }
    }

    #[Layout('leadership.layouts.app')]
    public function render()
    {
        $user = Auth::user();
        $service = new LeadershipService($user);

        // Strict is_leadership: solo áreas donde leader_id = userId (sin fallback admin)
        $areas = AreaConocimiento::where('leader_id', $user->id)->orderBy('name')->get();

        // Pensums estrictos vía AreaConocimiento.leader_id = userId
        $strictPensumIds = $this->getStrictLeadershipPensumIds($user);

        // Solo cadena activa (paridad profesors): pensum.status_active = 1
        // y grado/pestudio.status_active = 'true'. Todo lo demás (facetas,
        // preguntas, sesiones, métricas) deriva de este conjunto.
        $strictPensumIds = $strictPensumIds->intersect(
            Pensum::whereIn('id', $strictPensumIds)
                ->where('status_active', 1)
                ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
                ->whereHas('pestudio', fn ($q) => $q->where('status_active', 'true'))
                ->pluck('id')->map(fn ($id) => (int) $id)
        )->values();

        // Inicialización para evitar undefined en la vista si hay excepción temprana
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();

        // Facetas anidadas Pestudio → Grado → Pensum (+ Área y Profesor):
        // intersección de cada faceta con los pensums estrictos del líder.
        $toIntIds = fn ($ids) => collect($ids)->map(fn ($id) => (int) $id)->unique()->values();
        $facetPensumIds = $toIntIds($strictPensumIds);
        if ($this->filterAreaId !== '') {
            $areaForFacet = AreaConocimiento::find((int) $this->filterAreaId);
            if ($areaForFacet) {
                $facetPensumIds = $facetPensumIds->intersect($toIntIds(
                    CampoConocimiento::where('area_conocimiento_id', $areaForFacet->id)->whereNotNull('pensum_id')->pluck('pensum_id')
                ));
            }
        }
        if ($this->filterPestudioId !== '') {
            $facetPensumIds = $facetPensumIds->intersect($toIntIds(
                Pensum::where('pestudio_id', (int) $this->filterPestudioId)->pluck('id')
            ));
        }
        if ($this->filterGradoId !== '') {
            $facetPensumIds = $facetPensumIds->intersect($toIntIds(
                Pensum::where('grado_id', (int) $this->filterGradoId)->pluck('id')
            ));
        }
        if ($this->filterProfesorId !== '') {
            $facetPensumIds = $facetPensumIds->intersect($toIntIds(
                Pevaluacion::where('profesor_id', (int) $this->filterProfesorId)->pluck('pensum_id')
            ));
        }
        $facetPensumIds = $facetPensumIds->values();

        // Opciones de los selects anidados (solo dentro del scope del líder)
        $pestudioOptions = Pestudio::whereIn('id', Pensum::whereIn('id', $strictPensumIds)->pluck('pestudio_id'))
            ->orderBy('code')->get(['id', 'code', 'name']);
        $gradoOptions = collect();
        if ($this->filterPestudioId !== '') {
            $gradoOptions = Grado::where('pestudio_id', (int) $this->filterPestudioId)
                ->whereIn('id', Pensum::whereIn('id', $strictPensumIds)->pluck('grado_id'))
                ->where('status_active', 'true')
                ->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
        }
        $profesorOptions = Profesor::whereIn('id', Pevaluacion::whereIn('pensum_id', $strictPensumIds)->distinct()->pluck('profesor_id'))
            ->orderBy('lastname')->orderBy('name')->get(['id', 'name', 'lastname']);

        // Pensums anidados a Área/Pestudio/Grado/Profesor
        $pensumsOptions = Pensum::with(['asignatura', 'grado'])
            ->whereIn('id', $facetPensumIds)
            ->orderBy('pestudio_id')->orderBy('grado_id')->get();

        // El pensum elegido debe seguir perteneciendo al conjunto facetado
        if ($this->filterPensumId !== '' && ! $facetPensumIds->contains((int) $this->filterPensumId)) {
            $this->filterPensumId = '';
        }

        // Conjunto efectivo para preguntas, sesiones y métricas (facetas + pensum explícito)
        $effectivePensumIds = $facetPensumIds;
        if ($this->filterPensumId !== '') {
            $effectivePensumIds = collect([(int) $this->filterPensumId]);
        }
        $effectivePensumIdsArray = $effectivePensumIds->values()->all();

        $query = $this->scopedQuery();

        $query->whereIn('pensum_id', $effectivePensumIdsArray);
        if ($this->search !== '') {
            $query->where('pregunta', 'like', '%'.$this->search.'%');
        }
        if ($this->filterActive !== '') {
            $query->where('activo', (bool) $this->filterActive);
        }
        if ($this->filterTipo !== '') {
            $query->where('tipo_pregunta', $this->filterTipo);
        }
        if ($this->filterDiagMain !== '') {
            $query->where('diag_main_id', (int) $this->filterDiagMain);
        }

        $questions = $query->orderBy('pensum_id')->orderBy('orden')->orderBy('id')->paginate($this->paginate);

        $baseScoped = $this->scopedQuery();
        // Strict is_leadership: baseScoped ya filtra por strictPensumIds vía scopedQuery(); si es vacío, ya es whereRaw 1=0
        $baseScoped->whereIn('pensum_id', $effectivePensumIdsArray);
        if ($this->filterDiagMain !== '') {
            $baseScoped->where('diag_main_id', (int) $this->filterDiagMain);
        }
        $metrics = [
            'total' => (clone $baseScoped)->count(),
            'activas' => (clone $baseScoped)->where('activo', true)->count(),
            'inactivas' => (clone $baseScoped)->where('activo', false)->count(),
            'multiples' => (clone $baseScoped)->where('tipo_pregunta', 'multiple')->count(),
        ];

        // Estudiantes y precisión en el mismo ámbito is_leadership (strict)
        $sessionScopeForMetrics = DiagSession::query()
            ->when($strictPensumIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))
            ->when($strictPensumIds->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $strictPensumIds))
            ->whereIn('pensum_id', $effectivePensumIdsArray)
            ->when($this->filterDiagMain !== '', fn ($q) => $q->where('diag_main_id', (int) $this->filterDiagMain));
        $metrics['estudiantes'] = (clone $sessionScopeForMetrics)->whereNotNull('estudiant_id')->distinct('estudiant_id')->count('estudiant_id');

        $precisionBaseForMetrics = DiagAnswer::whereHas('question', function ($q) use ($strictPensumIds, $effectivePensumIdsArray) {
            $q->where('tipo_pregunta', 'multiple')->where('activo', 1)
                ->when($strictPensumIds->isEmpty(), fn ($qq) => $qq->whereRaw('1=0'))
                ->when($strictPensumIds->isNotEmpty(), fn ($qq) => $qq->whereIn('pensum_id', $strictPensumIds))
                ->whereIn('pensum_id', $effectivePensumIdsArray)
                ->when($this->filterDiagMain !== '', fn ($qq) => $qq->where('diag_main_id', (int) $this->filterDiagMain));
        })->whereNotNull('completado_at')->whereNotNull('option_id');
        $metrics['precisionTotal'] = (clone $precisionBaseForMetrics)->count();
        $metrics['precisionCorrect'] = (clone $precisionBaseForMetrics)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
        $metrics['precision'] = $metrics['precisionTotal'] > 0 ? round((100 * $metrics['precisionCorrect']) / $metrics['precisionTotal'], 1) : null;
        $metrics['estudiantes'] = $metrics['estudiantes'] ?? 0;

        // ── Réplica planning: header Lapso/Pestudio/Referente + 8-grid (s2526 completitud/abandono) ──
        $questionsCount = $metrics['total'];
        // totalAnswers / preguntas con resp / pensums con resp (scoped + filtros)
        $answerScopedForGrid = DiagAnswer::whereHas('question', function ($q) use ($strictPensumIds, $effectivePensumIdsArray) {
            $q->when($strictPensumIds->isEmpty(), fn ($qq) => $qq->whereRaw('1=0'))
                ->when($strictPensumIds->isNotEmpty(), fn ($qq) => $qq->whereIn('pensum_id', $strictPensumIds))
                ->whereIn('pensum_id', $effectivePensumIdsArray)
                ->when($this->filterDiagMain !== '', fn ($qq) => $qq->where('diag_main_id', (int) $this->filterDiagMain));
        })->whereNotNull('completado_at');
        $totalAnswersCount = (clone $answerScopedForGrid)->count();
        $answerQuestionIds = (clone $answerScopedForGrid)->pluck('question_id')->unique()->filter()->values();
        $questionsWithAnswersCount = $answerQuestionIds->count();
        $pensumsWithAnswersCount = $answerQuestionIds->isNotEmpty()
            ? DiagQuestion::whereIn('id', $answerQuestionIds)->pluck('pensum_id')->unique()->filter()->count()
            : 0;

        // % Completitud / Tasa Abandono desde sesiones del scope
        $sessionsCount = (clone $sessionScopeForMetrics)->count();
        $completedSessions = (clone $sessionScopeForMetrics)->whereNotNull('completado_at')->count();
        $completionRate = $sessionsCount > 0 ? round((100 * $completedSessions) / $sessionsCount, 1) : null;
        $abandonRate = $sessionsCount > 0 ? round(100 - $completionRate, 1) : null;

        // Header 3 cols: Lapso / Pestudio / Referente (derivado de filterDiagMain, paridad planning DiagMainViewer)
        $displayLapso = null;
        $displayPestudio = null;
        $displayReferent = null;
        if ($this->filterDiagMain !== '') {
            $dmForHeader = DiagMain::with(['lapso', 'pestudio', 'referent'])->find((int) $this->filterDiagMain);
            if ($dmForHeader) {
                $displayLapso = $dmForHeader->lapso;
                $displayPestudio = $dmForHeader->pestudio;
                $displayReferent = $dmForHeader->referent;
            }
        }
        if (! $displayPestudio) {
            // Si no hay diagMain seleccionado, derivar pestudio del scope (múltiples → null)
            $scopePensumIds = (clone $baseScoped)->distinct()->pluck('pensum_id')->filter()->values();
            if ($scopePensumIds->isNotEmpty()) {
                $pestudioIds = Pensum::whereIn('id', $scopePensumIds)->pluck('pestudio_id')->unique()->filter()->values();
                if ($pestudioIds->count() === 1) {
                    $displayPestudio = \App\Models\app\Academy\Pestudio::find($pestudioIds->first());
                }
            }
        }

        // Pensum progress para Resumen por área (paridad planning, scoped)
        $pensumProgress = collect();
        $progressPestudios = collect();
        $progressGrados = collect();
        $progressPensums = collect();
        $scopePensumIdsForProgress = (clone $baseScoped)->distinct()->pluck('pensum_id')->filter()->values()->unique();
        if ($scopePensumIdsForProgress->isNotEmpty()) {
            $pensumProgress = Pensum::whereIn('id', $scopePensumIdsForProgress)->with(['asignatura', 'grado'])->get()->map(function (Pensum $pensum) use ($strictPensumIds) {
                $pid = $pensum->id;
                // totalQ ya está en este pensum, no necesita filtro extra si strict ya garantiza pertenencia; pero mantenemos guard para vacío
                $totalQ = DiagQuestion::where('pensum_id', $pid)->when($strictPensumIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))->count();
                // sesiones del pensum (respeta scope)
                $totalS = DiagSession::where('pensum_id', $pid)->when($strictPensumIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))->count();
                $completedS = DiagSession::where('pensum_id', $pid)->whereNotNull('completado_at')->when($strictPensumIds->isEmpty(), fn ($q) => $q->whereRaw('1=0'))->count();
                $completion = $totalS > 0 ? round((100 * $completedS) / $totalS, 1) : 0;
                $ansBase = DiagAnswer::whereHas('question', fn ($q) => $q->where('pensum_id', $pid)->where('tipo_pregunta', 'multiple'))->whereNotNull('completado_at')->whereNotNull('option_id');
                $totalAns = (clone $ansBase)->count();
                $correctAns = (clone $ansBase)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
                $prec = $totalAns > 0 ? round((100 * $correctAns) / $totalAns, 1) : null;

                return (object) [
                    'pensum' => $pensum,
                    'fullname' => $pensum->full_name ?? ($pensum->grado?->name.' - '.$pensum->asignatura?->name),
                    'total_questions' => $totalQ,
                    'total_sessions' => $totalS,
                    'completed_sessions' => $completedS,
                    'completion_percentage' => $completion,
                    'precision' => $prec,
                    'total_answered' => $totalAns,
                    'correct_answers' => $correctAns,
                ];
            })->sortByDesc('completion_percentage')->values();
            // Paginación Resumen por área — 10 por página
            $allProgress = $pensumProgress;
            $perPage = 10;
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('pensumProgressPage');
            $total = $pensumProgress->count();
            $items = $pensumProgress->forPage($currentPage, $perPage)->values();
            $pensumProgress = new \Illuminate\Pagination\LengthAwarePaginator($items, $total, $perPage, $currentPage, ['path' => request()->url(), 'pageName' => 'pensumProgressPage']);
            // Para filtros del resumen (simplificado: pestudio/grado del scope)
            $progressPestudios = \App\Models\app\Academy\Pestudio::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
            $progressGrados = \App\Models\app\Academy\Grado::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('grado_id'))->where('status_active', 'true')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
            $progressPensums = Pensum::whereIn('id', $scopePensumIdsForProgress)->with(['asignatura', 'grado'])->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
            // Grado resumen — agregado por grado; sigue los filtros globales (sin filtros locales)
            $gradoProgress = $allProgress->groupBy(fn ($pp) => $pp->pensum->grado_id)->map(function ($items) {
                $grado = $items->first()->pensum->grado;
                $totalQ = $items->sum('total_questions');
                $totalS = $items->sum('total_sessions');
                $completedS = $items->sum('completed_sessions');
                $completion = $totalS > 0 ? round((100 * $completedS) / $totalS, 1) : 0;
                $totalAns = $items->sum('total_answered');
                $correctAns = $items->sum('correct_answers');
                $prec = $totalAns > 0 ? round((100 * $correctAns) / $totalAns, 1) : null;

                return (object) [
                    'grado' => $grado,
                    'fullname' => $grado?->name ?? '—',
                    'total_questions' => $totalQ,
                    'total_sessions' => $totalS,
                    'completed_sessions' => $completedS,
                    'completion_percentage' => $completion,
                    'precision' => $prec,
                    'total_answered' => $totalAns,
                    'correct_answers' => $correctAns,
                ];
            })->values()->sortBy('fullname')->values();
            // Sólo grados activos, coherente con las opciones del select anidado.
            $activeGradoIds = \App\Models\app\Academy\Grado::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('grado_id'))->where('status_active', 'true')->pluck('id')->map(fn ($id) => (int) $id)->all();
            $gradoProgress = $gradoProgress->filter(fn ($gp) => $gp->grado && in_array((int) $gp->grado->id, $activeGradoIds, true))->values();
            // Paginación por defecto 10 filas por página para Resumen por Grado
            $gradoPerPage = 10;
            $gradoCurrentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('gradoProgressPage');
            $gradoTotal = $gradoProgress->count();
            $gradoItems = $gradoProgress->forPage($gradoCurrentPage, $gradoPerPage)->values();
            $gradoProgress = new \Illuminate\Pagination\LengthAwarePaginator($gradoItems, $gradoTotal, $gradoPerPage, $gradoCurrentPage, ['path' => request()->url(), 'pageName' => 'gradoProgressPage']);
        } else {
            $gradoProgress = collect();
        }

        // Sección enriquecida — diferida (wire:init) y respeta is_leadership (AreaConocimiento→Pensum) y filtros de área/diagMain (incluso para admin con áreas)
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        if ($this->enrichedLoaded) {
            // Strict: solo pensums del líder
            $assignedForEnriched = $strictPensumIds;
            $recentSessions = DiagSession::with(['estudiant', 'pensum'])
                ->when($assignedForEnriched->isEmpty(), fn ($q) => $q->whereRaw('1=0'))
                ->when($assignedForEnriched->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForEnriched))
                ->whereIn('pensum_id', $effectivePensumIdsArray)
                ->when($this->filterDiagMain !== '', fn ($q) => $q->where('diag_main_id', (int) $this->filterDiagMain))
                ->orderByDesc('iniciado_at')->limit(5)->get();
        }

        $typeBase = null;
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        if ($this->enrichedLoaded) {
            $typeBase = $this->scopedQuery();
            $typeBase->whereIn('pensum_id', $effectivePensumIdsArray);
            if ($this->filterDiagMain !== '') {
                $typeBase->where('diag_main_id', (int) $this->filterDiagMain);
            }
            $questionsByType = $typeBase->select('tipo_pregunta as type', DB::raw('count(*) as count'))->groupBy('tipo_pregunta')->get();
            $questionsByDifficulty = (clone $typeBase)->select('difficulty', DB::raw('count(*) as count'))->groupBy('difficulty')->get();
        }

        $tipos = (clone $this->scopedQuery())->distinct()->pluck('tipo_pregunta')->filter()->sort()->values();
        $diagMains = DiagMain::whereIn('id', (clone $this->scopedQuery())->distinct()->pluck('diag_main_id')->filter())->orderBy('name')->get(['id', 'name']);

        // Pensums con preguntas en el scope — para el wizard de edición.
        $wizardPensums = Pensum::with(['asignatura', 'grado'])
            ->whereIn('id', (clone $this->scopedQuery())->distinct()->pluck('pensum_id'))
            ->orderBy('pestudio_id')->orderBy('grado_id')->get();

        // Pensums activos del ámbito — para el registro de preguntas.
        $createPensums = Pensum::with(['asignatura', 'grado'])
            ->whereIn('id', $this->activeLeadershipPensumIds())
            ->orderBy('pestudio_id')->orderBy('grado_id')->get();

        // ── Resultados por estudiante (réplica del tab Sesiones de profesors) ──
        // Scope: pensums estrictos del líder ∩ pensums efectivos por facetas.
        // `diag_sessions.diag_main_id` queda NULL en todas las sesiones (las crea
        // Diagnostic::startDiagnostic sin persistirlo), así que —igual que en
        // profesors— se acepta la pertenencia implícita: sesiones cuyo pensum
        // tiene preguntas del diagnóstico filtrado.
        $sessionScopeIds = $effectivePensumIdsArray;
        $diagMainPensumIds = [];
        if ($this->filterDiagMain !== '') {
            $diagMainPensumIds = DiagQuestion::where('diag_main_id', (int) $this->filterDiagMain)
                ->distinct()->pluck('pensum_id')->filter()
                ->map(fn ($id) => (int) $id)->values()->all();
        }

        $sessionsQuery = DiagSession::with([
            'estudiant:id,name,lastname,email',
            'estudiant.inscripcion.seccion.grado',
            'pensum.asignatura:id,name',
            'diagMain',
            'answers.selectedOption',
            'answeredQuestions:diag_main_id',
        ])->whereIn('pensum_id', $sessionScopeIds ?: [0]);

        if ($this->filterDiagMain !== '') {
            $sessionsQuery->where(function ($q) use ($diagMainPensumIds) {
                $q->where('diag_main_id', (int) $this->filterDiagMain);

                if (! empty($diagMainPensumIds)) {
                    $q->orWhereIn('pensum_id', $diagMainPensumIds);
                }
            });
        }

        if ($this->searchSessions !== '') {
            $term = '%'.$this->searchSessions.'%';
            $sessionsQuery->whereHas('estudiant', fn ($q) => $q
                ->where('name', 'like', $term)
                ->orWhere('lastname', 'like', $term));
        }

        if ($this->filterDateFrom !== '') {
            $sessionsQuery->whereDate('iniciado_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo !== '') {
            $sessionsQuery->whereDate('iniciado_at', '<=', $this->filterDateTo);
        }

        $sessions = $sessionsQuery->latest('iniciado_at')->paginate(10, ['*'], 'sessionsPage');

        // KPIs del bloque: estudiantes del scope vs. los que tienen sesión.
        $sessionSeccionIds = Pevaluacion::whereIn('pensum_id', $sessionScopeIds ?: [0])
            ->distinct()->pluck('seccion_id')->filter()->map(fn ($id) => (int) $id);
        $totalStudentsCount = $sessionSeccionIds->isEmpty()
            ? 0
            : (int) Inscripcion::whereIn('seccion_id', $sessionSeccionIds)->distinct()->count('estudiant_id');
        $studentsWithSessions = (int) (clone $sessionsQuery)->distinct()->count('estudiant_id');
        $sessionStats = [
            'total_students' => $totalStudentsCount,
            'students_with_sessions' => $studentsWithSessions,
            'students_without_sessions' => max(0, $totalStudentsCount - $studentsWithSessions),
        ];

        $sessionDetail = null;
        if ($this->showSessionDetail && $this->selectedSessionId) {
            // Rehidratado acotado al scope: no se expone la sesión de otro ámbito.
            $sessionDetail = DiagSession::with(['answers.question', 'answers.selectedOption', 'diagMain', 'answeredQuestions:diag_main_id'])
                ->whereIn('pensum_id', $sessionScopeIds ?: [0])
                ->find($this->selectedSessionId);
        }

        $selected = null;
        if ($this->showDetail && $this->selectedId) {
            $selected = $this->scopedQuery()->with(['pensum.asignatura', 'pensum.grado', 'pensum.pestudio', 'competency', 'indicator', 'options', 'diagMain'])->find($this->selectedId);
        }

        return view('livewire.leadership.diagnostic-question-review', [
            'questions' => $questions,
            'areas' => $areas,
            'pensumsOptions' => $pensumsOptions,
            'pestudioOptions' => $pestudioOptions,
            'gradoOptions' => $gradoOptions,
            'profesorOptions' => $profesorOptions,
            'wizardPensums' => $wizardPensums,
            'createPensums' => $createPensums,
            'metrics' => $metrics,
            'tipos' => $tipos,
            'diagMains' => $diagMains,
            'selected' => $selected,
            'recentSessions' => $recentSessions,
            'questionsByType' => $questionsByType,
            'questionsByDifficulty' => $questionsByDifficulty,
            'enrichedLoaded' => $this->enrichedLoaded,
            // Réplica planning grid
            'questionsCount' => $questionsCount ?? $metrics['total'] ?? 0,
            'pensumsWithAnswersCount' => $pensumsWithAnswersCount ?? 0,
            'questionsWithAnswersCount' => $questionsWithAnswersCount ?? 0,
            'totalAnswersCount' => $totalAnswersCount ?? 0,
            'studentsEvaluated' => $metrics['estudiantes'] ?? 0,
            'sessionsCount' => $sessionsCount ?? 0,
            'completedSessions' => $completedSessions ?? 0,
            'completionRate' => $completionRate ?? null,
            'abandonRate' => $abandonRate ?? null,
            'displayLapso' => $displayLapso ?? null,
            'displayPestudio' => $displayPestudio ?? null,
            'displayReferent' => $displayReferent ?? null,
            'pensumProgress' => $pensumProgress ?? collect(),
            'progressPestudios' => $progressPestudios ?? collect(),
            'progressGrados' => $progressGrados ?? collect(),
            'progressPensums' => $progressPensums ?? collect(),
            'gradoProgress' => $gradoProgress ?? collect(),
            'precision' => $metrics['precision'] ?? null,
            'precisionCorrect' => $metrics['precisionCorrect'] ?? 0,
            'precisionTotal' => $metrics['precisionTotal'] ?? 0,
            // Resultados por estudiante
            'sessions' => $sessions,
            'sessionStats' => $sessionStats,
            'sessionDetail' => $sessionDetail,
        ]);
    }
}
