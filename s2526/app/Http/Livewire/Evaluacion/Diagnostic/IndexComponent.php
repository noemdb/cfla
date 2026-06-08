<?php

namespace App\Http\Livewire\Evaluacion\Diagnostic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\app\AI\AiPrompt;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportAiDraft;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Institucion;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagResult;
use App\Models\app\Instrument\DiagReportIndicatorResult;
use App\Models\app\Instrument\DiagReportPensum;
use App\Models\app\Instrument\DiagReportAuditLog;
use App\Models\app\Instrument\DiagIndicator;
use App\Models\app\Instrument\DiagRecommendation;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;

use Carbon\Carbon;
use Exception;

class IndexComponent extends Component
{
    use WithPagination;
    use DeepSeekReportTrait;
    use QwenReportTrait;
    use GeminiReportTrait;
    use OpenRouterReportTrait;

    protected $paginationTheme = 'bootstrap-4';

    public $isLoading = false;

    public $activeTab = 'dashboard';
    public $selectedPensum = '';
    public $dateRange = '365';
    public $list_pensums;
    public $list_grados;
    public $list_secciones = [];

    public $filterPensumId = '';
    public $filterDiagMainId = ''; // New filter
    public $filterTipoPregunta = '';
    public $filterEstadoPregunta = '';
    public $filterProfesorId = '';
    public $dashboardGradoFilter = '';
    public $search = '';

    public $filterGradoId = '';
    public $filterSeccionId = '';
    public $sessionsStudentNameFilter = '';
    public $questionsStatusFilter = '';

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $showQuestionsModal = false;
    public $selectedPensumForQuestions = null;
    public $selectedPensumQuestions = [];

    public $showSessionDetailsModal = false;
    public $showSessionAnswersModal = false;
    protected $selectedSession = null;
    public $selectedSessionAnswers = [];

    public $showActivationModal = false;
    public $selectedPensumForActivation = null;
    public $activationAction = 'activate';

    public $showBulkDeactivationModal = false;

    // Report Logic
    public $showReportModal = false;
    public $selectedReport = null;

    // DiagMain Properties
    public $showCreateDiagMainModal = false;
    public $diagMainId;
    public $diagMainName;
    public $diagMainDescription;
    public $diagMainActive = true;

    // AI Service deepseek/qwen/gemini/openrouter
    public $selected_ai_service = 'qwen';

    // Section Report Logic
    public $showSectionReportModal = false;
    public $selectedSectionReport = null;
    public $sectionsPage = 1;



    // UI Trigger to force render
    public $ui_hash;

    public function startLoading()
    {
        $this->isLoading = true;
    }

    public function stopLoading()
    {
        $this->isLoading = false;
        $this->ui_hash = uniqid(); // Force render to ensure loading states clear
    }

    public function updatedFilterGradoId()
    {
        $this->startLoading();
        $this->resetPage('sessionsPage');
        $this->resetPage('questionsPage');
        $this->resetPage('pensumPage');

        $this->filterSeccionId = '';
        $this->list_secciones = [];

        if ($this->filterGradoId) {
            $this->list_secciones = Seccion::where('grado_id', $this->filterGradoId)
                ->where('status_active', 'true')
                ->get();
        }

        $this->emit('refreshDashboard');
        $this->dispatchBrowserEvent('filter-applied');
        $this->stopLoading();
    }

    public function updatedFilterSeccionId()
    {
        $this->startLoading();
        $this->resetPage('sessionsPage');
        $this->emit('refreshDashboard');
        $this->dispatchBrowserEvent('filter-applied');
        $this->stopLoading();
    }

    public function updatedSessionsStudentNameFilter()
    {
        $this->startLoading();
        $this->resetPage('sessionsPage');
        $this->dispatchBrowserEvent('filter-applied');
        $this->stopLoading();
    }

    public function updatedDateRange()
    {
        $this->startLoading();
        $this->resetPage('sessionsPage');
        $this->dispatchBrowserEvent('filter-applied');
        $this->stopLoading();
    }

    public function updatedQuestionsStatusFilter()
    {
        $this->startLoading();
        $this->resetPage('questionsPage');
        $this->dispatchBrowserEvent('filter-applied');
        $this->stopLoading();
    }



    public function mount()
    {
        $this->list_pensums = Pensum::list_pestudio_pensum();
        $this->list_grados = Grado::list_pestudio_grado();
    }

    public function setActiveTab($tab)
    {
        $this->startLoading();

        $this->resetPage('pensumPage');
        $this->resetPage('questionsPage');
        $this->resetPage('sessionsPage');

        $this->activeTab = $tab;
        $this->dispatchBrowserEvent('tab-loaded', ['tab' => $tab]);

        $this->stopLoading();
    }

    public function resetFilters()
    {
        $this->startLoading();

        $this->filterPensumId = '';
        $this->filterDiagMainId = '';
        $this->filterTipoPregunta = '';
        $this->filterEstadoPregunta = '';
        $this->filterProfesorId = '';
        $this->search = '';
        $this->filterGradoId = '';
        $this->filterSeccionId = '';
        $this->list_secciones = [];
        $this->sessionsStudentNameFilter = '';
        $this->questionsStatusFilter = '';

        $this->stopLoading();
    }

    private function applyFilters($query)
    {
        return $query->when($this->filterPensumId, function ($q) {
            $q->where('pensum_id', $this->filterPensumId);
        })
            ->when($this->filterDiagMainId, function ($q) {
                // If query is on DiagQuestion, it has diag_main_id
                $q->where('diag_main_id', $this->filterDiagMainId);
            })
            ->when($this->filterTipoPregunta, function ($q) {
                $q->where('tipo_pregunta', $this->filterTipoPregunta);
            })
            ->when($this->filterEstadoPregunta, function ($q) {
                if ($this->filterEstadoPregunta === 'completada') {
                    $q->whereHas('answers');
                } elseif ($this->filterEstadoPregunta === 'en_progreso') {
                    $q->whereDoesntHave('answers')->where('activo', true);
                }
            })
            ->when($this->filterProfesorId, function ($q) {
                $q->whereHas('pensum.profesor', function ($subQ) {
                    $subQ->where('id', $this->filterProfesorId);
                });
            })
            ->when($this->search, function ($q) {
                $q->where('pregunta', 'like', '%' . $this->search . '%');
            });
    }

