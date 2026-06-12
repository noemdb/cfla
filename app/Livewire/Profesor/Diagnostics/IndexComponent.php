<?php

namespace App\Livewire\Profesor\Diagnostics;

use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportAiDraft;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pescolar;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Entity\Institucion;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Learner\Estudiant;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions, WithPagination;
    use \App\Http\Livewire\Evaluacion\Diagnostic\QwenReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\DeepSeekReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\GeminiReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\OpenRouterReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\NvidiaReportTrait;

    public $cacheKey;
    public $lastUpdated;

    // Propiedades principales
    public $activeTab = 'dashboard';
    public $showQuestionModal = false;
    public $SessionModalReport = false;
    public $editingQuestion = null;
    public $selectedSession = null;
    public $wizardStep = 1;

    public $selectedPensumId = null;
    public $profesor = null;
    public $pensumIds = [];

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
        $this->pensumIds = $this->profesor?->pensums?->pluck('id')->toArray() ?? [];
        $this->cacheKey = 'diagnostics_' . Auth::id();
        $this->lastUpdated = now();

        // Load grados for filter grade for profesor
        $this->list_grados = Profesor::list_grado($this->profesor->id);
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
        if ($this->selectedPensumId) {
            return [$this->selectedPensumId];
        }
        return $this->profesor ? $this->profesor->pensums->pluck('id')->toArray() : [];
    }

    public function getStatsProperty()
    {
        $filtersKey = "_{$this->filterDiagMainId}_{$this->filterGradoId}_{$this->filterSeccionId}";
        $cacheKey = "stats_" . Auth::id() . "_" . ($this->selectedPensumId ?? 'all') . $filtersKey;

        return Cache::remember($cacheKey, 1800, function () {
            $pensumIds = $this->pensumIds;

            $accuracyStats = $this->getStudentAccuracyStats();

            $applyFilters = function ($query) {
                if ($this->filterDiagMainId) {
                    $query->where('diag_main_id', $this->filterDiagMainId);
                }
                if ($this->filterGradoId) {
                    $query->whereHas('pensum', function ($q) {
                        $q->where('grado_id', $this->filterGradoId);
                    });
                }
                if ($this->filterSeccionId) {
                    $query->whereHas('estudiant.inscripcion', function ($q) {
                        $q->where('seccion_id', $this->filterSeccionId);
                    });
                }
            };

            return [
                'total_questions' => DiagQuestion::whereIn('pensum_id', $pensumIds)
                    ->when($this->filterGradoId, function ($q) {
                        $q->whereHas('pensum', function ($qq) {
                            $qq->where('grado_id', $this->filterGradoId);
                        });
                    })->count(),

                'total_sessions' => DiagSession::whereIn('pensum_id', $pensumIds)
                    ->where(function ($q) use ($applyFilters) {
                        $applyFilters($q);
                    })->count(),

                'completed_sessions' => DiagSession::whereIn('pensum_id', $pensumIds)
                    ->whereNotNull('completado_at')
                    ->where(function ($q) use ($applyFilters) {
                        $applyFilters($q);
                    })->count(),

                'active_sessions' => DiagSession::whereIn('pensum_id', $pensumIds)
                    ->where('activo', true)
                    ->whereNull('completado_at')
                    ->where(function ($q) use ($applyFilters) {
                        $applyFilters($q);
                    })->count(),

                'student_accuracy' => $accuracyStats['accuracy'] ?? 0,
                'correct_answers' => $accuracyStats['correct_answers'] ?? 0,
                'total_answered' => $accuracyStats['total_answered'] ?? 0,
            ];
        });
    }

    public function getSubjectsProperty()
    {
        return $this->profesor && $this->profesor->pensums
            ? $this->profesor->pensums->pluck('asignatura.full_name', 'id')
            : collect();
    }

    public function updatedSelectedPensumId()
    {
        $this->resetPage();
        $this->clearCache();
        $this->dispatch('refreshCharts');
    }

    public function clearCache()
    {
        Cache::forget("stats_" . Auth::id() . "_" . ($this->selectedPensumId ?? 'all'));
        Cache::forget("stats_" . Auth::id() . "_all");
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
            'pensum_id' => 'required|exists:pensums,id',
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
            $this->editingQuestion = DiagQuestion::with('options')->find($questionId);
            $this->pregunta = $this->editingQuestion->pregunta;
            $this->tipo_pregunta = $this->editingQuestion->tipo_pregunta;
            $this->orden = $this->editingQuestion->orden ?? 1;
            $this->activo = $this->editingQuestion->activo ?? true;
            $this->weighing = $this->editingQuestion->weighing ?? 1;
            $this->difficulty = $this->editingQuestion->difficulty ?? 'medium';
            $this->pensum_id = $this->editingQuestion->pensum_id;

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
                    if (!empty($option['opcion'])) {
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

                if (!empty($optionsData)) {
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
                'Ocurrió un error al guardar la pregunta: ' . $e->getMessage()
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

    public function validateStep()
    {
        if ($this->wizardStep === 1) {
            $this->validate([
                'pensum_id' => 'required|exists:pensums,id',
                'tipo_pregunta' => 'required|in:multiple,open,scale',
            ], [
                'pensum_id.required' => 'Debe seleccionar un área de formación.',
                'pensum_id.exists' => 'El área de formación seleccionada no es válida.',
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
                $rules['correct_option_index'] = 'required|integer|min:0|max:' . (count($this->options) - 1);

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

            $question = DiagQuestion::findOrFail($questionId);

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
                'Ocurrió un error al eliminar la pregunta: ' . $e->getMessage()
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

        $sessions->whereIn('pensum_id', $this->pensumIds);

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
                    ->whereIn('pensum_id', $this->pensumIds);
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

    private function getSessionsPaginated()
    {
        try {
            $query = DiagSession::with(['estudiant', 'pensum.grado', 'answers'])
                ->select('estudiant_id')
                ->selectRaw('
                    COUNT(DISTINCT diag_sessions.id) as total_sessions,
                    COUNT(DISTINCT CASE WHEN completado_at IS NOT NULL THEN diag_sessions.id END) as completed_sessions,
                    MAX(iniciado_at) as last_session_date,
                    MIN(iniciado_at) as first_session_date,
                    SUM(CASE WHEN completado_at IS NOT NULL THEN 1 ELSE 0 END) as sessions_completed,
                    AVG(CASE
                        WHEN completado_at IS NOT NULL AND iniciado_at IS NOT NULL
                        THEN TIMESTAMPDIFF(MINUTE, iniciado_at, completado_at)
                        ELSE NULL
                    END) as avg_duration_minutes
                ')
                ->whereIn('pensum_id', $this->pensumIds);

            if ($this->filterDiagMainId) {
                $query->where('diag_sessions.diag_main_id', $this->filterDiagMainId);
            }

            if ($this->filterGradoId) {
                $query->whereHas('pensum', function ($q) {
                    $q->where('grado_id', $this->filterGradoId);
                });
            }

            if ($this->filterSeccionId) {
                $query->whereHas('estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }

            if ($this->sessionsGradoFilter) {
                $query->whereHas('pensum', function ($q) {
                    $q->where('grado_id', $this->sessionsGradoFilter);
                });
            }

            $dateFilter = now()->subDays($this->dateRange);
            $query->where('diag_sessions.iniciado_at', '>=', $dateFilter);

            $students = $query->groupBy('estudiant_id')
                ->orderBy('last_session_date', 'desc')
                ->paginate(15, ['*'], 'sessionsPage');

            $students->getCollection()->transform(function ($studentData) {
                try {
                    $student = Estudiant::find($studentData->estudiant_id);

                    if ($student) {
                        $studentData->estudiant = $student;

                        $sessions = DiagSession::with(['pensum.grado', 'answers'])
                            ->where('estudiant_id', $studentData->estudiant_id)
                            ->whereIn('pensum_id', $this->pensumIds)
                            ->where('iniciado_at', '>=', now()->subDays($this->dateRange));

                        if ($this->filterDiagMainId) {
                            $sessions->where('diag_main_id', $this->filterDiagMainId);
                        }

                        if ($this->filterGradoId) {
                            $sessions->whereHas('pensum', function ($q) {
                                $q->where('grado_id', $this->filterGradoId);
                            });
                        }

                        if ($this->filterSeccionId) {
                            $sessions->whereHas('estudiant.inscripcion', function ($q) {
                                $q->where('seccion_id', $this->filterSeccionId);
                            });
                        }

                        if ($this->sessionsGradoFilter) {
                            $sessions->whereHas('pensum', function ($q) {
                                $q->where('grado_id', $this->sessionsGradoFilter);
                            });
                        }

                        $studentData->sessions = $sessions->get();

                        $totalQuestions = 0;
                        $answeredQuestions = 0;

                        foreach ($studentData->sessions as $session) {
                            $sessionTotalQuestions = DiagQuestion::where('pensum_id', $session->pensum_id)
                                ->where('activo', 1)
                                ->count();
                            $sessionAnsweredQuestions = $session->answers->where('completado_at', '!=', null)->count();

                            $totalQuestions += $sessionTotalQuestions;
                            $answeredQuestions += $sessionAnsweredQuestions;
                        }

                        $studentData->overall_progress = $totalQuestions > 0
                            ? round(($answeredQuestions * 100.0) / $totalQuestions, 0)
                            : 0;
                        $studentData->total_questions = $totalQuestions;
                        $studentData->answered_questions = $answeredQuestions;

                        $studentData->grados = $studentData->sessions->pluck('pensum.grado')->unique()->filter();
                    } else {
                        $studentData->estudiant = (object) [
                            'full_name' => 'Estudiante no encontrado',
                            'gsemail' => 'N/A',
                            'ci_estudiant' => 'N/A',
                        ];
                        $studentData->sessions = collect([]);
                        $studentData->overall_progress = 0;
                        $studentData->total_questions = 0;
                        $studentData->answered_questions = 0;
                        $studentData->grados = collect([]);
                    }

                    return $studentData;
                } catch (\Exception $e) {
                    Log::error('Error processing student data: ' . $e->getMessage());
                    $studentData->estudiant = (object) [
                        'full_name' => 'Error al cargar datos',
                        'gsemail' => 'N/A',
                        'ci_estudiant' => 'N/A',
                    ];
                    $studentData->sessions = collect([]);
                    $studentData->overall_progress = 0;
                    $studentData->total_questions = 0;
                    $studentData->answered_questions = 0;
                    $studentData->grados = collect([]);
                    return $studentData;
                }
            });

            return $students;
        } catch (\Exception $e) {
            Log::error('Error in getSessionsPaginated: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                [
                    'path' => request()->url(),
                    'pageName' => 'sessionsPage',
                ]
            );
        }
    }

    public function render()
    {
        $pensumIds = $this->pensumIds;

        $questions = $this->getQuestionsPaginationView();

        $sessions = DiagSession::with(['estudiant:id,name,lastname', 'pensum.asignatura:id,name'])
            ->select(['id', 'estudiant_id', 'pensum_id', 'iniciado_at', 'completado_at', 'progreso', 'total_preguntas', 'activo'])
            ->when($pensumIds, function ($query) use ($pensumIds) {
                $query->whereIn('pensum_id', $pensumIds);
            })
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

        $sessionsPaginated = $this->getSessionsPaginated();
        $generalStats = [
            'total_sessions' => $stats['total_sessions'] ?? 0,
            'completed_sessions' => $stats['completed_sessions'] ?? 0,
        ];

        $selectedSessionObject = null;
        if ($this->selectedSession && $this->SessionModalReport) {
            $selectedSessionObject = DiagSession::with(['answers.question', 'answers.selectedOption'])
                ->find($this->selectedSession);
        }

        return view('livewire.profesor.diagnostics.index-component', [
            'questions' => $questions,
            'sessions' => $sessions,
            'stats' => $stats,
            'subjects' => $this->subjects,
            'allQuestions' => DiagQuestion::whereIn('pensum_id', $pensumIds)->get(),
            'allSessions' => DiagSession::whereIn('pensum_id', $pensumIds)->get(),
            'allAnswers' => DiagAnswer::whereHas('question', function ($q) use ($pensumIds) {
                $q->whereIn('pensum_id', $pensumIds);
            })->with(['selectedOption', 'question'])->get(),
            'questionTypes' => ['multiple', 'open', 'scale'],
            'profesor' => $this->profesor,
            'sessionsPaginated' => $sessionsPaginated,
            'generalStats' => $generalStats,
            'showSessionDetailsModal' => $this->showSessionDetailsModal,
            'showSessionAnswersModal' => $this->showSessionAnswersModal,
            'selectedSessionAnswers' => $this->selectedSessionAnswers,
            'selectedSessionData' => $this->selectedSessionData,
            'selectedStudentData' => $this->selectedStudentData,
            'selectedSession' => $selectedSessionObject,
            'diagMains' => DiagMain::all(),
            'diagMainCurrent' => DiagMain::find($this->filterDiagMainId),
            'list_grados' => $this->list_grados,
            'list_secciones' => $this->list_secciones,
        ]);
    }

    private function getQuestionsPaginationView()
    {
        $pensumIds = $this->pensumIds;

        $questions = DiagQuestion::with(['options', 'pensum.asignatura'])
            ->when($pensumIds, function ($query) use ($pensumIds) {
                $query->whereIn('pensum_id', $pensumIds);
            })
            ->when($this->search, function ($query) {
                $query->where('pregunta', 'like', '%' . $this->search . '%');
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
            $pensumIds = $this->pensumIds;

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
                ->whereHas('session', function ($q) use ($pensumIds) {
                    $q->whereIn('pensum_id', $pensumIds);

                    if ($this->filterDiagMainId) {
                        $q->where('diag_main_id', $this->filterDiagMainId);
                    }

                    if ($this->filterGradoId) {
                        $q->whereHas('pensum', function ($qq) {
                            $qq->where('grado_id', $this->filterGradoId);
                        });
                    }

                    if ($this->filterSeccionId) {
                        $q->whereHas('estudiant.inscripcion', function ($qq) {
                            $qq->where('seccion_id', $this->filterSeccionId);
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
            Log::error('Error in getStudentAccuracyStats (Professor): ' . $e->getMessage());
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
            Log::error('Error getting AI report (Profesor): ' . $e->getMessage());
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

                if (!is_array($draftData)) {
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
            Log::error('Error viewing report (Profesor): ' . $e->getMessage());
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
