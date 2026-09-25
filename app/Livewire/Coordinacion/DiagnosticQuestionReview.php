<?php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Services\Lms\CoordinacionScopeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public ?int $selectedId = null;

    public bool $showDetail = false;

    public bool $enrichedLoaded = false;

    public ?int $resumenGradoId = null;

    public ?int $resumenPestudioId = null;

    // ─── Wizard de edición de pregunta ─────────────────────────────
    public bool $showQuestionModal = false;

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

    public int $paginate = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterAreaId' => ['except' => ''],
        'filterPensumId' => ['except' => ''],
        'filterActive' => ['except' => ''],
        'filterTipo' => ['except' => ''],
        'filterDiagMain' => ['except' => ''],
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
    }

    public function updatedResumenGradoId(): void
    {
        $this->resetPage();
    }

    public function updatedResumenPestudioId(): void
    {
        $this->resumenGradoId = null;
        $this->resetPage();
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
        $this->resetPage();
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

    // ─── Wizard: edición de pregunta (sin crear) ───────────────────

    public function openQuestionModal(int $id): void
    {
        $this->resetForm();
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
        if (! $this->editingQuestion) {
            $this->notification()->error('Sin pregunta', 'No hay una pregunta en edición.');

            return;
        }

        $this->validateStep();

        // Autorización: el área (pensum) debe pertenecer al scope del líder.
        $service = new CoordinacionScopeService(Auth::user());
        if (! Auth::user()->is_admin) {
            $service->assertCanAccessPensum((int) $this->pensum_id);
        }

        try {
            DB::beginTransaction();

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

            if ($this->tipo_pregunta === 'multiple') {
                $question->options()->delete();

                $optionsData = [];
                foreach ($this->options as $index => $option) {
                    if (! empty($option['opcion'])) {
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
            $this->notification()->success('Pregunta actualizada', 'La pregunta se ha guardado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->notification()->error('Error', 'Ocurrió un error al guardar la pregunta: '.$e->getMessage());
        }
    }

    public function closeQuestionModal(): void
    {
        $this->showQuestionModal = false;
        $this->wizardStep = 1;
        $this->resetForm();
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
        $service = new CoordinacionScopeService(Auth::user());
        $query = DiagQuestion::query()->with(['pensum.asignatura', 'pensum.grado.pestudio', 'diagMain', 'competency', 'indicator', 'options']);

        return $service->scopeDiagQuestions($query);
    }

    private function assertCanReview(DiagQuestion $q): void
    {
        if (Auth::user()->is_admin) {
            return;
        }
        $service = new CoordinacionScopeService(Auth::user());
        if ($q->pensum_id) {
            $service->assertCanAccessPensum((int) $q->pensum_id);
        } else {
            abort(403, 'Pregunta sin pensum asociado.');
        }
    }

    #[Layout('coordinacion.layouts.app')]
    public function render()
    {
        $user = Auth::user();
        $service = new CoordinacionScopeService($user);

        $pestudioIdsForAreas = $service->getPestudioIds();
        if ($pestudioIdsForAreas->isNotEmpty()) {
            $areas = AreaConocimiento::whereIn('pestudio_id', $pestudioIdsForAreas)->orderBy('name')->get();
        } elseif ($service->isUnrestricted()) {
            $areas = AreaConocimiento::orderBy('name')->get();
        } else {
            $areas = collect();
        }

        // Inicialización para evitar undefined en la vista si hay excepción temprana
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();

        // Pensums anidados a la selección de área (solo los adscritos a esa área)
        $pensumsOptions = collect();
        if ($this->filterAreaId !== '') {
            $areaPensumIds = CampoConocimiento::where('area_conocimiento_id', (int) $this->filterAreaId)
                ->whereNotNull('pensum_id')->pluck('pensum_id')->unique();
            $pensumsOptions = Pensum::with(['asignatura', 'grado'])
                ->whereIn('id', $areaPensumIds)
                ->whereIn('id', (clone $this->scopedQuery())->distinct()->pluck('pensum_id'))
                ->orderBy('pestudio_id')->orderBy('grado_id')->get();
        }

        $query = $this->scopedQuery();

        if ($this->filterAreaId !== '') {
            $area = AreaConocimiento::find((int) $this->filterAreaId);
            if ($area) {
                $pensumIds = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id')->unique();
                $query->whereIn('pensum_id', $pensumIds);
            }
        }
        if ($this->filterPensumId !== '') {
            $query->where('pensum_id', (int) $this->filterPensumId);
        }
        if ($this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('pregunta', 'like', "%{$s}%")
                    ->orWhere('tipo_pregunta', 'like', "%{$s}%");
            });
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
        // Fuerza el ámbito is_leadership incluso para admin con áreas asignadas
        $coordPestIdsForBase = $service->getPestudioIds();
        $assignedForBase = $coordPestIdsForBase->isNotEmpty() ? \App\Models\app\Academy\Pensum::whereIn('pestudio_id', $coordPestIdsForBase)->pluck('id') : collect();
        if ($assignedForBase->isNotEmpty()) {
            $baseScoped->whereIn('pensum_id', $assignedForBase);
        }
        if ($this->filterAreaId !== '') {
            $area = AreaConocimiento::find((int) $this->filterAreaId);
            if ($area) {
                $pensumIds = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                $baseScoped->whereIn('pensum_id', $pensumIds);
            }
        }
        if ($this->filterPensumId !== '') {
            $baseScoped->where('pensum_id', (int) $this->filterPensumId);
        }
        if ($this->filterDiagMain !== '') {
            $baseScoped->where('diag_main_id', (int) $this->filterDiagMain);
        }
        $metrics = [
            'total' => (clone $baseScoped)->count(),
            'activas' => (clone $baseScoped)->where('activo', true)->count(),
            'inactivas' => (clone $baseScoped)->where('activo', false)->count(),
            'multiples' => (clone $baseScoped)->where('tipo_pregunta', 'multiple')->count(),
        ];

        // Estudiantes y precisión en el mismo ámbito is_leadership
        $sessionScopeForMetrics = DiagSession::query()
            ->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForBase))
            ->when($this->filterAreaId !== '', function ($q) {
                $area = AreaConocimiento::find((int) $this->filterAreaId);
                if ($area) {
                    $pids = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                    $q->whereIn('pensum_id', $pids);
                }
            })
            ->when($this->filterPensumId !== '', fn ($q) => $q->where('pensum_id', (int) $this->filterPensumId))
            ->when($this->filterDiagMain !== '', fn ($q) => $q->where('diag_main_id', (int) $this->filterDiagMain));
        $metrics['estudiantes'] = (clone $sessionScopeForMetrics)->whereNotNull('estudiant_id')->distinct('estudiant_id')->count('estudiant_id');

        $precisionBaseForMetrics = DiagAnswer::whereHas('question', function ($q) use ($assignedForBase) {
            $q->where('tipo_pregunta', 'multiple')->where('activo', 1)
                ->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($qq) => $qq->whereIn('pensum_id', $assignedForBase))
                ->when($this->filterAreaId !== '', function ($qq) {
                    $area = AreaConocimiento::find((int) $this->filterAreaId);
                    if ($area) {
                        $pids = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                        $qq->whereIn('pensum_id', $pids);
                    }
                })
                ->when($this->filterPensumId !== '', fn ($qq) => $qq->where('pensum_id', (int) $this->filterPensumId))
                ->when($this->filterDiagMain !== '', fn ($qq) => $qq->where('diag_main_id', (int) $this->filterDiagMain));
        })->whereNotNull('completado_at')->whereNotNull('option_id');
        $metrics['precisionTotal'] = (clone $precisionBaseForMetrics)->count();
        $metrics['precisionCorrect'] = (clone $precisionBaseForMetrics)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
        $metrics['precision'] = $metrics['precisionTotal'] > 0 ? round((100 * $metrics['precisionCorrect']) / $metrics['precisionTotal'], 1) : null;
        $metrics['estudiantes'] = $metrics['estudiantes'] ?? 0;

        // ── Réplica planning: header Lapso/Pestudio/Referente + 8-grid (s2526 completitud/abandono) ──
        $questionsCount = $metrics['total'];
        $answerScopedForGrid = DiagAnswer::whereHas('question', function ($q) use ($assignedForBase) {
            $q->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($qq) => $qq->whereIn('pensum_id', $assignedForBase))
                ->when($this->filterAreaId !== '', function ($qq) {
                    $area = AreaConocimiento::find((int) $this->filterAreaId);
                    if ($area) {
                        $pids = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                        $qq->whereIn('pensum_id', $pids);
                    }
                })
                ->when($this->filterPensumId !== '', fn ($qq) => $qq->where('pensum_id', (int) $this->filterPensumId))
                ->when($this->filterDiagMain !== '', fn ($qq) => $qq->where('diag_main_id', (int) $this->filterDiagMain));
        })->whereNotNull('completado_at');
        $totalAnswersCount = (clone $answerScopedForGrid)->count();
        $answerQuestionIds = (clone $answerScopedForGrid)->pluck('question_id')->unique()->filter()->values();
        $questionsWithAnswersCount = $answerQuestionIds->count();
        $pensumsWithAnswersCount = $answerQuestionIds->isNotEmpty()
            ? DiagQuestion::whereIn('id', $answerQuestionIds)->pluck('pensum_id')->unique()->filter()->count()
            : 0;

        $sessionsCount = (clone $sessionScopeForMetrics)->count();
        $completedSessions = (clone $sessionScopeForMetrics)->whereNotNull('completado_at')->count();
        $completionRate = $sessionsCount > 0 ? round((100 * $completedSessions) / $sessionsCount, 1) : null;
        $abandonRate = $sessionsCount > 0 ? round(100 - $completionRate, 1) : null;

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
            $scopePensumIds = (clone $baseScoped)->distinct()->pluck('pensum_id')->filter()->values();
            if ($scopePensumIds->isNotEmpty()) {
                $pestudioIds = Pensum::whereIn('id', $scopePensumIds)->pluck('pestudio_id')->unique()->filter()->values();
                if ($pestudioIds->count() === 1) {
                    $displayPestudio = \App\Models\app\Academy\Pestudio::find($pestudioIds->first());
                }
            }
        }

        $pensumProgress = collect();
        $progressPestudios = collect();
        $progressGrados = collect();
        $progressPensums = collect();
        $scopePensumIdsForProgress = (clone $baseScoped)->distinct()->pluck('pensum_id')->filter()->values()->unique();
        if ($scopePensumIdsForProgress->isNotEmpty()) {
            $pensumProgress = Pensum::whereIn('id', $scopePensumIdsForProgress)->with(['asignatura', 'grado'])->get()->map(function (Pensum $pensum) use ($assignedForBase) {
                $pid = $pensum->id;
                $totalQ = DiagQuestion::where('pensum_id', $pid)->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForBase))->count();
                $totalS = DiagSession::where('pensum_id', $pid)->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForBase))->count();
                $completedS = DiagSession::where('pensum_id', $pid)->whereNotNull('completado_at')->when($assignedForBase && $assignedForBase->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForBase))->count();
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
            $progressPestudios = \App\Models\app\Academy\Pestudio::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
            $progressGrados = \App\Models\app\Academy\Grado::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('grado_id'))->where('status_active', 'true')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
            $progressPensums = Pensum::whereIn('id', $scopePensumIdsForProgress)->with(['asignatura', 'grado'])->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
            // Grado resumen — agregado por grado con filtro pestudio
            $pestudiosForGradoResumen = Pestudio::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
            $gradosForResumenQuery = Grado::whereIn('id', Pensum::whereIn('id', $scopePensumIdsForProgress)->pluck('grado_id'))->where('status_active', 'true');
            if ($this->resumenPestudioId) {
                $gradosForResumenQuery->where('pestudio_id', (int) $this->resumenPestudioId);
            }
            $gradosForResumen = $gradosForResumenQuery->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
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
            if ($this->resumenPestudioId) {
                $gradoProgress = $gradoProgress->filter(fn ($gp) => $gp->grado && (int) $gp->grado->pestudio_id === (int) $this->resumenPestudioId)->values();
            }
            if ($this->resumenGradoId) {
                $gradoProgress = $gradoProgress->where('grado.id', (int) $this->resumenGradoId)->values();
            }
            // Paginación por defecto 10 filas por página para Resumen por Grado
            $gradoPerPage = 10;
            $gradoCurrentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('gradoProgressPage');
            $gradoTotal = $gradoProgress->count();
            $gradoItems = $gradoProgress->forPage($gradoCurrentPage, $gradoPerPage)->values();
            $gradoProgress = new \Illuminate\Pagination\LengthAwarePaginator($gradoItems, $gradoTotal, $gradoPerPage, $gradoCurrentPage, ['path' => request()->url(), 'pageName' => 'gradoProgressPage']);
        } else {
            $gradosForResumen = collect();
            $gradoProgress = collect();
        }

        // Sección enriquecida — diferida (wire:init) y respeta coordinacion (Pestudio→Pensum) y filtros de área/diagMain
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        if ($this->enrichedLoaded) {
            $coordPestIdsEnriched2 = $service->getPestudioIds();
            $assignedForEnriched = $coordPestIdsEnriched2->isNotEmpty() ? \App\Models\app\Academy\Pensum::whereIn('pestudio_id', $coordPestIdsEnriched2)->pluck('id') : collect();
            if ($assignedForEnriched->isEmpty() && $service->isUnrestricted()) {
                $assignedForEnriched = null;
            }
            $recentSessions = DiagSession::with(['estudiant', 'pensum'])
                ->when($assignedForEnriched && $assignedForEnriched->isNotEmpty(), fn ($q) => $q->whereIn('pensum_id', $assignedForEnriched))
                ->when($this->filterAreaId !== '', function ($q) {
                    $area = AreaConocimiento::find((int) $this->filterAreaId);
                    if ($area) {
                        $pids = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                        $q->whereIn('pensum_id', $pids);
                    }
                })
                ->when($this->filterDiagMain !== '', fn ($q) => $q->where('diag_main_id', (int) $this->filterDiagMain))
                ->orderByDesc('iniciado_at')->limit(5)->get();
        }

        $typeBase = null;
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        if ($this->enrichedLoaded) {
            $typeBase = $this->scopedQuery();
            if ($this->filterAreaId !== '') {
                $area = AreaConocimiento::find((int) $this->filterAreaId);
                if ($area) {
                    $pids = CampoConocimiento::where('area_conocimiento_id', $area->id)->whereNotNull('pensum_id')->pluck('pensum_id');
                    $typeBase->whereIn('pensum_id', $pids);
                }
            }
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

        $selected = null;
        if ($this->showDetail && $this->selectedId) {
            $selected = $this->scopedQuery()->with(['pensum.asignatura', 'pensum.grado', 'pensum.pestudio', 'competency', 'indicator', 'options', 'diagMain'])->find($this->selectedId);
        }

        return view('livewire.coordinacion.diagnostic-question-review', [
            'questions' => $questions,
            'areas' => $areas,
            'pensumsOptions' => $pensumsOptions,
            'wizardPensums' => $wizardPensums,
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
            'gradosForResumen' => $gradosForResumen ?? collect(),
            'gradoProgress' => $gradoProgress ?? collect(),
            'pestudiosForGradoResumen' => $pestudiosForGradoResumen ?? collect(),
            'precision' => $metrics['precision'] ?? null,
            'precisionCorrect' => $metrics['precisionCorrect'] ?? 0,
            'precisionTotal' => $metrics['precisionTotal'] ?? 0,
        ]);
    }
}