    public function render()
    {
        try {
            $data = [
                'generalStats' => $this->getGeneralStats(),
                'questionsByDifficulty' => $this->getQuestionsByDifficulty(),
                'questionsByType' => $this->getQuestionsByType(),
                'pensumProgress' => $this->getPensumProgress(),
                'recentSessions' => $this->getRecentSessions(),
                'responseStats' => $this->getResponseStats(),
                'pensums' => Pensum::where('status_active', 'true')->get(),
                'grados' => Grado::where('status_active', 'true')->get(),
                'questions' => $this->getFilteredQuestions(),
                'profesors' => $this->getProfesors(),
                'tiposPreguntas' => ['multiple', 'open', 'scale'],
                'estadosPreguntas' => [
                    'completada' => 'Completada',
                    'en_progreso' => 'En Progreso'
                ],
                'questionsPensums' => $this->getQuestionsPensums(),
                'questionsPensumsChart' => $this->getQuestionsPensumsForChart(),
                'sessionsPaginated' => $this->getSessionsPaginated(),
                'showQuestionsModal' => $this->showQuestionsModal,
                'selectedPensumForQuestions' => $this->selectedPensumForQuestions,
                'selectedPensumQuestions' => $this->selectedPensumQuestions,
                'showSessionDetailsModal' => $this->showSessionDetailsModal,
                'selectedSession' => $this->selectedSession,
                'showSessionAnswersModal' => $this->showSessionAnswersModal,
                'selectedSessionAnswers' => $this->selectedSessionAnswers,
                'showActivationModal' => $this->showActivationModal,
                'selectedPensumForActivation' => $this->selectedPensumForActivation,
                'activationAction' => $this->activationAction,
                'showBulkDeactivationModal' => $this->showBulkDeactivationModal,
                'diagMains' => DiagMain::all(), // Retrieve all DiagMains
                'diagMainCurrent' => DiagMain::find($this->filterDiagMainId),
                'totalActiveDiagnosticPensums' => Pensum::where('status_active', true)
                    ->where('status_active_diagnostic', true)
                    ->when($this->filterGradoId, function ($q) {
                        $q->where('grado_id', $this->filterGradoId);
                    })
                    ->count(),
                'sectionsPaginated' => $this->getSectionsPaginated(),
            ];

            return view('livewire.evaluacion.diagnostic.index-component', $data);
        } catch (Exception $e) {
            Log::error('Error en IndexComponent render: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getFilteredQuestions()
    {
        $query = DiagQuestion::with(['pensum', 'options'])->where('activo', true);
        $query = $this->applyFilters($query);
        return $query->orderBy($this->sortBy, $this->sortDirection)->paginate(15);
    }

    private function getProfesors()
    {
        return User::whereHas('profesor.pensums')
            ->with('profesor')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->profesor->id,
                    'name' => $user->name . ' ' . $user->lastname
                ];
            });
    }

