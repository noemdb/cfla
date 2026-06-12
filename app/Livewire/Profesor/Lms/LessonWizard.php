<?php

namespace App\Livewire\Profesor\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Instrument\DiagReferent;
use App\Services\Lms\LmsMediaUploadService;
use App\Services\Lms\LmsPublicationService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class LessonWizard extends Component
{
    use WithPagination, WithFileUploads, WireUiActions;

    // ─── Mode: 'list' | 'wizard' ──────────────────────────────
    public string $mode = 'list';
    public ?int $selectedActivityId = null;
    public ?Activity $selectedActivity = null;

    // ─── Filtros del listado ───────────────────────────────────
    public $lapsoId = null;
    public $pestudioId = null;
    public $gradoId = null;
    public $seccionId = null;
    public string $search = '';
    public string $detailActivityId = '';
    public bool $showDetailModal = false;
    public $detailActivity = null;

    // ─── Wizard: paso actual ───────────────────────────────────
    public int $currentStep = 1;

    // ─── Wizard: Datos de la lección ───────────────────────────
    public string $lessonTitle = '';
    public string $lessonDescription = '';
    public bool $allowDownloads = true;

    // ─── Wizard: Referentes normativos (paso 1) ──────────────
    public ?array $wizardReferents = null;

    // ─── Wizard: Secciones (colección en memoria) ──────────────
    public array $wizardSections = [];
    public string $newSectionTitle = '';

    // ─── Wizard: Contenido temporal ────────────────────────────
    public ?int $editingSectionIndex = null;
    public string $contentTitle = '';
    public string $contentBody = '';

    // ─── Wizard: Generación con IA ─────────────────────────────
    public ?int $generatingSection = null;
    public bool $generatingStep1 = false;
    public bool $generatingStep2 = false;
    public ?string $generationError = null;

    // ─── Wizard: Resultado de generación (typewriter overlay) ──
    public bool $showGenerationResult = false;
    public ?string $generationType = null; // 'step1' | 'step2' | 'section'

    // ─── Export/Import ─────────────────────────────────────────
    public bool $showExportModal = false;
    public bool $showImportModal = false;
    public ?int $exportActivityId = null;
    public ?int $importActivityId = null;
    public ?int $exportTargetSectionId = null;
    public ?int $exportTargetActivityId = null;
    public array $exportAvailableSections = [];
    public array $exportAvailableActivities = [];
    public ?int $importSourceSectionId = null;
    public ?int $importSourceActivityId = null;
    public array $importAvailableSections = [];
    public array $importAvailableActivities = [];

    // ─── Import wizard ─────────────────────────────────────────
    public int $importWizardStep = 1;
    public ?array $importPreviewData = null;

    // ─── Export wizard ─────────────────────────────────────────
    public int $exportWizardStep = 1;
    public ?array $exportPreviewData = null;

    // ─── Wizard: Vista previa estudiante ──────────────────────
    public bool $showStudentPreview = false;

    // ─── List mode: Vista previa estudiante desde DB ─────────
    public bool $showListStudentPreview = false;
    public ?array $listPreviewData = null;

    // ─── Wizard: Recursos temporales ───────────────────────────
    public $resourceFile;
    public string $resourceName = '';
    public array $wizardResources = [];

    // ─── Wizard: Enlaces temporales ────────────────────────────
    public string $linkTitle = '';
    public string $linkUrl = '';
    public string $linkType = 'REFERENCE';
    public array $wizardLinks = [];

    // ─── Wizard: Publicación ───────────────────────────────────
    public string $pubStatus = 'DRAFT';
    public ?string $publishAt = null;
    public bool $saved = false;
    public bool $showPublishConfirm = false;

    protected $paginationTheme = 'tailwind';

    protected function rules(): array
    {
        return [
            'newSectionTitle' => 'required|string|max:255',
            'contentBody'     => 'required|string|min:1',
            'resourceFile'    => 'nullable|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,mp4,mp3',
            'resourceName'    => 'required_with:resourceFile|nullable|string|max:255',
            'linkTitle'       => 'required_with:linkUrl|nullable|string|max:255',
            'linkUrl'         => 'required_with:linkTitle|nullable|url|max:1000',
        ];
    }

    // ─── Lifecycle ─────────────────────────────────────────────

    public function mount(): void
    {
        $this->lapsoId = Lapso::current()?->id;

        // Si se pasa activity_id en la URL, iniciar wizard directamente
        $activityId = request()->query('activity_id');
        if ($activityId) {
            $this->startWizard((int) $activityId);
        }
    }

    // ─── Listado: filtros ──────────────────────────────────────

    public function updatingSearch()    { $this->resetPage(); }
    public function updatingPestudioId(){ $this->resetPage(); $this->gradoId = null; $this->seccionId = null; }
    public function updatingGradoId()   { $this->resetPage(); $this->seccionId = null; }
    public function updatingSeccionId() { $this->resetPage(); }
    public function updatingLapsoId()   { $this->resetPage(); }

    // ─── Listado: detalle en modal ─────────────────────────────

    public function showDetails(int $activityId): void
    {
        $this->detailActivity = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'achievements',
            'lmsPublication',
        ])->findOrFail($activityId);
        $this->showDetailModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailModal = false;
        $this->detailActivity = null;
    }

    // ─── Wizard: iniciar ───────────────────────────────────────

    public function startWizard(int $activityId): void
    {
        $activity = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'achievements',
            'lmsPublication',
            'lmsSections.contents',
        ])->findOrFail($activityId);

        abort_unless(
            auth()->user()->is_admin
            || $activity->pevaluacion->profesor_id === auth()->id(),
            403
        );

        $this->selectedActivity   = $activity;
        $this->selectedActivityId = $activityId;
        $this->lessonTitle        = $activity->topic ?? '';
        $this->lessonDescription  = $activity->description ?? '';
        $this->allowDownloads     = $activity->lmsPublication?->allow_downloads ?? true;

        // Cargar secciones existentes en el wizard
        $this->wizardSections = $activity->lmsSections()
            ->with('contents')
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $this->wizardResources = $activity->lmsResources()
            ->where('is_visible', true)
            ->with('media')
            ->get()
            ->toArray();

        $this->wizardLinks = $activity->lmsLinks()
            ->where('is_visible', true)
            ->get()
            ->toArray();

        $this->currentStep = 1;
        $this->mode = 'wizard';

        // Cargar referentes normativos del plan de estudio,
        // filtrando competencias a través del pensum
        $pensum = $activity->pevaluacion?->pensum;
        $this->wizardReferents = $this->loadWizardReferents(
            $activity->pevaluacion?->seccion?->grado?->pestudio_id,
            $pensum
        );
    }

    // ─── Wizard: navegación ────────────────────────────────────

    public function goToStep(int $step): void
    {
        $this->currentStep = max(1, min(4, $step));
    }

    /**
     * Descarta el overlay de resultado de generación.
     */
    public function dismissGenerationResult(): void
    {
        $this->showGenerationResult = false;
        $this->generationType = null;
    }

    public function backToList(): void
    {
        $this->mode = 'list';
        $this->selectedActivity = null;
        $this->selectedActivityId = null;
        $this->currentStep = 1;
        $this->wizardSections = [];
        $this->wizardResources = [];
        $this->wizardLinks = [];
        $this->wizardReferents = null;
        $this->saved = false;
        $this->showPublishConfirm = false;
        $this->showGenerationResult = false;
        $this->generationType = null;
        $this->generationError = null;
        $this->lessonTitle = '';
        $this->lessonDescription = '';
        $this->publishAt = null;
        $this->allowDownloads = true;
        // Resetear filtros del listado para mostrar actividades limpias
        $this->lapsoId = null;
        $this->pestudioId = null;
        $this->gradoId = null;
        $this->seccionId = null;
        $this->search = '';
        // Limpiar activity_id de la URL para evitar conflictos al re-ingresar al wizard
        $this->js("window.history.replaceState({}, '', window.location.pathname)");
    }

    // ─── List mode: Vista estudiante desde la BD ──────────────

    /**
     * Carga los datos LMS guardados de una actividad para mostrar
     * la vista previa del estudiante desde el listado.
     */
    public function openListStudentPreview(int $activityId): void
    {
        // Cerrar primero cualquier otro modal que pueda interferir
        $this->closeExportModal();
        $this->closeImportModal();

        try {
            $activity = Activity::with([
                'pevaluacion.pensum.asignatura',
                'lmsPublication',
                'lmsSections' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order'),
                'lmsSections.contents' => fn($q) => $q->where('is_visible', true),
                'lmsResources' => fn($q) => $q->where('is_visible', true),
                'lmsResources.media',
                'lmsLinks' => fn($q) => $q->where('is_visible', true),
            ])->findOrFail($activityId);

            // Reiniciar propiedades primero para forzar detección de cambio
            $this->showListStudentPreview = false;
            $this->listPreviewData = null;

            $this->listPreviewData = [
                'activity_id'   => $activity->id,
                'subject'       => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
                'title'         => $activity->topic ?? 'Lección',
                'description'   => $activity->description ?? '',
                'start_date'    => $activity->finicial,
                'end_date'      => $activity->ffinal,
                'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
                'sections'      => $activity->lmsSections->toArray(),
                'resources'     => $activity->lmsResources->toArray(),
                'links'         => $activity->lmsLinks->toArray(),
            ];

            $this->showListStudentPreview = true;
        } catch (\Throwable $e) {
            $this->notification()->error(
                'Error al cargar vista',
                'No se pudo cargar la vista previa: ' . $e->getMessage()
            );
        }
    }

    public function closeListStudentPreview(): void
    {
        $this->showListStudentPreview = false;
        $this->listPreviewData = null;
    }

    // ─── List mode: Eliminar lección (todo el contenido LMS) ──

    /**
     * Muestra diálogo de confirmación para eliminar todo el contenido
     * LMS de una actividad (secciones, contenidos, recursos, enlaces,
     * publicación y logs).
     */
    public function confirmDeleteLesson(int $activityId): void
    {
        $activity = Activity::findOrFail($activityId);

        $this->dialog()->confirm([
            'title'       => 'Eliminar Lección',
            'description' => "¿Eliminar todo el contenido LMS de la actividad \"{$activity->topic}\"? Se eliminarán secciones, contenidos, recursos, enlaces y la publicación. Esta acción no se puede deshacer.",
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Eliminar',
                'method' => 'deleteLesson',
                'params' => $activityId,
                'color'  => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    /**
     * Elimina completamente el contenido LMS de una actividad.
     */
    public function deleteLesson(int $activityId): void
    {
        $activity = Activity::findOrFail($activityId);

        // Verificar permisos
        abort_unless(
            auth()->user()->is_admin
            || $activity->pevaluacion->profesor_id === auth()->id(),
            403
        );

        try {
            \DB::transaction(function () use ($activity) {
                // Eliminar contenidos de cada sección
                $sectionIds = $activity->lmsSections()->pluck('id');
                \App\Models\app\Academy\Lms\LmsActivityContent::whereIn('section_id', $sectionIds)->delete();

                // Eliminar secciones
                $activity->lmsSections()->delete();

                // Eliminar recursos
                $activity->lmsResources()->delete();

                // Eliminar enlaces
                $activity->lmsLinks()->delete();

                // Eliminar publicación
                $activity->lmsPublication()?->delete();

                // Eliminar logs
                $activity->lmsLogs()?->delete();
            });

            $this->notification()->success(
                'Lección eliminada',
                'Todo el contenido LMS de la actividad se eliminó correctamente.'
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                'Error',
                'No se pudo eliminar el contenido: ' . $e->getMessage()
            );
        }
    }

    // ─── Wizard: Paso 1 — Generar con IA ───────────────────────

    /**
     * Genera el título y descripción de la lección (paso 1)
     * usando el contexto de la actividad, competencias e indicadores.
     */
    public function generateStep1Content(): void
    {
        $this->generatingStep1 = true;
        $this->generationError = null;

        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        // ─── Contexto de la actividad ───────────────────────────
        $gradeName   = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName = $pevaluacion?->seccion?->name ?? '—';

        $activityContext = collect([
            'Tema generador'       => $activity->topic,
            'Tejido temático'      => $activity->thematic,
            'Actividad evaluativa' => $activity->description,
            'Enseñanza'            => $activity->teaching,
            'Aprendizaje esperado' => $activity->learning,
            'Referentes teóricos'  => $activity->references,
            'ODS/Sistematización'  => $activity->observations,
        ])->filter()->map(fn($v, $k) => "• {$k}: {$v}")->implode("\n");

        // ─── Indicadores de logro ───────────────────────────────
        $indicators = $activity?->achievements?->pluck('name')?->filter() ?? collect();
        $indicatorsText = $indicators->isNotEmpty()
            ? $indicators->map(fn($n) => "• {$n}")->implode("\n")
            : '—';

        // ─── Referentes normativos ──────────────────────────────
        $referentsText = $this->getReferentsContext($pevaluacion?->pensum?->pestudio_id, $pevaluacion?->pensum);

        // ─── Construir prompt ───────────────────────────────────
        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Dado contexto actividad, genera titulo y descripcion leccion LMS.

Formato respuesta (SOLO 2 lineas, separadas por "||"):
Linea 1 → Titulo (max 120 chars, claro, atractivo, acorde al grado)
Linea 2 → Descripcion (2-3 oraciones, max 300 chars, resumir proposito y aprendizaje)

NO incluyas nada mas.
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Curso:** {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Actividad pedagogica:**
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

Genera titulo y descripcion.
PROMPT;

        // ─── Llamar al servicio con compactación ───────────────
        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 256],
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingStep1 = false;
                $this->notification()->error('Error al generar', $result['error']);
                return;
            }

            $content = trim($result['content'] ?? '');

            if (empty($content)) {
                $this->generationError = 'La IA no generó contenido.';
                $this->generatingStep1 = false;
                return;
            }

            // ─── Parsear resultado: título y descripción ─────────
            [$this->lessonTitle, $this->lessonDescription] = $this->parseTitleDescription($content);

            if (empty($this->lessonTitle) && empty($this->lessonDescription)) {
                $this->generationError = 'La respuesta de la IA no contiene título ni descripción válidos.';
                $this->notification()->error('Error al generar', $this->generationError);
                return;
            }

            // Mostrar overlay con resultado
            $this->generationType = 'step1';
            $this->showGenerationResult = true;

            $this->notification()->success(
                'Contenido generado',
                'El título y la descripción se generaron correctamente.'
            );
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error', $e->getMessage());
        } finally {
            $this->generatingStep1 = false;
        }
    }

    // ─── Wizard: Paso 2 — Secciones ────────────────────────────

    public function addWizardSection(): void
    {
        $this->validate(['newSectionTitle' => 'required|string|max:255']);

        $this->wizardSections[] = [
            'id'         => 'temp_' . uniqid(),
            'title'      => $this->newSectionTitle,
            'is_visible' => true,
            'contents'   => [],
        ];

        $this->newSectionTitle = '';
    }

    public function toggleWizardSectionVisibility(int $index): void
    {
        if (isset($this->wizardSections[$index])) {
            $this->wizardSections[$index]['is_visible'] =
                !$this->wizardSections[$index]['is_visible'];
        }
    }

    public function removeWizardSection(int $index): void
    {
        unset($this->wizardSections[$index]);
        $this->wizardSections = array_values($this->wizardSections);
    }

    public function addWizardContent(int $sectionIndex): void
    {
        $this->validate(['contentBody' => 'required|string|min:1']);

        $this->wizardSections[$sectionIndex]['contents'][] = [
            'id'       => 'temp_' . uniqid(),
            'type'     => 'TEXT',
            'title'    => $this->contentTitle ?: null,
            'body'     => $this->contentBody,
            'is_visible' => true,
            'media'    => null,
        ];

        $this->contentTitle       = '';
        $this->contentBody        = '';
        $this->editingSectionIndex = null;
    }

    public function removeWizardContent(int $sectionIndex, int $contentIndex): void
    {
        unset($this->wizardSections[$sectionIndex]['contents'][$contentIndex]);
        $this->wizardSections[$sectionIndex]['contents'] =
            array_values($this->wizardSections[$sectionIndex]['contents']);
    }

    // ─── Wizard: IA - Generar contenido de sección ────────────

    /**
     * Genera contenido con IA para una sección usando el contexto
     * de la actividad y los referentes normativos.
     */
    public function generateSectionContent(int $sectionIndex): void
    {
        if (!isset($this->wizardSections[$sectionIndex])) {
            return;
        }

        $this->generatingSection = $sectionIndex;
        $this->generationError = null;

        $sectionTitle = $this->wizardSections[$sectionIndex]['title'];
        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        // ─── Contexto de la actividad ───────────────────────────
        $gradeName  = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName = $pevaluacion?->seccion?->name ?? '—';

        $activityContext = collect([
            'Tema generador'       => $activity->topic,
            'Tejido temático'      => $activity->thematic,
            'Actividad evaluativa' => $activity->description,
            'Enseñanza'            => $activity->teaching,
            'Aprendizaje esperado' => $activity->learning,
            'Referentes teóricos'  => $activity->references,
            'ODS/Sistematización'  => $activity->observations,
        ])->filter()->map(fn($v, $k) => "• {$k}: {$v}")->implode("\n");

        // ─── Indicadores de logro ───────────────────────────────
        $indicators = $activity?->achievements?->pluck('name')?->filter() ?? collect();
        $indicatorsText = $indicators->isNotEmpty()
            ? $indicators->map(fn($n) => "• {$n}")->implode("\n")
            : '—';

        // ─── Referentes normativos ──────────────────────────────
        $referentsText = $this->getReferentsContext($pevaluacion?->pensum?->pestudio_id, $pevaluacion?->pensum);

        // ─── Construir prompt ───────────────────────────────────
        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Genera contenido pedagogico para seccion de leccion LMS.
Formato: solo texto (150-400 palabras), parrafos estructurados, viñetas si aplica.
NO incluyas titulos, metadatos ni explicaciones. Lenguaje acorde al grado.
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Curso:** {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Actividad pedagogica:**
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

**Seccion:** {$sectionTitle}

Genera contenido para esta seccion.
PROMPT;

        // ─── Llamar al servicio con compactación ───────────────
        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 512],
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingSection = null;
                $this->notification()->error('Error al generar', $result['error']);
                return;
            }

            $content = trim($result['content'] ?? '');

            if (empty($content)) {
                $this->generationError = 'La IA no generó contenido.';
                $this->generatingSection = null;
                return;
            }

            // ─── Agregar como nuevo bloque de contenido ─────────
            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'       => 'temp_' . uniqid(),
                'type'     => 'TEXT',
                'title'    => null,
                'body'     => $content,
                'is_visible' => true,
                'media'    => null,
            ];

            // Mostrar overlay con resultado
            $sectionName = $this->wizardSections[$sectionIndex]['title'] ?? 'Sección';
            $this->generationType = 'section';
            $this->showGenerationResult = true;

            $this->notification()->success('Contenido generado', "El contenido de \"{$sectionName}\" se generó correctamente.");
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error', $e->getMessage());
        } finally {
            $this->generatingSection = null;
        }
    }

    /**
     * Obtiene los referentes normativos con competencias e indicadores
     * como arreglo estructurado para mostrar en el paso 1 del wizard.
     *
     * Las competencias se filtran por pensum_id a través del pensum
     * correspondiente (activity → pevaluacion → pensum → diag_competencies).
     */
    private function loadWizardReferents(?int $pestudioId, $pensum): ?array
    {
        if (!$pestudioId) {
            return null;
        }

        $referents = DiagReferent::with(['competencies' => function ($q) use ($pensum) {
            $q->where('pensum_id', $pensum?->id);
        }, 'competencies.indicators'])
            ->where('pestudio_id', $pestudioId)
            ->where('active', true)
            ->get();

        return $referents->isNotEmpty() ? $referents->toArray() : null;
    }

    /**
     * Obtiene el contexto de referentes normativos formateado,
     * filtrado por pensum para reducir tokens.
     */
    private function getReferentsContext(?int $pestudioId, $pensum = null): string
    {
        if (!$pestudioId) {
            return '—';
        }

        $referents = DiagReferent::with(['competencies' => function ($q) use ($pensum) {
            if ($pensum) {
                $q->where('pensum_id', $pensum->id);
            }
        }, 'competencies.indicators'])
            ->where('pestudio_id', $pestudioId)
            ->where('active', true)
            ->get();

        if ($referents->isEmpty()) {
            return 'No hay referentes registrados para este plan de estudio.';
        }

        $lines = [];
        foreach ($referents as $ref) {
            $lines[] = "Referente: {$ref->name} ({$ref->code})";
            foreach ($ref->competencies as $comp) {
                $indList = $comp->indicators->take(3);
                $text = mb_strlen($comp->name) > 80
                    ? mb_substr($comp->name, 0, 80) . '…'
                    : $comp->name;
                $lines[] = "  {$text}";
                foreach ($indList as $ind) {
                    $t = mb_strlen($ind->description) > 60
                        ? mb_substr($ind->description, 0, 60) . '…'
                        : $ind->description;
                    $lines[] = "    - {$t}";
                }
            }
        }

        return implode("\n", $lines);
    }

    // ─── Wizard: Paso 2 — Generar secciones con IA ────────────

    /**
     * Genera la estructura completa de secciones (Inicio, Desarrollo, Cierre)
     * con su contenido pedagógico, usando el contexto de la actividad,
     * competencias e indicadores.
     */
    public function generateStep2Sections(): void
    {
        $this->generatingStep2 = true;
        $this->generationError = null;

        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        // ─── Contexto de la actividad ───────────────────────────
        $gradeName   = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName = $pevaluacion?->seccion?->name ?? '—';
        $lessonTitle = $this->lessonTitle ?: $activity->topic ?? '';

        $activityContext = collect([
            'Tema generador'       => $activity->topic,
            'Tejido temático'      => $activity->thematic,
            'Actividad evaluativa' => $activity->description,
            'Enseñanza'            => $activity->teaching,
            'Aprendizaje esperado' => $activity->learning,
            'Referentes teóricos'  => $activity->references,
            'ODS/Sistematización'  => $activity->observations,
        ])->filter()->map(fn($v, $k) => "• {$k}: {$v}")->implode("\n");

        // ─── Indicadores de logro ───────────────────────────────
        $indicators = $activity?->achievements?->pluck('name')?->filter() ?? collect();
        $indicatorsText = $indicators->isNotEmpty()
            ? $indicators->map(fn($n) => "• {$n}")->implode("\n")
            : '—';

        // ─── Referentes normativos ──────────────────────────────
        $referentsText = $this->getReferentsContext($pevaluacion?->pensum?->pestudio_id, $pevaluacion?->pensum);

        // ─── Construir prompt ───────────────────────────────────
        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Genera contenido pedagógico para una lección LMS.

Estructura obligatoria (incluye //INICIO, //DESARROLLO, //CIERRE tal cual):

//INICIO
Título de inicio (máx 10 palabras, atractivo para estudiantes)
Contenido de inicio (1-2 párrafos, 80-150 palabras)

//DESARROLLO (3 a 6 bloques, cada bloque separado por línea en blanco)
Título del bloque (máx 10 palabras)
Contenido del bloque (1-2 párrafos, 100-250 palabras)

Título del siguiente bloque
Contenido

//CIERRE
Título de cierre (máx 10 palabras)
Contenido de cierre (1 párrafo, 80-150 palabras)

Reglas: lenguaje acorde al grado, ejemplos concretos, alineado con referentes normativos. NO uses la palabra TITULO ni CONTENIDO como etiquetas.
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Curso:** {$lessonTitle} · {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Actividad pedagogica:**
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

Genera estructura completa leccion.
PROMPT;

        // ─── Llamar al servicio con compactación ───────────────
        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 768],
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingStep2 = false;
                $this->notification()->error('Error al generar', $result['error']);
                return;
            }

            $content = trim($result['content'] ?? '');

            if (empty($content)) {
                $this->generationError = 'La IA no generó contenido.';
                $this->generatingStep2 = false;
                return;
            }

            // ─── Parsear secciones ───────────────────────────────
            $this->wizardSections = [];

            // Dividir por marcadores de sección
            $parts = preg_split('/^\/\/(INICIO|DESARROLLO|CIERRE)\s*$/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            $currentMarker = null;
            $buffer = '';

            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === 'INICIO' || $part === 'DESARROLLO' || $part === 'CIERRE') {
                    // Guardar bloque anterior
                    if ($currentMarker && !empty(trim($buffer))) {
                        $this->parseSectionBlock($currentMarker, $buffer);
                    }
                    $currentMarker = $part;
                    $buffer = '';
                } else {
                    $buffer .= $part . "\n";
                }
            }
            // Último bloque
            if ($currentMarker && !empty(trim($buffer))) {
                $this->parseSectionBlock($currentMarker, $buffer);
            }

            if (empty($this->wizardSections)) {
                $this->generationError = 'No se pudieron extraer secciones del contenido generado.';
                $this->notification()->error('Error de formato', $this->generationError . ' La respuesta de la IA no incluyó los marcadores //INICIO, //DESARROLLO, //CIERRE.');
                $this->generatingStep2 = false;
                return;
            }

            $count = count($this->wizardSections);

            // Mostrar overlay con resultado
            $this->generationType = 'step2';
            $this->showGenerationResult = true;

            $this->notification()->success(
                'Secciones generadas',
                "Se crearon {$count} secciones con contenido pedagógico."
            );
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error', $e->getMessage());
        } finally {
            $this->generatingStep2 = false;
        }
    }

    /**
     * Parsea un bloque de sección (INICIO, DESARROLLO o CIERRE)
     * y lo agrega a wizardSections.
     */
    private function parseSectionBlock(string $marker, string $text): void
    {
        $lines = explode("\n", trim($text));
        $title = '';
        $bodyLines = [];
        $inBody = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if (!$inBody) {
                // Primera línea no vacía es el título
                $title = $line;
                $inBody = true;
            } else {
                $bodyLines[] = $line;
            }
        }

        $body = implode("\n", $bodyLines);

        if (empty($title)) {
            $title = $marker === 'INICIO' ? 'Inicio' : ($marker === 'CIERRE' ? 'Cierre' : 'Desarrollo');
        }

        // Para DESARROLLO, crear una sección por cada título/contenido
        if ($marker === 'DESARROLLO') {
            // Si hay múltiples bloques separados por doble salto,
            // los partimos en secciones independientes
            $blocks = preg_split('/\n\s*\n/', $text);
            $blockIndex = 0;
            foreach ($blocks as $block) {
                $block = trim($block);
                if (empty($block)) {
                    continue;
                }
                $bLines = explode("\n", $block);
                $bTitle = trim(array_shift($bLines));
                $bBody = trim(implode("\n", $bLines));

                if (empty($bTitle)) {
                    $bTitle = 'Desarrollo ' . ($blockIndex + 1);
                }
                if (empty($bBody)) {
                    continue;
                }

                $this->wizardSections[] = [
                    'id'         => 'temp_' . uniqid(),
                    'title'      => $bTitle,
                    'is_visible' => true,
                    'contents'   => [[
                        'id'         => 'temp_' . uniqid(),
                        'type'       => 'TEXT',
                        'title'      => null,
                        'body'       => $bBody,
                        'is_visible' => true,
                        'media'      => null,
                    ]],
                ];
                $blockIndex++;
            }
        } else {
            // INICIO o CIERRE — una sola sección
            $sectionTitle = $marker === 'INICIO' ? ($title ?: 'Inicio') : ($title ?: 'Cierre');

            $this->wizardSections[] = [
                'id'         => 'temp_' . uniqid(),
                'title'      => $sectionTitle,
                'is_visible' => true,
                'contents'   => [[
                    'id'         => 'temp_' . uniqid(),
                    'type'       => 'TEXT',
                    'title'      => null,
                    'body'       => $body ?: $text,
                    'is_visible' => true,
                    'media'      => null,
                ]],
            ];
        }
    }

    // ─── Wizard: Paso 3 — Recursos y Enlaces ───────────────────

    public function addWizardResource(): void
    {
        $this->validate([
            'resourceFile' => 'required|file|max:51200',
            'resourceName' => 'required|string|max:255',
        ]);

        $media = app(LmsMediaUploadService::class)->upload($this->resourceFile, auth()->id());

        $this->wizardResources[] = [
            'id'           => 'temp_' . uniqid(),
            'media_id'     => $media->id,
            'uploaded_by'  => auth()->id(),
            'display_name' => $this->resourceName,
            'description'  => '',
            'media'        => [
                'id'              => $media->id,
                'original_name'   => $media->original_name,
                'size_for_humans' => $media->size_for_humans,
                'public_url'      => $media->public_url,
            ],
        ];

        $this->reset('resourceFile', 'resourceName');
    }

    public function removeWizardResource(int $index): void
    {
        unset($this->wizardResources[$index]);
        $this->wizardResources = array_values($this->wizardResources);
    }

    public function addWizardLink(): void
    {
        $this->validate([
            'linkTitle' => 'required|string|max:255',
            'linkUrl'   => 'required|url|max:1000',
        ]);

        $this->wizardLinks[] = [
            'id'        => 'temp_' . uniqid(),
            'title'     => $this->linkTitle,
            'url'       => $this->linkUrl,
            'link_type' => $this->linkType,
        ];

        $this->reset('linkTitle', 'linkUrl');
        $this->linkType = 'REFERENCE';
    }

    public function removeWizardLink(int $index): void
    {
        unset($this->wizardLinks[$index]);
        $this->wizardLinks = array_values($this->wizardLinks);
    }

    // ─── Wizard: Paso 4 — Guardar y Publicar ───────────────────

    public function confirmPublish(): void
    {
        if (blank($this->publishAt)) {
            $this->showPublishConfirm = true;
        } else {
            $this->saveAndPublish();
        }
    }

    public function saveAndPublish(): void
    {
        $activityId = $this->selectedActivityId;

        // 1. Guardar secciones
        foreach ($this->wizardSections as $key => $sectionData) {
            if (str_starts_with((string)$sectionData['id'], 'temp_')) {
                $section = LmsActivitySection::create([
                    'activity_id' => $activityId,
                    'title'       => $sectionData['title'],
                    'sort_order'  => $sectionData['sort_order'] ?? 1,
                    'is_visible'  => $sectionData['is_visible'],
                ]);

                // Actualizar el id temporal con el id real de BD
                // para que el cleanup no elimine la sección recién creada
                $this->wizardSections[$key]['id'] = $section->id;
            } else {
                $section = LmsActivitySection::find($sectionData['id']);
                if ($section) {
                    $section->update([
                        'title'      => $sectionData['title'],
                        'is_visible' => $sectionData['is_visible'],
                    ]);
                }
            }

            if ($section) {
                // Limpiar contenidos existentes de la sección (si ya existía)
                if (!str_starts_with((string)$sectionData['id'], 'temp_')) {
                    LmsActivityContent::where('section_id', $section->id)->delete();
                }

                foreach ($sectionData['contents'] as $i => $contentData) {
                    LmsActivityContent::create([
                        'section_id' => $section->id,
                        'type'       => 'TEXT',
                        'title'      => $contentData['title'] ?? null,
                        'body'       => $contentData['body'] ?? '',
                        'sort_order' => $i + 1,
                        'is_visible' => true,
                    ]);
                }
            }
        }

        // 2. Limpiar secciones eliminadas en el wizard
        $existingIds = array_filter(array_map(fn($s) => is_numeric($s['id']) ? $s['id'] : null, $this->wizardSections));
        LmsActivitySection::where('activity_id', $activityId)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // 3. Guardar recursos (ya se subieron en addWizardResource)
        // Los recursos ya se crearon en la BD al subirlos, solo aseguramos visibilidad
        $visibleResourceIds = array_filter(array_map(fn($r) => is_numeric($r['id']) ? $r['id'] : null, $this->wizardResources));
        LmsActivityResource::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleResourceIds)
            ->update(['is_visible' => false]);
        LmsActivityResource::whereIn('id', $visibleResourceIds)
            ->update(['is_visible' => true]);

        // 4. Publicar
        app(LmsPublicationService::class)->publish(
            $this->selectedActivity,
            [
                'publish_at'      => $this->publishAt,
                'allow_downloads' => $this->allowDownloads,
            ],
            auth()->id()
        );

        LmsActivityLog::record($activityId, auth()->id(), 'PUBLISH');

        $this->saved = true;
        $this->dispatch('lesson-saved');
    }

    // ─── Export/Import ─────────────────────────────────────────

    /**
     * Abre el modal para exportar contenido LMS de una actividad
     * a otra actividad en una sección diferente del mismo grado.
     */
    public function showExport(int $activityId): void
    {
        $activity = Activity::with('pevaluacion.seccion.grado', 'pevaluacion.lapso')
            ->findOrFail($activityId);

        $currentSectionId = $activity->pevaluacion->seccion_id;
        $gradeId = $activity->pevaluacion->seccion->grado_id;

        $sections = Seccion::where('grado_id', $gradeId)
            ->where('id', '!=', $currentSectionId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        if (empty($sections)) {
            $this->notification()->warning(
                'Sin secciones',
                'No hay otras secciones en el mismo grado para exportar contenido.'
            );
            return;
        }

        $this->exportActivityId = $activityId;
        $this->exportTargetSectionId = null;
        $this->exportTargetActivityId = null;
        $this->exportAvailableSections = $sections;
        $this->exportAvailableActivities = [];
        $this->exportWizardStep = 1;
        $this->exportPreviewData = null;
        $this->showExportModal = true;
    }

    /**
     * Carga las actividades disponibles para la sección destino (exportar).
     */
    public function updatedExportTargetSectionId($value): void
    {
        $this->exportTargetActivityId = null;
        $this->exportAvailableActivities = [];

        if (!$value || !$this->exportActivityId) {
            return;
        }

        $activity = Activity::with('pevaluacion')->find($this->exportActivityId);
        if (!$activity) {
            return;
        }

        $this->exportAvailableActivities = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->withCount('contents'),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
        ])->whereHas('pevaluacion', function ($q) use ($value, $activity) {
            $q->where('seccion_id', $value)
              ->where('lapso_id', $activity->pevaluacion->lapso_id)
              ->where('profesor_id', $activity->pevaluacion->profesor_id);
        })->where('id', '!=', $this->exportActivityId)
            ->orderBy('topic')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'topic'       => $a->topic ?? 'Actividad sin título',
                'description' => $a->description ?? '',
                'start_date'  => optional(\Carbon\Carbon::parse($a->finicial))->format('d/m'),
                'end_date'    => optional(\Carbon\Carbon::parse($a->ffinal))->format('d/m'),
                'asignatura'  => $a->pevaluacion?->pensum?->asignatura?->name ?? '—',
                'grado'       => $a->pevaluacion?->pensum?->grado?->name ?? '—',
                'seccion'     => $a->pevaluacion?->seccion?->name ?? '—',
                'lapso'       => $a->pevaluacion?->lapso?->name ?? '—',
                'has_lms'     => ($a->lmsSections->isNotEmpty() || $a->lmsResources->isNotEmpty() || $a->lmsLinks->isNotEmpty()),
                'sections_count' => $a->lmsSections->count(),
                'contents_count' => $a->lmsSections->sum(fn($s) => $s->contents_count ?? 0),
                'resources_count' => $a->lmsResources->count(),
                'links_count'     => $a->lmsLinks->count(),
            ])->values()->toArray();
    }

    /**
     * Ejecuta la exportación: copia contenido LMS de la actividad actual
     * a la actividad destino seleccionada.
     */
    public function exportLesson(): void
    {
        $this->validate([
            'exportTargetSectionId' => 'required',
            'exportTargetActivityId' => 'required',
        ], [], [
            'exportTargetSectionId' => 'sección destino',
            'exportTargetActivityId' => 'actividad destino',
        ]);

        try {
            $this->copyLmsContent($this->exportActivityId, $this->exportTargetActivityId);

            $this->notification()->success(
                'Lección exportada',
                'El contenido LMS se copió a la actividad seleccionada correctamente.'
            );
            $this->closeExportModal();
        } catch (\Throwable $e) {
            $this->notification()->error('Error al exportar', $e->getMessage());
        }
    }

    /**
     * Carga la vista previa del contenido a exportar y avanza al paso 2.
     */
    public function loadExportPreview(): void
    {
        if (!$this->exportTargetActivityId) {
            return;
        }

        $activity = Activity::with([
            'pevaluacion.pensum.asignatura',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order'),
            'lmsSections.contents' => fn($q) => $q->where('is_visible', true),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsResources.media',
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
        ])->findOrFail($this->exportActivityId);

        $this->exportPreviewData = [
            'activity_id'   => $activity->id,
            'subject'       => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title'         => $activity->topic ?? 'Lección',
            'description'   => $activity->description ?? '',
            'start_date'    => $activity->finicial,
            'end_date'      => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'sections'      => $activity->lmsSections->toArray(),
            'resources'     => $activity->lmsResources->toArray(),
            'links'         => $activity->lmsLinks->toArray(),
        ];

        $this->exportWizardStep = 2;
    }

    public function goToExportStep(int $step): void
    {
        $this->exportWizardStep = max(1, min(3, $step));
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
        $this->exportActivityId = null;
        $this->exportTargetSectionId = null;
        $this->exportTargetActivityId = null;
        $this->exportAvailableSections = [];
        $this->exportAvailableActivities = [];
        $this->exportWizardStep = 1;
        $this->exportPreviewData = null;
    }

    // ─── Import ───────────────────────────────────────────────

    /**
     * Abre el modal para importar contenido LMS desde otra actividad
     * de una sección diferente del mismo grado.
     */
    public function showImport(int $activityId): void
    {
        $activity = Activity::with('pevaluacion.seccion.grado', 'pevaluacion.lapso')
            ->findOrFail($activityId);

        // Si la actividad ya tiene contenido LMS, no se puede importar
        $hasLmsContent = $activity->lmsSections()
            ->where('is_visible', true)
            ->exists();
        if ($hasLmsContent) {
            $this->notification()->warning(
                'Lección existente',
                'Esta actividad ya tiene contenido LMS. No se puede importar contenido adicional.'
            );
            return;
        }

        $currentSectionId = $activity->pevaluacion->seccion_id;
        $gradeId = $activity->pevaluacion->seccion->grado_id;

        $sections = Seccion::where('grado_id', $gradeId)
            ->where('id', '!=', $currentSectionId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        if (empty($sections)) {
            $this->notification()->warning(
                'Sin secciones',
                'No hay otras secciones en el mismo grado para importar contenido.'
            );
            return;
        }

        $this->importActivityId = $activityId;
        $this->importSourceSectionId = null;
        $this->importSourceActivityId = null;
        $this->importAvailableSections = $sections;
        $this->importAvailableActivities = [];
        $this->importWizardStep = 1;
        $this->importPreviewData = null;
        $this->showImportModal = true;
    }

    /**
     * Carga las actividades disponibles para la sección origen (importar).
     */
    public function updatedImportSourceSectionId($value): void
    {
        $this->importSourceActivityId = null;
        $this->importAvailableActivities = [];

        if (!$value || !$this->importActivityId) {
            return;
        }

        $activity = Activity::with('pevaluacion')->find($this->importActivityId);
        if (!$activity) {
            return;
        }

        $this->importAvailableActivities = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->withCount('contents'),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
        ])->whereHas('pevaluacion', function ($q) use ($value, $activity) {
            $q->where('seccion_id', $value)
              ->where('lapso_id', $activity->pevaluacion->lapso_id)
              ->where('profesor_id', $activity->pevaluacion->profesor_id);
        })->where('id', '!=', $this->importActivityId)
            ->orderBy('topic')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'topic'       => $a->topic ?? 'Actividad sin título',
                'description' => $a->description ?? '',
                'start_date'  => optional(\Carbon\Carbon::parse($a->finicial))->format('d/m'),
                'end_date'    => optional(\Carbon\Carbon::parse($a->ffinal))->format('d/m'),
                'asignatura'  => $a->pevaluacion?->pensum?->asignatura?->name ?? '—',
                'grado'       => $a->pevaluacion?->pensum?->grado?->name ?? '—',
                'seccion'     => $a->pevaluacion?->seccion?->name ?? '—',
                'lapso'       => $a->pevaluacion?->lapso?->name ?? '—',
                'has_lms'     => ($a->lmsSections->isNotEmpty() || $a->lmsResources->isNotEmpty() || $a->lmsLinks->isNotEmpty()),
                'sections_count' => $a->lmsSections->count(),
                'contents_count' => $a->lmsSections->sum(fn($s) => $s->contents_count ?? 0),
                'resources_count' => $a->lmsResources->count(),
                'links_count'     => $a->lmsLinks->count(),
            ])->values()->toArray();
    }

    /**
     * Ejecuta la importación: copia contenido LMS desde la actividad origen
     * seleccionada hacia la actividad actual.
     */
    public function importLesson(): void
    {
        $this->validate([
            'importSourceSectionId' => 'required',
            'importSourceActivityId' => 'required',
        ], [], [
            'importSourceSectionId' => 'sección origen',
            'importSourceActivityId' => 'actividad origen',
        ]);

        try {
            $this->copyLmsContent($this->importSourceActivityId, $this->importActivityId);

            $this->notification()->success(
                'Lección importada',
                'El contenido LMS se copió desde la actividad seleccionada correctamente.'
            );
            $this->closeImportModal();
        } catch (\Throwable $e) {
            $this->notification()->error('Error al importar', $e->getMessage());
        }
    }

    /**
     * Carga la vista previa del contenido a importar y avanza al paso 2.
     */
    public function loadImportPreview(): void
    {
        if (!$this->importSourceActivityId) {
            return;
        }

        $activity = Activity::with([
            'pevaluacion.pensum.asignatura',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order'),
            'lmsSections.contents' => fn($q) => $q->where('is_visible', true),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsResources.media',
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
        ])->findOrFail($this->importSourceActivityId);

        $this->importPreviewData = [
            'activity_id'   => $activity->id,
            'subject'       => $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Asignatura',
            'title'         => $activity->topic ?? 'Lección',
            'description'   => $activity->description ?? '',
            'start_date'    => $activity->finicial,
            'end_date'      => $activity->ffinal,
            'allow_downloads' => $activity->lmsPublication?->allow_downloads ?? false,
            'sections'      => $activity->lmsSections->toArray(),
            'resources'     => $activity->lmsResources->toArray(),
            'links'         => $activity->lmsLinks->toArray(),
        ];

        $this->importWizardStep = 2;
    }

    public function goToImportStep(int $step): void
    {
        $this->importWizardStep = max(1, min(3, $step));
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importActivityId = null;
        $this->importSourceSectionId = null;
        $this->importSourceActivityId = null;
        $this->importAvailableSections = [];
        $this->importAvailableActivities = [];
        $this->importWizardStep = 1;
        $this->importPreviewData = null;
    }

    // ─── Copia de contenido LMS ─────────────────────────────────

    /**
     * Copia todo el contenido LMS (secciones, contenidos, recursos y enlaces)
     * de una actividad origen a una actividad destino.
     */
    private function copyLmsContent(int $sourceActivityId, int $targetActivityId): void
    {
        // 1. Copiar secciones + contenidos
        $sections = LmsActivitySection::with('contents')
            ->where('activity_id', $sourceActivityId)
            ->orderBy('sort_order')
            ->get();

        foreach ($sections as $section) {
            $newSection = $section->replicate();
            $newSection->activity_id = $targetActivityId;
            $newSection->save();

            foreach ($section->contents as $content) {
                $newContent = $content->replicate();
                $newContent->section_id = $newSection->id;
                $newContent->save();
            }
        }

        // 2. Copiar recursos visibles (comparten el mismo media_id)
        $resources = LmsActivityResource::where('activity_id', $sourceActivityId)
            ->where('is_visible', true)
            ->get();

        foreach ($resources as $resource) {
            $newResource = $resource->replicate();
            $newResource->activity_id = $targetActivityId;
            $newResource->save();
        }

        // 3. Copiar enlaces visibles
        $links = LmsActivityLink::where('activity_id', $sourceActivityId)
            ->where('is_visible', true)
            ->get();

        foreach ($links as $link) {
            $newLink = $link->replicate();
            $newLink->activity_id = $targetActivityId;
            $newLink->save();
        }

        // 4. Crear o actualizar la publicación en estado borrador si no existe
        LmsActivityPublication::firstOrCreate(
            ['activity_id' => $targetActivityId],
            [
                'published_by'    => auth()->id(),
                'status'          => 'DRAFT',
                'allow_comments'  => true,
                'allow_downloads' => true,
            ]
        );
    }

    // ─── Helpers para la vista previa ──────────────────────────

    public function getPreviewSectionsProperty()
    {
        return array_filter($this->wizardSections, fn($s) => $s['is_visible']);
    }

    // ─── Estrategia de compactación de prompts ─────────────────
    //
    // Para evitar que prompts con muchos referentes normativos
    // excedan el budget de tokens en OpenRouter, se sigue esta
    // estrategia en 2 fases:
    //
    //   1. NVIDIA (gratuito, qwen3.5-122b) → compacta el user
    //      prompt cuando supera el budget, preservando datos
    //      curriculares esenciales.
    //   2. OpenRouter (modelo superior) → recibe el prompt
    //      compactado y genera la respuesta final.
    //
    // Si la compactación falla, se envía el original a OpenRouter
    // como fallback.

    /**
     * Estima tokens de forma conservadora (~3.5 chars/token para español).
     */
    private function estimateTokens(string $text): int
    {
        return max(1, (int) ceil(mb_strlen($text) / 3.5));
    }

    /**
     * Compacta texto vía NvidiaService preservando la información
     * pedagógica esencial. Si falla, retorna el texto original.
     */
    private function compactWithNvidia(string $text): string
    {
        /** @var NvidiaService $nvidia */
        $nvidia = app(NvidiaService::class);

        $result = $nvidia->ask(
            'Eres un asistente que compacta texto pedagógico. Preserva TODA la información esencial: datos curriculares, nombres de competencias, indicadores de logro, áreas de aprendizaje y contenidos. Elimina solo redundancias, relleno y repeticiones. No pierdas contenido sustantivo ni datos clave. Responde SOLO con el texto compactado, sin explicaciones ni metadatos.',
            $text,
            [
                'max_tokens'  => min(1536, (int) ceil($this->estimateTokens($text) * 0.55)),
                'temperature' => 0.3,
            ]
        );

        if (!$result['success'] || empty(trim($result['content'] ?? ''))) {
            return $text;
        }

        $compacted = trim($result['content']);

        // Limpiar anotaciones de seguridad que Nvidia a veces prefija
        $compacted = $this->stripSafetyAnnotations($compacted);

        if (empty($compacted)) {
            return $text;
        }

        // Solo usar si realmente se redujo (evita respuestas espurias)
        if (mb_strlen($compacted) >= mb_strlen($text) * 0.95) {
            return $text;
        }

        return $compacted;
    }

    /**
     * Elimina líneas de anotaciones de seguridad que ciertos modelos
     * (Nvidia, etc.) prefijan en las respuestas.
     *
     * Ejemplos: "User Safety: safe", "**Content Safety:** medium_low",
     * "Output Safety: high", "Safety: safe".
     */
    private function stripSafetyAnnotations(string $text): string
    {
        $lines = explode("\n", $text);
        $filtered = array_filter($lines, function (string $line): bool {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                return true;
            }
            // "User Safety: safe", "**User Safety:** safe"
            if (preg_match('/^(?:\*{1,2}\s*)?(?:User|Content|Output|Model)\s+Safety\s*:\s*(?:\*{1,2}\s*)?\w+/i', $trimmed)) {
                return false;
            }
            // "Safety: high", "**Safety:** safe"
            if (preg_match('/^(?:\*{1,2}\s*)?Safety\s*:\s*(?:\*{1,2}\s*)?\w+\s*$/i', $trimmed)) {
                return false;
            }
            return true;
        });
        return trim(implode("\n", $filtered));
    }

    /**
     * Envía un prompt a OpenRouter, compactándolo automáticamente
     * con Nvidia si el user prompt supera el token budget.
     *
     * Si OpenRouter falla (ej: créditos insuficientes), cae en
     * NvidiaService como fallback automático.
     *
     * @param  string       $systemPrompt  Instrucción del sistema.
     * @param  string       $userPrompt    Mensaje del usuario.
     * @param  array        $overrides     Overrides para el LLM.
     * @param  int          $tokenBudget   Máx. tokens del user prompt antes de compactar.
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    private function askWithCompaction(
        string $systemPrompt,
        string $userPrompt,
        array  $overrides = [],
        int    $tokenBudget = 2000
    ): array {
        $estimatedTokens = $this->estimateTokens($userPrompt);
        $compacted = false;
        $originalSize = mb_strlen($userPrompt);

        if ($estimatedTokens > $tokenBudget) {
            $compactResult = $this->compactWithNvidia($userPrompt);

            if ($compactResult !== $userPrompt && mb_strlen($compactResult) < $originalSize * 0.9) {
                $userPrompt = $compactResult;
                $compacted = true;

                $this->notification()->info(
                    'Prompt compactado',
                    'El contexto se compactó vía NVIDIA para optimizar tokens ('
                    . number_format($originalSize) . ' → ' . number_format(mb_strlen($compactResult)) . ' chars).'
                );
            }
        }

        // ─── Fase 1: intentar con OpenRouter ─────────────────────
        /** @var OpenRouterService $llm */
        $llm = app(OpenRouterService::class);
        $result = $llm->ask($systemPrompt, $userPrompt, $overrides);

        // ─── Fase 2: si OpenRouter falla, caer en Nvidia ─────────
        if (!$result['success']) {
            $errorMsg = $result['error'] ?? '';

            // HTTP 429 (rate limit) → fallback automático a Nvidia
            if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Rate limit exceeded') || str_contains($errorMsg, 'free-models-per-day')) {
                $this->notification()->info(
                    'Usando NVIDIA (fallback)',
                    'OpenRouter alcanzó el límite de requests. Usando modelo NVIDIA como alternativa.'
                );

                /** @var NvidiaService $nvidia */
                $nvidia = app(NvidiaService::class);
                return $nvidia->ask($systemPrompt, $userPrompt, $overrides);
            }

            // Errores que no son rate limit: informar al usuario
            if (str_contains($errorMsg, '402') || str_contains($errorMsg, 'Insufficient credits')) {
                $this->notification()->error(
                    'OpenRouter sin créditos',
                    'La generación requiere créditos en OpenRouter. Agrega créditos en openrouter.ai/settings/credits y vuelve a intentar.'
                );
            }

            return $result;
        }

        return $result;
    }

    // ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───

    // ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───

    // ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───

    /**
     * Parsea la respuesta del LLM extrayendo título y descripción.
     * Soporta múltiples formatos de respuesta:
     *
     *   "Título || Descripción"        (separador ||)
     *   "Título\nDescripción"           (primera línea = título)
     *   "**Título:** ...\n**Descripción:** ..."  (markdown)
     *   "Título: ...\nDescripción: ..." (etiquetas literales)
     */
    private function parseTitleDescription(string $content): array
    {
        $content = trim($content);
        if (empty($content)) {
            return ['', ''];
        }

        // ── Pre-procesamiento: eliminar líneas de seguridad/anotaciones ──
        $content = $this->stripSafetyAnnotations($content);

        // Si después de filtrar solo quedaban anotaciones de seguridad
        if (empty($content)) {
            return ['', ''];
        }

        // ── Estrategia 1: separador "||" (formato original) ──────
        if (str_contains($content, '||')) {
            $parts = explode('||', $content, 2);
            $title = trim($parts[0]);
            $desc  = trim($parts[1] ?? '');
            // Limpiar posibles prefijos tipo "Título:" o "Linea 1 →"
            $title = $this->stripLabelPrefix($title, ['titulo', 'título', 'title', 'linea 1', 'línea 1']);
            $desc  = $this->stripLabelPrefix($desc, ['descripcion', 'descripción', 'description', 'linea 2', 'línea 2']);
            if (!empty($title)) {
                return [$title, $desc];
            }
        }

        // ── Estrategia 2: etiquetas markdown "**Título:** /**Descripción:**" ─
        $mdPattern = '/\*\*(?:T[íi]tulo|Título|Title|Descripci[oó]n|Description)\s*:\s*\*\*(.*?)(?=\s*\*\*(?:T[íi]tulo|Descripci[oó]n|))\s*/ius';
        if (preg_match_all($mdPattern, $content, $mdMatches)) {
            $title = '';
            $desc  = '';
            foreach ($mdMatches[0] as $i => $fullMatch) {
                $value = trim($mdMatches[1][$i] ?? '');
                if (stripos($fullMatch, 'título') !== false || stripos($fullMatch, 'titulo') !== false || stripos($fullMatch, 'title') !== false) {
                    $title = $value;
                } elseif (stripos($fullMatch, 'descripción') !== false || stripos($fullMatch, 'descripcion') !== false || stripos($fullMatch, 'description') !== false) {
                    $desc = $value;
                }
            }
            if (!empty($title) && !empty($desc)) {
                return [$title, $desc];
            }
        }

        // ── Estrategia 3: etiquetas literales "Título:" / "Descripción:" ──
        $labelPattern = '/(?:T[íi]tulo|Título|Title|Descripci[oó]n|Description)\s*:\s*(.*?)(?=(?:\n(?:T[íi]tulo|Descripci[oó]n|Description)\s*:))/ius';
        // Buscar párrafos etiquetados
        $lines = explode("\n", $content);
        $title = '';
        $desc  = '';
        $currentLabel = null;
        $buffer = '';
        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if (empty($line)) {
                continue;
            }
            // Detectar etiqueta
            $matched = false;
            foreach (['Título:', 'Titulo:', 'Title:', 'Descripción:', 'Descripcion:', 'Description:'] as $label) {
                if (str_starts_with(mb_strtolower($line), mb_strtolower($label))) {
                    // Guardar buffer anterior
                    if ($currentLabel === 'title' && !empty($buffer)) {
                        $title = trim($buffer);
                    } elseif ($currentLabel === 'desc' && !empty($buffer)) {
                        $desc = trim($buffer);
                    }
                    $currentLabel = (stripos($label, 'título') !== false || stripos($label, 'titulo') !== false || stripos($label, 'title') !== false) ? 'title' : 'desc';
                    $buffer = trim(mb_substr($line, mb_strlen($label)));
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $buffer .= "\n" . $line;
            }
        }
        // Último buffer
        if ($currentLabel === 'title' && !empty($buffer)) {
            $title = trim($buffer);
        } elseif ($currentLabel === 'desc' && !empty($buffer)) {
            $desc = trim($buffer);
        }
        if (!empty($title) && !empty($desc)) {
            return [$title, $desc];
        }

        // ── Estrategia 4: primera línea = título, resto = descripción ──
        $nonEmpty = array_values(array_filter(explode("\n", $content), fn($l) => !empty(trim($l))));
        if (count($nonEmpty) >= 2) {
            $first = trim($nonEmpty[0]);
            $rest  = trim(implode("\n", array_slice($nonEmpty, 1)));
            // La primera línea no debería ser muy larga para ser título
            if (mb_strlen($first) <= 200 && !empty($rest)) {
                return [$first, $rest];
            }
        }

        // ── Estrategia 5 (fallback absoluto): todo es el título ──
        $maxTitle = 120;
        $fallbackTitle = mb_strlen($content) > $maxTitle ? mb_substr($content, 0, $maxTitle) . '…' : $content;
        return [$fallbackTitle, ''];
    }

    /**
     * Elimina prefijos de etiqueta como "Título:" o "Línea 1 →" del texto.
     */
    private function stripLabelPrefix(string $text, array $labels): string
    {
        $text = trim($text);
        foreach ($labels as $label) {
            // Con dos puntos
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label) . ':')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 1));
            }
            // Con flecha "→"
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label) . '→')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 1));
            }
            // Con guión " - " o " -> "
            if (str_starts_with(mb_strtolower($text), mb_strtolower($label) . ' -')) {
                $text = trim(mb_substr($text, mb_strlen($label) + 2));
            }
        }
        return trim($text);
    }

    // ─── Render ────────────────────────────────────────────────

    public function render()
    {
        if ($this->mode === 'wizard') {
            return view('livewire.profesor.lms.lesson-wizard', [
                'totalSteps' => 4,
            ])->layout('profesors.layouts.app');
        }

        // Modo listado
        $profesor = Profesor::where('user_id', auth()->id())->first();

        $query = Activity::whereHas('pevaluacion', function ($q) use ($profesor) {
            $q->where('profesor_id', $profesor?->id);
            if ($this->lapsoId) {
                $q->where('lapso_id', $this->lapsoId);
            }
            if ($this->pestudioId) {
                $q->whereHas('pensum', fn($pq) => $pq->where('pestudio_id', $this->pestudioId));
            }
            if ($this->gradoId) {
                $q->whereHas('pensum', fn($pq) => $pq->where('grado_id', $this->gradoId));
            }
            if ($this->seccionId) {
                $q->where('seccion_id', $this->seccionId);
            }
        })->with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections' => fn($q) => $q->withCount('contents'),
            'lmsResources' => fn($q) => $q->where('is_visible', true),
            'lmsLinks' => fn($q) => $q->where('is_visible', true),
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', '%' . $this->search . '%')
                  ->orWhere('thematic', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $activities = $query->orderBy('finicial', 'desc')->paginate(12);

        // Listas para filtros
        $listLapso    = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');
        $listPestudio = Pestudio::where('planning_module', true)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->pluck('name', 'id');
        $listGrado    = $this->pestudioId
            ? Grado::where('pestudio_id', $this->pestudioId)->pluck('name', 'id')
            : collect();
        $listSeccion  = $this->gradoId
            ? Seccion::where('grado_id', $this->gradoId)->pluck('name', 'id')
            : collect();

        return view('livewire.profesor.lms.lesson-wizard', compact(
            'activities', 'listLapso', 'listPestudio', 'listGrado', 'listSeccion'
        ))->layout('profesors.layouts.app');
    }
}
