<?php

namespace App\Livewire\Profesor\Diagnostics;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use App\Services\OpenRouterService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use \App\Http\Livewire\Evaluacion\Diagnostic\DeepSeekReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\GeminiReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\NvidiaReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\OpenRouterReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\QwenReportTrait;
    use WireUiActions, WithPagination;

    public $cacheKey;

    public $lastUpdated;

    // Propiedades principales
    public $activeTab = 'dashboard';

    public $showQuestionModal = false;

    public $generatingQuestion = false;

    public $SessionModalReport = false;

    public $editingQuestion = null;

    public $selectedSession = null;

    public $wizardStep = 1;

    public $selectedPensumId = null;

    public $profesor = null;

    public $pensumIds = [];

    public $seccionIds = [];

    // Carga académica (pevaluacions) del profesor en el lapso actual
    public $lapsoId = null;

    public $cargaPevaluacions = [];

    // Propiedades del formulario de preguntas
    public $pregunta = '';

    public $tipo_pregunta = 'multiple';

    public $orden = 1;

    public $activo = true;

    public $options = [];

    public $correct_option_index = 0;

    public $min_value = 1;

    public $max_value = 10;

    public $pensum_id = null;

    public $weighing = null;

    public $difficulty = null;

    public $diag_main_id = null;

    public $expected_answer = '';

    // Filtros y búsqueda
    public $search = '';

    public $filterType = '';

    public $filterSubject = '';

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $filterStatus = '';

    public $filterPensum = '';

    public $sessionsGradoFilter = '';

    public $dateRange = '365';

    // Sessions tab filters
    public $searchSessions = '';

    public $filterDateFrom = '';

    public $filterDateTo = '';

    // New filters
    public $filterDiagMainId = '';

    public $filterGradoId = '';

    public $filterSeccionId = '';

    public $list_grados;

    public $list_secciones = [];

    // AI Report properties
    public $selectedReport = null;

    public $showReportModal = false;

    public $isLoading = false;

    public $selected_ai_service = 'qwen';

    public $showSessionDetailsModal = false;

    public $showSessionAnswersModal = false;

    protected $selectedSessionAnswers = [];

    protected $selectedSessionData = null;

    protected $selectedStudentData = null;

    // Lapso actual resuelto (no serializable, se recalcula por request)
    protected $currentLapsoModel = null;

    protected $currentLapsoResolved = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterSubject' => ['except' => ''],
        'activeTab' => ['except' => 'dashboard'],
        'selectedPensumId' => ['except' => null],
        'sessionsGradoFilter' => ['except' => ''],
        'dateRange' => ['except' => '365'],
        'filterDiagMainId' => ['except' => ''],
        'filterGradoId' => ['except' => ''],
        'filterSeccionId' => ['except' => ''],
    ];

    protected $listeners = [
        'questionDeleted' => 'refreshQuestions',
        'sessionUpdated' => 'refreshSessions',
        'confirmDeleteQuestion',
        'deleteQuestion',
        'showStudentDetails',
        'showStudentSessions',
        'refreshAnalytics' => 'refreshAnalytics',
    ];

    public function mount()
    {
        $this->resetOptions();
        $this->profesor = Profesor::where('user_id', Auth::user()->id)->first();

        // El módulo se acota a la carga académica del lapso actual:
        // pevaluacions = profesor + lapso + seccion + pensum.
        $this->lapsoId = Lapso::current()?->id;

        $this->loadCargaAcademica();

        $this->cacheKey = 'diagnostics_'.Auth::id();
        $this->lastUpdated = now();
    }

    /**
     * Carga las pevaluacions (carga académica) del profesor para el lapso
     * actual y deriva de ahí los pensums, secciones y grados visibles.
     */
    protected function loadCargaAcademica(): void
    {
        if (! $this->profesor) {
            $this->cargaPevaluacions = collect();
            $this->pensumIds = [];
            $this->seccionIds = [];
            $this->list_grados = collect();

            return;
        }

        $query = Pevaluacion::with(['pensum.asignatura', 'pensum.grado', 'seccion', 'lapso'])
            ->where('profesor_id', $this->profesor->id);

        if ($this->lapsoId) {
            $query->where('lapso_id', $this->lapsoId);
        }

        $this->cargaPevaluacions = $query->get()
            ->filter(fn ($pevaluacion) => $pevaluacion->pensum !== null)
            ->values();

        $this->pensumIds = $this->cargaPevaluacions->pluck('pensum_id')
            ->filter()->unique()->map(fn ($id) => (int) $id)->values()->toArray();

        $this->seccionIds = $this->cargaPevaluacions->pluck('seccion_id')
            ->filter()->unique()->map(fn ($id) => (int) $id)->values()->toArray();

        $this->list_grados = $this->cargaPevaluacions
            ->map(fn ($pevaluacion) => $pevaluacion->pensum?->grado)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    /**
     * Pensums efectivos a consultar: el seleccionado en el filtro de área
     * (si pertenece a la carga) o todos los de la carga académica.
     */
    protected function scopedPensumIds(): array
    {
        if ($this->selectedPensumId && in_array((int) $this->selectedPensumId, $this->pensumIds, true)) {
            return [(int) $this->selectedPensumId];
        }

        return $this->pensumIds;
    }

    /**
     * Secciones efectivas a consultar: las de la carga académica,
     * opcionalmente acotadas por el filtro de sección.
     */
    protected function scopedSeccionIds(): array
    {
        if ($this->filterSeccionId && in_array((int) $this->filterSeccionId, $this->seccionIds, true)) {
            return [(int) $this->filterSeccionId];
        }

        return $this->seccionIds;
    }

    /**
     * Acota una consulta de sesiones al lapso actual. Las sesiones creadas
     * por el estudiante (Diagnostic::startDiagnostic) no guardan lapso_id, por
     * lo que se infiere desde la pevaluacion comparando la fecha de inicio
     * contra el rango del lapso (finicial - ffinal).
     */
    protected function currentLapso(): ?Lapso
    {
        if (! $this->currentLapsoResolved) {
            $this->currentLapsoModel = $this->lapsoId ? Lapso::find($this->lapsoId) : null;
            $this->currentLapsoResolved = true;
        }

        return $this->currentLapsoModel;
    }

    protected function scopeToLapso($query, string $dateColumn = 'iniciado_at'): void
    {
        $lapso = $this->currentLapso();

        if (! $lapso || ! $lapso->finicial || ! $lapso->ffinal) {
            return;
        }

        $finicial = $lapso->finicial;
        $ffinal = $lapso->ffinal;

        $query->where(function ($q) use ($dateColumn, $finicial, $ffinal) {
            $q->where('lapso_id', $this->lapsoId)
                ->orWhere(function ($qq) use ($dateColumn, $finicial, $ffinal) {
                    $qq->whereNull('lapso_id')
                        ->whereDate($dateColumn, '>=', $finicial)
                        ->whereDate($dateColumn, '<=', $ffinal);
                });
        });
    }

    /**
     * Restringe una consulta de sesiones (o de respuestas vía whereHas('session'))
     * a la carga académica: pensum + sección del estudiante + lapso.
     */
    protected function scopeToCarga($query, string $dateColumn = 'iniciado_at'): void
    {
        $pensumIds = $this->scopedPensumIds();

        if (empty($pensumIds)) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereIn('pensum_id', $pensumIds);

        $seccionIds = $this->scopedSeccionIds();
        if (! empty($seccionIds)) {
            $query->whereHas('estudiant.inscripcion', function ($q) use ($seccionIds) {
                $q->whereIn('seccion_id', $seccionIds);
            });
        }

        $this->scopeToLapso($query, $dateColumn);
    }

    public function updatedFilterDiagMainId()
    {
        $this->resetPage('sessionsPage');
    }

    public function updatedFilterGradoId()
    {
        $this->resetPage('sessionsPage');
        $this->filterSeccionId = '';
        $this->list_secciones = [];

        if ($this->filterGradoId) {
            $this->list_secciones = Seccion::where('grado_id', $this->filterGradoId)
                ->whereIn('id', $this->seccionIds)
                ->where('status_active', 'true')
                ->get();
        }
    }

    public function updatedFilterSeccionId()
    {
        $this->resetPage('sessionsPage');
    }

    public function getPensumIdsProperty()
    {
        return $this->scopedPensumIds();
    }

    protected function statsCacheKey(): string
    {
        return 'diag_prof_stats_'.Auth::id()
            .'_'.($this->lapsoId ?? 'none')
            .'_'.($this->selectedPensumId ?? 'all')
            .'_'.($this->filterDiagMainId ?: 'all')
            .'_'.($this->filterGradoId ?: 'all')
            .'_'.($this->filterSeccionId ?: 'all');
    }

    public function getStatsProperty()
    {
        return Cache::remember($this->statsCacheKey(), 1800, function () {
            $pensumIds = $this->scopedPensumIds();
            $seccionIds = $this->scopedSeccionIds();

            $accuracyStats = $this->getStudentAccuracyStats();

            // Sesiones de la carga académica (pensum + sección + lapso) y filtros.
            $sessionScope = function ($query) {
                $this->scopeToCarga($query);

                if ($this->filterDiagMainId) {
                    $query->where('diag_main_id', $this->filterDiagMainId);
                }
                if ($this->filterGradoId) {
                    $query->whereHas('pensum', function ($q) {
                        $q->where('grado_id', $this->filterGradoId);
                    });
                }
            };

            $questionQuery = DiagQuestion::whereIn('pensum_id', $pensumIds ?: [0]);
            if ($this->filterDiagMainId) {
                $questionQuery->where('diag_main_id', $this->filterDiagMainId);
            }
            if ($this->filterGradoId) {
                $questionQuery->whereHas('pensum', function ($q) {
                    $q->where('grado_id', $this->filterGradoId);
                });
            }

            return [
                'total_questions' => $questionQuery->count(),

                'total_sessions' => DiagSession::where($sessionScope)->count(),

                'completed_sessions' => DiagSession::whereNotNull('completado_at')
                    ->where($sessionScope)->count(),

                'active_sessions' => DiagSession::where('activo', true)
                    ->whereNull('completado_at')
                    ->where($sessionScope)->count(),

                'student_accuracy' => $accuracyStats['accuracy'] ?? 0,
                'correct_answers' => $accuracyStats['correct_answers'] ?? 0,
                'total_answered' => $accuracyStats['total_answered'] ?? 0,

                'students_with_sessions' => (int) DiagSession::where($sessionScope)
                    ->distinct()->count('estudiant_id'),

                'total_students' => empty($seccionIds) ? 0 : (int) Inscripcion::whereIn('seccion_id', $seccionIds)
                    ->distinct()->count('estudiant_id'),
            ];
        });
    }

    public function getSubjectsProperty()
    {
        $subjects = [];

        foreach ($this->cargaPevaluacions as $pevaluacion) {
            if ($pevaluacion->pensum && ! isset($subjects[$pevaluacion->pensum_id])) {
                $subjects[$pevaluacion->pensum_id] = $pevaluacion->pensum->asignatura?->full_name
                    ?? $pevaluacion->pensum->full_name;
            }
        }

        return collect($subjects);
    }

    public function updatedSelectedPensumId()
    {
        $this->resetPage();
        $this->clearCache();
        $this->dispatch('refreshCharts');
    }

    public function clearCache()
    {
        Cache::forget($this->statsCacheKey());
    }

    public function resetOptions()
    {
        $this->options = [
            ['opcion' => '', 'valor' => 0, 'orden' => 1],
            ['opcion' => '', 'valor' => 0, 'orden' => 2],
        ];
    }

    public function addOption()
    {
        if (count($this->options) < 6) {
            $this->options[] = [
                'opcion' => '',
                'valor' => 0,
                'orden' => count($this->options) + 1,
            ];
        }
    }

    public function removeOption($index)
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    public function rules()
    {
        $rules = [
            'pregunta' => 'required|string|min:10|max:500',
            'tipo_pregunta' => 'required|in:multiple,open,scale',
            'orden' => 'nullable|integer|min:1',
            'activo' => 'boolean',
            'weighing' => 'integer|min:1|max:5',
            'difficulty' => 'string',
            'pensum_id' => ['required', 'exists:pensums,id', Rule::in($this->pensumIds)],
        ];

        if ($this->tipo_pregunta === 'multiple') {
            $rules['options'] = 'required|array|min:2|max:6';
            $rules['options.*.opcion'] = 'required|string|max:200';
            $rules['options.*.valor'] = 'nullable|numeric';
            $rules['correct_option_index'] = 'required|integer|min:0';
        } elseif ($this->tipo_pregunta === 'scale') {
            $rules['min_value'] = 'required|integer|min:1|max:10';
            $rules['max_value'] = 'required|integer|min:2|max:10|gt:min_value';
        }

        return $rules;
    }

    protected $validationAttributes = [
        'pregunta' => 'pregunta',
        'tipo_pregunta' => 'tipo de pregunta',
        'pensum_id' => 'área de formación',
        'options' => 'opciones',
        'options.*.opcion' => 'opción',
        'correct_option_index' => 'opción correcta',
        'min_value' => 'valor mínimo',
        'max_value' => 'valor máximo',
        'weighing' => 'ponderación',
        'difficulty' => 'dificultad',
    ];

    public function openQuestionModal($questionId = null)
    {
        $this->resetForm();
        $this->wizardStep = 1;

        if ($questionId) {
            $this->editingQuestion = DiagQuestion::with('options')
                ->whereIn('pensum_id', $this->pensumIds ?: [0])
                ->find($questionId);

            if (! $this->editingQuestion) {
                $this->notification()->error(
                    'Pregunta no disponible',
                    'La pregunta no pertenece a su carga académica.'
                );

                return;
            }

            $this->pregunta = $this->editingQuestion->pregunta;
            $this->tipo_pregunta = $this->editingQuestion->tipo_pregunta;
            $this->orden = $this->editingQuestion->orden ?? 1;
            $this->activo = $this->editingQuestion->activo ?? true;
            $this->weighing = $this->editingQuestion->weighing ?? 1;
            $this->difficulty = $this->editingQuestion->difficulty ?? 'medium';
            $this->pensum_id = $this->editingQuestion->pensum_id;
            $this->diag_main_id = $this->editingQuestion->diag_main_id;

            if ($this->tipo_pregunta === 'multiple') {
                $this->options = $this->editingQuestion->options->map(function ($option, $index) {
                    return [
                        'opcion' => $option->opcion,
                        'valor' => $option->valor,
                        'orden' => $option->orden ?? $index + 1,
                    ];
                })->toArray();

                $correctIndex = $this->editingQuestion->options->search(function ($option) {
                    return $option->valor > 0;
                });
                $this->correct_option_index = $correctIndex !== false ? $correctIndex : 0;
            }
        } else {
            $this->pensum_id = $this->selectedPensumId;
            $this->resetOptions();
        }

        $this->showQuestionModal = true;
        $this->dispatch('refreshCharts');
    }

    /**
     * Abre un dialog (WireUI) con el detalle completo de la pregunta.
     */
    public function showQuestionDetails($questionId)
    {
        $question = DiagQuestion::with(['options', 'pensum.asignatura', 'diagMain', 'competency', 'indicator'])
            ->whereIn('pensum_id', $this->pensumIds ?: [0])
            ->find($questionId);

        if (! $question) {
            $this->notification()->error(
                'Pregunta no disponible',
                'La pregunta no pertenece a su carga académica.'
            );

            return;
        }

        $this->dialog()->show([
            'icon' => 'info',
            'style' => 'center',
            'title' => 'Detalle de la pregunta',
            'description' => view('livewire.profesor.diagnostics.partials.question-details', [
                'question' => $question,
            ])->render(),
        ]);
    }

    /**
     * Genera una pregunta de diagnóstico con IA (OpenRouter) enriquecida con
     * las actividades del lapso y los referentes/competencias/indicadores del
     * área de formación seleccionada.
     */
    public function generateQuestionWithAi()
    {
        if (! $this->pensum_id || ! in_array((int) $this->pensum_id, $this->pensumIds, true)) {
            $this->notification()->warning(
                'Área requerida',
                'Selecciona primero el área de formación y el tipo de pregunta.'
            );

            return;
        }

        $this->generatingQuestion = true;

        try {
            $pensum = Pensum::with(['asignatura', 'grado', 'pestudio'])->find($this->pensum_id);

            if (! $pensum) {
                $this->notification()->error(
                    'Área no disponible',
                    'No se encontró el área de formación seleccionada.'
                );

                return;
            }

            $result = app(OpenRouterService::class)->ask(
                $this->buildQuestionSystemPrompt(),
                $this->buildQuestionUserPrompt($pensum),
                ['max_tokens' => 1500, 'temperature' => 0.7, 'timeout' => 120],
            );

            if (! ($result['success'] ?? false)) {
                $this->notification()->error(
                    'Error al generar',
                    $result['error'] ?? 'No se pudo generar la pregunta.'
                );

                return;
            }

            $payload = $this->parseQuestionAiPayload($result['content'] ?? null);

            if (! $payload) {
                $this->notification()->error(
                    'Respuesta inválida',
                    'La IA no devolvió una pregunta con formato válido. Intenta nuevamente.'
                );

                return;
            }

            $this->applyQuestionAiPayload($payload);

            $this->notification()->success(
                'Pregunta generada',
                'Revisa y ajusta la pregunta antes de guardarla.'
            );
        } catch (\Throwable $e) {
            Log::error('Diagnostics AI question generation failed', [
                'user_id' => Auth::id(),
                'pensum_id' => $this->pensum_id,
                'error' => $e->getMessage(),
            ]);

            $this->notification()->error('Error inesperado', $e->getMessage());
        } finally {
            $this->generatingQuestion = false;
        }
    }

    private function buildQuestionSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres docente venezolano experto en evaluación diagnóstica.
Generas preguntas de diagnóstico de alta calidad pedagógica, alineadas al área de formación, al grado y a los referentes, competencias e indicadores entregados.

REGLAS:
- Español formal, claro y preciso; lenguaje pedagógico.
- Una sola pregunta por respuesta, contextualizada y con valor diagnóstico.
- Si el tipo es "multiple": 4 opciones plausibles, solo una correcta, e incluye "correct_index" (0-3).
- Si el tipo es "open": incluye "expected_answer" con criterios de evaluación.
- Si el tipo es "scale": incluye "min_value" y "max_value" (rango 1-5).
- Evita repetir preguntas ya registradas.
- Responde EXCLUSIVAMENTE con un objeto JSON válido, sin markdown ni texto adicional.

FORMATO JSON:
{"pregunta":"...","options":["...","...","...","..."],"correct_index":0,"expected_answer":"...","min_value":1,"max_value":5}
PROMPT;
    }

    private function buildQuestionUserPrompt(Pensum $pensum): string
    {
        $asignatura = $pensum->asignatura?->name ?? $pensum->full_name ?? '—';
        $grado = $pensum->grado?->name ?? '—';

        $tipoLabel = match ($this->tipo_pregunta) {
            'multiple' => 'Selección múltiple (una sola respuesta correcta)',
            'open' => 'Pregunta abierta (respuesta libre)',
            'scale' => 'Escala de valoración',
            default => (string) $this->tipo_pregunta,
        };

        $activitiesText = $this->questionActivitiesContext($pensum);
        $referentsText = $this->questionReferentsContext($pensum);

        return <<<PROMPT
### Área de formación
{$asignatura} · {$grado}

### Tipo de pregunta solicitado
{$tipoLabel}

### Actividades y contenidos del lapso
{$activitiesText}

### Referentes, competencias e indicadores
{$referentsText}

Genera la pregunta de diagnóstico.
PROMPT;
    }

    /**
     * Contexto de actividades (LMS) del área de formación para el lapso actual.
     */
    private function questionActivitiesContext(Pensum $pensum): string
    {
        $activities = Activity::query()
            ->whereHas('pevaluacion', function ($q) use ($pensum) {
                $q->where('pensum_id', $pensum->id);

                if ($this->lapsoId) {
                    $q->where('lapso_id', $this->lapsoId);
                }

                if ($this->profesor) {
                    $q->where('profesor_id', $this->profesor->id);
                }
            })
            ->latest('created_at')
            ->limit(8)
            ->get(['topic', 'thematic', 'description', 'teaching', 'learning', 'references', 'observations']);

        if ($activities->isEmpty()) {
            return '—';
        }

        return $activities->map(function (Activity $activity): string {
            return collect([
                'Tema generador' => $activity->topic,
                'Tejido temático' => $activity->thematic,
                'Actividad evaluativa' => $activity->description,
                'Enseñanza' => $activity->teaching,
                'Aprendizaje' => $activity->learning,
                'Referentes teóricos' => $activity->references,
                'ODS/Sistematización' => $activity->observations,
            ])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value, $key) => "  - {$key}: ".mb_substr(trim((string) $value), 0, 300))
                ->implode("\n");
        })->implode("\n");
    }

    /**
     * Contexto de referentes → competencias → indicadores del área.
     */
    private function questionReferentsContext(Pensum $pensum): string
    {
        $referents = DiagReferent::with([
            'competencies' => fn ($q) => $q->where('pensum_id', $pensum->id),
            'competencies.indicators',
        ])
            ->where('pestudio_id', $pensum->pestudio_id)
            ->where('active', true)
            ->get();

        if ($referents->isEmpty()) {
            return '—';
        }

        return $referents->map(function (DiagReferent $referent): string {
            $lines = ["Referente: {$referent->name} ({$referent->code})"];

            foreach ($referent->competencies as $competency) {
                $lines[] = '  Competencia: '.mb_substr(trim((string) $competency->name), 0, 200);

                foreach ($competency->indicators as $indicator) {
                    $lines[] = '    Indicador: '.mb_substr(trim((string) $indicator->description), 0, 200);
                }
            }

            return implode("\n", $lines);
        })->implode("\n");
    }

    private function parseQuestionAiPayload(?string $content): ?array
    {
        $content = trim((string) $content);

        if ($content === '') {
            return null;
        }

        // Quita fences de markdown ```json ... ```
        $content = trim((string) preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content));

        $data = json_decode($content, true);

        if (! is_array($data) && preg_match('/\{.*\}/s', $content, $matches)) {
            $data = json_decode($matches[0], true);
        }

        if (! is_array($data) || blank($data['pregunta'] ?? null)) {
            return null;
        }

        return $data;
    }

    private function applyQuestionAiPayload(array $payload): void
    {
        $this->pregunta = mb_substr(trim((string) $payload['pregunta']), 0, 500);

        if ($this->tipo_pregunta === 'multiple') {
            $options = collect($payload['options'] ?? [])
                ->map(fn ($option) => is_array($option) ? ($option['opcion'] ?? '') : (string) $option)
                ->map(fn ($option) => mb_substr(trim($option), 0, 200))
                ->filter()
                ->take(6)
                ->values();

            if ($options->count() < 2) {
                $options = collect(['', '']);
            }

            $this->options = $options->map(fn ($opcion, $index) => [
                'opcion' => $opcion,
                'valor' => 0,
                'orden' => $index + 1,
            ])->all();

            $correct = (int) ($payload['correct_index'] ?? 0);
            $this->correct_option_index = max(0, min($correct, count($this->options) - 1));
        } elseif ($this->tipo_pregunta === 'open') {
            $this->expected_answer = mb_substr(trim((string) ($payload['expected_answer'] ?? '')), 0, 1000);
        } elseif ($this->tipo_pregunta === 'scale') {
            $min = (int) ($payload['min_value'] ?? 1);
            $max = (int) ($payload['max_value'] ?? 5);

            $this->min_value = max(1, min(9, $min));
            $this->max_value = max($this->min_value + 1, min(10, $max));
        }
    }

    public function saveQuestion()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->editingQuestion) {
                $question = $this->editingQuestion;
                $question->update([
                    'pregunta' => $this->pregunta,
                    'tipo_pregunta' => $this->tipo_pregunta,
                    'pensum_id' => $this->pensum_id,
                    'diag_main_id' => $this->diag_main_id,
                    'orden' => $this->orden,
                    'weighing' => $this->weighing,
                    'difficulty' => $this->difficulty,
                    'activo' => $this->activo,
                ]);
            } else {
                $nextOrder = DiagQuestion::where('pensum_id', $this->pensum_id)->max('orden') + 1;

                $question = DiagQuestion::create([
                    'pregunta' => $this->pregunta,
                    'tipo_pregunta' => $this->tipo_pregunta,
                    'pensum_id' => $this->pensum_id,
                    'diag_main_id' => $this->diag_main_id,
                    'orden' => $this->orden ?: $nextOrder,
                    'activo' => $this->activo,
                    'weighing' => $this->weighing,
                    'difficulty' => $this->difficulty,
                ]);
            }

            if ($this->tipo_pregunta === 'multiple') {
                if ($this->editingQuestion) {
                    $question->options()->delete();
                }

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
            $this->clearCache();
            $this->closeQuestionModal();

            $this->notification()->success(
                $this->editingQuestion ? 'Pregunta actualizada' : 'Pregunta creada',
                'La pregunta se ha guardado correctamente.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(
                'Error',
                'Ocurrió un error al guardar la pregunta: '.$e->getMessage()
            );
        }
    }

    public function nextStep()
    {
        try {
            $this->validateStep();
            if ($this->wizardStep < 3) {
                $this->wizardStep++;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }
    }

    public function prevStep()
    {
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= 3) {
            $this->wizardStep = $step;
        }
    }

    public function validateStep()
    {
        if ($this->wizardStep === 1) {
            $this->validate([
                'pensum_id' => ['required', 'exists:pensums,id', Rule::in($this->pensumIds)],
                'tipo_pregunta' => 'required|in:multiple,open,scale',
            ], [
                'pensum_id.required' => 'Debe seleccionar un área de formación.',
                'pensum_id.exists' => 'El área de formación seleccionada no es válida.',
                'pensum_id.in' => 'El área de formación no pertenece a su carga académica.',
                'tipo_pregunta.required' => 'Debe seleccionar un tipo de pregunta.',
                'tipo_pregunta.in' => 'El tipo de pregunta seleccionado no es válido.',
            ]);
        } elseif ($this->wizardStep === 2) {
            $rules = [
                'pregunta' => 'required|string|min:10|max:500',
            ];

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

    public function closeQuestionModal()
    {
        $this->showQuestionModal = false;
        $this->wizardStep = 1;
        $this->resetForm();
        $this->dispatch('refreshCharts');
    }

    public function closeSessionModalReport()
    {
        $this->SessionModalReport = false;
        $this->selectedSession = null;
    }

    public function resetSessionFilters()
    {
        $this->searchSessions = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterGradoId = '';
        $this->filterSeccionId = '';
        $this->filterDiagMainId = '';
        $this->resetPage('sessionsPage');
    }

    public function viewSession($sessionId)
    {
        $this->openSessionModal($sessionId);
    }

    public function openAiReport($estudiantId, $diagMainId)
    {
        $this->getAIReport($estudiantId, $diagMainId);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->dispatch('refreshCharts');

        if ($tab === 'analytics') {
            $this->clearCache();
            $this->dispatch('refreshAnalytics');
        }
    }

    public function confirmDeleteQuestion($questionId)
    {
        $this->notification()->confirm([
            'title' => '¿Eliminar pregunta?',
            'description' => 'Esta acción no se puede deshacer.',
            'acceptLabel' => 'Eliminar',
            'rejectLabel' => 'Cancelar',
            'method' => 'deleteQuestion',
            'params' => [$questionId],
        ]);
    }

    public function deleteQuestion($questionId)
    {
        try {
            DB::beginTransaction();

            $question = DiagQuestion::whereIn('pensum_id', $this->pensumIds ?: [0])
                ->findOrFail($questionId);

            $hasAnswers = DiagAnswer::where('question_id', $questionId)->exists();

            if ($hasAnswers) {
                $this->notification()->warning(
                    'No se puede eliminar',
                    'Esta pregunta tiene respuestas asociadas y no puede ser eliminada.'
                );

                return;
            }

            $question->delete();

            DB::commit();
            $this->clearCache();

            $this->notification()->success(
                'Pregunta eliminada',
                'La pregunta se ha eliminado correctamente.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(
                'Error',
                'Ocurrió un error al eliminar la pregunta: '.$e->getMessage()
            );
        }
    }

    private function resetForm()
    {
        $this->editingQuestion = null;
        $this->pregunta = '';
        $this->tipo_pregunta = 'multiple';
        $this->orden = 1;
        $this->activo = true;
        $this->correct_option_index = 0;
        $this->min_value = 1;
        $this->max_value = 10;
        $this->weighing = 1;
        $this->difficulty = 'medium';
        $this->pensum_id = null;
        $this->diag_main_id = null;
        $this->expected_answer = '';
        $this->resetOptions();
        $this->resetValidation();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterSubject = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function openSessionModal($sessionId)
    {
        $this->selectedSession = $sessionId;
        $this->SessionModalReport = true;
    }

    public function showStudentDetails($estudiantId)
    {
        $student = Estudiant::find($estudiantId);

        $sessions = DiagSession::with(['pensum.grado', 'answers'])
            ->where('estudiant_id', $estudiantId)
            ->where('iniciado_at', '>=', now()->subDays($this->dateRange));

        if ($this->sessionsGradoFilter) {
            $sessions->whereHas('pensum', function ($q) {
                $q->where('grado_id', $this->sessionsGradoFilter);
            });
        }

        $this->scopeToCarga($sessions);

        $studentSessions = $sessions->get();

        $pensumStats = [];
        $totalSessions = $studentSessions->count();
        $completedSessions = $studentSessions->where('completado_at', '!=', null)->count();

        foreach ($studentSessions->groupBy('pensum_id') as $pensumId => $pensumSessions) {
            $pensum = $pensumSessions->first()->pensum;

            $totalQuestionsForPensum = DiagQuestion::where('pensum_id', $pensumId)
                ->where('activo', 1)
                ->count();

            $answeredQuestionsForPensum = DiagAnswer::where('estudiant_id', $estudiantId)
                ->whereHas('question', function ($q) use ($pensumId) {
                    $q->where('pensum_id', $pensumId)->where('activo', 1);
                })
                ->whereNotNull('completado_at')
                ->distinct('question_id')
                ->count('question_id');

            $pensumStats[$pensumId] = [
                'pensum' => $pensum,
                'total_questions' => $totalQuestionsForPensum,
                'answered_questions' => $answeredQuestionsForPensum,
                'progress' => $totalQuestionsForPensum > 0
                    ? round(($answeredQuestionsForPensum * 100.0) / $totalQuestionsForPensum, 1)
                    : 0,
                'sessions_count' => $pensumSessions->count(),
                'completed_sessions' => $pensumSessions->where('completado_at', '!=', null)->count(),
            ];
        }

        $totalQuestions = collect($pensumStats)->sum('total_questions');
        $answeredQuestions = collect($pensumStats)->sum('answered_questions');
        $overallProgress = $totalQuestions > 0
            ? round(($answeredQuestions * 100.0) / $totalQuestions, 1)
            : 0;

        $recentSession = $studentSessions->sortByDesc('iniciado_at')->first();

        $completedSessionsWithDuration = $studentSessions->filter(function ($session) {
            return $session->completado_at && $session->iniciado_at;
        });

        $avgDuration = $completedSessionsWithDuration->count() > 0
            ? $completedSessionsWithDuration->avg(function ($session) {
                return Carbon::parse($session->iniciado_at)
                    ->diffInMinutes(Carbon::parse($session->completado_at));
            })
            : 0;

        $this->selectedSession = $estudiantId;
        $this->selectedSessionData = (object) [
            'estudiant_id' => $estudiantId,
            'estudiant' => $student,
            'pensum' => $recentSession ? $recentSession->pensum : null,
            'session_progress' => $overallProgress,
            'total_preguntas' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'iniciado_at' => $recentSession ? $recentSession->iniciado_at : null,
            'completado_at' => $completedSessions == $totalSessions ? $studentSessions->max('completado_at') : null,
            'activo' => $totalSessions > $completedSessions,
            'last_session_date' => $recentSession ? $recentSession->iniciado_at : null,
            'avg_duration_minutes' => round($avgDuration, 1),
            'pensum_stats' => $pensumStats,
            'unique_pensums' => count($pensumStats),
        ];

        $this->showSessionDetailsModal = true;
    }

    public function showStudentSessions($estudiantId)
    {
        $student = Estudiant::find($estudiantId);

        $this->selectedSession = $estudiantId;
        $this->selectedStudentData = (object) [
            'estudiant_id' => $estudiantId,
            'estudiant' => $student,
        ];

        $allAnswers = DiagAnswer::with(['question.options', 'question.pensum', 'selectedOption'])
            ->where('estudiant_id', $estudiantId)
            ->whereNotNull('completado_at')
            ->whereHas('question', function ($query) {
                $query->where('activo', 1)
                    ->whereIn('pensum_id', $this->scopedPensumIds());
            })
            ->whereHas('session', function ($query) {
                $this->scopeToCarga($query);
            })
            ->orderBy('completado_at')
            ->get();

        $answersGroupedByPensum = [];

        foreach ($allAnswers->groupBy('question.pensum.id') as $pensumId => $answers) {
            $pensum = $answers->first()->question->pensum;
            $totalQuestions = DiagQuestion::where('pensum_id', $pensumId)->where('tipo_pregunta', 'multiple')->where('activo', 1)->count();
            $answeredQuestions = $answers->count();
            $progress = $totalQuestions > 0 ? round(($answeredQuestions * 100.0) / $totalQuestions, 1) : 0;

            $correctAnswers = $answers->filter(function ($answer) {
                return $answer->isCorrect();
            })->count();

            $answersGroupedByPensum[$pensumId] = [
                'pensum' => $pensum,
                'pensum_name' => $pensum->full_name ?? $pensum->name,
                'total_questions' => $totalQuestions,
                'answered_questions' => $answeredQuestions,
                'correct_answers' => $correctAnswers,
                'accuracy' => $answeredQuestions > 0 ? round(($correctAnswers * 100.0) / $answeredQuestions, 1) : 0,
                'progress' => $progress,
                'answers' => $answers->sortBy('completado_at'),
            ];
        }

        $this->selectedSessionAnswers = collect($answersGroupedByPensum);
        $this->showSessionAnswersModal = true;
    }

    public function closeSessionDetailsModal()
    {
        $this->showSessionDetailsModal = false;
        $this->selectedSession = null;
        $this->selectedSessionData = null;
    }

    public function closeSessionAnswersModal()
    {
        $this->showSessionAnswersModal = false;
        $this->selectedSession = null;
        $this->selectedSessionAnswers = [];
        $this->selectedStudentData = null;
    }

    public function render()
    {
        $pensumIds = $this->scopedPensumIds();

        $questions = $this->getQuestionsPaginationView();

        $sessionsQuery = DiagSession::with(['estudiant:id,name,lastname', 'estudiant.inscripcion.seccion.grado', 'pensum.asignatura:id,name', 'diagMain', 'answers'])
            ->select(['id', 'estudiant_id', 'pensum_id', 'diag_main_id', 'iniciado_at', 'completado_at', 'progreso', 'total_preguntas', 'activo']);

        $this->scopeToCarga($sessionsQuery);

        if ($this->filterDiagMainId) {
            $sessionsQuery->where('diag_main_id', $this->filterDiagMainId);
        }

        if ($this->filterGradoId) {
            $sessionsQuery->whereHas('pensum', function ($q) {
                $q->where('grado_id', $this->filterGradoId);
            });
        }

        $sessions = $sessionsQuery
            ->when($this->filterStatus, function ($query) {
                if ($this->filterStatus === 'completed') {
                    $query->whereNotNull('completado_at');
                } elseif ($this->filterStatus === 'in_progress') {
                    $query->where('activo', true)->whereNull('completado_at');
                } elseif ($this->filterStatus === 'abandoned') {
                    $query->where('activo', false)->whereNull('completado_at');
                }
            })
            ->when($this->filterPensum, function ($query) {
                $query->where('pensum_id', $this->filterPensum);
            })
            ->latest('iniciado_at')
            ->paginate(10);

        $stats = $this->getStatsProperty();

        $generalStats = [
            'total_sessions' => $stats['total_sessions'] ?? 0,
            'completed_sessions' => $stats['completed_sessions'] ?? 0,
        ];

        $selectedSessionObject = null;
        if ($this->selectedSession && $this->SessionModalReport) {
            $selectedSessionObject = DiagSession::with(['answers.question', 'answers.selectedOption'])
                ->whereIn('pensum_id', $pensumIds ?: [0])
                ->find($this->selectedSession);
        }

        $allSessionsQuery = DiagSession::query();
        $this->scopeToCarga($allSessionsQuery);

        $cargaPensums = $this->cargaPevaluacions
            ->map(fn ($pevaluacion) => $pevaluacion->pensum)
            ->filter()
            ->unique('id')
            ->sortBy(fn ($pensum) => $pensum->asignatura?->name ?? $pensum->full_name)
            ->values();

        return view('livewire.profesor.diagnostics.index-component', [
            'questions' => $questions,
            'sessions' => $sessions,
            'stats' => $stats,
            'subjects' => $this->subjects,
            'allQuestions' => DiagQuestion::whereIn('pensum_id', $pensumIds ?: [0])->get(),
            'allSessions' => $allSessionsQuery->get(),
            'allAnswers' => DiagAnswer::whereHas('question', function ($q) use ($pensumIds) {
                $q->whereIn('pensum_id', $pensumIds ?: [0]);
            })->whereHas('session', function ($q) {
                $this->scopeToCarga($q);
            })->with(['selectedOption', 'question'])->get(),
            'questionTypes' => ['multiple', 'open', 'scale'],
            'profesor' => $this->profesor,
            'cargaPensums' => $cargaPensums,
            'lapso' => $this->currentLapso(),
            'generalStats' => $generalStats,
            'showSessionDetailsModal' => $this->showSessionDetailsModal,
            'showSessionAnswersModal' => $this->showSessionAnswersModal,
            'selectedSessionAnswers' => $this->selectedSessionAnswers,
            'selectedSessionData' => $this->selectedSessionData,
            'selectedStudentData' => $this->selectedStudentData,
            'selectedSession' => $selectedSessionObject,
            'diagMains' => DiagMain::query()
                ->when($this->lapsoId, function ($query) {
                    $query->where(function ($q) {
                        $q->where('lapso_id', $this->lapsoId)->orWhereNull('lapso_id');
                    });
                })
                ->get(),
            'diagMainCurrent' => DiagMain::find($this->filterDiagMainId),
            'list_grados' => $this->list_grados,
            'list_secciones' => $this->list_secciones,
        ]);
    }

    private function getQuestionsPaginationView()
    {
        $pensumIds = $this->scopedPensumIds();

        $questions = DiagQuestion::with(['options', 'pensum.asignatura'])
            ->whereIn('pensum_id', $pensumIds ?: [0])
            ->when($this->search, function ($query) {
                $query->where('pregunta', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterType, function ($query) {
                $query->where('tipo_pregunta', $this->filterType);
            })
            ->when($this->filterSubject, function ($query) {
                $query->where('pensum_id', $this->filterSubject);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10, ['*'], 'page');

        return $questions;
    }

    private function getStudentAccuracyStats()
    {
        try {
            $pensumIds = $this->scopedPensumIds();

            if (empty($pensumIds)) {
                return [
                    'accuracy' => 0,
                    'correct_answers' => 0,
                    'total_answered' => 0,
                ];
            }

            $query = DiagAnswer::with(['selectedOption', 'question.pensum'])
                ->whereNotNull('completado_at')
                ->whereNotNull('option_id')
                ->whereHas('session', function ($q) {
                    $this->scopeToCarga($q);

                    if ($this->filterDiagMainId) {
                        $q->where('diag_main_id', $this->filterDiagMainId);
                    }

                    if ($this->filterGradoId) {
                        $q->whereHas('pensum', function ($qq) {
                            $qq->where('grado_id', $this->filterGradoId);
                        });
                    }
                })
                ->whereHas('question', function ($q) use ($pensumIds) {
                    $q->where('activo', 1)
                        ->where('tipo_pregunta', 'multiple')
                        ->whereIn('pensum_id', $pensumIds);
                });

            $answers = $query->get();

            if ($answers->isEmpty()) {
                return [
                    'accuracy' => 0,
                    'correct_answers' => 0,
                    'total_answered' => 0,
                ];
            }

            $correctAnswers = $answers->filter(function ($answer) {
                return $answer->isCorrect();
            })->count();

            $totalAnswered = $answers->count();
            $accuracy = $totalAnswered > 0 ? round((100 * $correctAnswers) / $totalAnswered, 2) : 0;

            return [
                'accuracy' => $accuracy,
                'correct_answers' => $correctAnswers,
                'total_answered' => $totalAnswered,
            ];
        } catch (Exception $e) {
            Log::error('Error in getStudentAccuracyStats (Professor): '.$e->getMessage());

            return [
                'accuracy' => 0,
                'correct_answers' => 0,
                'total_answered' => 0,
            ];
        }
    }

    public function refreshAnalytics()
    {
        $this->clearCache();
        $this->dispatch('refreshCharts');
    }

    // ========================================
    // AI Report Generation Methods
    // ========================================

    public function getAIReport($estudiantId, $diagMainId)
    {
        $this->isLoading = true;

        try {
            $report = DiagReport::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $diagMainId)
                ->first();

            if ($report) {
                $this->viewReport($report->id);
            } else {
                $this->notification()->warning(
                    'Reporte no disponible',
                    'El reporte AI no ha sido generado aún para este estudiante.'
                );
            }
        } catch (Exception $e) {
            Log::error('Error getting AI report (Profesor): '.$e->getMessage());
            $this->notification()->error(
                'Error',
                'Ocurrió un error al buscar el reporte.'
            );
        } finally {
            $this->isLoading = false;
        }
    }

    public function viewReport($reportId)
    {
        try {
            $report = DiagReport::with(['estudiant', 'diagMain', 'referent', 'latestDraft'])
                ->find($reportId);

            if ($report) {
                $draftRaw = $report->latestDraft->output_text ?? '{}';
                $draftData = json_decode($draftRaw, true);

                if (! is_array($draftData)) {
                    $draftData = [];
                }

                if (isset($draftData['areas']) && is_array($draftData['areas'])) {
                    $filteredAreas = [];

                    foreach ($draftData['areas'] as $area) {
                        $pensumId = substr($area['id'], 5);
                        if (isset($area['id']) && in_array($pensumId, $this->pensumIds)) {
                            $filteredAreas[] = $area;
                        }
                    }

                    $draftData['areas'] = $filteredAreas;
                }

                if (isset($draftData['contrast']) && is_array($draftData['contrast'])) {
                    $filteredContrast = [];

                    foreach ($draftData['contrast'] as $contrast) {
                        if (isset($contrast['pensum_id']) && in_array($contrast['pensum_id'], $this->pensumIds)) {
                            $filteredContrast[] = $contrast;
                        }
                    }

                    $draftData['contrast'] = $filteredContrast;
                }

                $report->latestDraft->output_text = json_encode($draftData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

                $this->selectedReport = $report;
                $this->SessionModalReport = true;

                Log::info('Report viewed (Profesor)', [
                    'report_id' => $reportId,
                    'pensum_ids' => $this->pensumIds,
                    'filtered_areas_count' => count($draftData['areas'] ?? []),
                ]);
            } else {
                $this->notification()->error(
                    'Error',
                    'No se encontró el reporte solicitado.'
                );
            }
        } catch (Exception $e) {
            Log::error('Error viewing report (Profesor): '.$e->getMessage());
            $this->notification()->error(
                'Error',
                'Ocurrió un error al cargar el reporte.'
            );
        }
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->selectedReport = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterSubject()
    {
        $this->resetPage();
    }
}