    private function getGeneralStats()
    {
        try {
            $questionsQuery = DiagQuestion::where('activo', true);
            $questionsQuery = $this->applyFilters($questionsQuery);

            $totalQuestions = $questionsQuery->count();

            $sessionsQuery = DiagSession::query();
            if ($this->filterSeccionId) {
                $sessionsQuery->whereHas('estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }
            if ($this->filterDiagMainId) {
                $sessionsQuery->where('diag_main_id', $this->filterDiagMainId);
            }
            $totalSessions = $sessionsQuery->count();

            $completedSessionsQuery = DiagSession::whereNotNull('completado_at');
            if ($this->filterSeccionId) {
                $completedSessionsQuery->whereHas('estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }
            if ($this->filterDiagMainId) {
                $completedSessionsQuery->where('diag_main_id', $this->filterDiagMainId);
            }
            $completedSessions = $completedSessionsQuery->count();

            $responsesQuery = DiagAnswer::whereNotNull('completado_at');
            if ($this->filterSeccionId) {
                $responsesQuery->whereHas('session.estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }
            if ($this->filterDiagMainId) {
                $responsesQuery->whereHas('question', function ($q) {
                    $q->where('diag_main_id', $this->filterDiagMainId);
                });
            }
            $totalResponses = $responsesQuery->count();

            $activeSessionsQuery = DiagSession::where('activo', true)->whereNull('completado_at');
            if ($this->filterSeccionId) {
                $activeSessionsQuery->whereHas('estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }
            if ($this->filterDiagMainId) {
                $activeSessionsQuery->where('diag_main_id', $this->filterDiagMainId);
            }
            $activeSessions = $activeSessionsQuery->count();

            $avgCompletionRate = $this->getAverageCompletionRate();
            $accuracyStats = $this->getStudentAccuracyStats(); // Note: method not shown in context, but likely needs update too or relies on global state if properties are used

            return [
                'total_questions' => $totalQuestions ?? 0,
                'total_sessions' => $totalSessions ?? 0,
                'completed_sessions' => $completedSessions ?? 0,
                'total_responses' => $totalResponses ?? 0,
                'active_sessions' => $activeSessions ?? 0,
                'avg_completion_rate' => $avgCompletionRate ?? 0,
                'student_accuracy' => $accuracyStats['accuracy'] ?? 0,
                'correct_answers' => $accuracyStats['correct_answers'] ?? 0,
                'total_answered' => $accuracyStats['total_answered'] ?? 0,
                'grado_name' => $accuracyStats['grado_name'] ?? 0,
            ];
        } catch (Exception $e) {
            return [
                'total_questions' => 0,
                'total_sessions' => 0,
                'completed_sessions' => 0,
                'total_responses' => 0,
                'active_sessions' => 0,
                'avg_completion_rate' => 0,
                'student_accuracy' => 0,
                'correct_answers' => 0,
                'total_answered' => 0,
            ];
        }
    }

    private function getAverageCompletionRate()
    {
        $sessionsQuery = DiagSession::query();
        if ($this->filterSeccionId) {
            $sessionsQuery->whereHas('estudiant.inscripcion', function ($q) {
                $q->where('seccion_id', $this->filterSeccionId);
            });
        }
        if ($this->filterDiagMainId) {
            $sessionsQuery->where('diag_main_id', $this->filterDiagMainId);
        }
        $totalSessions = $sessionsQuery->count();
        if ($totalSessions == 0) return 0;

        $completedSessionsQuery = DiagSession::whereNotNull('completado_at');
        if ($this->filterSeccionId) {
            $completedSessionsQuery->whereHas('estudiant.inscripcion', function ($q) {
                $q->where('seccion_id', $this->filterSeccionId);
            });
        }
        if ($this->filterDiagMainId) {
            $completedSessionsQuery->where('diag_main_id', $this->filterDiagMainId);
        }
        $completedSessions = $completedSessionsQuery->count();

        return round(($completedSessions / $totalSessions) * 100, 2);
    }

    private function getQuestionsByDifficulty()
    {
        $query = DiagQuestion::select('difficulty', DB::raw('count(*) as count'))
            ->where('activo', true);
        $query = $this->applyFilters($query);
        return $query->groupBy('difficulty')->get();
    }

    private function getQuestionsByType()
    {
        $query = DiagQuestion::select('tipo_pregunta as type', DB::raw('count(*) as count'))
            ->where('activo', true);
        $query = $this->applyFilters($query);
        return $query->groupBy('tipo_pregunta')->get();
    }

    private function getPensumProgress()
    {
        $query = Pensum::select('pensums.*')
            ->selectRaw('
                COUNT(DISTINCT dq.id) as total_questions,
                COUNT(DISTINCT ds.id) as total_sessions,
                COUNT(DISTINCT CASE WHEN ds.completado_at IS NOT NULL THEN ds.id END) as completed_sessions,
                COUNT(DISTINCT da.id) as total_responses,
                ROUND(
                    CASE
                        WHEN COUNT(DISTINCT ds.id) > 0 THEN
                            (COUNT(DISTINCT CASE WHEN ds.completado_at IS NOT NULL THEN ds.id END) * 100.0 / COUNT(DISTINCT ds.id))
                        ELSE 0
                    END, 2
                ) as completion_percentage
            ')
            ->leftJoin('diag_questions as dq', function ($join) {
                $join->on('pensums.id', '=', 'dq.pensum_id')
                    ->where('dq.activo', true);
                if ($this->filterDiagMainId) {
                    $join->where('dq.diag_main_id', $this->filterDiagMainId);
                }
            })
            ->leftJoin('diag_sessions as ds', function ($join) {
                $join->on('pensums.id', '=', 'ds.pensum_id');
                if ($this->filterDiagMainId) {
                    $join->where('ds.diag_main_id', $this->filterDiagMainId);
                }
                if ($this->filterSeccionId) {
                    $join->whereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('inscripcions')
                            ->whereColumn('inscripcions.estudiant_id', 'ds.estudiant_id')
                            ->where('inscripcions.seccion_id', $this->filterSeccionId);
                    });
                }
            })
            ->leftJoin('diag_answers as da', function ($join) {
                $join->on('ds.id', '=', 'da.session_id')
                    ->whereNotNull('da.completado_at');
            })
            ->where('pensums.status_active', true);

        if ($this->selectedPensum) {
            $query->where('pensums.id', $this->selectedPensum);
        }

        if ($this->filterGradoId) {
            $query->where('pensums.grado_id', $this->filterGradoId);
        }

        if ($this->filterSeccionId) {
            // Complex join for filtering pensum progress by section participation
            // This is tricky because Pensum is the main table.
            // We want to count sessions/answers ONLY for students in this section.
            // But the current query structure left joins sessions directly.
            // We need to add conditions to the ON clause or WHERE clause for the LEFT JOINs.
            // Re-writing the query logic here might be expansive.
            // An easier way is to filter the 'ds' (sessions) join.
            // But 'ds' logic is inside the join callback.

            // Let's modify the getPensumProgress query structure separately if needed, 
            // but for safe efficient update without breaking the massive query:
            // We can add a whereHas condition if we were using Eloquent relationships, but this is raw select.

            // Wait, the join for 'ds' is:
            // ->leftJoin('diag_sessions as ds', function ($join) { ... })

            // We can inject the section condition into the join?
            // "AND ds.estudiant_id IN (SELECT estudiant_id FROM inscripcions WHERE seccion_id = ...)"
            // That might be heavy.

            // Alternatively, we accept that 'total_sessions' counts might be global if we don't filter 'ds'.
            // But the user wants the filter.

            // Let's try to add stricter filtering to the 'ds' join.
        }

        return $query->groupBy('pensums.id', 'pensums.status_active')
            ->orderBy('pensums.id')
            ->paginate(10, ['*'], 'pensumPage');
    }

    private function getRecentSessions()
    {
        $query = DiagSession::with(['estudiant', 'pensum']);

        if ($this->filterDiagMainId) {
            $query->where('diag_main_id', $this->filterDiagMainId);
        }

        if ($this->filterPensumId) {
            $query->where('pensum_id', $this->filterPensumId);
        }

        return $query->orderBy('iniciado_at', 'desc')->limit(15)->get();
    }

    private function getResponseStats()
    {
        try {
            $dateFilter = now()->subDays($this->dateRange);

            $dailyResponsesQuery = DiagAnswer::select(
                DB::raw('DATE(diag_answers.completado_at) as date'),
                DB::raw('COUNT(*) as count')
            )
                ->join('diag_sessions as ds', 'diag_answers.session_id', '=', 'ds.id')
                ->join('diag_questions as dq', 'diag_answers.question_id', '=', 'dq.id')
                ->whereNotNull('diag_answers.completado_at')
                ->where('diag_answers.completado_at', '>=', $dateFilter);

            $responsesByPensumQuery = Pensum::select('pensums.id')
                ->selectRaw('COUNT(diag_answers.id) as count')
                ->join('diag_questions as dq', 'dq.pensum_id', '=', 'pensums.id')
                ->join('diag_answers', 'diag_answers.question_id', '=', 'dq.id')
                ->whereNotNull('diag_answers.completado_at')
                ->where('diag_answers.completado_at', '>=', $dateFilter)
                ->where('pensums.status_active', true)
                ->groupBy('pensums.id');

            if ($this->filterDiagMainId) {
                $dailyResponsesQuery->where('ds.diag_main_id', $this->filterDiagMainId);
                $responsesByPensumQuery->where('dq.diag_main_id', $this->filterDiagMainId);
            }

            if ($this->filterPensumId) {
                $dailyResponsesQuery->where('dq.pensum_id', $this->filterPensumId);
                $responsesByPensumQuery->where('pensums.id', $this->filterPensumId);
            }

            if ($this->filterGradoId) {
                $dailyResponsesQuery->whereHas('question.pensum', function ($q) {
                    $q->where('grado_id', $this->filterGradoId);
                });
                $responsesByPensumQuery->where('pensums.grado_id', $this->filterGradoId);
            }

            if ($this->filterSeccionId) {
                $dailyResponsesQuery->whereHas('session.estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
                $responsesByPensumQuery->whereHas('diag_questions.answers.estudiant.inscripcion', function ($q) {
                    $q->where('seccion_id', $this->filterSeccionId);
                });
            }

            $dailyResponses = $dailyResponsesQuery
                ->groupBy(DB::raw('DATE(diag_answers.completado_at)'))
                ->orderBy('date')
                ->get();

            $responsesByPensum = $responsesByPensumQuery
                ->orderBy('count', 'desc')
                ->get()
                ->map(function ($pensum) {
                    return [
                        'id' => $pensum->id,
                        'full_name' => $pensum->full_name,
                        'count' => $pensum->count,
                    ];
                });

            $completionStats = $this->getCompletionStats($dateFilter);

            return [
                'daily_responses' => $dailyResponses,
                'responses_by_pensum' => $responsesByPensum,
                'completion_stats' => $completionStats,
                'total_responses' => $responsesByPensum->sum('count'),
                'avg_responses_per_day' => $dailyResponses->count() > 0 ? round($dailyResponses->avg('count'), 1) : 0,
            ];
        } catch (Exception $e) {
            Log::error('Error in getResponseStats: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'filters' => [
                    'diagMainId' => $this->filterDiagMainId,
                    'pensumId' => $this->filterPensumId,
                    'gradoFilter' => $this->filterGradoId,
                    'dateRange' => $this->dateRange
                ],
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'daily_responses' => collect([]),
                'responses_by_pensum' => collect([]),
                'completion_stats' => [],
                'total_responses' => 0,
                'avg_responses_per_day' => 0,
            ];
        }
    }

    private function getCompletionStats($dateFilter)
    {
        try {
            // Re-writing to properly integrate filter
            $query = DB::table('diag_sessions as ds')
                ->select(
                    'p.id',
                    DB::raw('COUNT(DISTINCT ds.id) as total_sessions'),
                    DB::raw('COUNT(DISTINCT CASE WHEN ds.completado_at IS NOT NULL THEN ds.id END) as completed_sessions'),
                    DB::raw('COUNT(DISTINCT da.id) as total_answers'),
                    DB::raw('COUNT(DISTINCT dq.id) as total_questions'),
                    DB::raw('ROUND(AVG(CASE
                        WHEN ds.completado_at IS NOT NULL AND ds.iniciado_at IS NOT NULL
                        THEN TIMESTAMPDIFF(MINUTE, ds.iniciado_at, ds.completado_at)
                        ELSE NULL
                    END), 1) as avg_duration_minutes')
                )
                ->join('pensums as p', 'ds.pensum_id', '=', 'p.id')
                ->leftJoin('diag_questions as dq', function ($join) {
                    $join->on('p.id', '=', 'dq.pensum_id')
                        ->where('dq.activo', true);
                    if ($this->filterDiagMainId) {
                        $join->where('dq.diag_main_id', $this->filterDiagMainId);
                    }
                })
                ->leftJoin('diag_answers as da', function ($join) {
                    $join->on('ds.id', '=', 'da.session_id')
                        ->whereNotNull('da.completado_at');
                })
                ->where('ds.iniciado_at', '>=', $dateFilter)
                ->where('p.status_active', true);

            if ($this->filterDiagMainId) {
                $query->where('ds.diag_main_id', $this->filterDiagMainId);
            }

            $stats = $query->groupBy('p.id')->get();

            // Fetch Pensum models to get accessors (full_name) efficiently
            $pensums = Pensum::whereIn('id', $stats->pluck('id'))->get()->keyBy('id');

            return $stats->map(function ($stat) use ($pensums) {
                $pensum = $pensums[$stat->id] ?? null;

                $completionRate = $stat->total_sessions > 0
                    ? round(($stat->completed_sessions * 100.0) / $stat->total_sessions, 1)
                    : 0;

                $answerRate = $stat->total_questions > 0 && $stat->total_sessions > 0
                    ? round(($stat->total_answers * 100.0) / ($stat->total_questions * $stat->total_sessions), 1)
                    : 0;

                return (object)[
                    'id' => $stat->id,
                    'pensum' => $pensum ? $pensum->asignaura : null, // legacy field? kept just in case
                    'full_name' => $pensum ? $pensum->full_name : 'Unknown',
                    'total_sessions' => $stat->total_sessions,
                    'completed_sessions' => $stat->completed_sessions,
                    'total_answers' => $stat->total_answers,
                    'total_questions' => $stat->total_questions,
                    'completion_rate' => $completionRate,
                    'answer_rate' => $answerRate,
                    'avg_duration_minutes' => $stat->avg_duration_minutes ?? 0,
                ];
            });
        } catch (Exception $e) {
            Log::error('Error in getCompletionStats: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getQuestionsPensums()
    {
        try {
            $query = Pensum::select('pensums.*')
                ->selectRaw('
                    COUNT(DISTINCT dq.id) as total_questions,
                    COUNT(DISTINCT CASE WHEN dq.activo = 1 THEN dq.id END) as active_questions,
                    COUNT(DISTINCT da.id) as total_responses,
                    GROUP_CONCAT(DISTINCT dq.tipo_pregunta) as question_types
                ')
                ->leftJoin('diag_questions as dq', function ($join) {
                    $join->on('pensums.id', '=', 'dq.pensum_id');
                    if ($this->filterDiagMainId) {
                        $join->where('dq.diag_main_id', $this->filterDiagMainId);
                    }
                })
                ->leftJoin('diag_answers as da', function ($join) {
                    $join->on('dq.id', '=', 'da.question_id')
                        ->whereNotNull('da.completado_at');
                })
                ->where('pensums.status_active', true);

            if ($this->filterGradoId) {
                $query->where('pensums.grado_id', $this->filterGradoId);
            }

            if ($this->questionsStatusFilter !== '') {
                $query->where('pensums.status_active_diagnostic', $this->questionsStatusFilter == '1');
            }

            $result = $query->groupBy('pensums.id')
                ->orderBy('pensums.id')
                ->paginate(10, ['*'], 'questionsPage');

            return $result;
        } catch (Exception $e) {
            Log::error('Error in getQuestionsPensums: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => request()->url(), 'pageName' => 'questionsPage']
            );
        }
    }

    private function getQuestionsPensumsForChart()
    {
        $query = Pensum::select('pensums.*')
            ->selectRaw('
                COUNT(DISTINCT dq.id) as total_questions,
                COUNT(DISTINCT CASE WHEN dq.activo = 1 THEN dq.id END) as active_questions
            ')
            ->leftJoin('diag_questions as dq', function ($join) {
                $join->on('pensums.id', '=', 'dq.pensum_id');
                if ($this->filterDiagMainId) {
                    $join->where('dq.diag_main_id', $this->filterDiagMainId);
                }
            })
            ->where('pensums.status_active', true);

        if ($this->filterGradoId) {
            $query->where('pensums.grado_id', $this->filterGradoId);
        }

        $result = $query->groupBy('pensums.id')
            ->orderBy('pensums.id')
            ->limit(5)
            ->get();

        return $result;
    }

    // DiagMain Methods
    public function resetDiagMainInput()
    {
        $this->diagMainId = null;
        $this->diagMainName = null;
        $this->diagMainDescription = null;
        $this->diagMainActive = true;
    }

    public function saveDiagMain()
    {
        $this->validate([
            'diagMainName' => 'required|string|max:255',
        ]);

        if ($this->diagMainId) {
            $diagMain = DiagMain::find($this->diagMainId);
            $diagMain->update([
                'name' => $this->diagMainName,
                'description' => $this->diagMainDescription,
                'active' => $this->diagMainActive,
            ]);
        } else {
            DiagMain::create([
                'name' => $this->diagMainName,
                'token' => bin2hex(random_bytes(4)),
                'description' => $this->diagMainDescription,
                'active' => $this->diagMainActive,
            ]);
        }

        $this->showCreateDiagMainModal = false;
        $this->resetDiagMainInput();
        $this->dispatchBrowserEvent('close-modal'); // Assuming you handle this in JS to close BS modal
    }

    public function editDiagMain($id)
    {
        $diagMain = DiagMain::find($id);
        $this->diagMainId = $diagMain->id;
        $this->diagMainName = $diagMain->name;
        $this->diagMainDescription = $diagMain->description;
        $this->diagMainActive = $diagMain->active;
        $this->showCreateDiagMainModal = true;
    }

    public function deleteDiagMain($id)
    {
        DiagMain::find($id)->delete();
    }

    public function getSessionsPaginated()
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
                ');

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

            if ($this->sessionsStudentNameFilter) {
                $query->whereHas('estudiant', function ($q) {
                    $searchTerm = '%' . $this->sessionsStudentNameFilter . '%';
                    $q->where(function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', $searchTerm)
                            ->orWhere('lastname', 'like', $searchTerm)
                            ->orWhereRaw("CONCAT(lastname, ' ', name) LIKE ?", [$searchTerm])
                            ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE ?", [$searchTerm]);
                    });
                });
            }

            if ($this->filterDiagMainId) {
                $query->where('diag_main_id', $this->filterDiagMainId);
            }

            $dateFilter = now()->subDays($this->dateRange);
            $query->where('diag_sessions.iniciado_at', '>=', $dateFilter);

            $students = $query->groupBy('estudiant_id')
                ->orderBy('last_session_date', 'desc')
                ->paginate(15, ['*'], 'sessionsPage');

            $students->getCollection()->transform(function ($studentData) {
                try {
                    $student = \App\Models\app\Estudiant::with(['diag_sessions.pensum.grado'])
                        ->find($studentData->estudiant_id);

                    if ($student) {
                        $studentData->estudiant = $student;

                        $sessions = $student->diag_sessions()
                            ->with(['pensum.grado', 'answers'])
                            ->where('iniciado_at', '>=', now()->subDays($this->dateRange));

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

                        if ($this->filterDiagMainId) {
                            $sessions->where('diag_main_id', $this->filterDiagMainId);
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
                        $studentData->diag_main_id = $this->filterDiagMainId;

                        $studentData->grados = $studentData->sessions->pluck('pensum.grado')->unique()->filter();

                        // Check if report exists
                        $studentData->report = DiagReport::where('estudiant_id', $studentData->estudiant_id)
                            ->where('diag_main_id', $this->filterDiagMainId ?: 1)
                            ->latest()
                            ->first();
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
                } catch (Exception $e) {
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
        } catch (Exception $e) {
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

    public function showPensumQuestions($pensumId)
    {
        $this->startLoading();

        $this->selectedPensumForQuestions = Pensum::find($pensumId);
        $this->selectedPensumQuestions = DiagQuestion::with(['options' => function ($query) {
            $query->orderBy('orden');
        }])
            ->where('pensum_id', $pensumId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $this->showQuestionsModal = true;
        $this->stopLoading();
    }

    public function closeQuestionsModal()
    {
        $this->showQuestionsModal = false;
        $this->selectedPensumForQuestions = null;
        $this->selectedPensumQuestions = [];
    }

    public function showSessionDetails($sessionId)
    {
        $this->startLoading();

        $this->selectedSession = DiagSession::with(['estudiant', 'pensum.grado'])
            ->find($sessionId);
        $this->showSessionDetailsModal = true;

        $this->stopLoading();
    }

    public function showSessionAnswers($sessionId)
    {
        $this->startLoading();

        $this->selectedSession = DiagSession::with(['estudiant', 'pensum'])
            ->find($sessionId);

        $this->selectedSessionAnswers = DiagAnswer::with(['question.options'])
            ->where('estudiant_id', $this->selectedSession->estudiant_id)
            ->whereHas('question', function ($query) use ($sessionId) {
                $query->where('pensum_id', $this->selectedSession->pensum_id)
                    ->where('tipo_pregunta', 'multiple');
            })
            ->whereNotNull('completado_at')
            ->orderBy('completado_at')
            ->get();

        $this->showSessionAnswersModal = true;
        $this->stopLoading();
    }

    public function closeSessionDetailsModal()
    {
        $this->showSessionDetailsModal = false;
        $this->selectedSession = null;
    }

    public function closeSessionAnswersModal()
    {
        $this->showSessionAnswersModal = false;
        $this->selectedSession = null;
        $this->selectedSessionAnswers = [];
    }

    public function showStudentDetails($estudiantId)
    {
        $this->startLoading();

        $student = \App\Models\app\Estudiant::find($estudiantId);

        $sessions = DiagSession::with(['pensum.grado', 'answers'])
            ->where('estudiant_id', $estudiantId)
            ->where('iniciado_at', '>=', now()->subDays($this->dateRange));

        if ($this->filterGradoId) {
            $sessions->whereHas('pensum', function ($q) {
                $q->where('grado_id', $this->filterGradoId);
            });
        }

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

        $this->selectedSession = (object)[
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
            'active_pensums' => collect($pensumStats)->where('completed_sessions', '<', function ($stat) {
                return $stat['sessions_count'];
            })->count()
        ];

        $this->showSessionDetailsModal = true;
        $this->stopLoading();
    }

    public function showStudentSessions($estudiantId)
    {
        $this->startLoading();

        $student = Estudiant::find($estudiantId);

        $this->selectedSession = (object)[
            'estudiant_id' => $estudiantId,
            'estudiant' => $student
        ];

        $allAnswers = DiagAnswer::with(['question.options', 'question.pensum', 'selectedOption'])
            ->where('estudiant_id', $estudiantId)
            ->whereNotNull('completado_at')
            ->whereHas('question', function ($query) {
                $query->where('activo', 1)
                    ->where('tipo_pregunta', 'multiple')
                ;
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
        $this->stopLoading();
    }

    public function confirmActivatePensum($pensumId)
    {
        $this->selectedPensumForActivation = Pensum::find($pensumId);
        $this->activationAction = 'activate';
        $this->showActivationModal = true;
    }

    public function confirmDeactivatePensum($pensumId)
    {
        $this->selectedPensumForActivation = Pensum::find($pensumId);
        $this->activationAction = 'deactivate';
        $this->showActivationModal = true;
    }

    public function processPensumActivation()
    {
        if ($this->selectedPensumForActivation) {
            try {
                $this->startLoading();

                if ($this->activationAction == 'activate') {
                    $hasQuestions = $this->pensumHasQuestions($this->selectedPensumForActivation->id);
                    if (!$hasQuestions) {
                        //session()->flash('error', 'No se puede activar el área de formación porque no tiene preguntas activas asociadas.');
                        $this->closeActivationModal();
                        $this->stopLoading();

                        $this->dispatchBrowserEvent('swal', [
                            'title' => "No hay preguntas activas asociadas.",
                            'text' => 'No se puede activar el área de formación porque no tiene preguntas activas asociadas.',
                            'icon' => 'warning',
                            'confirmButtonText' => 'Aceptar'
                        ]);
                        return;
                    }
                }

                $newStatus = $this->activationAction == 'activate';

                $this->selectedPensumForActivation->update([
                    'status_active_diagnostic' => $newStatus
                ]);

                $message = $this->activationAction == 'activate'
                    ? 'El área de formación "' . $this->selectedPensumForActivation->full_name . '" ha sido activada para diagnóstico exitosamente.'
                    : 'El área de formación "' . $this->selectedPensumForActivation->full_name . '" ha sido desactivada para diagnóstico exitosamente.';

                //session()->flash('success', $message);

                $this->dispatchBrowserEvent('swal', [
                    'title' => "Excelente!",
                    'text' => $message,
                    'icon' => 'success',
                    'confirmButtonText' => 'Aceptar'
                ]);

                $this->closeActivationModal();

                $this->emit('refreshComponent');
                $this->stopLoading();
            } catch (Exception $e) {
                //session()->flash('error', 'Error al procesar la acción: ' . $e->getMessage());

                $this->dispatchBrowserEvent('swal', [
                    'title' => "Algo inusual ha ocurrido.",
                    'text' => $e->getMessage(),
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->closeActivationModal();
                $this->stopLoading();
            }
        }
    }

    public function closeActivationModal()
    {
        $this->showActivationModal = false;
        $this->selectedPensumForActivation = null;
        $this->activationAction = 'activate';
    }

    public function pensumHasQuestions($pensumId)
    {
        return DiagQuestion::where('pensum_id', $pensumId)
            ->where('activo', true)
            ->exists();
    }

    public function clearNotifications()
    {
        session()->forget(['success', 'error']);
    }

    public function confirmBulkDeactivatePensums()
    {
        $this->showBulkDeactivationModal = true;
    }

    public function processBulkDeactivation()
    {
        try {
            $this->startLoading();

            $activePensums = Pensum::where('status_active', true)
                //->where('status_active_diagnostic', true)
                ->get();

            $deactivatedCount = 0;

            foreach ($activePensums as $pensum) {
                $pensum->update(['status_active_diagnostic' => false]);
                $deactivatedCount++;
            }

            if ($deactivatedCount > 0) {
                //session()->flash('success', "Se han desactivado {$deactivatedCount} áreas de formación para diagnóstico exitosamente.");
                $this->dispatchBrowserEvent('swal', [
                    'title' => "Excelente!",
                    'text' => 'Se han desactivado ' . $deactivatedCount . ' áreas de formación para diagnóstico exitosamente.',
                    'icon' => 'success',
                    'confirmButtonText' => 'Aceptar'
                ]);
            } else {
                //session()->flash('error', 'No se encontraron áreas de formación activas para desactivar.');

                $this->dispatchBrowserEvent('swal', [
                    'title' => "Ha ocurrido algo inusual",
                    'text' => 'No se encontraron áreas de formación activas para desactivar.',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
            }

            $this->closeBulkDeactivationModal();
            $this->emit('refreshComponent');
            $this->stopLoading();
        } catch (Exception $e) {

            $this->dispatchBrowserEvent('swal', [
                'title' => "Ha ocurrido algo inusual",
                'text' => 'Error al desactivar las áreas de formación: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);

            //session()->flash('error', 'Error al desactivar las áreas de formación: ' . $e->getMessage());

            $this->closeBulkDeactivationModal();
            $this->stopLoading();
        }
    }

    public function closeBulkDeactivationModal()
    {
        $this->showBulkDeactivationModal = false;
    }

    private function getStudentAccuracyStats()
    {
        try {
            $pensumFilter = $this->dashboardGradoFilter ? null : null; // We'll handle grado filter differently

            // Get overall precision stats using the new method
            $precisionStats = DiagAnswer::getOverallPrecisionStats($pensumFilter);

            // If we have a grado filter, we need to apply it manually
            if ($this->dashboardGradoFilter) {
                $grado = Grado::findOrFail($this->dashboardGradoFilter);
                $query = DiagAnswer::with(['selectedOption', 'question.pensum'])
                    ->whereNotNull('completado_at')
                    ->whereNotNull('option_id')
                    ->whereHas('question', function ($q) {
                        $q->where('activo', 1)
                            ->where('tipo_pregunta', 'multiple')
                            ->whereHas('pensum', function ($subQ) {
                                $subQ->where('grado_id', $this->dashboardGradoFilter);
                            });
                    });

                if ($this->dateRange) {
                    $dateFilter = now()->subDays($this->dateRange);
                    $query->where('completado_at', '>=', $dateFilter);
                }

                $answers = $query->get();

                if ($answers->isEmpty()) {
                    $precisionStats = [
                        'precision' => 0,
                        'correct_answers' => 0,
                        'total_answered' => 0,
                        'grado_name' => $grado->name
                    ];
                } else {
                    $correctAnswers = $answers->filter(function ($answer) {
                        return $answer->isCorrect();
                    })->count();

                    $totalAnswered = $answers->count();
                    $precision = $totalAnswered > 0 ? round((100 * $correctAnswers) / $totalAnswered, 2) : 0;

                    $precisionStats = [
                        'precision' => $precision,
                        'correct_answers' => $correctAnswers,
                        'total_answered' => $totalAnswered,
                        'grado_name' => $grado->name
                    ];
                }
            }

            return [
                'accuracy' => $precisionStats['precision'], // Keep the same key name for compatibility
                'correct_answers' => $precisionStats['correct_answers'],
                'total_answered' => $precisionStats['total_answered'],
                'grado_name' => (array_key_exists('grado_name', $precisionStats)) ? $precisionStats['grado_name'] : null,
            ];
        } catch (Exception $e) {
            Log::error('Error in getStudentAccuracyStats: ' . $e->getMessage());
            return [
                'accuracy' => 0,
                'correct_answers' => 0,
                'total_answered' => 0,
            ];
        }
    }

    public function generateAIReport($estudiantId)
    {
        Log::info("generateAIReport Called", ['student_id' => $estudiantId, 'isLoading' => $this->isLoading, 'requestId' => uniqid()]);

        if ($this->isLoading) {
            Log::warning("generateAIReport blocked due to isLoading", ['student_id' => $estudiantId]);
            return; // Prevent double submission
        }
        $this->startLoading();

        // Increase execution time to 5 minutes to prevent timeouts
        set_time_limit(300);

        try {
            $student = Estudiant::find($estudiantId);
            $inscripcion = Inscripcion::where('estudiant_id', $estudiantId)->first();

            // Validate existing report
            $existingReport = DiagReport::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $this->filterDiagMainId ?: 1)
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

            // 1. Gather Data (Sessions & Results) - Only for selected diagnostic
            $diagMainId = $this->filterDiagMainId ?: 1;
            $allSessions = DiagSession::where('estudiant_id', $estudiantId)
                ->where('diag_main_id', $diagMainId)
                ->with(['pensum.asignatura', 'answers.question', 'diagMain'])
                ->get();

            $completedSessions = $allSessions->whereNotNull('completado_at');
            $incompleteSessions = $allSessions->whereNull('completado_at');

            if ($completedSessions->isEmpty()) {
                Log::warning("No completed sessions for student", ['student_id' => $estudiantId, 'diag_main_id' => $diagMainId]);
                $this->emit('show-notification', [
                    'type' => 'warning',
                    'message' => 'El estudiante no tiene sesiones completadas para generar un informe.'
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
                    'motivo' => $session->status ?? 'Sin finalizar', // Assuming 'status' generally holds context if not completed
                    'duracion' => $session->iniciado_at ? 'Iniciada el ' . Carbon::parse($session->iniciado_at)->format('d/m/Y h:i A') . ' - No finalizada' : 'No iniciada',
                ];
            })->values()->toArray();

            // Calculate Global Results Stats
            $global_cerradas_respondidas = DiagAnswer::whereIn('session_id', $completedSessions->pluck('id'))->whereHas('question', function ($q) {
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
                // Optimization: Use eager loaded answers instead of querying DB again
                $session_answers = $session->answers;
                $total_preguntas = $session_answers->count(); // Or use $session->total_preguntas if available/reliable
                $indicators_stats = [];
                $questions_list = [];

                foreach ($session_answers as $answer) {
                    $is_correct = $answer->isCorrect();
                    // Collect Question Details for the list (keep this logic)
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

                // Optimization: Calculate precision in-memory to avoid N+1 query
                $total_multiple = 0;
                $correct_multiple = 0;
                $answered_multiple = 0;

                foreach ($session_answers as $ans) {
                    if ($ans->question && $ans->question->tipo_pregunta === 'multiple') {
                        $total_multiple++; // Total multiple choice questions in this session (answered)
                        $answered_multiple++;
                        if ($ans->isCorrect()) {
                            $correct_multiple++;
                        }
                    }
                }

                // Note: The original calculateStudentPrecision counts 'total_answered'.
                // If we want 'total questions in pensum', that logic is different.
                // Assuming we want precision on ANSWERED questions for this session.

                $precision = $answered_multiple > 0 ? round(($correct_multiple / $answered_multiple) * 100, 2) : 0;
                $aciertos = $correct_multiple;
                $preguntas_respondidas = $answered_multiple;
                $total_preguntas = $preguntas_respondidas; // Matches previous logic

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

                // Limit lists
                $fortalezas = array_slice($fortalezas, 0, 3);
                $necesidades = array_slice($necesidades, 0, 3);

                $areas_evaluadas[] = [
                    'id' => 'SUBJ-' . ($session->pensum_id ?? 'UNK'), // no cambiar el sufijo
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

            //dd($referente_normativo);

            // Referente Curricular (Competencias e Indicadores por Grado/Pensum)
            $referente_curricular = [];
            // Optimized: Use Inscription -> Seccion -> Grado -> Pensums to avoid magic getter issues
            $grado = $student->inscripcion?->seccion?->grado;
            if ($grado && $grado->pensums) {
                // Optimization: Pre-load all competencies for all pensums of the grade
                $gradePensumIds = $grado->pensums->pluck('id');
                $gradeCompetenciesMap = DiagCompetency::whereIn('pensum_id', $gradePensumIds)
                    ->with('indicators')
                    ->get()
                    ->groupBy('pensum_id');

                foreach ($grado->pensums as $pensum) {
                    // Fetch competencies for this pensum from memory
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
            // Optimized: Pre-load all indicators for relevant pensums
            $relevantPensumIds = $completedSessions->pluck('pensum_id')->unique();
            $allCompetencies = DiagCompetency::whereIn('pensum_id', $relevantPensumIds)
                ->with('indicators')
                ->get()
                ->groupBy('pensum_id');

            // Re-iterate sessions to match indicators with results
            foreach ($completedSessions as $session) {
                if ($session->pensum) {
                    $session_answers = $session->answers;

                    // Get indicators from pre-loaded collection
                    $pensumCompetencies = $allCompetencies->get($session->pensum_id) ?? collect();
                    $indicators = $pensumCompetencies->pluck('indicators')->flatten();

                    foreach ($indicators as $indicator) {
                        // Filter answers for this indicator
                        $ind_answers = $session_answers->filter(function ($ans) use ($indicator) {
                            return $ans->question && $ans->question->indicator_id == $indicator->id;
                        });

                        $total = $ind_answers->count();
                        if ($total > 0) {
                            $correct = $ind_answers->where('is_correct', 1)->count();
                            $precision = ($correct / $total) * 100;

                            // Determine observed level
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

                            // Expected level mapping
                            $expected_val = 3; // Default Satisfactory
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
                            // No evidence
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
                    'id' => 'EST-' . now()->format('Y') . $student->id, // Generating a format similar to example
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
                    'nombre' => $student->inscripcion?->seccion?->grado?->name ?? 'N/A', // Optimized access
                    'seccion' => 'Sección ' . ($student->seccion?->name ? "'{$student->seccion->name}'" : 'N/A'),
                    'turno' => 'Mañana', // Defaulting as no explicit field found in Seccion/Grado
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
            Log::info('Initiating AI Report Generation', [
                'student_id' => $student->id,
                'service' => $this->selected_ai_service,
                'payload_keys' => array_keys($payload),
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

            Log::info('AI Response Received', [
                'service' => $this->selected_ai_service,
                'response_type' => gettype($aiResponse),
                'is_empty' => empty($aiResponse)
            ]);

            if ($aiResponse) {
                // Save to Database
                $reportId = $this->saveReportToDatabase($payload, $aiResponse, $student, $institucion);
                Log::info('Report Saved Successfully', ['report_id' => $reportId]);


                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Informe Generado',
                    'text' => 'El informe ha sido generado y guardado exitosamente. La página se recargará.',
                    'icon' => 'success',
                    'timer' => 3000,
                    'showConfirmButton' => false
                ]);

                // FORCE FINISH: Redirect to refresh state completely
                Log::info("Report generation finished successfully", ['student_id' => $estudiantId]);

                // Refresh the data to ensure the button updates in the UI
                $this->emit('refreshDashboard');
                $this->stopLoading();
                return;
                //return $reportId;
                // Delay slightly to let swal show? No, redirect happens fast.
                // Let's rely on session flash or just reload.
                // return redirect()->to(request()->header('Referer'));
            } else {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Error de Generación',
                    'text' => 'No se pudo generar el informe. La respuesta del servicio de IA fue nula o incompleta.',
                    'icon' => 'error',
                    'confirmButtonText' => 'Aceptar'
                ]);
                $this->stopLoading();
                return;
                //return null;
            }
        } catch (Exception $e) {
            Log::error('Error generating AI report: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al generar el informe: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
            $this->stopLoading();
            return;
            //return null;
        }

        $this->stopLoading();
    }

    /**
     * Persist generated AI report (LLM output stored ONLY as draft)
     */
    private function saveReportToDatabase($payload, $aiResponse, $student, $institution)
    {
        return DB::transaction(function () use ($payload, $aiResponse, $student) {

            // Normalize AI response (string | array | invalid)
            $aiData = is_array($aiResponse)
                ? $aiResponse
                : json_decode($aiResponse, true);

            if (!is_array($aiData)) {
                $aiData = [
                    'error' => 'Invalid or non-JSON AI response',
                    'raw_output' => is_string($aiResponse) ? $aiResponse : null,
                ];
            }

            /** ----------------------------------------------------------------
             * 1. Create minimal DiagReport (NO AI DATA HERE)
             * ---------------------------------------------------------------- */

            // Validation: Check if report already exists inside transaction for safety
            $existingReport = DiagReport::where('estudiant_id', $student->id)
                ->where('diag_main_id', 1) // Using 1 as default per original logic
                ->first();

            if ($existingReport) {
                // If it exists, return -1 or handle as needed to avoid duplicate
                // Ideally we should update it or just return it.
                // For now, let's return it so the caller knows it "succeeded" (is present).
                return $existingReport->id;
            }

            $report = DiagReport::create([
                'estudiant_id'  => $student->id,
                'diag_main_id'  => 1,
                'referent_id'   => DiagReferent::where('active', 1)->value('id'),
                'lapso_id'      => Lapso::current()->id ?? null,
                'status'        => 'generated',
                'generated_at'  => now(),
                // IMPORTANT: global analysis REMOVED from canonical table
                'global'        => null,
            ]);

            /** ----------------------------------------------------------------
             * 2. Persist AI Draft (ONLY SOURCE OF LLM DATA)
             * ---------------------------------------------------------------- */
            $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
            $userPrompt   = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

            DiagReportAiDraft::create([
                'report_id'           => $report->id,
                'llm_provider'        => $this->selected_ai_service,
                'llm_model'           => $this->selected_ai_service === 'deepseek'
                    ? 'deepseek-chat'
                    : ($this->selected_ai_service === 'qwen'
                        ? 'qwen-max'
                        : ($this->selected_ai_service === 'gemini'
                            ? 'gemini-2.5-flash'
                            : config('services.openrouter.model'))),
                'system_prompt_id'    => $systemPrompt->id ?? null,
                'user_prompt_id'      => $userPrompt->id ?? null,
                'prompt_version_label' => ($systemPrompt->version ?? 'v1')
                    . '/'
                    . ($userPrompt->version ?? 'v1'),
                'input_hash'          => hash('sha256', json_encode($payload)),
                'output_text'         => json_encode(
                    $aiData,
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                ),
                'status'              => 'generated',
            ]);

            /** ----------------------------------------------------------------
             * 3. Record Precision in DiagResult
             * ---------------------------------------------------------------- */
            $precisionStats = DiagAnswer::getStudentPrecisionStats($student->id);

            DiagResult::create([
                'report_id'                => $report->id,
                'total_answered_questions' => $precisionStats['total_answered'],
                'precision'                => $precisionStats['precision'],
                'open_ended_response_level' => 0, // Default for now
            ]);

            // 3.2 Record PER-AREA precision in DiagReportPensum
            $pensumIds = DiagAnswer::where('estudiant_id', $student->id)
                ->whereHas('question', function ($q) {
                    $q->where('activo', 1);
                })
                ->get()
                ->pluck('question.pensum_id')
                ->unique()
                ->filter();

            foreach ($pensumIds as $pensumId) {
                $areaStats = DiagAnswer::getStudentPrecisionStats($student->id, $pensumId);

                DiagReportPensum::create([
                    'report_id'                => $report->id,
                    'pensum_id'                => $pensumId,
                    'total_answered_questions' => $areaStats['total_answered'],
                    'precision'                => $areaStats['precision'],
                    'open_ended_level'         => 'MEDIUM', // Default
                ]);
            }

            /** ----------------------------------------------------------------
             * 4. Audit Log
             * ---------------------------------------------------------------- */
            DiagReportAuditLog::create([
                'report_id'   => $report->id,
                'user_id'     => auth()->id() ?? 1,
                'action'      => 'ai_generation',
                'details'     => 'AI draft stored successfully',
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);

            return $report->id;
        });
    }

    /**
     * Delete generated report for a student
     */
    public function deleteAIReport($estudiantId)
    {
        try {
            DB::transaction(function () use ($estudiantId) {
                $report = DiagReport::where('estudiant_id', $estudiantId)
                    ->where('diag_main_id', $this->filterDiagMainId ?: 1)
                    ->latest()
                    ->first();

                if ($report) {
                    // Manually delete related records if foreign keys are not cascading or for safety
                    $report->drafts()->delete();
                    $report->results()->delete();
                    $report->pensumResults()->delete();
                    $report->indicatorResults()->delete();
                    // Audit logs might be kept for history, but user asked for "complete" deletion.
                    // Assuming "complete" means wiping it out.
                    \App\Models\app\Instrument\DiagReportAuditLog::where('report_id', $report->id)->delete();

                    $report->delete();

                    $this->dispatchBrowserEvent('swal', [
                        'title' => 'Informe Eliminado',
                        'text' => 'El informe y todos sus datos asociados han sido eliminados correctamente.',
                        'icon' => 'success',
                        'timer' => 3000,
                        'showConfirmButton' => false
                    ]);
                } else {
                    $this->dispatchBrowserEvent('swal', [
                        'title' => 'Error',
                        'text' => 'No se encontró un informe para eliminar.',
                        'icon' => 'error',
                        'confirmButtonText' => 'Aceptar'
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error deleting AI report: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar el informe: ' . $e->getMessage(),
                'icon' => 'error',
                'confirmButtonText' => 'Aceptar'
            ]);
        }
    }

    public function viewAIReport($estudiantId)
    {
        $this->selectedReport = DiagReport::where('estudiant_id', $estudiantId)
            ->with([
                'latestDraft',
                'results', // puede existir o no
                'pensumResults.pensum.asignatura',
                'indicatorResults.indicator',
                'lapso',
                'referent',
                'diagMain',
                // 'estudiant.grado',
                // 'estudiant.seccion',
            ])
            ->where('diag_main_id', $this->filterDiagMainId)
            ->latest()
            ->first();

        if (!$this->selectedReport || !$this->selectedReport->latestDraft) {
            $this->stopLoading();

            $this->dispatchBrowserEvent('swal', [
                'title' => 'No hay informe',
                'text'  => 'No se encontró un informe generado por IA.',
                'icon'  => 'warning',
            ]);

            return;
        }

        $this->showReportModal = true;
        $this->stopLoading();
    }


    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->selectedReport = null;
    }

    /**
     * Helper to extract Pensum ID from payload code
     */
    private function getPensumIdFromCode($code)
    {
        // 1. If strictly numeric, return
        if (is_numeric($code)) {
            return (int)$code;
        }

        // 2. Try to parse "PEN-123", "MAT-4TO" (if 4TO is ID? No, usually ID is int)
        // Assuming format "PREFIX-ID"
        if (preg_match('/^[A-Za-z]+-(\d+)$/', $code, $matches)) {
            return (int)$matches[1];
        }

        // 3. Fallback: Log and return null
        Log::warning("getPensumIdFromCode: Could not parse ID from code '{$code}'");
        return null;
    }

    // Section Methods
    private function getSectionsPaginated()
    {
        $query = Seccion::with(['grado', 'inscripcions'])
            ->where('status_active', 'true');

        if ($this->filterGradoId) {
            $query->where('grado_id', $this->filterGradoId);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhereHas('grado', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        }

        return $query->paginate(10, ['*'], 'sectionsPage');
    }

    public function generateSectionReport($sectionId)
    {
        if (!$this->filterDiagMainId) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Atención',
                'text' => 'Debe seleccionar un diagnóstico en el filtro superior para generar el reporte de sección.',
                'icon' => 'warning'
            ]);
            return;
        }

        $this->startLoading();

        try {
            $aggregator = app(\App\Services\Diagnostic\Section\SectionDiagnosticAggregatorService::class);
            $builder = app(\App\Services\Diagnostic\Section\SectionReportBuilder::class);

            $data = $aggregator->aggregate($sectionId, $this->filterDiagMainId);

            if (empty($data)) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Sin Datos',
                    'text' => 'No se encontraron informes individuales para esta sección y diagnóstico.',
                    'icon' => 'info'
                ]);
            } else {
                $builder->build($data);
                $this->dispatchBrowserEvent('swal', [
                    'title' => 'Éxito',
                    'text' => 'Informe de sección generado correctamente.',
                    'icon' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error generating section report: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Error',
                'text' => 'Ocurrió un error al generar el informe: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }

        $this->stopLoading();
    }

    public function viewSectionReport($sectionId)
    {
        if (!$this->filterDiagMainId) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'Atención',
                'text' => 'Debe seleccionar un diagnóstico en el filtro superior para ver el reporte.',
                'icon' => 'warning'
            ]);
            return;
        }

        $report = \App\Models\app\Instrument\SectionDiagnosticReport::where('section_id', $sectionId)
            ->where('diagnostic_id', $this->filterDiagMainId)
            ->with(['globalResult', 'areaResults.insights', 'profile', 'contrast', 'recommendations'])
            ->first();

        if (!$report) {
            $this->dispatchBrowserEvent('swal', [
                'title' => 'No encontrado',
                'text' => 'Aún no se ha generado un reporte para esta sección y diagnóstico.',
                'icon' => 'info'
            ]);
            return;
        }

        $this->selectedSectionReport = $report;
        $this->showSectionReportModal = true;
    }

    public function closeSectionReportModal()
    {
        $this->showSectionReportModal = false;
        $this->selectedSectionReport = null;
    }
}
