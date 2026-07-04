<?php

namespace App\Http\Livewire\Profesor\Diagnostics;

use App\Models\app\Estudiant;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagAnswer;
use App\User;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportAiDraft;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Institucion;
use App\Models\app\Autoridad;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar;
use Carbon\Carbon;

class IndexComponent extends Component
{
    use WithPagination;
    use \App\Http\Livewire\Evaluacion\Diagnostic\QwenReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\DeepSeekReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\GeminiReportTrait;
    use \App\Http\Livewire\Evaluacion\Diagnostic\OpenRouterReportTrait;

    protected $paginationTheme = 'bootstrap-4';

    public $cacheKey;
    public $lastUpdated;

    // Propiedades principales
    public $activeTab = 'dashboard';
    public $showQuestionModal = false;
    public $SessionModalReport = false;
    public $editingQuestion = null;
    public $selectedSession = null; // Now stores only session ID or student ID
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
    public $selected_ai_service = 'qwen'; // Default AI service
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
        'filterSeccionId' => ['except' => '']
    ];

    protected $listeners = [
        'questionDeleted' => 'refreshQuestions',
        'sessionUpdated' => 'refreshSessions',
        'confirmDeleteQuestion',
        'deleteQuestion',
        'showStudentDetails',
        'showStudentSessions',
        'refreshAnalytics' => 'refreshAnalytics'
    ];

    public function mount()
    {
        $this->resetOptions();
        $this->profesor = auth()->user()?->profesor;
        $this->pensumIds = $this->profesor?->pensums?->pluck('id')->toArray() ?? [];
        $this->cacheKey = 'diagnostics_' . Auth::id();
        $this->lastUpdated = now();

        $user = User::find(Auth::id());
        $this->profesor = $user ? $user->profesor : null;

        // Load grados for filter grade for profesor
        $this->list_grados = Profesor::list_grado($this->profesor->id);
    }

    public function updatedFilterDiagMainId()
    {
        $this->resetPage('sessionsPage');
        $this->dispatchBrowserEvent('filter-applied');
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

        $this->dispatchBrowserEvent('filter-applied');
    }

    public function updatedFilterSeccionId()
    {
        $this->resetPage('sessionsPage');
        $this->dispatchBrowserEvent('filter-applied');
    }

    public function getProfesorProperty()
    {
        return Cache::remember("profesor_" . Auth::id(), 3600, function () {
            $user = User::find(Auth::id());
            return $this->profesor = $user ? $user->profesor : null;
        });
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
        $this->emit('refreshCharts');
    }

    public function clearCache()
    {
        Cache::forget("stats_" . Auth::id() . "_" . ($this->selectedPensumId ?? 'all'));
        Cache::forget("profesor_" . Auth::id());
    }

    public function resetOptions()
    {
        $this->options = [
            ['opcion' => '', 'valor' => 0, 'orden' => 1],
            ['opcion' => '', 'valor' => 0, 'orden' => 2]
        ];
    }

    public function addOption()
    {
        if (count($this->options) < 6) {
            $this->options[] = [
                'opcion' => '',
                'valor' => 0,
                'orden' => count($this->options) + 1
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
                        'orden' => $option->orden ?? $index + 1
                    ];
                })->toArray();

                // Encontrar el índice de la opción correcta
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
        $this->emit('refreshCharts');
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

            $this->emit('showAlert', [
                'type' => 'success',
                'title' => $this->editingQuestion ? 'Pregunta actualizada' : 'Pregunta creada',
                'message' => 'La pregunta se ha guardado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Ocurrió un error al guardar la pregunta: ' . $e->getMessage()
            ]);
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
            // Los errores de validación se manejan automáticamente por Livewire
            throw $e;
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
        $this->emit('refreshCharts');
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
        $this->emit('refreshCharts');

        if ($tab === 'analytics') {
            $this->clearCache();
            $this->emit('refreshAnalytics');
        }
    }

    public function confirmDeleteQuestion($questionId)
    {
        $this->emit('showConfirmation', [
            'type' => 'warning',
            'title' => '¿Eliminar pregunta?',
            'message' => 'Esta acción no se puede deshacer.',
            'confirmText' => 'Eliminar',
            'cancelText' => 'Cancelar',
            'method' => 'deleteQuestion',
            'params' => [$questionId]
        ]);
    }

    public function deleteQuestion($questionId)
    {
        try {
            DB::beginTransaction();

            $question = DiagQuestion::findOrFail($questionId);

            // Verificar si la pregunta tiene respuestas asociadas
            $hasAnswers = DiagAnswer::where('question_id', $questionId)->exists();

            if ($hasAnswers) {
                $this->emit('showAlert', [
                    'type' => 'warning',
                    'title' => 'No se puede eliminar',
                    'message' => 'Esta pregunta tiene respuestas asociadas y no puede ser eliminada.'
                ]);
                return;
            }

            $question->delete();

            DB::commit();
            $this->clearCache();

            $this->emit('showAlert', [
                'type' => 'success',
                'title' => 'Pregunta eliminada',
                'message' => 'La pregunta se ha eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('showAlert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Ocurrió un error al eliminar la pregunta: ' . $e->getMessage()
            ]);
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
        $this->weighing = 1; // Agregué valor por defecto para weighing
        $this->difficulty = 'medium'; // Agregué valor por defecto para difficulty
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
        $student = \App\Models\app\Estudiant::find($estudiantId);

        $sessions = DiagSession::with(['pensum.grado', 'answers'])
            ->where('estudiant_id', $estudiantId)
            ->where('iniciado_at', '>=', now()->subDays($this->dateRange));

        if ($this->sessionsGradoFilter) {
            $sessions->whereHas('pensum', function ($q) {
                $q->where('grado_id', $this->sessionsGradoFilter);
            });
        }

        // Filter by professor's pensums
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
                'completed_sessions' => $pensumSessions->where('completado_at', '!=', null)->count()
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
                return \Carbon\Carbon::parse($session->iniciado_at)
                    ->diffInMinutes(\Carbon\Carbon::parse($session->completado_at));
            })
            : 0;

        $this->selectedSession = $estudiantId;
        $this->selectedSessionData = (object)[
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
        $student = \App\Models\app\Estudiant::find($estudiantId);

        $this->selectedSession = $estudiantId;
        $this->selectedStudentData = (object)[
            'estudiant_id' => $estudiantId,
            'estudiant' => $student
        ];

        $allAnswers = DiagAnswer::with(['question.options', 'question.pensum', 'selectedOption'])
            ->where('estudiant_id', $estudiantId)
            ->whereNotNull('completado_at')
            ->whereHas('question', function ($query) {
                $query->where('activo', 1)
                    ->whereIn('pensum_id', $this->pensumIds); // Filter by professor's pensums
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
                'answers' => $answers->sortBy('completado_at')
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

            // Apply new filters
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

                        // Apply new filters
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
                        $studentData->estudiant = (object)[
                            'full_name' => 'Estudiante no encontrado',
                            'gsemail' => 'N/A',
                            'ci_estudiant' => 'N/A'
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
                    $studentData->estudiant = (object)[
                        'full_name' => 'Error al cargar datos',
                        'gsemail' => 'N/A',
                        'ci_estudiant' => 'N/A'
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
                    'pageName' => 'sessionsPage'
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

            // Get answers only for questions from professor's pensums
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
        $this->emit('refreshCharts');
    }

    public function getQuestionsPaginationView()
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
            ->paginate(10, ['*'], 'page'); // Changed from 'questionsPage' to 'page' for standard pagination

        return $questions;
    }

    // ========================================
    // AI Report Generation Methods
    // ========================================

    protected function startLoading()
    {
        $this->isLoading = true;
        $this->dispatchBrowserEvent('loading-start');
    }

    protected function stopLoading()
    {
        $this->isLoading = false;
        $this->dispatchBrowserEvent('loading-stop');
    }

    public function getAIReport($estudiantId, $diagMainId)
    {
        $this->startLoading();

        try {
            $report = DiagReport::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $diagMainId)
                ->first();

            if ($report) {
                $this->viewReport($report->id);
            } else {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Reporte no disponible',
                    'text' => 'El reporte AI no ha sido generado aún para este estudiante.',
                    'icon' => 'warning',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error getting AI report (Profesor): ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al buscar el reporte.',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        } finally {
            $this->stopLoading();
        }
    }

    public function _deprecated_generateAIReport($estudiantId, $diagMainId)
    {
        Log::info("generateAIReport Called (Profesor)", [
            'student_id' => $estudiantId,
            'diag_main_id' => $diagMainId,
            'pensum_ids' => $this->pensumIds,
            'isLoading' => $this->isLoading,
            'requestId' => uniqid()
        ]);

        if ($this->isLoading) {
            Log::warning("generateAIReport blocked due to isLoading", ['student_id' => $estudiantId]);
            return;
        }
        $this->startLoading();

        // Increase execution time to 5 minutes to prevent timeouts
        set_time_limit(300);

        try {
            $student = Estudiant::find($estudiantId);
            $inscripcion = Inscripcion::where('estudiant_id', $estudiantId)->first();

            // Validate existing report
            $existingReport = DiagReport::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $diagMainId)
                ->exists();

            Log::info("Checking existing report", ['student_id' => $estudiantId, 'exists' => $existingReport]);

            if ($existingReport) {
                Log::warning("Report already exists for student", ['student_id' => $estudiantId]);
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Informe Existente',
                    'text' => 'Este estudiante ya cuenta con un informe generado para el diagnóstico seleccionado.',
                    'icon' => 'warning',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->stopLoading();
                return;
            }

            if (!$student && $inscripcion) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error',
                    'text' => 'Estudiante no encontrado.',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->stopLoading();
                return;
            }

            // 1. Gather Data (Sessions & Results) - Filter by pensumIds AND diagMainId
            $allSessions = DiagSession::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $diagMainId)
                ->whereIn('pensum_id', $this->pensumIds) // KEY DIFFERENCE: Filter by professor's pensums
                ->with(['pensum.asignatura', 'answers.question', 'diagMain'])
                ->get();

            $completedSessions = $allSessions->whereNotNull('completado_at');
            $incompleteSessions = $allSessions->whereNull('completado_at');

            if ($completedSessions->isEmpty()) {
                Log::warning("No completed sessions for student", [
                    'student_id' => $estudiantId,
                    'diag_main_id' => $diagMainId,
                    'pensum_ids' => $this->pensumIds
                ]);
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Sin Sesiones Completadas',
                    'text' => 'El estudiante no tiene sesiones completadas en las áreas asignadas a usted para generar un informe.',
                    'icon' => 'warning',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->stopLoading();
                return;
            }

            // Fetch Institution Data
            $institucion = Institucion::first();
            $pescolar = Pescolar::first();

            $director = null;
            $coordinador = null;

            if ($institucion) {
                $director = $institucion->autoridads()
                    ->where('position', 'DIRECTOR GENERAL Y ADMINISTRATIVO')
                    ->where('finicial', '<=', now())
                    ->where('ffinal', '>=', now())
                    ->first();

                $coordinador = $institucion->autoridads()
                    ->where('position', 'COORDINADOR DE EVALUACIÓN')
                    ->where('finicial', '<=', now())
                    ->where('ffinal', '>=', now())
                    ->first();
            }

            // Calculate incomplete details
            $incompleteDetails = $incompleteSessions->map(function ($session) {
                return [
                    'area' => $session->pensum?->asignatura?->name ?? 'N/A',
                    'motivo' => $session->status ?? 'Sin finalizar',
                    'duracion' => $session->iniciado_at ? 'Iniciada el ' . Carbon::parse($session->iniciado_at)->format('d/m/Y h:i A') . ' - No finalizada' : 'No iniciada',
                ];
            })->values()->toArray();

            // Calculate Global Results Stats
            $global_cerradas_respondidas = DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))
                ->whereHas('question', function ($q) {
                    $q->where('tipo_pregunta', 'multiple');
                })->count();

            $global_aciertos_cerradas = DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))
                ->whereHas('question', function ($q) {
                    $q->where('tipo_pregunta', 'multiple');
                })
                ->whereHas('selectedOption', function ($q) {
                    $q->where('valor', 1);
                })
                ->count();

            $precision_global = $global_cerradas_respondidas > 0 ? round(($global_aciertos_cerradas / $global_cerradas_respondidas) * 100, 1) : 0;

            $nivel_global = 'Emergent';
            $etiqueta_global = 'Requiere atención inmediata';
            if ($precision_global >= 50) {
                $nivel_global = 'Developing';
                $etiqueta_global = 'Requiere acompañamiento';
            }
            if ($precision_global >= 70) {
                $nivel_global = 'Proficient';
                $etiqueta_global = 'Consolidado';
            }
            if ($precision_global >= 90) {
                $nivel_global = 'Advanced';
                $etiqueta_global = 'Destacado';
            }

            // Calculate Areas Evaluated
            $areas_evaluadas = [];
            foreach ($allSessions as $session) {
                $session_answers = $session->answers;
                $total_preguntas = $session_answers->count();
                $indicators_stats = [];
                $questions_list = [];

                foreach ($session_answers as $answer) {
                    $is_correct = $answer->isCorrect();
                    $questions_list[] = [
                        'question' => $answer->question->pregunta ?? 'N/A',
                        'answer' => $answer->selectedOption?->opcion ?? $answer->respuesta ?? 'N/A',
                        'is_correct' => $is_correct,
                    ];

                    // Group by Indicator
                    $question = $answer->question;
                    if ($question && $question->indicator) {
                        $ind_id = $question->indicator->id;
                        if (!isset($indicators_stats[$ind_id])) {
                            $indicators_stats[$ind_id] = [
                                'description' => $question->indicator->description,
                                'total' => 0,
                                'correct' => 0
                            ];
                        }
                        $indicators_stats[$ind_id]['total']++;
                        if ($is_correct) {
                            $indicators_stats[$ind_id]['correct']++;
                        }
                    }
                }

                // Calculate precision
                $total_multiple = 0;
                $correct_multiple = 0;
                $answered_multiple = 0;

                foreach ($session_answers as $ans) {
                    if ($ans->question && $ans->question->tipo_pregunta === 'multiple') {
                        $total_multiple++;
                        $answered_multiple++;
                        if ($ans->isCorrect()) {
                            $correct_multiple++;
                        }
                    }
                }

                $precision = $answered_multiple > 0 ? round(($correct_multiple / $answered_multiple) * 100, 2) : 0;
                $aciertos = $correct_multiple;
                $preguntas_respondidas = $answered_multiple;
                $total_preguntas = $preguntas_respondidas;

                $nivel = 'Emergent';
                $observacion = 'Desempeño bajo, requiere apoyo fundamental.';
                if ($precision >= 50) {
                    $nivel = 'Developing';
                    $observacion = 'En desarrollo, muestra habilidades básicas pero requiere práctica.';
                }
                if ($precision >= 70) {
                    $nivel = 'Proficient';
                    $observacion = 'Buen desempeño, domina los conceptos esenciales.';
                }
                if ($precision >= 90) {
                    $nivel = 'Advanced';
                    $observacion = 'Desempeño sobresaliente, demuestra dominio avanzado.';
                }

                if (!$session->completado_at) {
                    $observacion = 'Evidencia limitada debido a sesión incompleta.';
                }

                // Determine Strengths/Needs based on Indicator Precision
                $fortalezas = [];
                $necesidades = [];

                foreach ($indicators_stats as $stat) {
                    $ind_precision = ($stat['total'] > 0) ? ($stat['correct'] / $stat['total']) * 100 : 0;
                    if ($ind_precision >= 70) {
                        $fortalezas[] = $stat['description'];
                    } elseif ($ind_precision <= 50) {
                        $necesidades[] = $stat['description'];
                    }
                }

                $fortalezas = array_slice($fortalezas, 0, 3);
                $necesidades = array_slice($necesidades, 0, 3);

                $areas_evaluadas[] = [
                    'id' => 'SUBJ-' . ($session->pensum_id ?? 'UNK'),
                    'pensum_id' => $session->pensum_id,
                    'nombre' => $session->pensum?->asignatura?->name ?? 'N/A',
                    'total_preguntas' => $total_preguntas,
                    'preguntas_respondidas' => $preguntas_respondidas,
                    'aciertos' => $aciertos,
                    'precision' => $precision,
                    'nivel_cualitativo' => $nivel,
                    'fortalezas' => empty($fortalezas) ? ['Sin fortalezas identificadas'] : $fortalezas,
                    'necesidades' => empty($necesidades) ? ['Refuerzo general requerido'] : $necesidades,
                    'observacion' => $observacion,
                    'questions' => $questions_list,
                ];
            }

            // Referente Normativo
            $referente = DiagReferent::where('active', 1)->first();
            $referente_normativo = [
                'nombre' => $referente?->name ?? 'Reforma Curricular de Educación Media General 2017',
                'version' => $referente?->version ?? 'EMG-2017.1',
                'resolucion' => $referente?->code ?? 'DM/0033',
                'vigencia' => 'Desde 2017'
            ];

            // Referente Curricular (Competencias e Indicadores por Grado/Pensum)
            $referente_curricular = [];
            $grado = $student->inscripcion?->seccion?->grado;
            if ($grado && $grado->pensums) {
                $gradePensumIds = $grado->pensums->pluck('id');
                $gradeCompetenciesMap = DiagCompetency::whereIn('pensum_id', $gradePensumIds)
                    ->with('indicators')
                    ->get()
                    ->groupBy('pensum_id');

                foreach ($grado->pensums as $pensum) {
                    $competencies = $gradeCompetenciesMap->get($pensum->id) ?? collect();

                    if ($competencies->isNotEmpty()) {
                        $comp_data = [];
                        foreach ($competencies as $comp) {
                            $ind_data = [];
                            foreach ($comp->indicators as $ind) {
                                $ind_data[] = [
                                    'codigo' => $ind->code,
                                    'descripcion' => $ind->description,
                                    'nivel_esperado' => $ind->expected_level
                                ];
                            }
                            $comp_data[] = [
                                'nombre' => $comp->name,
                                'descripcion' => $comp->description,
                                'indicadores' => $ind_data
                            ];
                        }

                        $referente_curricular[] = [
                            'pensum_id' => $pensum->id,
                            'area_formacion' => $pensum->fullname ?? 'Unknown Subscription',
                            'competencias' => $comp_data
                        ];
                    }
                }
            }

            // Contrastes Curriculares logic
            $contrastes_curriculares = [];
            $relevantPensumIds = $completedSessions->pluck('pensum_id')->unique();
            $allCompetencies = DiagCompetency::whereIn('pensum_id', $relevantPensumIds)
                ->with('indicators')
                ->get()
                ->groupBy('pensum_id');

            foreach ($completedSessions as $session) {
                if ($session->pensum) {
                    $session_answers = $session->answers;
                    $pensumCompetencies = $allCompetencies->get($session->pensum_id) ?? collect();
                    $indicators = $pensumCompetencies->pluck('indicators')->flatten();

                    foreach ($indicators as $indicator) {
                        $ind_answers = $session_answers->filter(function ($ans) use ($indicator) {
                            return $ans->question && $ans->question->indicator_id == $indicator->id;
                        });

                        $total = $ind_answers->count();
                        if ($total > 0) {
                            $correct = $ind_answers->where('is_correct', 1)->count();
                            $precision = ($correct / $total) * 100;

                            $observed_level = 'Insufficient';
                            $observed_val = 1;

                            if ($precision >= 50) {
                                $observed_level = 'Developing';
                                $observed_val = 2;
                            }
                            if ($precision >= 70) {
                                $observed_level = 'Satisfactory';
                                $observed_val = 3;
                            }
                            if ($precision >= 90) {
                                $observed_level = 'Outstanding';
                                $observed_val = 4;
                            }

                            $expected_val = 3;
                            switch ($indicator->expected_level) {
                                case 'Insufficient':
                                    $expected_val = 1;
                                    break;
                                case 'Developing':
                                    $expected_val = 2;
                                    break;
                                case 'Satisfactory':
                                    $expected_val = 3;
                                    break;
                                case 'Outstanding':
                                    $expected_val = 4;
                                    break;
                            }

                            $gap = $expected_val - $observed_val;
                            $gap_label = 'Sin brecha significativa';
                            if ($gap == 1) $gap_label = 'Brecha media';
                            if ($gap >= 2) $gap_label = 'Brecha alta';
                            if ($gap < 0) $gap_label = 'Supera expectativa';

                            $contrastes_curriculares[] = [
                                'pensum_id' => $session->pensum_id,
                                'area' => $session->pensum->fullname,
                                'indicator_id' => $indicator->id,
                                'indicador' => $indicator->description,
                                'nivel_esperado' => $indicator->expected_level,
                                'nivel_observado' => $observed_level,
                                'brecha' => $gap_label,
                                'gap_value' => $gap
                            ];
                        } else {
                            $contrastes_curriculares[] = [
                                'pensum_id' => $session->pensum_id,
                                'area' => $session->pensum->fullname,
                                'indicator_id' => $indicator->id,
                                'indicador' => $indicator->description,
                                'nivel_esperado' => $indicator->expected_level,
                                'nivel_observado' => 'No evaluado',
                                'brecha' => 'No evaluado por ausencia de evidencia',
                                'gap_value' => null
                            ];
                        }
                    }
                }
            }

            // Metadatos Generacion
            $metadatos_generacion = [
                'fecha_generacion' => now()->format('d/m/Y'),
                'modelo_ia_utilizado' => $this->selected_ai_service,
                'version_system_prompt' => 'SYS-1.0',
                'version_user_prompt' => 'USER-1.0',
                'hash_datos' => uniqid()
            ];

            // Construct Payload
            $payload = [
                'institucion' => [
                    'nombre' => $institucion?->name ?? 'N/A',
                    'direccion' => $institucion?->address ?? 'N/A',
                    'telefono' => $institucion?->phone ?? 'N/A',
                    'email' => $institucion?->email_institution ?? 'N/A',
                    'rif' => $institucion?->rif_institution ?? 'N/A',
                    'director' => $director ? $director->fullname : 'N/A',
                    'coordinador_academico' => $coordinador ? $coordinador->fullname : 'N/A',
                ],
                'estudiante' => [
                    'id' => 'EST-' . now()->format('Y') . $student->id,
                    'cedula' => $student->ci_estudiant,
                    'nombre_completo' => $student->full_name,
                    'fecha_nacimiento' => $student->date_birth ? Carbon::parse($student->date_birth)->format('d/m/Y') : 'N/A',
                    'edad' => $student->age,
                    'sexo' => $student->gender,
                    'telefono_emergencia' => $student->cellphone ?? $student->phone ?? 'N/A',
                    'email' => $student->gsemail,
                ],
                'grado' => [
                    'id' => 'GRD-' . ($student->grado_inicial_id ?? 'N/A'),
                    'nombre' => $student->inscripcion?->seccion?->grado?->name ?? 'N/A',
                    'seccion' => 'Sección ' . ($student->seccion?->name ? "'{$student->seccion->name}'" : 'N/A'),
                    'turno' => 'Mañana',
                    'tutor' => $student->profesor_guia ? 'Prof. ' . $student->profesor_guia->name . ' ' . $student->profesor_guia->lastname : 'N/A',
                ],
                'lapso_diagnostico' => [
                    'id' => 'LAP-' . now()->format('Y') . '-' . (Lapso::current()?->code ?? 'N/A'),
                    'nombre' => Lapso::current()?->name ?? 'N/A',
                    'fecha_inicio' => Lapso::current()?->finicial ? Carbon::parse(Lapso::current()->finicial)->format('d/m/Y') : 'N/A',
                    'fecha_fin' => Lapso::current()?->ffinal ? Carbon::parse(Lapso::current()->ffinal)->format('d/m/Y') : 'N/A',
                    'ano_escolar' => $pescolar?->name ?? 'N/A',
                ],
                'instrumento_aplicado' => [
                    'id' => 'DIAG-EMG-' . now()->format('Y') . '-v1',
                    'nombre' => 'Diagnóstico Inicial de Competencias Curriculares EMG ' . now()->format('Y'),
                    'version' => '1.0',
                    'fecha_aplicacion_inicio' => $completedSessions->min('iniciado_at') ? Carbon::parse($completedSessions->min('iniciado_at'))->format('d/m/Y') : 'N/A',
                    'fecha_aplicacion_fin' => $completedSessions->max('completado_at') ? Carbon::parse($completedSessions->max('completado_at'))->format('d/m/Y') : 'N/A',
                    'total_preguntas' => DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->count(),
                    'preguntas_cerradas' => DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->whereHas('question', function ($q) {
                        $q->where('tipo_pregunta', 'multiple');
                    })->count(),
                    'preguntas_abiertas' => DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->whereHas('question', function ($q) {
                        $q->where('tipo_pregunta', 'open');
                    })->count(),
                    'proposito' => 'Identificar el nivel de desarrollo inicial de competencias curriculares para orientar la planificación docente.',
                    'alcance' => 'Evaluación diagnóstica inicial, no sumativa',
                    'limitaciones' => 'Considera solo evidencia recolectada en sesiones completadas'
                ],
                'sesiones' => [
                    'total_programadas' => $allSessions->count(),
                    'completadas' => $completedSessions->count(),
                    'incompletas' => $incompleteSessions->count(),
                    'incompletas_detalle' => $incompleteDetails,
                ],
                'resultados_globales' => [
                    'total_preguntas_respondidas' => DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->count(),
                    'preguntas_cerradas_respondidas' => $global_cerradas_respondidas,
                    'preguntas_abiertas_respondidas' => DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->whereHas('question', function ($q) {
                        $q->where('tipo_pregunta', 'open');
                    })->count(),
                    'aciertos_cerradas' => $global_aciertos_cerradas,
                    'precision_global_cerradas' => $precision_global,
                    'nivel_global_cualitativo' => $nivel_global,
                    'etiqueta_institucional' => $etiqueta_global,
                    'observaciones_generales' => 'El estudiante completó la mayoría de las sesiones programadas, mostrando disposición para la actividad. Se evidencia variabilidad en el desempeño según áreas.',
                ],
                'diagnostic_summary' => [
                    'total_sessions_completed' => $completedSessions->count(),
                    'generated_at' => now()->toIso8601String(),
                ],
                'areas_evaluadas' => $areas_evaluadas,
                'contrastes_curriculares' => $contrastes_curriculares,
                'referente_normativo' => $referente_normativo,
                'referente_curricular' => $referente_curricular,
                'metadatos_generacion' => $metadatos_generacion,
            ];

            // 3. Delegate to AI Service & Save
            Log::info('Initiating AI Report Generation (Profesor)', [
                'student_id' => $student->id,
                'service' => $this->selected_ai_service,
                'payload_keys' => array_keys($payload),
                'pensum_ids' => $this->pensumIds
            ]);

            $aiResponse = null;
            if ($this->selected_ai_service === 'deepseek') {
                $aiResponse = $this->dsGenerateReport($payload);
            } elseif ($this->selected_ai_service === 'qwen') {
                $aiResponse = $this->qwGenerateReport($payload);
            } elseif ($this->selected_ai_service === 'gemini') {
                $aiResponse = $this->gmGenerateReport($payload);
            } elseif ($this->selected_ai_service === 'openrouter') {
                $aiResponse = $this->orGenerateReport($payload);
            }

            Log::info('AI Response Received (Profesor)', [
                'service' => $this->selected_ai_service,
                'response_type' => gettype($aiResponse),
                'is_empty' => empty($aiResponse)
            ]);

            if ($aiResponse) {
                // Save to Database
                $reportId = $this->saveReportToDatabase($payload, $aiResponse, $student, $institucion, $diagMainId);
                Log::info('Report Saved Successfully (Profesor)', ['report_id' => $reportId]);

                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Informe Generado',
                    'text' => 'El informe ha sido generado y guardado exitosamente.',
                    'icon' => 'success',
                    'timer' => 3000,
                    'showConfirmButton' => false
                ]);

                Log::info("Report generation finished successfully (Profesor)", ['student_id' => $estudiantId]);

                // Refresh the component
                $this->emit('$refresh');
                $this->stopLoading();
                return;
            } else {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error de Generación',
                    'text' => 'No se pudo generar el informe. La respuesta del servicio de IA fue nula o incompleta.',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->stopLoading();
                return;
            }
        } catch (Exception $e) {
            Log::error('Error generating AI report (Profesor): ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al generar el informe: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
            $this->stopLoading();
            return;
        }

        $this->stopLoading();
    }


    public function viewReport($reportId)
    {
        try {
            $report = DiagReport::with(['estudiant', 'diagMain', 'referent', 'latestDraft'])
                ->find($reportId);

            if ($report) {
                // Decode the output_text from the draft
                $draftRaw = $report->latestDraft->output_text ?? '{}';
                $draftData = json_decode($draftRaw, true); //dd($draftData);

                if (!is_array($draftData)) {
                    $draftData = [];
                }

                // Filter areas by professor's pensumIds
                if (isset($draftData['areas']) && is_array($draftData['areas'])) {
                    $filteredAreas = [];

                    foreach ($draftData['areas'] as $area) {
                        $pensumId = substr($area['id'], 5); // linea critica, no cambiar, se debe mejorar
                        // Check if the area's pensum_id is in the professor's pensumIds
                        if (isset($area['id']) && in_array($pensumId, $this->pensumIds)) {
                            $filteredAreas[] = $area;
                        }
                    }

                    $draftData['areas'] = $filteredAreas;
                }

                // Filter contrastes_curriculares by professor's pensumIds
                if (isset($draftData['contrast']) && is_array($draftData['contrast'])) {
                    $filteredContrast = [];

                    foreach ($draftData['contrast'] as $contrast) {
                        if (isset($contrast['pensum_id']) && in_array($contrast['pensum_id'], $this->pensumIds)) {
                            $filteredContrast[] = $contrast;
                        }
                    }

                    $draftData['contrast'] = $filteredContrast;
                }

                // Re-encode the filtered data back to the draft
                $report->latestDraft->output_text = json_encode($draftData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

                $this->selectedReport = $report;
                $this->SessionModalReport = true;

                Log::info('Report viewed (Profesor)', [
                    'report_id' => $reportId,
                    'pensum_ids' => $this->pensumIds,
                    'filtered_areas_count' => count($draftData['areas'] ?? [])
                ]);
            } else {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error',
                    'text' => 'No se encontró el reporte solicitado.',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error viewing report (Profesor): ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al cargar el reporte.',
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->selectedReport = null;
    }
}
