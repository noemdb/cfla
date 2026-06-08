<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagReportAiDraft;
use App\Models\app\Instrument\DiagReportAuditLog;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Institucion;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\AI\AiPrompt;
use App\Services\GeminiService;
use App\Services\DeepSeekService;
use App\Services\QwenService;
use App\Services\OpenRouterService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDiagnosticReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnostic:generate-reports 
                            {diagMainId : ID of the DiagMain diagnostic}
                            {gradoId : ID of the grade (Grado)}
                            {cantidad : Number of reports to generate}
                            {--service=qwen : AI service to use (deepseek|qwen|gemini|openrouter)}
                            {--dry-run : Preview which students would get reports without generating them}';

    /** 
     * php artisan diagnostic:generate-reports --help
     * php artisan diagnostic:generate-reports 1 13 5 --service=deepseek
     * php artisan diagnostic:generate-reports 1 12 1 --dry-run
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate AI diagnostic reports for students enrolled in a specific grade';

    /**
     * Selected AI service
     */
    protected $selected_ai_service = 'qwen';

    /**
     * Get the processed help text.
     * This is called when --help is used.
     *
     * @return string
     */
    public function getProcessedHelp()
    {
        $help = parent::getProcessedHelp();

        // Add available grades to help output
        $gradesInfo = $this->getAvailableGradesText();

        return $help . "\n" . $gradesInfo;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $diagMainId = $this->argument('diagMainId');
        $gradoId = $this->argument('gradoId');
        $cantidad = $this->argument('cantidad');
        $this->selected_ai_service = $this->option('service');
        $dryRun = $this->option('dry-run');

        // Validate inputs
        $diagMain = DiagMain::find($diagMainId);
        if (!$diagMain) {
            $this->error("DiagMain with ID {$diagMainId} not found.");
            return 1;
        }

        $grado = Grado::find($gradoId);
        if (!$grado) {
            $this->error("Grado with ID {$gradoId} not found.");
            return 1;
        }

        if ($cantidad < 1) {
            $this->error("Cantidad must be at least 1.");
            return 1;
        }

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No reports will be generated");
        }
        $this->info("Generating {$cantidad} diagnostic reports for grade: {$grado->name}");
        $this->info("DiagMain: {$diagMain->name}");
        $this->info("AI Service: {$this->selected_ai_service}");

        $this->newLine();

        // Get enrolled students in the grade without existing reports
        // AND with at least one completed session for this diagnostic
        $students = Estudiant::whereHas('inscripcion', function ($query) use ($gradoId) {
            $query->whereHas('seccion', function ($q) use ($gradoId) {
                $q->where('grado_id', $gradoId);
            });
        })
            ->whereNotIn('id', function ($query) use ($diagMainId) {
                $query->select('estudiant_id')
                    ->from('diag_reports')
                    ->where('diag_main_id', $diagMainId);
            })
            ->whereHas('diag_sessions', function ($query) use ($diagMainId) {
                $query->where('diag_main_id', $diagMainId)
                    ->whereNotNull('completado_at');
            })
            ->with('inscripcion.seccion')
            ->limit($cantidad)
            ->get();

        if ($students->isEmpty()) {
            $this->warn("No students found without reports for this diagnostic in grade {$grado->name}.");
            return 0;
        }

        $this->info("Found {$students->count()} students to process.");

        if ($dryRun) {
            $this->newLine();
            $this->info("Students who would receive reports:");
            $this->table(
                ['ID', 'CI', 'Full Name', 'Email', 'Section'],
                $students->map(function ($student) {
                    return [
                        $student->id,
                        $student->ci_estudiant,
                        $student->full_name,
                        $student->gsemail,
                        $student->inscripcion?->seccion?->name ?? 'N/A'
                    ];
                })->toArray()
            );
            $this->newLine();
            $this->info("Dry run complete. No reports were generated.");
            return 0;
        }
        $this->newLine();

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($students as $student) {
            try {
                $result = $this->generateReportForStudent($student->id, $diagMainId);
                $this->newLine();
                $this->info("Student {$student->id} {$student->full_name} - {$result}");
                $this->newLine();

                if ($result === 'skipped') {
                    $skippedCount++;

                    $this->newLine();
                } elseif ($result) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $this->info("Error generating report for student {$student->id} -" . $result);
                }
            } catch (Exception $e) {
                $errorCount++;
                Log::error("Error generating report for student {$student->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Report Generation Summary:");
        $this->info("✓ Successfully generated: {$successCount}");
        $this->warn("⊘ Skipped (already exists): {$skippedCount}");
        $this->error("✗ Errors: {$errorCount}");

        return 0;
    }

    /**
     * Generate AI report for a single student
     * This is adapted from IndexComponent::generateAIReport()
     */
    protected function generateReportForStudent($estudiantId, $diagMainId)
    {
        // Increase execution time
        set_time_limit(300);

        $student = Estudiant::find($estudiantId);
        $inscripcion = Inscripcion::where('estudiant_id', $estudiantId)->first();

        // Validate existing report
        $existingReport = DiagReport::where('estudiant_id', $estudiantId)
            ->where('diag_main_id', $diagMainId)
            ->exists();

        if ($existingReport) {
            return 'skipped';
        }

        if (!$student || !$inscripcion) {
            Log::warning("Student {$estudiantId} not found or not enrolled.");
            return false;
        }

        // 1. Gather Data (Sessions & Results) - Only for this diagnostic
        $allSessions = DiagSession::where('estudiant_id', $estudiantId)
            ->where('diag_main_id', $diagMainId)
            ->with(['pensum.asignatura', 'answers.question', 'diagMain'])
            ->get();

        $completedSessions = $allSessions->whereNotNull('completado_at');
        $incompleteSessions = $allSessions->whereNull('completado_at');

        if ($completedSessions->isEmpty()) {
            Log::warning("Student {$estudiantId} has no completed sessions for diagnostic {$diagMainId}.");
            return false;
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

        // Referente Curricular
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

        // Contrastes Curriculares
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

        if ($aiResponse) {
            // Save to Database
            $reportId = $this->saveReportToDatabase($payload, $aiResponse, $student, $institucion, $diagMainId);
            return $reportId;
        }

        return false;
    }

    /**
     * AI Service Methods (adapted from traits)
     */
    protected function dsGenerateReport($payload)
    {
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('DeepSeek Report: Missing active system or user prompts.');
            return null;
        }

        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        $deepseek = app(DeepSeekService::class);
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt->content],
            ['role' => 'user', 'content' => $promptContent]
        ];

        try {
            $response = $deepseek->chat($messages);
            $content = $response['choices'][0]['message']['content'] ?? null;

            if ($content) {
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    }
                }

                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));
                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        } catch (Exception $e) {
            Log::error('DeepSeek Report Generation Error: ' . $e->getMessage());
        }

        return null;
    }

    protected function qwGenerateReport($payload)
    {
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('Qwen Report: Missing active system or user prompts.');
            return null;
        }

        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        $qwen = app(QwenService::class);
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt->content],
            ['role' => 'user', 'content' => $promptContent]
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
                // 1. First method: Robust JSON regex extraction
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    } else {
                        Log::warning('Qwen: Regex found potential JSON but parse failed.', ['error' => json_last_error_msg()]);
                    }
                }

                // 2. Second method: Direct cleanup (strip markdown)
                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));

                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    Log::info('Qwen: JSON extracted via direct cleanup method.');
                    return $decoded;
                }

                // If both failed, log full error context
                Log::error('Qwen: No valid JSON found.', [
                    'full_content' => substr($content, 0, 2000),
                    'json_error' => json_last_error_msg()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Qwen Report Generation Error: ' . $e->getMessage());
        }

        return null;
    }

    protected function gmGenerateReport($payload)
    {
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('Gemini Report: Missing active system or user prompts.');
            return null;
        }

        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        $gemini = app(GeminiService::class);
        $messages = [
            ['role' => 'user', 'text' => $systemPrompt->content . "\n\n" . $promptContent]
        ];

        try {
            $response = $gemini->chat($messages);
            $content = $response['text'] ?? null;

            if ($content) {
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    }
                }

                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));
                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        } catch (Exception $e) {
            Log::error('Gemini Report Generation Error: ' . $e->getMessage());
        }

        return null;
    }

    protected function orGenerateReport($payload)
    {
        $systemPrompt = AiPrompt::where('prompt_type', 'system')->where('active', 1)->first();
        $userPrompt = AiPrompt::where('prompt_type', 'user')->where('active', 1)->first();

        if (!$systemPrompt || !$userPrompt) {
            Log::error('OpenRouter Report: Missing active system or user prompts.');
            return null;
        }

        $jsonPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $promptContent = str_replace(
            ['{{ payload_json }}', '{{PAYLOAD_DIAGNOSTICO_COMPLETO}}'],
            $jsonPayload,
            $userPrompt->content
        );

        $openrouter = app(OpenRouterService::class);
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt->content],
            ['role' => 'user', 'content' => $promptContent]
        ];

        try {
            $response = $openrouter->chat($messages);
            $content = $response['choices'][0]['message']['content'] ?? null;

            if ($content) {
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
                    $jsonContent = $matches[0];
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        return $decoded;
                    }
                }

                $cleanContent = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                $cleanContent = preg_replace('/^```\s*|\s*```$/i', '', trim($cleanContent));
                $decoded = json_decode($cleanContent, true);
                if ($decoded) {
                    return $decoded;
                }
            }
        } catch (Exception $e) {
            Log::error('OpenRouter Report Generation Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Persist generated AI report
     */
    protected function saveReportToDatabase($payload, $aiResponse, $student, $institution, $diagMainId)
    {
        return DB::transaction(function () use ($payload, $aiResponse, $student, $diagMainId) {
            $aiData = is_array($aiResponse)
                ? $aiResponse
                : json_decode($aiResponse, true);

            if (!is_array($aiData)) {
                $aiData = [
                    'error' => 'Invalid or non-JSON AI response',
                    'raw_output' => is_string($aiResponse) ? $aiResponse : null,
                ];
            }

            $existingReport = DiagReport::where('estudiant_id', $student->id)
                ->where('diag_main_id', $diagMainId)
                ->first();

            if ($existingReport) {
                return $existingReport->id;
            }

            $report = DiagReport::create([
                'estudiant_id'  => $student->id,
                'diag_main_id'  => $diagMainId,
                'referent_id'   => DiagReferent::where('active', 1)->value('id'),
                'lapso_id'      => Lapso::current()->id ?? null,
                'status'        => 'generated',
                'generated_at'  => now(),
                'global'        => null,
            ]);

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

            DiagReportAuditLog::create([
                'report_id'   => $report->id,
                'user_id'     => 1, // System user for CLI commands
                'action'      => 'ai_generation_cli',
                'details'     => 'AI draft stored successfully via CLI command',
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Artisan CLI',
            ]);

            return $report->id;
        });
    }

    /**
     * Display available active grades
     */
    protected function displayAvailableGrades()
    {
        $this->newLine();
        $this->info('Available Active Grades (from current enrollments):');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get active grades from inscriptions
        $activeGrades = Inscripcion::with('seccion.grado')
            ->whereHas('seccion.grado')
            ->get()
            ->pluck('seccion.grado')
            ->unique('id')
            ->filter()
            ->sortBy('id')
            ->values();

        if ($activeGrades->isEmpty()) {
            $this->warn('  No active grades found in current enrollments.');
        } else {
            foreach ($activeGrades as $grado) {
                $this->line(sprintf('  • ID: %-3s | %s', $grado->id, $grado->name));
            }
        }

        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
    }

    /**
     * Get available active grades as formatted text for help output
     *
     * @return string
     */
    protected function getAvailableGradesText()
    {
        // Get active grades from inscriptions
        $activeGrades = Inscripcion::with('seccion.grado')
            ->whereHas('seccion.grado')
            ->get()
            ->pluck('seccion.grado')
            ->unique('id')
            ->filter()
            ->sortBy('id')
            ->values();

        $output = "\n\n";
        $output .= "\033[32mAvailable Active Grades (from current enrollments):\033[0m\n";
        $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        if ($activeGrades->isEmpty()) {
            $output .= "  \033[33mNo active grades found in current enrollments.\033[0m\n";
        } else {
            foreach ($activeGrades as $grado) {
                $output .= sprintf("  • ID: %-3s | %s\n", $grado->id, $grado->name);
            }
        }

        $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        return $output;
    }
}
