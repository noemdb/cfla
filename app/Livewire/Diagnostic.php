<?php

namespace App\Livewire;

use App\Models\app\Academy\Pensum;
use Livewire\Component;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Learner\Estudiant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use WireUi\Traits\Actions;

class Diagnostic extends Component
{
    use Actions;
    
    public $currentView = 'student-identification'; // student-identification, dashboard, wizard, summary, guide
    public $studentCi = '';
    public $currentStudent = null;
    public $isStudentVerified = false;

    // Estados principales
    public $selectedPensum = null;
    public $currentSession = null;

    // Wizard
    public $currentQuestionIndex = 0;
    public $currentQuestion = null;
    public $selectedAnswer = null;
    public $answers = [];
    public $progress = 0;

    public $isReviewMode = false;
    public $showAnsweredQuestions = false; // Added property to toggle between answered/unanswered questions
    public $unansweredQuestions;
    public $answeredQuestions;
    public $showAnsweredModal = false; // Added property for modal state

    // Guide
    public $activeTab = 'overview'; // overview, process, questions, tips

    // Datos
    public $pensums = [];
    public $questions;
    public $sessionStats = [];

    public $isProcessing = false;

    protected $listeners = [
        'startDiagnostic',
        'nextQuestion',
        'previousQuestion',
        'finishDiagnostic',
        'reviewAnswers',
        'toggleQuestionView',
        'openAnsweredQuestionsModal', // Added new listener for modal
        'closeAnsweredQuestionsModal'  // Added new listener for modal
    ];

    protected function rules()
    {
        $rules = [];

        if ($this->currentView === 'student-identification') {
            $rules['studentCi'] = 'required|string|min:6|max:15';
        }

        if ($this->currentView === 'wizard') {
            $rules['selectedAnswer'] = 'required';
        }

        return $rules;
    }

    protected $messages = [
        'studentCi.required' => 'La cédula es obligatoria.',
        'studentCi.min' => 'La cédula debe tener al menos 6 caracteres.',
        'studentCi.max' => 'La cédula no puede tener más de 15 caracteres.',
        'selectedAnswer.required' => 'Debes seleccionar una respuesta antes de continuar.',
    ];

    public function mount()
    {
        $this->currentView = 'student-identification';
        $this->activeTab = 'overview';
    }

    public function verifyStudent()
    {
        $this->validate([
            'studentCi' => 'required|string|min:6|max:15'
        ]);

        try {
            // Search for student by CI
            $student = Estudiant::where('ci_estudiant', $this->studentCi)
                ->first();

            if (!$student) {
                $this->addError('studentCi', 'No se encontró un estudiante con esta cédula.');
                return;
            }

            // Verify student is active
            if (!$student->status_active == 'true') {
                $this->addError('studentCi', 'El estudiante no está activo en el sistema.');
                return;
            }

            $this->currentStudent = $student;
            $this->isStudentVerified = true;

            // Load student's data
            $this->loadStudentData();

            // Move to dashboard
            $this->currentView = 'dashboard';

            $this->notification()->success(
                'Bienvenido/a',
                'Hola ' . $student->user->name . '. Puedes comenzar tu diagnóstico.'
            );
        } catch (Exception $e) {
            session()->flash('error', 'Error al verificar estudiante: ' . $e->getMessage());
        }
    }

    private function loadStudentData()
    {
        if (!$this->currentStudent) {
            return;
        }

        try {
            $this->loadAvailablePensums();
            $this->loadSessionStats();
        } catch (Exception $e) {
            session()->flash('error', 'Error al cargar datos del estudiante: ' . $e->getMessage());
        }
    }

    public function loadAvailablePensums()
    {
        if (!$this->currentStudent) {
            $this->pensums = [];
            return;
        }

        $studentPensums = $this->currentStudent->pensums;

        $this->pensums = $studentPensums
            ->filter(function ($pensum) {
                // Only include pensums that have active diagnostic questions
                return $pensum->diagQuestions()->where('activo', true)->exists();
            })
            ->map(function ($pensum) {
                $totalQuestions = $pensum->diagQuestions()->where('activo', true)->count();
                $completedQuestions = $this->getCompletedQuestionsCount($pensum->id);

                return [
                    'id' => $pensum->id,
                    'name' => $pensum->asignatura->full_name ?? 'Área sin nombre',
                    'description' => $pensum->asignatura->description ?? 'Sin descripción',
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $completedQuestions,
                    'progress_percentage' => $totalQuestions > 0 ? round(($completedQuestions / $totalQuestions) * 100) : 0,
                    'is_completed' => $completedQuestions >= $totalQuestions,
                    'difficulty_distribution' => $this->getDifficultyDistribution($pensum->id)
                ];
            })
            ->values()
            ->toArray();
    }

