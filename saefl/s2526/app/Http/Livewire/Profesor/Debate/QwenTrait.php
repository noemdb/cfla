<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Educational\DebateCompetition as Competition;
use App\Models\app\Profesor\Activity;
use App\Rules\MaxWords;
use App\Services\QwenService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait QwenTrait
{
    public $status_ai_ok = false;
    public $context;

    /**
     * Start the competition creation process with AI
     */
    public function qwCreateCompetition()
    {
        $this->close();
        $this->modeCreatorGeminiCompetition = true;
        $this->activities = collect();
    }

    /**
     * Generate competition data using Qwen
     */
    public function qwGenerateCompetition()
    {
        $this->validate([
            'grado_id' => 'required|integer',
            'checkboxes' => 'required|array|min:1'
        ], [
            'checkboxes.required' => 'Debes seleccionar al menos una opción.',
            'checkboxes.min' => 'Debes seleccionar al menos una opción.',
            'grado_id.required' => 'Debes seleccionar el grado/año.',
        ]);

        $this->status_ai_ok = false;
        $context = $this->qwBuildContextFromActivities();

        if ($context) {
            $prompt = Competition::getPrompt($context);
            $arrData = $this->qwGetQwenResponse($prompt);

            if ($this->qwValidateCompetitionData($arrData)) {
                $this->qwSaveGeneratedCompetition($arrData, $context);

                $title = '¡Excelente, buen trabajo! ';
                $html = 'Se ha generado los datos principales para una nueva competición';
                $this->showSwal($title, $html);
            } else {
                $this->showErrorSwal();
            }
        }
    }

    /**
     * Start the debate creation process with AI
     */
    public function qwCreateDebate($id)
    {
        $this->close();
        $this->modeCreatorGeminiDebate = true;
        $competition = Competition::findOrFail($id);
        $this->competition_id = $competition->id;
        $this->context = $competition->context;
    }

    /**
     * Generate debate and questions using Qwen
     */
    public function qwGenerateAiDebate($id)
    {
        $this->validate([
            'referents' => ['required', 'string', new MaxWords(200)],
        ], [
            'referents.required' => 'Información teórica adicional es requerida.',
            'referents.max' => '200 palabras como máximo.',
        ]);

        $competition = Competition::findOrFail($id);
        $this->status_ai_ok = false;

        $prompt = $this->qwBuildDebatePrompt($competition);
        $arrData = $this->qwGetQwenResponse($prompt);

        if ($this->qwValidateDebateData($arrData)) {
            $this->qwSaveGeneratedDebate($competition, $arrData);

            $title = '¡Excelente, buen trabajo! ';
            $html = 'Se ha generado y registrado un debate con 5 preguntas y sus opciones.';
            $this->showSwal($title, $html);
            $this->close();
            $this->competition_id = $competition->id;
            $this->competition = $competition;
        } else {
            $this->qwShowErrorSwal();
        }
    }

    private function qwGetQwenResponse($prompt)
    {
        $qwen = app(QwenService::class);
        $messages = [
            ['role' => 'system', 'content' => 'Eres un experto en pedagogía integradora y metodología STEM. Responde siempre en formato JSON válido.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        try {
            $response = $qwen->sendMessage($messages);

            $content = null;
            if (isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
            } elseif (isset($response['output']['text'])) {
                $content = $response['output']['text'];
            }

            if ($content) {
                // Robust JSON extraction
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    return json_decode($jsonContent, true);
                }
            }
        } catch (\Exception $e) {
            Log::error('Qwen Generation Error: ' . $e->getMessage());
        }

        return null;
    }

    private function qwBuildContextFromActivities()
    {
        $context = "";
        $i = 0;
        foreach ($this->checkboxes as $id => $value) {
            if (!$value) continue;
            $i++;
            $activity = Activity::findOrFail($id);
            $asignatura = $activity->getAsignatura();
            $context .= "-. Actividad $i:\n";
            $context .= "   Área: " . $asignatura->name . "\n";
            $context .= "   Tema: " . $activity->topic . "\n";
            $context .= "   Tejido: " . $activity->thematic . "\n";
            $context .= "   Referentes: " . $activity->references . "\n";
            $context .= "   Aprendizaje: " . $activity->learning . "\n";
            $context .= "   Evaluación: " . $activity->description . "\n";
        }
        return $context;
    }

    private function qwBuildDebatePrompt($competition)
    {
        $perspectives = $this->qwGetSelectedPerspectives();
        $cognitive = $this->qwGetSelectedCognitiveStyles();

        $prompt = "Actúa como un experto en pedagogía integradora (STEM). Genera un debate académico para estudiantes.\n\n";
        $prompt .= "CONTEXTO EDUCATIVO (Actividades previas):\n" . $competition->context . "\n\n";
        $prompt .= "COMPETICIÓN:\n" . $competition->string . "\n\n";
        $prompt .= "MARCO TEÓRICO ADICIONAL:\n" . $this->referents . "\n\n";

        if ($perspectives) $prompt .= "ENFOQUES PEDAGÓGICOS: $perspectives\n";
        if ($cognitive) $prompt .= "ESTILOS COGNITIVOS: $cognitive\n";
        if ($this->statusEmpiricalEvidence) $prompt .= "NOTA: Se debe valorar el uso de evidencia empírica en las preguntas.\n";
        if ($this->crossCutting) $prompt .= "CONCEPTOS TRANSVERSALES: " . $this->crossCutting . "\n";

        $prompt .= "\nREQUERIMIENTO: Genera un JSON con este formato exacto:\n";
        $prompt .= "{\n  \"name\": \"Nombre creativo del debate (max 10 palabras)\",\n  \"description\": \"Descripción breve\",\n  \"questions\": [\n    {\n      \"category\": \"Categoría\",\n      \"text\": \"Texto de la pregunta\",\n      \"observation\": \"Justificación pedagógica\",\n      \"options\": [\n        {\"text\": \"Opción A\", \"observation\": \"Por qué es correcta/incorrecta\", \"status_option_correct\": true},\n        {\"text\": \"Opción B\", \"observation\": \"...\", \"status_option_correct\": false},\n        {\"text\": \"Opción C\", \"observation\": \"...\", \"status_option_correct\": false},\n        {\"text\": \"Opción D\", \"observation\": \"...\", \"status_option_correct\": false}\n      ]\n    }\n  ]\n}\n";
        $prompt .= "Genera 5 preguntas con 4 opciones cada una. Solo una opción correcta por pregunta.\n";

        return $prompt;
    }

    private function qwValidateCompetitionData($data)
    {
        return is_array($data) &&
            isset($data['name'], $data['description'], $data['motive']);
    }

    private function qwValidateDebateData($data)
    {
        return is_array($data) &&
            isset($data['name'], $data['description'], $data['questions']) &&
            is_array($data['questions']);
    }

    private function qwSaveGeneratedCompetition($data, $context)
    {
        $this->resetModel();
        $this->competition->user_id = Auth::id();
        $this->competition->name = $data['name'];
        $this->competition->description = $data['description'];
        $this->competition->motive = $data['motive'];
        $this->competition->date = Carbon::now();
        $this->competition->status_active = true;
        $this->competition->context = $context;

        $this->close();
        $this->modeCreator = true;
        $this->status_ai_ok = true;
    }

    private function qwSaveGeneratedDebate($competition, $data)
    {
        $debate = Debate::create([
            'competition_id' => $competition->id,
            'token' => Debate::genTokenSm(),
            'grado_id' => $this->grado_id,
            'name' => $data['name'],
            'description' => $data['description'],
            'question_max' => 20,
            'status_active' => true,
            'context' => $this->referents,
        ]);

        foreach ($data['questions'] as $qData) {
            $question = DebateQuestion::create([
                'debate_id' => $debate->id,
                'category' => $qData['category'] ?? 'General',
                'text' => $qData['text'],
                'time' => 45,
                'weighting' => 50,
                'observation' => $qData['observation'] ?? '',
                'option_max' => 4,
                'status_active' => true,
            ]);

            if (isset($qData['options']) && is_array($qData['options'])) {
                foreach ($qData['options'] as $oData) {
                    DebateOption::create([
                        'question_id' => $question->id,
                        'text' => $oData['text'],
                        'observation' => $oData['observation'] ?? '',
                        'status_option_correct' => (bool) $oData['status_option_correct'],
                    ]);
                }
            }
        }
    }

    private function qwGetSelectedPerspectives()
    {
        $list = [];
        if ($this->statusApproachConstructivist) $list[] = 'Constructivista';
        if ($this->statusApproachSociocultural) $list[] = 'Sociocultural';
        if ($this->statusApproachHumanist) $list[] = 'Humanista';
        if ($this->statusApproachCritical) $list[] = 'Crítico';
        if ($this->statusApproachCulturalHistorical) $list[] = 'Histórico-cultural';
        if ($this->statusApproachEcological) $list[] = 'Ecológico';
        return implode(', ', $list);
    }

    private function qwGetSelectedCognitiveStyles()
    {
        $list = [];
        if ($this->statusCognitiveInductive) $list[] = 'Analítico';
        if ($this->statusCognitiveSynthetic) $list[] = 'Creativo';
        if ($this->statusCognitiveAnalytical) $list[] = 'Sintético';
        if ($this->statusCognitiveCreativo) $list[] = 'Crítico';
        if ($this->statusCognitiveCritical) $list[] = 'Inductivo';
        return implode(', ', $list);
    }

    private function qwShowErrorSwal()
    {
        $this->showSwal('¡Ocurrieron Errores!', 'No se pudo generar el contenido con IA. Intentalo nuevamente.', 'error');
    }
}
