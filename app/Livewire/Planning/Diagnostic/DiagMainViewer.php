<?php

namespace App\Livewire\Planning\Diagnostic;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DiagMainViewer extends Component
{
    use WithPagination;

    #[Url]
    public ?int $selectedId = null;

    // Filtros + paginación para "Resumen por área de formación" — anidados pestudio→grado→pensum
    public string $progressSearch = '';

    public ?int $progressPestudioId = null;

    public ?int $progressGradoId = null;

    public ?int $progressPensumId = null;

    public int $paginate = 10;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        // #[Url] hidrata selectedId desde la query string antes del mount:
        // no forzar null para no borrar el parámetro de la URL.
        if ($this->selectedId) {
            $this->dispatch('diag-main-selected', id: $this->selectedId);
        }
    }

    public function updatedSelectedId(): void
    {
        $this->progressSearch = '';
        $this->progressGradoId = null;
        $this->progressPensumId = null;
        $this->resetPage('pensumProgressPage');
        $this->dispatch('diag-main-selected', id: $this->selectedId);
    }

    public function triggerCreate(): void
    {
        $this->dispatch('open-diag-create');
    }

    public function updatedProgressSearch(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressPestudioId(): void
    {
        $this->progressGradoId = null;
        $this->progressPensumId = null;
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressGradoId(): void
    {
        $this->progressPensumId = null;
        $this->resetPage('pensumProgressPage');
    }

    public function updatedProgressPensumId(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function updatedPaginate(): void
    {
        $this->resetPage('pensumProgressPage');
    }

    public function render()
    {
        $diagMains = DiagMain::with(['lapso', 'pestudio', 'referent'])
            ->orderByRaw('active DESC')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'active', 'lapso_id', 'pestudio_id', 'referent_id']);

        $selected = null;
        if ($this->selectedId) {
            $selected = DiagMain::with(['lapso', 'pestudio', 'referent'])
                ->withCount(['questions', 'sessions'])
                ->find($this->selectedId);
        }

        // Stats y display derivados — corrige cifras cuando diag_main_id no está en sesiones
        $completedSessions = null;
        $studentsEvaluated = null;
        $displayPestudio = null;
        $displayLapso = null;
        $displayReferent = null;
        $questionsCount = null;
        $sessionsCount = null;
        $precision = null;
        $precisionCorrect = 0;
        $precisionTotal = 0;
        $pensumProgress = collect();
        $recentSessions = collect();
        $questionsByType = collect();
        $questionsByDifficulty = collect();
        $progressPestudios = collect();
        $progressGrados = collect();
        $progressPensums = collect();

        // Nuevos indicadores solicitados (pensums/respuestas) + completitud/abandono s2526
        $pensumsWithAnswersCount = null;
        $questionsWithAnswersCount = null;
        $totalAnswersCount = null;
        $completionRate = null;
        $abandonRate = null;

        if ($selected) {
            $questionsCount = $selected->questions_count;

            // Pensums vinculados a las preguntas de este diagnóstico
            $questionPensumIds = DiagQuestion::where('diag_main_id', $selected->id)->pluck('pensum_id')->unique()->filter()->values();

            // Cálculo de pensums con al menos un answer / preguntas con respuestas / total answers
            $answerScoped = DiagAnswer::whereHas('question', fn ($q) => $q->where('diag_main_id', $selected->id))->whereNotNull('completado_at');
            $totalAnswersCount = (clone $answerScoped)->count();
            $answerQuestionIds = (clone $answerScoped)->pluck('question_id')->unique()->filter()->values();
            $questionsWithAnswersCount = $answerQuestionIds->count();
            $pensumsWithAnswersCount = $answerQuestionIds->isNotEmpty()
                ? DiagQuestion::whereIn('id', $answerQuestionIds)->pluck('pensum_id')->unique()->filter()->count()
                : 0;

            // Sesiones: primero por diag_main_id, si da 0 usar pensum de las preguntas (datos reales)
            $sessionsCount = DiagSession::where('diag_main_id', $selected->id)->count();
            $sessionQuery = $sessionsCount > 0
                ? DiagSession::where('diag_main_id', $selected->id)
                : DiagSession::whereIn('pensum_id', $questionPensumIds);

            if ($questionPensumIds->isNotEmpty() && $sessionsCount === 0) {
                $sessionsCount = (clone $sessionQuery)->count();
            }

            $completedSessions = (clone $sessionQuery)->whereNotNull('completado_at')->count();
            $studentsEvaluated = (clone $sessionQuery)->whereNotNull('estudiant_id')->distinct('estudiant_id')->count('estudiant_id');

            // Precisión global del diagnóstico (como en s2526: correctas / total contestadas)
            $answersBase = DiagAnswer::whereHas('question', function ($q) use ($selected) {
                $q->where('diag_main_id', $selected->id)->where('activo', 1)->where('tipo_pregunta', 'multiple');
            })->whereNotNull('completado_at')->whereNotNull('option_id');

            $precisionTotal = (clone $answersBase)->count();
            $precisionCorrect = (clone $answersBase)->whereHas('selectedOption', fn ($q) => $q->where('valor', 1))->count();
            $precision = $precisionTotal > 0 ? round((100 * $precisionCorrect) / $precisionTotal, 1) : null;

            // % Completitud y Tasa Abandono (s2526: getGeneralStats / analytics.blade.php:70)
            $completionRate = $sessionsCount > 0 ? round((100 * $completedSessions) / $sessionsCount, 1) : null;
            $abandonRate = $sessionsCount > 0 ? round(100 - $completionRate, 1) : null;

            // Pensum progress — inspirado en s2526 getPensumProgress (con precisión por área)
            if ($questionPensumIds->isNotEmpty()) {
                $pensumProgress = Pensum::whereIn('id', $questionPensumIds)->with(['asignatura', 'grado'])->get()->map(function (Pensum $pensum) use ($selected) {
                    $pid = $pensum->id;
                    $totalQ = DiagQuestion::where('pensum_id', $pid)->where('diag_main_id', $selected->id)->where('activo', 1)->count();
                    $totalS = DiagSession::where('pensum_id', $pid)->count();
                    if ($totalS === 0) {
                        $totalS = DiagSession::where('diag_main_id', $selected->id)->where('pensum_id', $pid)->count();
                    }
                    $completedS = DiagSession::where('pensum_id', $pid)->whereNotNull('completado_at')->count();
                    $completion = $totalS > 0 ? round((100 * $completedS) / $totalS, 1) : 0;

                    $ansBase = DiagAnswer::whereHas('question', fn ($q) => $q->where('pensum_id', $pid)->where('diag_main_id', $selected->id)->where('tipo_pregunta', 'multiple'))->whereNotNull('completado_at')->whereNotNull('option_id');
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
            }

            // Filtros + paginación para el resumen (search / grado / pensum) — como s2526
            $progressPestudios = collect();
            $progressGrados = collect();
            $progressPensums = collect();
            if ($questionPensumIds->isNotEmpty()) {
                $progressPestudios = Pestudio::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('pestudio_id'))->orderBy('code')->get(['id', 'code', 'name']);
                if ($this->progressPestudioId) {
                    $progressGrados = Grado::where('pestudio_id', $this->progressPestudioId)->whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
                    $progressPensums = Pensum::whereIn('id', $questionPensumIds)->where('pestudio_id', $this->progressPestudioId)->with(['asignatura', 'grado'])->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
                    if ($this->progressGradoId) {
                        $progressPensums = $progressPensums->where('grado_id', $this->progressGradoId)->values();
                    }
                } else {
                    $progressGrados = Grado::whereIn('id', Pensum::whereIn('id', $questionPensumIds)->pluck('grado_id'))->where('status_active', 'true')->orderBy('pestudio_id')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
                    $progressPensums = Pensum::whereIn('id', $questionPensumIds)->with(['asignatura', 'grado'])->orderBy('pestudio_id')->orderBy('grado_id')->get(['id', 'grado_id', 'pestudio_id', 'asignatura_id']);
                    if ($this->progressGradoId) {
                        $progressPensums = $progressPensums->where('grado_id', $this->progressGradoId)->values();
                    }
                }
            }

            // Aplicar filtros al collection ya ordenado y paginar
            $filteredProgress = $pensumProgress;
                if ($this->progressSearch !== '') {
                    $s = mb_strtolower($this->progressSearch);
                    $filteredProgress = $filteredProgress->filter(fn ($pp) => str_contains(mb_strtolower($pp->fullname ?? ''), $s) || str_contains(mb_strtolower($pp->pensum->asignatura?->name ?? ''), $s) || str_contains(mb_strtolower($pp->pensum->asignatura?->code ?? ''), $s));
                }
                if ($this->progressPestudioId) {
                    $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->pestudio_id === (int) $this->progressPestudioId);
                }
                if ($this->progressGradoId) {
                    $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->grado_id === (int) $this->progressGradoId);
                }
                if ($this->progressPensumId) {
                    $filteredProgress = $filteredProgress->filter(fn ($pp) => (int) $pp->pensum->id === (int) $this->progressPensumId);
                }
            $perPage = max(1, (int) $this->paginate);
            $currentPage = LengthAwarePaginator::resolveCurrentPage('pensumProgressPage');
            $total = $filteredProgress->count();
            $items = $filteredProgress->forPage($currentPage, $perPage)->values();
            $pensumProgress = new LengthAwarePaginator($items, $total, $perPage, $currentPage, ['path' => request()->url(), 'pageName' => 'pensumProgressPage']);

            // Sesiones recientes y distribución por tipo/dificultad (como en s2526 dashboard)
            $recentSessions = (clone $sessionQuery)->with(['estudiant', 'pensum'])->orderByDesc('iniciado_at')->limit(5)->get();
            $questionsByType = DiagQuestion::where('diag_main_id', $selected->id)->select('tipo_pregunta as type', DB::raw('count(*) as count'))->groupBy('tipo_pregunta')->get();
            $questionsByDifficulty = DiagQuestion::where('diag_main_id', $selected->id)->select('difficulty', DB::raw('count(*) as count'))->groupBy('difficulty')->get();

            // Display: si el campo directo es null, derivar de las preguntas
            $displayLapso = $selected->lapso;
            $displayPestudio = $selected->pestudio;
            $displayReferent = $selected->referent;

            if (! $displayPestudio && $questionPensumIds->isNotEmpty()) {
                $pestudioIds = Pensum::whereIn('id', $questionPensumIds)->pluck('pestudio_id')->unique()->filter()->values();
                if ($pestudioIds->count() === 1) {
                    $displayPestudio = Pestudio::find($pestudioIds->first());
                } elseif ($pestudioIds->count() > 1) {
                    $displayPestudio = null;
                }
            }
        }

        return view('livewire.planning.diagnostic.diag-main-viewer', [
            'diagMains' => $diagMains,
            'selected' => $selected,
            'completedSessions' => $completedSessions,
            'studentsEvaluated' => $studentsEvaluated,
            'displayPestudio' => $displayPestudio ?? null,
            'displayLapso' => $displayLapso ?? null,
            'displayReferent' => $displayReferent ?? null,
            'questionsCount' => $questionsCount,
            'sessionsCount' => $sessionsCount,
            'pensumsWithAnswersCount' => $pensumsWithAnswersCount,
            'questionsWithAnswersCount' => $questionsWithAnswersCount,
            'totalAnswersCount' => $totalAnswersCount,
            'completionRate' => $completionRate,
            'abandonRate' => $abandonRate,
            'precision' => $precision,
            'precisionCorrect' => $precisionCorrect,
            'precisionTotal' => $precisionTotal,
            'pensumProgress' => $pensumProgress,
            'recentSessions' => $recentSessions,
            'questionsByType' => $questionsByType,
            'questionsByDifficulty' => $questionsByDifficulty,
            'progressPestudios' => $progressPestudios,
            'progressGrados' => $progressGrados,
            'progressPensums' => $progressPensums,
        ]);
    }
}