    public function loadSessionStats()
    {
        if (!$this->currentStudent) {
            $this->sessionStats = [];
            return;
        }

        $this->sessionStats = [
            'total_sessions' => DiagSession::where('estudiant_id', $this->currentStudent->id)->count(),
            'completed_sessions' => DiagSession::where('estudiant_id', $this->currentStudent->id)
                ->whereNotNull('completado_at')->count(),
            'total_answers' => DiagAnswer::where('estudiant_id', $this->currentStudent->id)->count(),
            'average_progress' => DiagSession::where('estudiant_id', $this->currentStudent->id)
                ->avg('progreso') ?? 0
        ];
    }

    public function startDiagnostic($pensumId)
    {
        try {
            DB::beginTransaction();

            $this->selectedPensum = Pensum::with('asignatura')->findOrFail($pensumId);
            $allQuestions = DiagQuestion::with('options')
                ->where('pensum_id', $pensumId)
                ->where('activo', true)
                ->orderBy('orden')
                ->get();

            if ($allQuestions->isEmpty()) {
                throw new Exception('No hay preguntas disponibles para esta área.');
            }

            $this->isReviewMode = false;
            $this->showAnsweredQuestions = false; // Reset to show unanswered questions by default

            // Crear o recuperar sesión activa
            $estudiantId = $this->currentStudent->id;
            $this->currentSession = DiagSession::firstOrCreate([
                'estudiant_id' => $estudiantId,
                'pensum_id' => $pensumId,
                'activo' => true,
                'completado_at' => null
            ], [
                'iniciado_at' => now(),
                'total_preguntas' => $allQuestions->count(),
                'progreso' => 0
            ]);

            $this->filterQuestionsByAnsweredStatus($allQuestions);

            if ($this->questions->isEmpty()) {
                session()->flash('success', 'Has completado todas las preguntas de esta área.');
                $this->backToDashboard();
                return;
            }

            $this->currentQuestionIndex = 0;
            $this->setCurrentQuestion();
            $this->currentView = 'wizard';

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al iniciar diagnóstico: ' . $e->getMessage());
        }
    }

    private function filterQuestionsByAnsweredStatus($allQuestions)
    {
        $answeredQuestionIds = DiagAnswer::where('estudiant_id', $this->currentStudent->id)
            ->whereHas('question', function ($query) {
                $query->where('pensum_id', $this->selectedPensum->id);
            })
            ->pluck('question_id')
            ->toArray();

        $this->unansweredQuestions = $allQuestions->filter(function ($question) use ($answeredQuestionIds) {
            return !in_array($question->id, $answeredQuestionIds);
        });

        $this->unansweredQuestions = $this->unansweredQuestions->shuffle();

        $this->answeredQuestions = $allQuestions->filter(function ($question) use ($answeredQuestionIds) {
            return in_array($question->id, $answeredQuestionIds);
        });

        $this->questions = $this->showAnsweredQuestions ?
            $this->answeredQuestions->values() :
            $this->unansweredQuestions->values();

        // Load existing answers for current questions
        $this->loadExistingAnswers();
    }

    public function toggleQuestionView()
    {
        $this->showAnsweredQuestions = !$this->showAnsweredQuestions;

        $this->questions = $this->showAnsweredQuestions ?
            $this->answeredQuestions->values() :
            $this->unansweredQuestions->values();

        // Reset to first question
        $this->currentQuestionIndex = 0;
        $this->setCurrentQuestion();

        // Load answers for current question set
        $this->loadExistingAnswers();
    }

