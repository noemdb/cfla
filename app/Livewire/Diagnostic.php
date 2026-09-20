<?php

namespace App\Livewire;

use App\Models\app\Academy\Pensum;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Diagnostic extends Component
{
    use WireUiActions;

    public $currentView = 'student-identification'; // student-identification, dashboard, wizard, summary, guide

    public $studentCi = '';

    public $currentStudentId = null;

    public $isStudentVerified = false;

    // Estados principales
    public $selectedPensumId = null;

    public $currentSessionId = null;

    // Wizard
    public $currentQuestionIndex = 0;

    public $currentQuestionId = null;

    public $selectedAnswer = null;

    public $answers = [];

    public $progress = 0;

    public $isReviewMode = false;

    public $showAnsweredQuestions = false;

    public $questionIds = [];

    public $unansweredQuestionIds = [];

    public $answeredQuestionIds = [];

    public $showAnsweredModal = false;

    // Guide
    public $activeTab = 'overview'; // overview, process, questions, tips

    // Datos
    public $pensums = [];

    public $sessionStats = [];

    public $results = [];

    public $isProcessing = false;

    // Cachés por request (no se serializan entre peticiones Livewire).
    private ?Estudiant $studentModel = null;

    private bool $studentLoaded = false;

    private ?Pensum $pensumModel = null;

    private bool $pensumLoaded = false;

    private ?DiagSession $sessionModel = null;

    private bool $sessionLoaded = false;

    private ?DiagQuestion $questionModel = null;

    private bool $questionLoaded = false;

    protected $listeners = [
        'startDiagnostic',
        'nextQuestion',
        'previousQuestion',
        'finishDiagnostic',
        'reviewAnswers',
        'toggleQuestionView',
        'openAnsweredQuestionsModal',
        'closeAnsweredQuestionsModal',
    ];

    protected function rules()
    {
        $rules = [];

        if ($this->currentView === 'student-identification') {
            $rules['studentCi'] = 'required|string|min:6|max:15';
        }

        return $rules;
    }

    protected $messages = [
        'studentCi.required' => 'La cédula es obligatoria.',
        'studentCi.min' => 'La cédula debe tener al menos 6 caracteres.',
        'studentCi.max' => 'La cédula no puede tener más de 15 caracteres.',
    ];

    public function mount()
    {
        $this->currentView = 'student-identification';
        $this->activeTab = 'overview';
    }

    // ─────────────────────────────────────────────────────────────
    // Hidratación de modelos bajo demanda (evita serializar
    // colecciones Eloquent en propiedades públicas).
    // ─────────────────────────────────────────────────────────────

    private function student(): ?Estudiant
    {
        if (! $this->studentLoaded) {
            $this->studentModel = $this->currentStudentId ? Estudiant::find($this->currentStudentId) : null;
            $this->studentLoaded = true;
        }

        return $this->studentModel;
    }

    private function pensum(): ?Pensum
    {
        if (! $this->pensumLoaded) {
            $this->pensumModel = $this->selectedPensumId
                ? Pensum::with('asignatura')->find($this->selectedPensumId)
                : null;
            $this->pensumLoaded = true;
        }

        return $this->pensumModel;
    }

    private function session(): ?DiagSession
    {
        if (! $this->sessionLoaded) {
            $this->sessionModel = $this->currentSessionId ? DiagSession::find($this->currentSessionId) : null;
            $this->sessionLoaded = true;
        }

        return $this->sessionModel;
    }

    private function question(): ?DiagQuestion
    {
        if (! $this->questionLoaded) {
            $this->questionModel = $this->currentQuestionId
                ? DiagQuestion::with('options')->find($this->currentQuestionId)
                : null;
            $this->questionLoaded = true;
        }

        return $this->questionModel;
    }

    private function forgetPensum(): void
    {
        $this->pensumModel = null;
        $this->pensumLoaded = false;
    }

    private function forgetSession(): void
    {
        $this->sessionModel = null;
        $this->sessionLoaded = false;
    }

    private function forgetQuestion(): void
    {
        $this->questionModel = null;
        $this->questionLoaded = false;
    }

    public function verifyStudent()
    {
        $this->validate([
            'studentCi' => 'required|string|min:6|max:15',
        ]);

        try {
            $student = Estudiant::where('ci_estudiant', $this->studentCi)->first();

            if (! $student) {
                $this->addError('studentCi', 'No se encontró un estudiante con esta cédula.');

                return;
            }

            if ($student->status_active != 'true') {
                $this->addError('studentCi', 'El estudiante no está activo en el sistema.');

                return;
            }

            $this->currentStudentId = $student->id;
            $this->studentModel = $student;
            $this->studentLoaded = true;
            $this->isStudentVerified = true;

            $this->loadStudentData();

            $this->currentView = 'dashboard';

            $this->notification()->success(
                'Bienvenido/a',
                'Hola '.($student->user?->name ?? $student->full_name).'. Puedes comenzar tu diagnóstico.'
            );
        } catch (Exception $e) {
            session()->flash('error', 'Error al verificar estudiante: '.$e->getMessage());
        }
    }

    private function loadStudentData()
    {
        if (! $this->currentStudentId) {
            return;
        }

        try {
            $this->loadAvailablePensums();
            $this->loadSessionStats();
        } catch (Exception $e) {
            session()->flash('error', 'Error al cargar datos del estudiante: '.$e->getMessage());
        }
    }

    public function loadAvailablePensums()
    {
        $student = $this->student();

        if (! $student) {
            $this->pensums = [];

            return;
        }

        // Filtramos por status_active_diagnostic = true para garantizar que
        // solo se muestren los pensums activos para diagnóstico.
        $studentPensums = $student->pensums
            ->where('status_active_diagnostic', true)
            ->load('asignatura');

        $pensumIds = $studentPensums->pluck('id')->all();

        if (empty($pensumIds)) {
            $this->pensums = [];

            return;
        }

        // Una sola query por agregado en vez de N+1 por pensum.
        $activeCounts = DiagQuestion::whereIn('pensum_id', $pensumIds)
            ->where('activo', true)
            ->selectRaw('pensum_id, COUNT(*) as total')
            ->groupBy('pensum_id')
            ->pluck('total', 'pensum_id');

        $completedCounts = DiagAnswer::join('diag_questions', 'diag_questions.id', '=', 'diag_answers.question_id')
            ->where('diag_answers.estudiant_id', $student->id)
            ->whereIn('diag_questions.pensum_id', $pensumIds)
            ->selectRaw('diag_questions.pensum_id, COUNT(*) as total')
            ->groupBy('diag_questions.pensum_id')
            ->pluck('total', 'pensum_id');

        $difficultyDistribution = DiagQuestion::whereIn('pensum_id', $pensumIds)
            ->where('activo', true)
            ->selectRaw('pensum_id, difficulty, COUNT(*) as total')
            ->groupBy('pensum_id', 'difficulty')
            ->get()
            ->groupBy('pensum_id')
            ->map(fn ($rows) => $rows->pluck('total', 'difficulty')->toArray());

        $this->pensums = $studentPensums
            ->filter(fn ($pensum) => ($activeCounts[$pensum->id] ?? 0) > 0)
            ->map(function ($pensum) use ($activeCounts, $completedCounts, $difficultyDistribution) {
                $totalQuestions = (int) ($activeCounts[$pensum->id] ?? 0);
                $completedQuestions = (int) ($completedCounts[$pensum->id] ?? 0);

                return [
                    'id' => $pensum->id,
                    'name' => $pensum->asignatura->full_name ?? 'Área sin nombre',
                    'description' => $pensum->asignatura->description ?? 'Sin descripción',
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $completedQuestions,
                    'progress_percentage' => $totalQuestions > 0 ? min(100, round(($completedQuestions / $totalQuestions) * 100)) : 0,
                    'is_completed' => $completedQuestions >= $totalQuestions,
                    'difficulty_distribution' => $difficultyDistribution[$pensum->id] ?? [],
                ];
            })
            ->values()
            ->toArray();
    }

    public function loadSessionStats()
    {
        if (! $this->currentStudentId) {
            $this->sessionStats = [];

            return;
        }

        $this->sessionStats = [
            'total_sessions' => DiagSession::where('estudiant_id', $this->currentStudentId)->count(),
            'completed_sessions' => DiagSession::where('estudiant_id', $this->currentStudentId)
                ->whereNotNull('completado_at')->count(),
            'total_answers' => DiagAnswer::where('estudiant_id', $this->currentStudentId)->count(),
            'average_progress' => DiagSession::where('estudiant_id', $this->currentStudentId)
                ->avg('progreso') ?? 0,
        ];
    }

    public function startDiagnostic($pensumId)
    {
        try {
            DB::beginTransaction();

            $pensum = Pensum::with('asignatura')->find($pensumId);

            if (! $pensum) {
                throw new Exception('Área no encontrada.');
            }

            $this->selectedPensumId = $pensumId;
            $this->pensumModel = $pensum;
            $this->pensumLoaded = true;

            $totalQuestions = DiagQuestion::where('pensum_id', $pensumId)
                ->where('activo', true)
                ->count();

            if ($totalQuestions === 0) {
                throw new Exception('No hay preguntas disponibles para esta área.');
            }

            $this->isReviewMode = false;
            $this->showAnsweredQuestions = false;

            // Crear o recuperar sesión activa.
            $session = DiagSession::firstOrCreate([
                'estudiant_id' => $this->currentStudentId,
                'pensum_id' => $pensumId,
                'activo' => true,
                'completado_at' => null,
            ], [
                'iniciado_at' => now(),
                'total_preguntas' => $totalQuestions,
                'progreso' => 0,
            ]);

            $this->currentSessionId = $session->id;
            $this->sessionModel = $session;
            $this->sessionLoaded = true;

            $this->refreshQuestionIds();

            if (empty($this->questionIds)) {
                DB::rollBack();
                session()->flash('success', 'Has completado todas las preguntas de esta área.');
                $this->backToDashboard();

                return;
            }

            $this->currentQuestionIndex = 0;
            $this->setCurrentQuestion();
            $this->loadExistingAnswers();
            $this->currentView = 'wizard';

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al iniciar diagnóstico: '.$e->getMessage());
        }
    }

    /**
     * Recalcula los IDs de preguntas (pendientes/contestadas) sin cargar
     * modelos ni opciones: solo pluck de IDs.
     */
    private function refreshQuestionIds(): void
    {
        if (! $this->selectedPensumId) {
            $this->questionIds = [];
            $this->unansweredQuestionIds = [];
            $this->answeredQuestionIds = [];

            return;
        }

        $allIds = DiagQuestion::where('pensum_id', $this->selectedPensumId)
            ->where('activo', true)
            ->orderBy('orden')
            ->pluck('id')
            ->all();

        $answeredIds = DiagAnswer::where('estudiant_id', $this->currentStudentId)
            ->whereHas('question', fn ($query) => $query->where('pensum_id', $this->selectedPensumId))
            ->pluck('question_id')
            ->all();

        $unanswered = array_values(array_diff($allIds, $answeredIds));
        shuffle($unanswered);

        $this->answeredQuestionIds = array_values(array_intersect($allIds, $answeredIds));
        $this->unansweredQuestionIds = $unanswered;
        $this->questionIds = $this->showAnsweredQuestions
            ? $this->answeredQuestionIds
            : $this->unansweredQuestionIds;
    }

    public function toggleQuestionView()
    {
        $this->showAnsweredQuestions = ! $this->showAnsweredQuestions;

        $this->questionIds = $this->showAnsweredQuestions
            ? $this->answeredQuestionIds
            : $this->unansweredQuestionIds;

        $this->currentQuestionIndex = 0;
        $this->setCurrentQuestion();
        $this->loadExistingAnswers();
    }

    public function loadExistingAnswers()
    {
        if (! $this->currentSessionId || empty($this->questionIds)) {
            $this->answers = [];
            $this->updateProgress();

            return;
        }

        $existingAnswers = DiagAnswer::where('session_id', $this->currentSessionId)
            ->get()
            ->keyBy('question_id');

        $this->answers = [];
        foreach ($this->questionIds as $index => $questionId) {
            if (isset($existingAnswers[$questionId])) {
                $this->answers[$index] = $existingAnswers[$questionId]->respuesta;
            }
        }

        $this->updateProgress();
    }

    public function setCurrentQuestion()
    {
        $this->currentQuestionId = $this->questionIds[$this->currentQuestionIndex] ?? null;
        $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
        $this->forgetQuestion();
    }

    #[Renderless]
    public function saveAnswer(): bool
    {
        $question = $this->question();

        if (! $this->selectedAnswer || ! $question || ! $this->currentSessionId) {
            return false;
        }

        if ($question->tipo_pregunta === 'open'
            && mb_strlen(trim((string) $this->selectedAnswer)) < 2) {
            $this->notification()->error(
                'Respuesta muy corta',
                'Para preguntas abiertas, la respuesta debe tener al menos 2 caracteres.'
            );

            return false;
        }

        try {
            DB::beginTransaction();

            $optionId = null;

            if ($question->tipo_pregunta === 'multiple') {
                $option = $question->options
                    ->where('opcion', $this->selectedAnswer)
                    ->first();

                $valorNumerico = $option ? $option->valor : 0;
                $optionId = $option ? $option->id : null;
            } elseif ($question->tipo_pregunta === 'scale') {
                $valorNumerico = (int) $this->selectedAnswer;
            } else {
                $valorNumerico = 0;
            }

            DiagAnswer::updateOrCreate([
                'estudiant_id' => $this->currentStudentId,
                'question_id' => $question->id,
                'session_id' => $this->currentSessionId,
            ], [
                'respuesta' => $this->selectedAnswer,
                'valor_numerico' => $valorNumerico,
                'option_id' => $optionId,
                'completado_at' => now(),
            ]);

            $this->answers[$this->currentQuestionIndex] = $this->selectedAnswer;
            $this->markCurrentAsAnswered();
            $this->updateProgress();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar respuesta: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Actualiza los contadores locales sin volver a consultar todas las
     * preguntas (antes: refreshAnsweredQuestions hacía 2 queries pesadas).
     */
    private function markCurrentAsAnswered(): void
    {
        $id = $this->currentQuestionId;

        if (! $id) {
            return;
        }

        if (! in_array($id, $this->answeredQuestionIds, true)) {
            $this->answeredQuestionIds[] = $id;
        }

        $this->unansweredQuestionIds = array_values(array_diff($this->unansweredQuestionIds, [$id]));
    }

    public function nextQuestion()
    {
        if ($this->isProcessing) {
            return;
        }

        // Vista de contestadas: solo navegar.
        if ($this->showAnsweredQuestions) {
            if ($this->currentQuestionIndex < count($this->questionIds) - 1) {
                $this->currentQuestionIndex++;
                $this->setCurrentQuestion();
            }

            return;
        }

        if (! $this->selectedAnswer) {
            $this->notification()->error(
                'Respuesta requerida',
                'Selecciona o escribe una respuesta antes de continuar.'
            );

            return;
        }

        $this->isProcessing = true;

        try {
            if (! $this->saveAnswer()) {
                return;
            }

            if ($this->currentQuestionIndex < count($this->questionIds) - 1) {
                $this->currentQuestionIndex++;
                $this->setCurrentQuestion();

                return;
            }

            // Fin del set: recalcular pendientes solo con IDs.
            $this->refreshQuestionIds();

            if (empty($this->questionIds)) {
                $this->finishDiagnostic();

                return;
            }

            $this->currentQuestionIndex = 0;
            $this->setCurrentQuestion();
            $this->loadExistingAnswers();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function previousQuestion()
    {
        if ($this->isProcessing) {
            return;
        }

        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->setCurrentQuestion();
        }
    }

    public function confirmFinish()
    {
        if (! $this->selectedAnswer) {
            $this->notification()->error(
                'Respuesta requerida',
                'Selecciona o escribe una respuesta antes de continuar.'
            );

            return;
        }

        $this->dialog()->confirm([
            'title' => '¿Finalizar el diagnóstico?',
            'description' => 'Revisa que hayas respondido todas las preguntas. Una vez finalizado no podrás modificar tus respuestas.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Sí, finalizar',
                'method' => 'finalizeDiagnostic',
                'color' => 'positive',
            ],
            'reject' => [
                'label' => 'Cancelar',
                'color' => 'secondary',
            ],
        ]);
    }

    public function finalizeDiagnostic()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            if (! $this->saveAnswer()) {
                return;
            }

            $this->finishDiagnostic();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function finishDiagnostic()
    {
        try {
            DB::beginTransaction();

            $session = $this->session();

            if ($session) {
                $session->update([
                    'completado_at' => now(),
                    'progreso' => 100,
                    'activo' => false,
                ]);
            }

            $this->results = $this->buildResults();

            $this->currentView = 'summary';
            $this->loadSessionStats();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al finalizar diagnóstico: '.$e->getMessage());
        }
    }

    /**
     * Resultados del diagnóstico: precisión en preguntas de selección
     * y desglose de aciertos por nivel de dificultad.
     */
    private function buildResults(): array
    {
        if (! $this->currentStudentId || ! $this->selectedPensumId) {
            return [];
        }

        $precision = DiagAnswer::calculateStudentPrecision($this->currentStudentId, $this->selectedPensumId);

        $byDifficulty = DiagAnswer::with(['question', 'selectedOption'])
            ->where('estudiant_id', $this->currentStudentId)
            ->whereNotNull('completado_at')
            ->whereHas('question', function ($query) {
                $query->where('pensum_id', $this->selectedPensumId)->where('activo', true);
            })
            ->get()
            ->groupBy(fn ($answer) => $answer->question->difficulty ?? 'sin')
            ->map(fn ($group) => [
                'total' => $group->count(),
                'correct' => $group->filter(fn ($answer) => $answer->isCorrect())->count(),
            ])
            ->toArray();

        return [
            'precision' => $precision['precision'],
            'correct_answers' => $precision['correct_answers'],
            'total_answered' => $precision['total_answered'],
            'by_difficulty' => $byDifficulty,
        ];
    }

    public function backToDashboard()
    {
        $this->currentView = 'dashboard';
        $this->selectedPensumId = null;
        $this->currentSessionId = null;
        $this->currentQuestionId = null;
        $this->selectedAnswer = null;
        $this->currentQuestionIndex = 0;
        $this->isReviewMode = false;
        $this->results = [];
        $this->questionIds = [];
        $this->unansweredQuestionIds = [];
        $this->answeredQuestionIds = [];
        $this->forgetPensum();
        $this->forgetSession();
        $this->forgetQuestion();
        $this->loadAvailablePensums();
    }

    public function restartIdentification()
    {
        $this->currentView = 'student-identification';
        $this->studentCi = '';
        $this->currentStudentId = null;
        $this->isStudentVerified = false;
        $this->selectedPensumId = null;
        $this->currentSessionId = null;
        $this->currentQuestionId = null;
        $this->selectedAnswer = null;
        $this->currentQuestionIndex = 0;
        $this->pensums = [];
        $this->sessionStats = [];
        $this->results = [];
        $this->questionIds = [];
        $this->unansweredQuestionIds = [];
        $this->answeredQuestionIds = [];
        $this->studentModel = null;
        $this->studentLoaded = false;
        $this->forgetPensum();
        $this->forgetSession();
        $this->forgetQuestion();
    }

    private function updateProgress()
    {
        $session = $this->session();

        // Denominador: total de preguntas del área (no el set pendiente actual).
        $totalQuestions = (int) ($session->total_preguntas ?? 0);

        if ($totalQuestions <= 0 && $this->selectedPensumId) {
            $totalQuestions = DiagQuestion::where('pensum_id', $this->selectedPensumId)
                ->where('activo', true)
                ->count();
        }

        if ($totalQuestions <= 0) {
            $totalQuestions = count($this->questionIds);
        }

        $answeredQuestions = $session
            ? DiagAnswer::where('session_id', $session->id)->count()
            : 0;

        $this->progress = $totalQuestions > 0
            ? (int) min(100, round(($answeredQuestions / $totalQuestions) * 100))
            : 0;

        if ($session) {
            $session->update(['progreso' => $this->progress]);
        }
    }

    public function getAnsweredQuestionsWithAnswers()
    {
        if (! $this->currentStudentId || ! $this->selectedPensumId) {
            return collect();
        }

        // Acotar a una sola sesión: la activa o, si se revisa desde el
        // dashboard, la última sesión de esta área. Evita mezclar intentos.
        $sessionId = $this->currentSessionId;

        if (! $sessionId) {
            $sessionId = DiagSession::where('estudiant_id', $this->currentStudentId)
                ->where('pensum_id', $this->selectedPensumId)
                ->orderByDesc('completado_at')
                ->orderByDesc('id')
                ->value('id');
        }

        return DiagAnswer::where('estudiant_id', $this->currentStudentId)
            ->whereHas('question', function ($query) {
                $query->where('pensum_id', $this->selectedPensumId);
            })
            ->when($sessionId, fn ($query) => $query->where('session_id', $sessionId))
            ->with(['question.options'])
            ->get()
            ->map(function ($answer) {
                return [
                    'question' => $answer->question,
                    'answer' => $answer->respuesta,
                    'completed_at' => $answer->completado_at,
                ];
            });
    }

    public function reviewAnswers($pensumId)
    {
        try {
            $pensum = Pensum::with('asignatura')->findOrFail($pensumId);
            $this->selectedPensumId = $pensum->id;
            $this->pensumModel = $pensum;
            $this->pensumLoaded = true;
            $this->showAnsweredModal = true;
        } catch (Exception $e) {
            session()->flash('error', 'Error al cargar respuestas: '.$e->getMessage());
        }
    }

    public function openAnsweredQuestionsModal()
    {
        $this->showAnsweredModal = true;
    }

    public function closeAnsweredQuestionsModal()
    {
        $this->showAnsweredModal = false;
    }

    public function showGuide()
    {
        $this->currentView = 'guide';
        $this->activeTab = 'overview';
    }

    public function render()
    {
        return view('livewire.diagnostic', [
            'currentStudent' => $this->student(),
            'selectedPensum' => $this->pensum(),
            'currentQuestion' => $this->question(),
        ]);
    }
}