    public function loadExistingAnswers()
    {
        if (!$this->currentSession || !$this->questions) {
            $this->answers = [];
            return;
        }

        $existingAnswers = DiagAnswer::where('estudiant_id', $this->currentStudent->id)
            ->where('session_id', $this->currentSession->id)
            ->get()
            ->keyBy('question_id');

        $this->answers = [];
        foreach ($this->questions as $index => $question) {
            if (isset($existingAnswers[$question->id])) {
                $this->answers[$index] = $existingAnswers[$question->id]->respuesta;
            }
        }

        $this->updateProgress();
    }

    public function setCurrentQuestion()
    {
        if (isset($this->questions[$this->currentQuestionIndex])) {
            $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
            $this->selectedAnswer = $this->answers[$this->currentQuestionIndex] ?? null;
        }
    }

    public function saveAnswer()
    {
        if (!$this->selectedAnswer || !$this->currentQuestion) {
            return;
        }

        try {
            DB::beginTransaction();

            $estudiantId = $this->currentStudent->id;

            // Obtener valor numérico según el tipo de pregunta
            $valorNumerico = $this->calculateNumericValue();

            DiagAnswer::updateOrCreate([
                'estudiant_id' => $estudiantId,
                'question_id' => $this->currentQuestion->id,
                'session_id' => $this->currentSession->id
            ], [
                'respuesta' => $this->selectedAnswer,
                'valor_numerico' => $valorNumerico,
                'completado_at' => now()
            ]);

            $this->answers[$this->currentQuestionIndex] = $this->selectedAnswer;
            $this->updateProgress();

            $this->refreshAnsweredQuestions();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar respuesta: ' . $e->getMessage());
        }
    }

    public function nextQuestion()
    {
        if ($this->isProcessing) {
            return;
        }

        if (!$this->showAnsweredQuestions && $this->selectedAnswer) {
            $this->isProcessing = true;

            try {
                $this->saveAnswer();

                if ($this->currentQuestionIndex < count($this->questions) - 1) {
                    $this->currentQuestionIndex++;
                    $this->setCurrentQuestion();
                } else {
                    // Check if there are more unanswered questions
                    $this->filterQuestionsByAnsweredStatus(
                        DiagQuestion::with('options')
                            ->where('pensum_id', $this->selectedPensum->id)
                            ->where('activo', true)
                            ->orderBy('orden')
                            ->get()
                    );

                    if ($this->unansweredQuestions->isEmpty()) {
                        $this->finishDiagnostic();
                    } else {
                        $this->questions = $this->unansweredQuestions;
                        $this->currentQuestionIndex = 0;
                        $this->setCurrentQuestion();
                    }
                }
            } finally {
                $this->isProcessing = false;
            }
        } else {
            // Just navigate for answered questions view
            if ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
                $this->setCurrentQuestion();
            }
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

    public function getCanProceedProperty()
    {
        Log::info("[v0] Checking canProceed - selectedAnswer: " . json_encode($this->selectedAnswer));
        Log::info("[v0] isProcessing: " . json_encode($this->isProcessing));

        if ($this->isProcessing) {
            return false;
        }

        // More robust validation for different question types
        if ($this->currentQuestion) {
            switch ($this->currentQuestion->tipo_pregunta) {
                case 'multiple':
                    return !empty($this->selectedAnswer) && trim($this->selectedAnswer) !== '';
                case 'scale':
                    return !empty($this->selectedAnswer) && is_numeric($this->selectedAnswer) &&
                        $this->selectedAnswer >= 1 && $this->selectedAnswer <= 10;
                case 'open':
                    return !empty($this->selectedAnswer) && trim($this->selectedAnswer) !== '';
                default:
                    return !empty($this->selectedAnswer);
            }
        }

        return false;
    }

    public function updatedSelectedAnswer($value)
    {
        Log::info("[v0] Answer updated to: " . json_encode($value));
        // Force re-render to update button state
        $this->dispatch('answer-updated');
    }

    public function finishDiagnostic()
    {
        try {
            DB::beginTransaction();

            $this->currentSession->update([
                'completado_at' => now(),
                'progreso' => 100,
                'activo' => false
            ]);

            $this->currentView = 'summary';
            $this->loadSessionStats();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al finalizar diagnóstico: ' . $e->getMessage());
        }
    }

    public function backToDashboard()
    {
        $this->currentView = 'dashboard';
        $this->selectedPensum = null;
        $this->currentSession = null;
        $this->currentQuestion = null;
        $this->selectedAnswer = null;
        $this->currentQuestionIndex = 0;
        $this->isReviewMode = false;
        $this->loadAvailablePensums();
    }

    public function restartIdentification()
    {
        $this->currentView = 'student-identification';
        $this->studentCi = '';
        $this->currentStudent = null;
        $this->isStudentVerified = false;
        $this->selectedPensum = null;
        $this->currentSession = null;
        $this->currentQuestion = null;
        $this->selectedAnswer = null;
        $this->currentQuestionIndex = 0;
        $this->pensums = [];
        $this->sessionStats = [];
    }

    private function getCompletedQuestionsCount($pensumId)
    {
        if (!$this->currentStudent) return 0;

        return DiagAnswer::where('estudiant_id', $this->currentStudent->id)
            ->whereHas('question', function ($query) use ($pensumId) {
                $query->where('pensum_id', $pensumId);
            })
            ->count();
    }

    private function getDifficultyDistribution($pensumId)
    {
        return DiagQuestion::where('pensum_id', $pensumId)
            ->where('activo', true)
            ->selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->pluck('count', 'difficulty')
            ->toArray();
    }

    private function findNextUnansweredQuestion()
    {
        foreach ($this->questions as $index => $question) {
            if (!isset($this->answers[$index])) {
                return $index;
            }
        }
        return 0;
    }

    private function calculateNumericValue()
    {
        if ($this->currentQuestion->tipo_pregunta === 'multiple') {
            $option = $this->currentQuestion->options
                ->where('opcion', $this->selectedAnswer)
                ->first();
            return $option ? $option->valor : 0;
        } elseif ($this->currentQuestion->tipo_pregunta === 'scale') {
            return (int) $this->selectedAnswer;
        }
        return 0;
    }

    private function updateProgress()
    {
        $answeredQuestions = count(array_filter($this->answers));
        $totalQuestions = count($this->questions);
        $this->progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;

        if ($this->currentSession) {
            $this->currentSession->update(['progreso' => $this->progress]);
        }
    }

    public function getAnsweredQuestionsWithAnswers()
    {
        if (!$this->currentStudent || !$this->selectedPensum) {
            return collect();
        }

        return DiagAnswer::where('estudiant_id', $this->currentStudent->id)
            ->whereHas('question', function ($query) {
                $query->where('pensum_id', $this->selectedPensum->id);
            })
            ->with(['question.options'])
            ->get()
            ->map(function ($answer) {
                return [
                    'question' => $answer->question,
                    'answer' => $answer->respuesta,
                    'completed_at' => $answer->completado_at
                ];
            });
    }

    public function reviewAnswers($pensumId)
    {
        try {
            $this->selectedPensum = Pensum::with('asignatura')->findOrFail($pensumId);
            $this->showAnsweredModal = true;
        } catch (Exception $e) {
            session()->flash('error', 'Error al cargar respuestas: ' . $e->getMessage());
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

    private function refreshAnsweredQuestions()
    {
        if (!$this->selectedPensum) {
            return;
        }

        $allQuestions = DiagQuestion::with('options')
            ->where('pensum_id', $this->selectedPensum->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $answeredQuestionIds = DiagAnswer::where('estudiant_id', $this->currentStudent->id)
            ->whereHas('question', function ($query) {
                $query->where('pensum_id', $this->selectedPensum->id);
            })
            ->pluck('question_id')
            ->toArray();

        $this->answeredQuestions = $allQuestions->filter(function ($question) use ($answeredQuestionIds) {
            return in_array($question->id, $answeredQuestionIds);
        });

        $this->unansweredQuestions = $allQuestions->filter(function ($question) use ($answeredQuestionIds) {
            return !in_array($question->id, $answeredQuestionIds);
        });

        $this->unansweredQuestions = $this->unansweredQuestions->shuffle();
    }

    public function showGuide()
    {
        $this->currentView = 'guide';
        $this->activeTab = 'overview';
    }

    public function render()
    {
        return view('livewire.diagnostic');
    }
}
