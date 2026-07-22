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
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\app\Instrument\DiagReferent;
use App\Services\Lms\HtmlTaggingService;
use App\Services\Lms\LmsMediaUploadService;
use App\Services\Lms\LmsPublicationService;
use App\Services\NapkinAiService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class LessonWizard extends Component
{
    use WithPagination, WithFileUploads, WireUiActions;

    // ─── FALLBACK REINFORCEMENT ─────────────────────────────────
    private const FALLBACK_REINFORCEMENT = <<<'TEXT'

⚠️ CORRECCIÓN — Intento anterior no siguió las instrucciones.

Reglas críticas:
1. Todo en ESPAÑOL académico. NO uses inglés.
2. Usa SOLO el contexto de la actividad — nada de superhéroes, aventuras fantásticas, identidades secretas ni temas genéricos.
3. Estructura exacta:
   //INICIO
   ...
   //DESARROLLO
   Bloque 1
   ...
   (mínimo 5 bloques separados por línea en blanco)
   //CIERRE
   ...
4. Sin meta-comentarios, explicaciones ni introducciones.
5. El ejemplo en las instrucciones es solo para mostrar el FORMATO — usa el contexto real de la actividad.
TEXT;

    // ─── Mode: 'list' | 'wizard' ──────────────────────────────
    public string $mode = 'list';
    public string $viewMode = 'grid';
    public ?int $selectedActivityId = null;

    // ─── Lifecycle hooks ─────────────────────────────────────────

    public function updatedViewMode($value): void
    {
        session()->put('lesson_wizard_view_mode', $value);
    }
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

    // ─── Wizard: Preguntas de repaso (markdown) ─────────────────
    public string $reviewQuestions = '';

    // ─── Wizard: Panel de recursos por sección (paso 2) ─────────
    public ?int $resourcePanelSection = null;

    // ─── Slide navigation (paso 2) ────────────────────────────
    public int $currentSlideIndex = 0;
    public bool $showSlideHtmlPreview = false;

    // ─── Wizard: Generación con IA ─────────────────────────────
    public ?int $generatingSection = null;
    public bool $generatingStep1 = false;
    public bool $generatingStep2 = false;
    public ?string $generationError = null;
    public ?string $debugRawContent = null;

    // ─── Wizard: Resultado de generación (typewriter overlay) ──
    public bool $showGenerationResult = false;
    public ?string $generationType = null; // 'step1' | 'step2' | 'section'

    // ─── Wizard: Vista previa profesor (full-screen dialog) ──
    public bool $showFullPreview = false;

    // ─── Review Questions preview ────────────────────────────
    public bool $showReviewPreview = false;

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

    // ─── Step 3: Section selector for image prompt ─────────────
    public mixed $step3ImageSectionId = null;

    // ─── Export wizard ─────────────────────────────────────────
    public int $exportWizardStep = 1;
    public ?array $exportPreviewData = null;

    // ─── Vista previa estudiante (unificada) ─────────────────
    public bool $showListStudentPreview = false;
    public ?array $listPreviewData = null;

    // ─── Wizard: Recursos temporales ───────────────────────────
    public $resourceFile;
    public string $resourceName = '';
    public mixed $resourceSectionId = null;
    public array $wizardResources = [];
    public ?int $editingResourceIndex = null;

    // ─── Wizard: Enlaces temporales ────────────────────────────
    public string $linkTitle = '';
    public string $linkUrl = '';
    public string $linkType = 'REFERENCE';
    public mixed $linkSectionId = null;
    public array $wizardLinks = [];

    // ─── Wizard: HTML embeds temporales ────────────────────────
    public array $wizardHtmlEmbeds = [];
    public string $embedTitle = '';
    public string $embedHtml = '';
    public mixed $embedSectionId = null;
    public ?int $previewEmbedIndex = null;
    public ?int $editingEmbedIndex = null;
    public ?int $previewResourceIndex = null;
    public bool $generatingEmbedCard = false;
    public bool $showEmbedPreview = false;
    public string $embedDiagramType = '';
    public string $embedPromptRefinement = '';

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
            'resourceFile'    => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,mp4,mp3',
            'resourceName'    => 'required_with:resourceFile|nullable|string|max:255',
            'linkTitle'       => 'required_with:linkUrl|nullable|string|max:255',
            'linkUrl'         => 'required_with:linkTitle|nullable|url|max:1000',
        ];
    }

    // ─── Lifecycle ─────────────────────────────────────────────

    public function mount(): void
    {
        $this->lapsoId = Lapso::current()?->id;

        // Restaurar modo de vista desde sesión
        $this->viewMode = session('lesson_wizard_view_mode', 'grid');

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

        $profesor = \App\Models\app\Academy\Profesor::where('user_id', auth()->id())->first();
        abort_unless(
            auth()->user()->is_admin
            || ($profesor && $activity->pevaluacion->profesor_id === $profesor->id),
            403
        );

        $this->selectedActivity   = $activity;
        $this->selectedActivityId = $activityId;
        $this->lessonTitle        = $activity->topic ?? '';
        $this->lessonDescription  = $activity->description ?? '';
        $this->allowDownloads     = $activity->lmsPublication?->allow_downloads ?? true;

        // Cargar secciones existentes en el wizard (sanear)
        $this->wizardSections = $activity->lmsSections()
            ->with('contents')
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // Sanear títulos y cuerpos de contenido al cargar
        foreach ($this->wizardSections as $sKey => $section) {
            $this->wizardSections[$sKey]['title'] = $this->sanitizeText($section['title']);
            foreach ($section['contents'] ?? [] as $cKey => $content) {
                $this->wizardSections[$sKey]['contents'][$cKey]['title'] = $this->sanitizeText($content['title'] ?? null);
                $this->wizardSections[$sKey]['contents'][$cKey]['body']  = $this->sanitizeText($content['body'] ?? '');
            }
        }

        // ─── Extraer preguntas de repaso de las secciones ─────
        $this->reviewQuestions = '';
        foreach ($this->wizardSections as $sKey => $section) {
            if (($section['title'] ?? '') === 'Preguntas de Repaso') {
                $reviewBody = $section['contents'][0]['body'] ?? '';
                if (!empty($reviewBody)) {
                    $this->reviewQuestions = $this->sanitizeText($reviewBody);
                }
                unset($this->wizardSections[$sKey]);
                break;
            }
        }
        $this->wizardSections = array_values($this->wizardSections);

        $this->wizardResources = $activity->lmsResources()
            ->where('is_visible', true)
            ->with('media')
            ->get()
            ->toArray();

        $this->wizardLinks = $activity->lmsLinks()
            ->where('is_visible', true)
            ->get()
            ->toArray();

        $this->wizardHtmlEmbeds = $activity->lmsHtmlEmbeds()
            ->where('is_visible', true)
            ->get()
            ->map(fn($e) => $this->ensureMermaidWrapper($e->toArray()))
            ->values()
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
        // Al volver al wizard desde el estado "guardado", ocultar el mensaje de éxito
        $this->saved = false;
    }

    // ─── Slide navigation (paso 2) ────────────────────────────

    public function goToSlide(int $index): void
    {
        $max = max(0, count($this->wizardSections) - 1);
        $this->currentSlideIndex = max(0, min($max, $index));
    }

    public function nextSlide(): void
    {
        $this->goToSlide($this->currentSlideIndex + 1);
    }

    public function prevSlide(): void
    {
        $this->goToSlide($this->currentSlideIndex - 1);
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
        $this->wizardHtmlEmbeds = [];
        $this->wizardReferents = null;
        $this->saved = false;
        $this->showPublishConfirm = false;
        $this->editingSectionIndex = null;
        $this->resourcePanelSection = null;
        $this->showGenerationResult = false;
        $this->generationType = null;
        $this->showFullPreview = false;
        $this->generationError = null;
        $this->lessonTitle = '';
        $this->lessonDescription = '';
        $this->reviewQuestions = '';
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
                'pevaluacion.lapso',
                'pevaluacion.seccion',
                'pevaluacion.pensum.grado',
                'pevaluacion.pensum.asignatura',
                'pevaluacion.pensum.pestudio.peducativo.pescolar.institucion',
                'lmsPublication',
                'lmsSections' => fn($q) => $q->where('is_visible', true)->orderBy('sort_order'),
                'lmsSections.contents' => fn($q) => $q->where('is_visible', true),
                'lmsResources' => fn($q) => $q->where('is_visible', true),
                'lmsResources.media',
                'lmsLinks' => fn($q) => $q->where('is_visible', true),
                'lmsHtmlEmbeds' => fn($q) => $q->where('is_visible', true),
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
                // Separar "Preguntas de Repaso" de las secciones normales
                'review_questions' => collect($activity->lmsSections->toArray())
                    ->filter(fn($s) => ($s['title'] ?? '') === 'Preguntas de Repaso')
                    ->flatMap(fn($s) => collect($s['contents'] ?? [])->pluck('body'))
                    ->filter()
                    ->implode("\n\n"),
                'sections'      => $activity->lmsSections
                    ->reject(fn($s) => $s->title === 'Preguntas de Repaso')
                    ->values()
                    ->toArray(),
                'resources'     => $activity->lmsResources->toArray(),
                'links'         => $activity->lmsLinks->toArray(),
                'html_embeds'   => $activity->lmsHtmlEmbeds
                    ->map(fn($e) => $this->ensureMermaidWrapper($e->toArray()))
                    ->values()
                    ->toArray(),
                // Portada institucional
                'institution'       => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->name ?? '',
                'institution_rif'   => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->rif_institution ?? '',
                'institution_city'  => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->institucion?->city ?? '',
                'periodo'           => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->name ?? '',
                'periodo_finicial'  => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->finicial ?? '',
                'periodo_ffinal'    => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->pescolar?->ffinal ?? '',
                'plan_educativo'    => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->name ?? '',
                'plan_educativo_desc' => $activity->pevaluacion?->pensum?->pestudio?->peducativo?->description ?? '',
                'plan_estudio'      => $activity->pevaluacion?->pensum?->pestudio?->name ?? '',
                'plan_estudio_code' => $activity->pevaluacion?->pensum?->pestudio?->code ?? '',
                'grado'             => $activity->pevaluacion?->pensum?->grado?->name ?? '',
                'grado_code'        => $activity->pevaluacion?->pensum?->grado?->code ?? '',
                'seccion'           => $activity->pevaluacion?->seccion?->name ?? '',
                'seccion_desc'      => $activity->pevaluacion?->seccion?->description ?? '',
                'seccion_students'  => $activity->pevaluacion?->seccion?->amount_student ?? '',
                'pensum'            => $activity->pevaluacion?->pensum?->asignatura?->name ?? '',
                'asignatura_code'   => $activity->pevaluacion?->pensum?->asignatura?->code ?? '',
                'asignatura_hours'  => $activity->pevaluacion?->pensum?->asignatura?->hour_t_week ?? '',
                'lapso'             => $activity->pevaluacion?->lapso?->name ?? '',
                'lapso_finicial'    => $activity->pevaluacion?->lapso?->finicial ?? '',
                'lapso_ffinal'      => $activity->pevaluacion?->lapso?->ffinal ?? '',
                // Activity extras
                'thematic'          => $activity->thematic ?? '',
                'references'        => $activity->references ?? '',
                'activity_status'   => $activity->status ?? false,
                'teaching'          => $activity->teaching ?? '',
                'has_teaching_structure' => $activity->hasTeachingStructure(),
                'teaching_sections' => collect($activity->getTeachingSections())
                    ->map(fn($content, $title) => compact('title', 'content'))
                    ->values()
                    ->toArray(),
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

    public function openStudentPreviewFromSaved(): void
    {
        if ($this->selectedActivityId) {
            $this->openListStudentPreview($this->selectedActivityId);
        }
    }

    /**
     * Normaliza los datos del wizard (sin guardar) al formato
     * del componente unificado de vista estudiante.
     */
    public function openWizardStudentPreview(): void
    {
        $act = $this->selectedActivity;

        $sections = [];
        foreach ($this->previewSections as $sec) {
            $contents = [];
            foreach ($sec['contents'] as $c) {
                $contents[] = [
                    'title' => $c['title'] ?? '',
                    'body'  => $c['body'] ?? '',
                ];
            }
            $sections[] = [
                'id'       => $sec['id'],
                'title'    => $sec['title'],
                'contents' => $contents,
            ];
        }

        $pe = $act?->pevaluacion;

        $this->listPreviewData = [
            'activity_id'   => $act?->id,
            'subject'       => $pe?->pensum?->asignatura?->name ?? 'Asignatura',
            'title'         => $this->lessonTitle ?: $act?->topic ?? 'Lección',
            'description'   => $this->lessonDescription ?: $act?->description ?? '',
            'start_date'    => $act?->finicial,
            'end_date'      => $act?->ffinal,
            'allow_downloads'        => $this->allowDownloads,
            'allow_comments'         => true,
            'review_questions'       => $this->reviewQuestions,
            'sections'               => $sections,
            'resources'     => $this->wizardResources,
            'links'         => $this->wizardLinks,
            'html_embeds'   => collect($this->wizardHtmlEmbeds)
                ->map(fn($e) => $this->ensureMermaidWrapper($e))
                ->toArray(),
            // Portada — no hay datos institucionales en wizard, se usan defaults
            'institution'       => '',
            'institution_rif'   => '',
            'institution_city'  => '',
            'periodo'           => '',
            'plan_educativo'    => '',
            'plan_estudio'      => '',
            'plan_estudio_code' => '',
            'grado'             => $pe?->pensum?->grado?->name ?? '',
            'grado_code'        => $pe?->pensum?->grado?->code ?? '',
            'seccion'           => $pe?->seccion?->name ?? '',
            'seccion_desc'      => '',
            'seccion_students'  => '',
            'pensum'            => $pe?->pensum?->asignatura?->name ?? '',
            'asignatura_code'   => $pe?->pensum?->asignatura?->code ?? '',
            'asignatura_hours'  => $pe?->pensum?->asignatura?->hour_t_week ?? '',
            'lapso'             => $pe?->lapso?->name ?? '',
            'lapso_finicial'    => $pe?->lapso?->finicial ?? '',
            'lapso_ffinal'      => $pe?->lapso?->ffinal ?? '',
            // Activity extras
            'thematic'          => $act?->thematic ?? '',
            'references'        => $act?->references ?? '',
            'activity_status'   => $act?->status ?? false,
            'teaching'          => $act?->teaching ?? '',
            'has_teaching_structure' => $act?->hasTeachingStructure() ?? false,
            'teaching_sections' => $act?->hasTeachingStructure()
                ? collect($act->getTeachingSections())
                    ->map(fn($content, $title) => compact('title', 'content'))
                    ->values()
                    ->toArray()
                : [],
        ];

        $this->showListStudentPreview = true;
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
        $profesor = \App\Models\app\Academy\Profesor::where('user_id', auth()->id())->first();
        abort_unless(
            auth()->user()->is_admin
            || ($profesor && $activity->pevaluacion->profesor_id === $profesor->id),
            403
        );

        try {
            DB::transaction(function () use ($activity) {
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

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado, con la calidad narrativa de un best seller. Vocabulario preciso, sintaxis cuidada, tono pedagógico pero elegante. Cada oración debe aportar valor y mantener un estilo literario impecable.

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
                ['max_tokens' => 8192, 'timeout' => 120],
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

            // Sanear antes de parsear (eliminar **, espacios extra, etc.)
            $content = $this->sanitizeText($content);

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

    /**
     * Muestra diálogo de confirmación para limpiar todas las secciones.
     */
    public function confirmResetWizardSections(): void
    {
        $count = count($this->wizardSections);
        if ($count === 0) {
            $this->notification()->info('Sin secciones', 'No hay diapositivas que limpiar.');
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Limpiar todas las diapositivas',
            'description' => "¿Eliminar las {$count} diapositivas de la estructura actual? Se borrarán títulos, contenidos y bloques. Esta acción no se puede deshacer.",
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Limpiar todo',
                'method' => 'resetWizardSections',
                'color'  => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function resetWizardSections(): void
    {
        // ─── Eliminar registros de la BD si existen ────────────
        $realSectionIds = [];
        $realContentIds = [];

        foreach ($this->wizardSections as $section) {
            $sectionId = $section['id'] ?? null;
            if ($sectionId && !str_starts_with((string)$sectionId, 'temp_')) {
                $realSectionIds[] = (int) $sectionId;

                if (!empty($section['contents'])) {
                    foreach ($section['contents'] as $content) {
                        $contentId = $content['id'] ?? null;
                        if ($contentId && !str_starts_with((string)$contentId, 'temp_')) {
                            $realContentIds[] = (int) $contentId;
                        }
                    }
                }
            }
        }

        if (!empty($realSectionIds)) {
            DB::transaction(function () use ($realSectionIds, $realContentIds) {
                if (!empty($realContentIds)) {
                    LmsActivityContent::whereIn('id', $realContentIds)->delete();
                } else {
                    LmsActivityContent::whereIn('section_id', $realSectionIds)->delete();
                }
                LmsActivitySection::whereIn('id', $realSectionIds)->delete();
            });
        }

        // ─── Limpiar estado en memoria ──────────────────────────
        $this->wizardSections = [];
        $this->reviewQuestions = '';
        $this->currentSlideIndex = 0;
        $this->generatingSection = null;
        $this->generationError = null;
        $this->debugRawContent = null;

        $this->notification()->success(
            'Secciones limpiadas',
            'La estructura de diapositivas se ha reiniciado correctamente.'
        );
    }

    public function addWizardContent(int $sectionIndex): void
    {
        $this->validate(['contentBody' => 'required|string|min:1']);

        $this->wizardSections[$sectionIndex]['contents'][] = [
            'id'       => 'temp_' . uniqid(),
            'type'     => 'TEXT',
            'title'    => $this->sanitizeText($this->contentTitle) ?: null,
            'body'     => $this->sanitizeText($this->contentBody),
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

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado, con la calidad narrativa de un best seller. Vocabulario preciso, sintaxis cuidada, tono pedagógico pero elegante. Cada oración debe aportar valor y mantener un estilo literario impecable. El contenido debe redactarse como si formara parte de un libro de texto de alta calidad editorial.

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
                ['max_tokens' => 512, 'timeout' => 120],
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

            // ─── Agregar como nuevo bloque de contenido (sanear) ─
            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'       => 'temp_' . uniqid(),
                'type'     => 'TEXT',
                'title'    => null,
                'body'     => $this->sanitizeText($content),
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

    // ─── Slide editor: Generar texto HTML para diapositiva actual ──

    /**
     * Genera contenido HTML para la diapositiva (secci├│n) actual usando IA.
     * Similar a generateSectionContent() pero produce HTML sem├íntico
     * con clases Tailwind en lugar de texto plano.
     */
    public function generateSlideText(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex])) {
            return;
        }
        if (count($this->wizardSections[$sectionIndex]['contents'] ?? []) >= 2) {
            $this->notification()->warning('Límite alcanzado', 'Máximo 2 bloques de contenido por diapositiva.');
            return;
        }

        $this->generatingSection = $sectionIndex;
        $this->generationError = null;

        $sectionTitle = $this->wizardSections[$sectionIndex]['title'];
        $originalBody = $this->wizardSections[$sectionIndex]['contents'][0]['body'] ?? '';
        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        $gradeName   = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';

        $activityContext = collect([
            'Tema generador'       => $activity->topic,
            'Tejido tem├ítico'      => $activity->thematic,
            'Actividad evaluativa' => $activity->description,
            'Ense├▒anza'            => $activity->teaching,
            'Aprendizaje esperado' => $activity->learning,
            'Referentes te├│ricos'  => $activity->references,
            'ODS/Sistematizaci├│n'  => $activity->observations,
        ])->filter()->map(fn($v, $k) => "├ó—ó {$k}: {$v}")->implode("\n");

        $indicators = $activity?->achievements?->pluck('name')?->filter() ?? collect();
        $indicatorsText = $indicators->isNotEmpty()
            ? $indicators->map(fn($n) => "├ó—ó {$n}")->implode("\n")
            : '—';

        $referentsText = $this->getReferentsContext($pevaluacion?->pensum?->pestudio_id, $pevaluacion?->pensum);

        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Enriquece el contenido pedagógico existente convirtiéndolo en Markdown estructurado, preservando su significado original, ejemplos concretos y tono narrativo.

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado. Vocabulario preciso, sintaxis cuidada, tono pedagógico pero elegante.

CRÍTICO — CONSISTENCIA NARRATIVA: Preserva los mismos ejemplos concretos que aparecen en el contenido original (artesano, cultivos, costos de producción, etc.) y el mismo tono (primera persona del plural si el original la usa). No cambies los ejemplos ni introduzcas casos genéricos no relacionados. El contenido generado debe sentirse como una continuación natural del texto original, no como un reemplazo.

EXTENSIÓN OBLIGATORIA: Mínimo 500 caracteres.
Si generas menos de 500 caracteres tu respuesta será rechazada.

Estructura mínima:
1. ## Título llamativo (derivado del contenido existente)
2. Párrafo introductorio (2-3 oraciones)
3. Tabla o lista con información clave (basada en el contenido original)
4. Párrafo de desarrollo retomando los ejemplos originales
5. Párrafo de cierre

Usa Markdown estándar: ## títulos, **negritas**, tablas | |, listas -, *cursivas*.
NO uses HTML, NO uses ```, NO incluyas explicaciones.
Responde SOLO con el contenido Markdown.

FONDOS: Si incluyes tablas o elementos con color, usa siempre fondos claros (blanco o gris muy claro). NUNCA fondos oscuros.
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Curso:** {$gradeName} · {$subjectName}

**Actividad pedag├│gica:
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

**Diapositiva:** {$sectionTitle}

### Contenido original de la diapositiva (presérvalo como base)

{$originalBody}

INSTRUCCIÓN: Usa el contenido original como punto de partida. Enriquece su estructura añadiendo título, tabla o lista y párrafos, pero PRESERVA el significado, los ejemplos concretos y el tono narrativo original. No reemplaces los ejemplos por otros genéricos. El Markdown generado debe sentirse como una mejora del texto original, manteniendo su esencia.

CRUCIAL: Nada de fondos oscuros ni colores saturados. Todo debe tener fondo claro y texto oscuro legible. Mínimo 500 caracteres obligatorio.
PROMPT;

        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 2048, 'timeout' => 120],
                2000,
                null,
                [
                    ['model' => config('openrouter.model_text_primary'),   'label' => 'Texto Ling 2.6 Flash primario'],
                    ['model' => config('openrouter.model_text_fallback1'), 'label' => 'Texto Nemotron 3 Nano fallback 1'],
                    ['model' => config('openrouter.model_text_fallback2'), 'label' => 'Texto Mistral Large fallback 2'],
                ]
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingSection = null;
                $this->notification()->error('Error al generar', $result['error']);
                return;
            }

            $content = trim($result['content'] ?? '');
            // DEBUG: enviar raw a la consola
            $this->dispatch('debug-raw', ['raw' => $content, 'length' => mb_strlen($content), 'model' => $result['model'] ?? '?']);

            if (empty($content)) {
                $this->generationError = 'La IA no gener├│ contenido.';
                $this->generatingSection = null;
                return;
            }

            // Limpiar posibles wrappers markdown (```, ```markdown, ```md)
            $content = preg_replace('/^```(?:markdown|md|html)?\s*\n?/i', '', $content);
            $content = preg_replace('/\n?```\s*$/s', '', $content);
            $content = trim($content);

            // Sanitizar
            $content = $this->sanitizeText($content, 'basic');
            // Limitar a 1500 caracteres, cortando en límite de párrafo para no partir tablas ni oraciones
            if (mb_strlen($content) > 1500) {
                $truncated = mb_substr($content, 0, 1500);
                // Buscar el último salto de párrafo doble (\n\n) dentro de los últimos 300 caracteres
                $lastBreak = mb_strrpos($truncated, "\n\n");
                if ($lastBreak !== false && $lastBreak > 1100) {
                    $content = mb_substr($truncated, 0, $lastBreak);
                } else {
                    // Fallback: último salto de línea simple
                    $lastNewline = mb_strrpos($truncated, "\n");
                    if ($lastNewline !== false && $lastNewline > 1100) {
                        $content = mb_substr($truncated, 0, $lastNewline);
                    } else {
                        $content = $truncated;
                    }
                }
                $content = trim($content);
            }
            // Siempre agregar como nuevo bloque adicional
            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'         => 'temp_' . uniqid(),
                'type'       => 'TEXT',
                'title'      => null,
                'body'       => $content,
                'is_visible' => true,
                'media'      => null,
            ];

            $charCount = mb_strlen($content);
            $this->dispatch('show-preview');

            $this->notification()->success(
                'Texto generado',
                "{$sectionTitle}: {$charCount} caracteres generados"
            );
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error', $e->getMessage());
        } finally {
            $this->generatingSection = null;
        }
    }

    // ─── Slide editor: Generar imagen para diapositiva actual ─────

    /**
     * Abre el panel de prompt de imagen para la diapositiva actual
     * y crea un bloque <img> placeholder en el contenido.
     */
    public function generateSlideImage(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex])) {
            return;
        }
        if (count($this->wizardSections[$sectionIndex]['contents'] ?? []) >= 2) {
            $this->notification()->warning('Límite alcanzado', 'Máximo 2 bloques de contenido por diapositiva.');
            return;
        }

        $sectionTitle = $this->wizardSections[$sectionIndex]['title'];
        $sectionBody = collect($this->wizardSections[$sectionIndex]['contents'] ?? [])
            ->pluck('body')
            ->filter()
            ->map(fn($b) => strip_tags($b))
            ->implode("\n");
        $sectionPreview = \Illuminate\Support\Str::limit($sectionBody, 300) ?: 'Contenido pedagógico de la sección';

        $gradeName   = $this->selectedActivity?->pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $this->selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—';

        // ─── OpenRouter: IA genera SVG ──────────────────────────────
        $svgHtml = null;

        $systemPrompt = <<<'PROMPT'
Eres un diseñador de diagramas educativos. Genera únicamente código SVG válido y auto-contenido.

REGLAS DE DISEÑO ESTRICTAS:
- Fondo GENERAL del SVG: blanco (#ffffff) o gris muy claro (#f8f9fa). NUNCA uses fondos oscuros.
- Cajas/nodos: fondos de colores pastel suaves (#e3f2fd, #fce4ec, #e8f5e9, #fff3e0, #f3e5f5, #e0f7fa, #f1f8e9, #fbe9e7). NUNCA fondos saturados ni oscuros.
- Texto: color oscuro (#333333 o #1a1a1a) sobre fondos claros. NUNCA texto blanco sobre fondo claro.
- Bordes: suaves (#cccccc o #bbbbbb), con border-radius de 8px en cajas.
- Espaciado generoso entre elementos (mínimo 20px). NADA solapado.
- Tipografía: sans-serif (Arial, Helvetica), tamaños legibles (14px+ texto normal, 16px+ títulos).
- Layout: limpio, bien alineado, con jerarquía visual clara. Usa diseño de arriba a abajo o izquierda a derecha.
- NO uses gradientes, sombras excesivas, ni efectos 3D.
- NO incluyas explicaciones, markdown ni texto fuera del código SVG.
- Responde SOLO <svg>...</svg>.
PROMPT;

        $userPrompt = <<<PROMPT
Genera un diagrama SVG educativo claro y bien estructurado sobre: "{$sectionTitle}".

Contexto pedagógico:
- Grado: {$gradeName}
- Asignatura: {$subjectName}
- Contenido de la sección: {$sectionPreview}

REQUISITOS VISUALES:
1. Fondo blanco (#ffffff) o gris muy clarito (#f8f9fa).
2. Cajas con fondos pastel suaves, bordes redondeados (rx="8"), texto oscuro legible.
3. Espaciado amplio entre elementos — NADA DEBE SOLAPARSE.
4. viewBox proporcional y bien dimensionado para que nada se corte.
5. Jerarquía visual clara: título principal grande, sub-elementos más pequeños.
6. Flechas o líneas de conexión simples y claras entre conceptos.
7. Sin JS, sin CSS externo, sin CDNs. Todo inline en el SVG.

Responde SOLO el código SVG. Sin markdown, sin explicaciones.
PROMPT;

        try {
            $aiResult = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 4096, 'temperature' => 0.4, 'timeout' => 300],
                2000,
                null,
                [
                    ['model' => config('openrouter.model_image_primary'),   'label' => 'Imagen Claude Sonnet 4 primario'],
                    ['model' => config('openrouter.model_image_fallback1'), 'label' => 'Imagen Nemotron 3 Nano fallback 1'],
                    ['model' => config('openrouter.model_image_fallback2'), 'label' => 'Imagen Mistral Large fallback 2'],
                ]
            );

            if ($aiResult['success'] && $aiResult['content']) {
                $rawSvg = $aiResult['content'];
                // Limpiar wrappers de markdown
                $rawSvg = preg_replace('/^```(?:svg|html)?\s*\n?/i', '', $rawSvg);
                $rawSvg = preg_replace('/\n?```\s*$/s', '', $rawSvg);
                $rawSvg = trim($rawSvg);

                // Extraer <svg>...</svg> aunque haya texto antes/después
                if (preg_match('/<svg[\s>].*?<\/svg>/is', $rawSvg, $svgMatch)) {
                    $rawSvg = $svgMatch[0];
                    $title = e('Diagrama: ' . $sectionTitle);
                    $svgHtml = '<figure class="my-6">' . "\n"
                        . '  <figcaption class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">'
                        . $title . '</figcaption>' . "\n"
                        . '  <div class="flex justify-center rounded-xl p-2">'
                        . "\n    " . $rawSvg . "\n  </div>\n"
                        . '</figure>';
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('generateSlideImage: OpenRouter falló', [
                'error' => $e->getMessage(),
            ]);
        }

        // ─── Insertar el SVG en la sección ──────────────────────────
        if (!empty($svgHtml)) {
            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'         => 'temp_' . uniqid(),
                'type'       => 'IMAGE',
                'title'      => 'Diagrama: ' . $sectionTitle,
                'body'       => $svgHtml,
                'is_visible' => true,
                'media'      => null,
            ];

            $this->notification()->success(
                'Diagrama SVG generado',
                "Se generó un diagrama educativo para \"{$sectionTitle}\"."
            );
            return;
        }

        // ─── Error ──────────────────────────────────────────────────
        $this->notification()->error(
            'Error al generar diagrama',
            'No se pudo generar el diagrama SVG. Intenta de nuevo o usa el editor HTML.'
        );
    }

    // ─── Slide editor: Generar ilustración SVG para diapositiva actual ───

    /**
     * Genera una ilustración SVG educativa para la diapositiva/sección actual
     * usando askWithCompaction() con cadena de modelos (prompt SVG-educativo-v3).
     * Inserta el resultado como un bloque de contenido en la sección.
     */
    public function generateSectionIllustration(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex])) {
            return;
        }
        if (count($this->wizardSections[$sectionIndex]['contents'] ?? []) >= 2) {
            $this->notification()->warning('Límite alcanzado', 'Máximo 2 bloques de contenido por diapositiva.');
            return;
        }

        $sectionTitle = $this->wizardSections[$sectionIndex]['title'] ?? 'Sección';

        $sectionBody = collect($this->wizardSections[$sectionIndex]['contents'] ?? [])
            ->pluck('body')
            ->filter()
            ->map(fn($b) => strip_tags($b))
            ->implode("\n");

        $sectionBody = \Illuminate\Support\Str::limit($sectionBody, 2000) ?: 'Contenido pedagógico de la sección';

        $gradeName   = $this->selectedActivity?->pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $this->selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—';

        $systemPrompt = \App\Services\Lms\GenerateIllustrationLesson::getSystemPrompt();

        $userPrompt = <<<PROMPT
Contexto pedagógico:
- Grado: {$gradeName}
- Asignatura: {$subjectName}
- Lección: {$this->lessonTitle}
- Sección: {$sectionTitle}

Contenido de la sección a ilustrar:

{$sectionBody}

Genera el SVG ilustrativo para esta sección siguiendo estrictamente el sistema de diseño, accesibilidad y contrato de salida especificados.
PROMPT;

        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 4096, 'temperature' => 0.4, 'timeout' => 300],
                2000,
                null,
                [
                    ['model' => config('openrouter.model_illustration_primary'),   'label' => 'Ilustración Claude Sonnet 4 primario'],
                    ['model' => config('openrouter.model_illustration_fallback1'), 'label' => 'Ilustración Nemotron 3 Nano fallback 1'],
                    ['model' => config('openrouter.model_illustration_fallback2'), 'label' => 'Ilustración Mistral Large fallback 2'],
                ]
            );

            if (!$result['success'] || empty($result['content'])) {
                $this->notification()->error(
                    'Error al generar ilustración',
                    $result['error'] ?? 'No se pudo generar la ilustración SVG.'
                );
                return;
            }

            $rawSvg = $result['content'] ?? '';

            // Limpiar wrappers markdown
            $rawSvg = preg_replace('/^```(?:svg|html)?\s*\n?/i', '', $rawSvg);
            $rawSvg = preg_replace('/\n?```\s*$/s', '', $rawSvg);
            $rawSvg = trim($rawSvg);

            // Extraer bloque <svg>...</svg>
            if (!preg_match('/<svg[\s>].*?<\/svg>/is', $rawSvg, $svgMatch)) {
                $this->notification()->error(
                    'Error al generar ilustración',
                    'La respuesta no contiene un SVG válido.'
                );
                return;
            }
            $svgCode = $svgMatch[0];

            // Envolver el SVG en HTML embed para renderizado
            $svgHtml = app(\App\Services\NapkinAiService::class)->buildEmbedHtml(
                $svgCode,
                null,
                'Ilustración: ' . $sectionTitle
            );

            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'         => 'temp_' . uniqid(),
                'type'       => 'IMAGE',
                'title'      => 'Ilustración: ' . $sectionTitle,
                'body'       => $svgHtml,
                'is_visible' => true,
                'media'      => null,
            ];

            $this->notification()->success(
                'Ilustración generada',
                "Se generó una ilustración SVG educativa para \"{$sectionTitle}\"."
            );

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('generateSectionIllustration: error', [
                'section' => $sectionTitle,
                'error'   => $e->getMessage(),
            ]);

            $this->notification()->error(
                'Error al generar ilustración',
                'Ocurrió un error inesperado. Intenta de nuevo.'
            );
        }
    }

    // ─── Slide editor: Generar diagrama Mermaid para diapositiva actual ──

    /**
     * Genera c├│digo HTML con diagrama Mermaid para la diapositiva actual
     * usando el contenido de la secci├│n y el contexto de la lecci├│n.
     * Reutiliza la l├│gica existente de generateEmbedCard() pero inyecta
     * el resultado directamente en el contenido de la diapositiva.
     */
    public function generateSlideDiagram(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex])) {
            return;
        }
        if (count($this->wizardSections[$sectionIndex]['contents'] ?? []) >= 2) {
            $this->notification()->warning('Límite alcanzado', 'Máximo 2 bloques de contenido por diapositiva.');
            return;
        }

        $sectionData = $this->wizardSections[$sectionIndex];
        $sectionTitle = $sectionData['title'] ?? 'Secci├│n';
        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        $gradeName    = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName  = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName  = $pevaluacion?->seccion?->name ?? '—';

        $activityContext = collect([
            'T├¡tulo de la lecci├│n'   => $this->lessonTitle,
            'Tema generador'         => $activity->topic,
            'Tejido tem├ítico'        => $activity->thematic,
            'Actividad evaluativa'   => $activity->description,
            'Ense├▒anza'              => $activity->teaching,
            'Aprendizaje esperado'   => $activity->learning,
        ])->filter()->map(fn($v, $k) => "├ó—ó {$k}: {$v}")->implode("\n");

        $sectionContents = collect($sectionData['contents'] ?? [])
            ->map(fn($c) => ($c['title'] ?? '') . ($c['title'] ? "\n" : '') . ($c['body'] ?? ''))
            ->filter()
            ->implode("\n\n");

        $sectionContentPreview = !empty($sectionContents)
            ? $sectionContents
            : '(La secci├│n no tiene contenido a├║n.)';

        $systemPrompt = <<<'PROMPT'
Eres un Staff Engineer frontend especializado en diagramas Mermaid.js y Tailwind CSS.
Genera c├│digo HTML aut├│nomo para un diagrama Mermaid enmarcado en un card simple.

Reglas:
1. Solo HTML plano. NO incluyas scripts CDN, etiquetas &lt;script&gt;, &lt;link&gt;, &lt;meta&gt;, &lt;html&gt;, &lt;head&gt; ni &lt;body&gt;. Sin Vue, React, Alpine.js.
2. El diagrama Mermaid dentro de <div class="mermaid">...</div>
3. Card contenedor: <div class="w-full max-w-full bg-white rounded-xl shadow-sm border border-gray-200"><div class="p-3 sm:p-4 overflow-x-auto"><div class="mermaid">...
4. w-full max-w-full, mobile-first (p-3, sm:p-4)
5. Elige el tipo de diagrama seg├║n el contenido (graph, sequenceDiagram, mindmap, etc.)
6. Sin scripts externos. Sin wrappers markdown. Solo HTML puro.
7. El diagrama debe reflejar fielmente el contenido pedag├│gico.

CONDICI├ôN NO NEGOCIABLE ŌĆö RESPONSIVE DESIGN:
- El diagrama debe visualizarse correctamente en pantallas anchas (1920px+) y estrechas (320px+).
- El contenedor debe usar max-w-full y overflow-x-auto para evitar desbordamiento.
- Mermaid renderiza el SVG con width:100%; height:auto; cuando el contenedor lo permite.
- Asegura que el overflow-x-auto del wrapping permita scroll horizontal sin truncar nodos.
- En pantallas grandes el diagrama debe ocupar el ancho disponible sin estirarse desproporcionadamente.
- NO uses max-w-2xl ni max-w-4xl que limiten el ancho del diagrama en monitores grandes.
- Prioriza graph TD (top-down, flujo vertical arriba→abajo) con hasta 3 niveles de profundidad.
- El texto dentro de los nodos debe distribuirse en múltiples líneas (con <br/> o arreglo multi-línea [linea1, linea2, ...]) para evitar truncamiento horizontal.
8. **NO incluyas explicaciones, introducciones, descripciones ni texto fuera del código HTML o Mermaid. Responde ÚNICAMENTE el código. Si es Mermaid, responde solo el código Mermaid. Si es HTML, responde solo el HTML desde `<div class="w-full...">`.**
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto educativo
**Curso:** {$gradeName} · {$subjectName} · Sec. {$sectionName}

### Actividad pedag├│gica
{$activityContext}

### Diapositiva destino
**Nombre:** {$sectionTitle}
**Contenido:**
{$sectionContentPreview}

Genera el código HTML del diagrama Mermaid para esta diapositiva.
Recuerda: el diagrama debe ser RESPONSIVE, visible correctamente desde
pantallas de celular hasta monitores anchos (condición no negociable).

Requisitos del diagrama:
1. Máximo 3 niveles de profundidad.
2. Texto multi-línea dentro de nodos (usa <br/> o arreglos [línea1, línea2]) para evitar truncamiento.
3. Flujo vertical TOP-DOWN (graph TD), prioriza alineación de arriba hacia abajo.

**IMPORTANTE:** Responde ÚNICAMENTE el código. Sin textos, explicaciones, introducciones ni despedidas. Solo el código.
PROMPT;

        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 4096, 'temperature' => 0.7, 'timeout' => 300],
                3500,
                null,
                [
                    ['model' => config('openrouter.model_diagram_primary'),   'label' => 'Diagrama Qwen 3.1 32B primario'],
                    ['model' => config('openrouter.model_diagram_fallback1'), 'label' => 'Diagrama Mistral Large fallback 1'],
                    ['model' => config('openrouter.model_diagram_fallback2'), 'label' => 'Diagrama Claude Sonnet 4 fallback 2'],
                ]
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingSection = null;
                $this->notification()->error('Error al generar diagrama', $result['error'] ?? 'Error desconocido');
                return;
            }

            $raw = trim($result['content'] ?? '');

            // ─── Estrategia 1: extraer código dentro de ``` ``` ───
            $code = $raw;
            if (preg_match('/```(?:html|mermaid)?\s*\n?(.*?)```/s', $raw, $m)) {
                $code = trim($m[1]);
            } else {
                // ─── Estrategia 2: sin fences — extraer código Mermaid desde su keyword ───
                $mermaidKeywords = 'flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline';
                if (preg_match('/\b(' . $mermaidKeywords . ')\s+(LR|TD|BT|RL)?/s', $raw, $kwMatch, PREG_OFFSET_CAPTURE)) {
                    $startPos = $kwMatch[0][1];
                    // Código desde el keyword hasta el final
                    $rawCode = substr($raw, $startPos);
                    // Si hay ``` al final, limpiarlo
                    $rawCode = preg_replace('/```\s*$/s', '', $rawCode);
                    $code = trim($rawCode);
                }
            }

            // Limpiar scripts y wrappers de documento completo
            $code = preg_replace('/<script\b[^>]*src=["\'][^"\']*cdn\.(?:tailwindcss|jsdelivr)[^"\']*["\'][^>]*><\/script>\s*/i', '', $code);
            $code = preg_replace('/<script\b[^>]*src=["\'][^"\']*mermaid[^"\']*["\'][^>]*><\/script>\s*/i', '', $code);
            $code = preg_replace('/<script>mermaid\.initialize\(.*?<\/script>\s*/is', '', $code);
            $code = preg_replace('/<\/?(?:html|head|body)[^>]*>\s*/i', '', $code);
            $code = preg_replace('/<meta[^>]*>\s*/i', '', $code);
            $code = preg_replace('/<link\b[^>]*href=["\'][^"\']*cdn\.(?:tailwindcss|jsdelivr)[^"\']*["\'][^>]*>\s*/i', '', $code);
            $code = trim($code);

            // ─── Limpiar trailing HTML tags (sobras de wrappers que la IA incluya) ───
            $code = preg_replace('/\s*<(\/)?div[^>]*>\s*$/s', '', trim($code));
            $code = preg_replace('/\s*<(\/)?div[^>]*>\s*$/s', '', trim($code));
            $code = trim($code);

            if (empty($code)) {
                $this->generationError = 'La IA no generó código de diagrama.';
                $this->generatingSection = null;
                $this->notification()->error('Respuesta vacía', 'La IA no generó ningún código de diagrama.');
                return;
            }

            // ─── Si es Mermaid puro (sin HTML), envolverlo ───
            $isMermaidRaw = !str_contains($code, '<div') && !str_contains($code, '<span')
                && preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', $code);

            if ($isMermaidRaw) {
                $code = '<div class="w-full bg-white rounded-xl shadow-sm border border-gray-200">'
                    . '<div class="p-3 sm:p-4 overflow-x-auto">'
                    . '<div class="mermaid">' . "\n"
                    . $code . "\n"
                    . '</div>'
                    . '</div>'
                    . '</div>';
            }

            // Agregar como bloque de contenido HTML
            $this->wizardSections[$sectionIndex]['contents'][] = [
                'id'         => 'temp_' . uniqid(),
                'type'       => 'HTML',
                'title'      => 'Diagrama: ' . $sectionTitle,
                'body'       => $code,
                'is_visible' => true,
                'media'      => null,
            ];

            $this->notification()->success(
                'Diagrama generado',
                "El diagrama Mermaid para \"{$sectionTitle}\" se generó correctamente."
            );
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error inesperado', $e->getMessage());
        } finally {
            $this->generatingSection = null;
        }
    }

    /**
     * Etiqueta el contenido de la diapositiva actual con HTML semántico
     * usando IA con prompt Staff Engineer. Analiza el body plano y lo
     * envuelve en etiquetas HTML5 apropiadas según su estructura.
     */
    public function generateSlideHtmlTags(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex]['contents'][0])) {
            $this->notification()->warning('Sin contenido', 'Esta diapositiva no tiene contenido para etiquetar. Genera texto primero.');
            return;
        }
        if (empty(trim(strip_tags($this->wizardSections[$sectionIndex]['contents'][0]['body'] ?? '')))) {
            $this->notification()->warning('Contenido vacío', 'El body de esta diapositiva está vacío.');
            return;
        }

        $this->generatingSection = $sectionIndex;
        $this->generationError = null;

        $originalBody = $this->wizardSections[$sectionIndex]['contents'][0]['body'];
        $sectionTitle = $this->wizardSections[$sectionIndex]['title'];

        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;
        $gradeName   = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';

        try {
            /** @var HtmlTaggingService $taggingService */
            $taggingService = app(HtmlTaggingService::class);

            $result = $taggingService->tag(
                $originalBody,
                $sectionTitle,
                $gradeName,
                $subjectName,
                fn(string $systemPrompt, string $userPrompt, array $overrides) => $this->askWithCompaction(
                    $systemPrompt,
                    $userPrompt,
                    $overrides
                ),
                activityContext: $activity ? [
                    'topic'       => $activity->topic ?? '',
                    'teaching'    => $activity->teaching ?? '',
                    'description' => $activity->description ?? '',
                ] : null,
            );

            if ($result['success']) {
                $this->wizardSections[$sectionIndex]['contents'][0]['body'] = $result['html'];
                $this->wizardSections[$sectionIndex]['contents'][0]['type'] = 'HTML';

                $this->notification()->success(
                    'HTML semántico generado',
                    'El contenido se etiquetó con HTML5 semántico correctamente.'
                );

                // Disparar evento para cambiar a pestaña preview y mostrar resultado
                $this->dispatch('show-preview');
            } else {
                $this->generationError = $result['error'];
                $this->notification()->error('Error de etiquetado', $result['error']);
            }
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error inesperado', $e->getMessage());
        } finally {
            $this->generatingSection = null;
        }
    }

    // ─── Wizard: Matemáticas / LaTeX ─────────────────────────────

    /**
     * Detecta expresiones matemáticas en el body de la diapositiva actual
     * y las convierte a LaTeX usando IA, para renderizado con KaTeX.
     */
    public function generateSlideMath(): void
    {
        $sectionIndex = $this->currentSlideIndex;
        if (!isset($this->wizardSections[$sectionIndex]['contents'][0])) {
            $this->notification()->warning('Sin contenido', 'Esta diapositiva no tiene contenido. Genera texto primero.');
            return;
        }
        $body = trim(strip_tags($this->wizardSections[$sectionIndex]['contents'][0]['body'] ?? ''));
        if (empty($body)) {
            $this->notification()->warning('Contenido vacío', 'El body de esta diapositiva está vacío.');
            return;
        }

        $this->generatingSection = $sectionIndex;
        $this->generationError = null;

        $systemPrompt = <<<'PROMPT'
Eres un asistente que procesa textos educativos: detecta expresiones matemáticas, las convierte a LaTeX y devuelve HTML semántico y estructurado.

INSTRUCCIONES ESTRICTAS:

1. **Detección total**: Analiza TODO el texto y detecta CADA expresión matemática (fórmulas, ecuaciones, símbolos, variables con exponentes/subíndices, operadores, fracciones, raíces, integrales, sumatorias, matrices, unidades de medida, probabilidades, funciones trigonométricas, etc.).

2. **Conversión a LaTeX**: Convierte TODAS las expresiones a LaTeX válido usando:
   - Expresiones inline: \(...\) (texto normal, dentro de un párrafo)
   - Expresiones en bloque: $$...$$ (fórmulas grandes, ecuaciones destacadas, integrales)

3. **Estructura HTML obligatoria**: Devuelve TODO el contenido como HTML válido con esta estructura exacta:
   ```
   <div id="math-block">
   <p>Primer párrafo con \(expresiones\) y texto normal.</p>
   <p>Segundo párrafo con más texto y $$expresión destacada$$.</p>
   </div>
   ```
   - `<div id="math-block">` envuelve todo el contenido (obligatorio).
   - Cada párrafo separado debe ir dentro de `<p>...</p>`.
   - Texto sin matemáticas: presérvalo EXACTAMENTE IGUAL dentro de los `<p>`.

4. **Prohibido**: NO agregues explicaciones, comentarios, introducciones, ni texto fuera del HTML solicitado. Responde SOLO con el HTML.

5. **Calidad LaTeX**: Asegúrate de que todo el LaTeX sea sintácticamente válido:
   - Fracciones: \frac{num}{den}
   - Raíces: \sqrt{expr}
   - Potencias: x^{n}
   - Subíndices: x_{n}
   - Operadores: \pm, \times, \cdot, \div, \sum, \int, \prod
   - Griegos: \pi, \alpha, \beta, \theta, \Delta, \mu
   - Funciones: \sin, \cos, \tan, \log, \ln, \lim
   - Paréntesis automáticos: \left( y \right)
   - Integrales: \int_{a}^{b}
   - Matrices: \begin{matrix}...\end{matrix}

EJEMPLOS:
- Texto: "La fórmula cuadrática es x = (-b ± √(b² - 4ac)) / 2a"
  HTML: <div id="math-block"><p>La fórmula cuadrática es \(x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}\)</p></div>

- Texto: "El área de un círculo es A = π r². La integral definida ∫₀¹ x² dx = 1/3."
  HTML: <div id="math-block"><p>El área de un círculo es \(A = \pi r^2\). La integral definida $$\int_{0}^{1} x^2 \, dx = \frac{1}{3}$$</p></div>

- Texto: "Si f(x) = 2x + 3 entonces f(5) = 13. La probabilidad es P(A) = 0.75."
  HTML: <div id="math-block"><p>Si \(f(x) = 2x + 3\) entonces \(f(5) = 13\). La probabilidad es \(P(A) = 0.75\).</p></div>

Responde SOLO con el HTML, sin texto adicional fuera de él.
PROMPT;

        $userPrompt = "Convierte TODAS las expresiones matemáticas del siguiente texto a LaTeX y devuelve el HTML completo dentro de `<div id=\"math-block\">`, respetando los párrafos con `<p>`:\n\n{$body}";

        $customChain = [
            ['model' => config('openrouter.model_math_primary'),   'label' => 'Math Qwen Coder primario'],
            ['model' => config('openrouter.model_math_fallback1'), 'label' => 'Math DeepSeek fallback 1'],
        ];

        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                ['max_tokens' => 8192, 'temperature' => 0.05],
                4000,
                null,
                $customChain
            );

            if ($result['success']) {
                $this->wizardSections[$sectionIndex]['contents'][0]['body']
                    = app(\App\Services\Lms\LmsHtmlSanitizerService::class)->sanitize($result['content']);
                $this->dispatch('math-updated');
                $this->notification()->success(
                    title: 'Matemáticas procesadas',
                    description: 'Las expresiones matemáticas se han convertido a LaTeX exitosamente.'
                );
            } else {
                $this->generationError = $result['error'] ?? 'Error al procesar las expresiones matemáticas.';
                $this->notification()->error(title: 'Error', description: $this->generationError);
            }
        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->notification()->error('Error inesperado', $e->getMessage());
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
        $this->debugRawContent = null;

        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        // ─── Contexto de la actividad ───────────────────────────
        $gradeName   = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName = $pevaluacion?->seccion?->name ?? '—';
        $lessonTitle = $this->lessonTitle ?: $activity->topic ?? '';
        $lessonDescription = $this->lessonDescription;

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

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado, con la calidad narrativa de un best seller. Vocabulario preciso, sintaxis cuidada, tono pedagógico pero elegante. Cada sección debe redactarse con el rigor y la elegancia de un libro de texto de alta calidad editorial.

Debes generar EXACTAMENTE el formato que se indica. NO expliques lo que vas a hacer, NO describas las reglas, NO incluyas meta-comentarios. Solamente escribe el contenido directamente.

Estructura obligatoria: //INICIO, luego //DESARROLLO con MÍNIMO 5 bloques, luego //CIERRE (total mínimo 7 secciones).

Reglas estrictas:
- MÍNIMO 5 bloques en //DESARROLLO (más si el contenido lo requiere)
- Cada bloque de DESARROLLO separado por una línea en blanco
- Primera línea de cada bloque = título (máx 10 palabras), lenguaje acorde al grado
- Cada bloque de DESARROLLO: mínimo 150 palabras (3-5 párrafos)
- SIN meta-comentarios, explicaciones ni introducciones antes del formato
- Alineado con los referentes normativos y el contexto de la actividad
- NO uses temas genéricos — usa EXACTAMENTE el contexto proporcionado

--- EJEMPLO 1 (Matemáticas - 5to grado) ---

//INICIO
Las fracciones en la vida cotidiana
En nuestra vida diaria nos encontramos constantemente con situaciones que requieren el uso de fracciones, aunque a veces no nos demos cuenta. Cuando compartimos una pizza entre amigos, medimos ingredientes para una receta o calculamos descuentos en una tienda, estamos aplicando conceptos fraccionarios de manera natural. En esta lección exploraremos qué son las fracciones, cómo se representan y cómo podemos operar con ellas para resolver problemas prácticos. Las fracciones son una herramienta matemática fundamental que nos permite expresar partes de un todo, y su dominio es esencial para avanzar en el estudio de las matemáticas y sus aplicaciones en la ciencia, la tecnología y la vida cotidiana.

//DESARROLLO
¿Qué es una fracción?
Una fracción representa una o varias partes iguales de un todo que ha sido dividido en partes iguales. Se compone de dos números separados por una línea horizontal: el numerador, que indica cuántas partes tomamos, y el denominador, que indica en cuántas partes iguales se divide el todo. Por ejemplo, si dividimos una torta en 8 partes iguales y tomamos 3, la fracción que representa esta situación es 3/8, donde 3 es el numerador y 8 es el denominador. Las fracciones nos permiten expresar cantidades que no son enteras y son fundamentales para la medición, la proporción y el cálculo en innumerables contextos.

Tipos de fracciones
Las fracciones se clasifican en tres tipos principales según la relación entre numerador y denominador. Las fracciones propias son aquellas donde el numerador es menor que el denominador, como 2/5 o 3/4, y representan cantidades menores que la unidad. Las fracciones impropias tienen el numerador mayor o igual que el denominador, como 7/4 o 5/3, y representan cantidades mayores o iguales que la unidad. Las fracciones mixtas combinan un número entero con una fracción propia, como 1 1/2, y son especialmente útiles en contextos de medición y cocina.

Fracciones equivalentes
Dos fracciones son equivalentes cuando representan la misma cantidad, aunque tengan diferentes numeradores y denominadores. Por ejemplo, 1/2, 2/4, 3/6 y 4/8 son todas fracciones equivalentes porque representan la mitad de un todo. Para obtener una fracción equivalente, multiplicamos o dividimos tanto el numerador como el denominador por el mismo número. Este concepto es fundamental para comparar fracciones, realizar operaciones aritméticas y simplificar resultados.

Suma y resta de fracciones con igual denominador
Cuando dos fracciones tienen el mismo denominador, sumarlas o restarlas es muy sencillo: simplemente sumamos o restamos los numeradores y mantenemos el mismo denominador. Por ejemplo, 2/7 + 3/7 = 5/7, y 5/8 - 2/8 = 3/8. Es importante recordar que el resultado debe simplificarse cuando sea posible. Este proceso es similar a contar partes del mismo tipo: si tenemos 2 séptimos y agregamos 3 séptimos, obtenemos 5 séptimos.

Multiplicación de fracciones
Para multiplicar fracciones, multiplicamos los numeradores entre sí y los denominadores entre sí, obteniendo una nueva fracción. Por ejemplo, 2/3 × 4/5 = 8/15. A diferencia de la suma y la resta, no es necesario que las fracciones tengan el mismo denominador, lo que hace que la multiplicación sea un proceso más directo. La multiplicación de fracciones tiene aplicaciones prácticas como calcular la fracción de una fracción, determinar proporciones en recetas de cocina y resolver problemas de áreas en geometría.

//CIERRE
Resumen y aplicación de las fracciones
A lo largo de esta lección hemos aprendido que las fracciones son una herramienta matemática esencial para representar partes de un todo. Hemos explorado los diferentes tipos de fracciones —propias, impropias y mixtas— y comprendido cómo identificarlas y utilizarlas. Estudiamos el concepto de fracciones equivalentes y aprendimos a sumar, restar y multiplicar fracciones con confianza. Estos conocimientos no solo son fundamentales para avanzar en matemáticas, sino que también tienen aplicaciones directas en situaciones cotidianas como cocinar, medir, compartir y calcular descuentos. La práctica constante nos ayudará a dominar estos conceptos y a reconocer las fracciones como una parte natural de nuestro entorno numérico.

--- EJEMPLO 2 (Historia - 3er año) ---

//INICIO
La independencia de Venezuela y sus protagonistas
El proceso de independencia de Venezuela representa uno de los capítulos más trascendentales de nuestra historia nacional, un período de profundas transformaciones políticas, sociales y económicas que marcaron el nacimiento de nuestra república. En esta lección recorreremos los acontecimientos fundamentales que llevaron a la emancipación del dominio español, conoceremos a los principales líderes que protagonizaron esta gesta heroica y comprenderemos las ideas que impulsaron el movimiento independentista. La independencia no fue un hecho aislado sino un proceso complejo que involucró múltiples factores internos y externos, desde las reformas borbónicas hasta la invasión napoleónica a España, pasando por las desigualdades sociales del sistema colonial.

//DESARROLLO
Causas de la independencia
Las causas de la independencia venezolana fueron múltiples y estuvieron interconectadas. Entre las causas externas destacan la Ilustración europea, cuyas ideas de libertad, igualdad y soberanía popular inspiraron a los criollos ilustrados, y la invasión de Napoleón a España en 1808, que generó un vacío de poder en las colonias americanas. Las causas internas incluyen el descontento de los criollos por la exclusión política y las restricciones económicas impuestas por la Corona española, así como las desigualdades sociales del sistema de castas colonial. La conjunción de estos factores creó el ambiente propicio para el inicio del proceso independentista.

Primera República
La Primera República se estableció el 5 de julio de 1811 con la firma del Acta de Independencia, convirtiendo a Venezuela en la primera colonia hispanoamericana en declarar su independencia. Este período estuvo marcado por intensos debates entre independentistas y realistas, la redacción de la primera constitución del país y los primeros enfrentamientos militares. Sin embargo, la República cayó en 1812 debido a diversos factores: el terremoto de Caracas que afectó principalmente las zonas republicanas, la falta de recursos militares, las divisiones internas entre los líderes patriotas y la hábil campaña del jefe realista Domingo de Monteverde.

La Campaña Admirable
La Campaña Admirable, liderada por Simón Bolívar en 1813, fue una de las operaciones militares más brillantes de la guerra de independencia. Bolívar partió desde Cúcuta con un ejército relativamente pequeño y en apenas seis meses logró liberar gran parte del territorio venezolano, obteniendo victorias decisivas en batallas como Niquitao, Barinas y Taguanes. El 6 de agosto de 1813, Bolívar entró triunfante en Caracas, donde recibió el título de Libertador. Esta campaña demostró el genio militar de Bolívar y su capacidad para movilizar y motivar a las tropas.

La Guerra a Muerte
El decreto de Guerra a Muerte, promulgado por Bolívar el 15 de junio de 1813 en Trujillo, fue una estrategia militar y política que radicalizó el conflicto. Este decreto establecía que todo español que no apoyara activamente la causa patriota sería ejecutado, mientras que los americanos serían perdonados incluso si apoyaban a los realistas. Aunque permitió a Bolívar ganar apoyo popular y debilitar la resistencia realista, también generó un ciclo de violencia que endureció el conflicto y tuvo consecuencias humanitarias devastadoras en ambos bandos.

La Batalla de Carabobo
La Batalla de Carabobo, librada el 24 de junio de 1821, fue el enfrentamiento militar más importante de la guerra de independencia venezolana. El ejército patriota, comandado por Simón Bolívar y con la participación crucial del General José Antonio Páez y sus lanceros llaneros, derrotó decisivamente a las fuerzas realistas del General Miguel de la Torre. La batalla aseguró la independencia de Venezuela y abrió el camino para la liberación de Ecuador, Perú y Bolivia. La victoria en Carabobo representó la culminación de años de lucha y sacrificio.

El Congreso de Angostura
El Congreso de Angostura, instalado por Bolívar el 15 de febrero de 1819, fue un evento político fundamental en el proceso independentista. En su discurso inaugural, Bolívar presentó su visión para la organización política de las nuevas repúblicas, incluyendo la creación de la Gran Colombia y propuestas constitucionales que reflejaban sus ideas sobre el poder ejecutivo fuerte, la división de poderes y la educación popular. Este congreso sentó las bases institucionales para la creación de la República de Colombia, demostrando que la independencia no solo era un proyecto militar sino también político y constitucional.

//CIERRE
Legado de la independencia
El proceso de independencia de Venezuela nos legó no solo la libertad política sino también un conjunto de ideales y valores que siguen siendo relevantes hoy. La gesta independentista nos enseñó que la unidad y la determinación pueden superar obstáculos aparentemente insuperables. Los líderes de la independencia nos dejaron un ejemplo de compromiso con la libertad, la justicia y la soberanía nacional que continúa inspirando a las nuevas generaciones. Comprender este proceso histórico es esencial para valorar nuestra identidad nacional y para enfrentar los desafíos del presente con la misma determinación que nuestros próceres.

--- AHORA GENERA LA LECCIÓN PARA EL CONTEXTO PROPORCIONADO ---
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Lección:** {$lessonTitle}
**Descripción:** {$lessonDescription}
**Curso:** {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Actividad pedagógica:**
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

Genera estructura completa con //INICIO, mínimo 5 bloques en //DESARROLLO y //CIERRE. Sin explicaciones ni meta-comentarios.
PROMPT;

        // ─── Validación de contenido mejorada ───────────────
        $contentValidator = function (string $content): bool {
            // 1. Debe contener los tres marcadores estructurales
            $hasInicio    = preg_match('/^\/\/INICIO\s*$/m', $content) === 1;
            $hasDesarrollo = preg_match('/^\/\/DESARROLLO\s*$/m', $content) === 1;
            $hasCierre    = preg_match('/^\/\/CIERRE\s*$/m', $content) === 1;

            if (!($hasInicio && $hasDesarrollo && $hasCierre)) {
                return false;
            }

            // Extraer contenido entre //DESARROLLO y //CIERRE
            $devMatch = null;
            preg_match('/^\/\/DESARROLLO\s*$(.*?)^\/\/CIERRE\s*$/ms', $content, $devMatch);

            if (empty($devMatch[1])) {
                return false;
            }

            $blocks = preg_split('/\n\s*\n/', trim($devMatch[1]));
            $validBlocks = array_filter($blocks, fn(string $b): bool => !empty(trim($b)));

            // Mínimo 5 bloques en DESARROLLO
            if (count($validBlocks) < 5) {
                return false;
            }

            // 2. Verificar idioma español (heurística combinada)
            // Buscar palabras funcionales españolas que aparecen en casi todo texto en español.
            // Esto es más fiable que el ratio de acentos, porque el español formal puede tener
            // pocos acentos (p.ej. "Las fracciones en la vida cotidiana" = 0 acentos).
            $spanishWords = [
                ' de ', ' la ', ' en ', ' el ', ' que ', ' con ',
                ' los ', ' las ', ' del ', ' por ', ' para ', ' se ',
                ' su ', ' una ', ' más ', ' entre ', ' esta ', ' son ',
                ' cada ', ' como ', ' sobre ', ' esta ', ' mismo ',
            ];
            $foundWords = 0;
            foreach ($spanishWords as $word) {
                if (mb_stripos($content, $word) !== false) {
                    $foundWords++;
                }
            }
            // Señal secundaria: ratio de caracteres acentuados
            $espanolChars = mb_strlen(preg_replace('/[^áéíóúüñÁÉÍÓÚÜÑ]/u', '', $content));
            $totalAlpha = mb_strlen(preg_replace('/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ\s]/u', '', $content));
            $accentRatio = $totalAlpha > 0 ? $espanolChars / $totalAlpha : 0;
            // Se rechaza solo si NO encuentra palabras funcionales Y el ratio de acentos es mínimo
            if ($foundWords < 2 && $accentRatio < 0.01) {
                Log::warning('generateStep2Sections: content rejected — not Spanish', [
                    'function_words' => $foundWords,
                    'accent_ratio' => round($accentRatio, 4),
                ]);
                return false;
            }

            // 3. Detectar contenido genérico
            $genericPatterns = [
                '/superhéroe/i', '/identidad secreta/i',
                '/viaje imaginario/i', '/tierra mágica/i',
                '/mundo fantástico/i', '/poderes especiales/i',
            ];
            foreach ($genericPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    Log::warning('generateStep2Sections: content rejected — generic theme detected', [
                        'pattern' => $pattern,
                    ]);
                    return false;
                }
            }

            // 4. Verificar títulos coherentes (sin placeholders)
            $titlePlaceholders = ['/^título$/im', '/^contenido$/im', '/^título del bloque$/im'];
            foreach ($validBlocks as $block) {
                $firstLine = trim(explode("\n", trim($block))[0]);
                foreach ($titlePlaceholders as $placeholder) {
                    if (preg_match($placeholder, $firstLine)) {
                        Log::warning('generateStep2Sections: content rejected — placeholder title', [
                            'title' => $firstLine,
                        ]);
                        return false;
                    }
                }
            }

            // 5. Verificar longitud mínima por bloque (150 palabras)
            foreach ($validBlocks as $block) {
                $wordCount = preg_match_all('/\p{L}+/u', trim($block));
                if ($wordCount < 150) {
                    Log::warning('generateStep2Sections: content rejected — block too short', [
                        'words' => $wordCount,
                        'preview' => mb_substr(trim($block), 0, 100),
                    ]);
                    return false;
                }
            }

            return true;
        };

        // ─── Llamar al servicio con cadena dedicada ──────────
        try {
            $result = $this->askWithCompaction(
                systemPrompt: $systemPrompt,
                userPrompt: $userPrompt,
                overrides: ['max_tokens' => 4096, 'timeout' => 180],
                tokenBudget: 3500,
                contentValidator: $contentValidator,
                customChain: [
                    ['model' => config('openrouter.model_step2_primary'),   'label' => 'Sonnet 4 primario'],
                    ['model' => config('openrouter.model_step2_fallback1'), 'label' => 'Qwen 32B fallback 1'],
                    ['model' => config('openrouter.model_step2_fallback2'), 'label' => 'Mistral Large fallback 2'],
                    ['model' => config('openrouter.model_step2_fallback3'), 'label' => 'Ling 2.6 Flash fallback 3'],
                ],
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingStep2 = false;
                $this->notification()->error('Error al generar', $result['error']);
                return;
            }

            $content = $this->sanitizeText($result['content'] ?? '');

            // Debug: guardar respuesta cruda para diagnóstico
            $this->debugRawContent = $result['content'] ?? '';

            if (empty($content)) {
                $this->generationError = 'La IA no generó contenido.';
                $this->generatingStep2 = false;
                return;
            }

            $this->wizardSections = [];

            // Dividir por marcadores de sección
            $parts = preg_split('/^\/\/(INICIO|DESARROLLO|CIERRE)\s*$/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            // Log detallado de las partes crudas del split
            $partsDebug = [];
            $currentPartIdx = 0;
            $currentMarkerForDebug = null;
            foreach ($parts as $p) {
                $p2 = trim($p);
                if (in_array($p2, ['INICIO', 'DESARROLLO', 'CIERRE'], true)) {
                    $currentMarkerForDebug = $p2;
                    $partsDebug[] = ['marker' => $p2, 'len' => 0, 'preview' => ''];
                } else {
                    $partsDebug[] = [
                        'marker'  => $currentMarkerForDebug ?? '?',
                        'len'     => mb_strlen($p),
                        'preview' => mb_substr(preg_replace('/\s+/', ' ', $p), 0, 200),
                    ];
                }
            }
            Log::debug('generateStep2Sections: parts from preg_split', [
                'part_count' => count($parts),
                'parts'      => $partsDebug,
            ]);

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

            // Debug: registrar el resultado de la generación
            Log::info('generateStep2Sections: resultado', [
                'section_count'      => $count,
                'titles'             => collect($this->wizardSections)->pluck('title')->toArray(),
                'content_length'     => mb_strlen($content),
                'used_model'         => $result['model'] ?? '—',
                'was_compacted'      => $result['compacted'] ?? false,
                'has_markers'        => [
                    'inicio'     => preg_match('/^\/\/INICIO\s*$/m', $content) === 1,
                    'desarrollo' => preg_match('/^\/\/DESARROLLO\s*$/m', $content) === 1,
                    'cierre'     => preg_match('/^\/\/CIERRE\s*$/m', $content) === 1,
                ],
            ]);

            // Mostrar overlay con resultado
            $this->generationType = 'step2';
            $this->showGenerationResult = true;

            $this->notification()->success(
                'Secciones generadas',
                "Se crearon {$count} secciones con contenido pedagógico."
            );

            // Si hay menos de 7 secciones, registrar para diagnóstico
            if ($count < 7) {
                $titles = collect($this->wizardSections)->pluck('title')->map(fn($t) => "• {$t}")->implode("\n");
                Log::warning('generateStep2Sections: pocas secciones', [
                    'count'            => $count,
                    'titles'           => collect($this->wizardSections)->pluck('title')->toArray(),
                    'content_total_len' => mb_strlen($content),
                    'full_content'     => $content,
                    'has_inicio_block' => collect($this->wizardSections)->first(fn($s) => ($s['title'] ?? '') !== '') !== null,
                    'desarrollo_sections' => collect($this->wizardSections)
                        ->filter(fn($s) => str_contains($s['title'] ?? '', 'Desarrollo') || preg_match('/^Desarrollo\s+\d+$/', $s['title'] ?? ''))
                        ->values()
                        ->toArray(),
                ]);
            }
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

            if ($marker === 'DESARROLLO') {
            // ─── Agrupar fragmentos del split en bloques (título + cuerpo) ──
            // Los modelos pueden generar formatos diferentes:
            //   Formato A) Título\nCuerpo\n\nTítulo2\nCuerpo2...
            //   Formato B) Título\n\nCuerpo\n\nTítulo2\n\nCuerpo2...
            //   Formato C) Título\n\nPárrafo1\n\nPárrafo2\n\nTítulo2...
            // En todos los casos el split por línea en blanco fragmenta bloques.
            // Estrategia: líneas cortas (≤ 80 chars) = título → nuevo bloque;
            //             líneas largas (> 80 chars) = cuerpo → se acumulan al bloque anterior.
            $blocks = preg_split('/\n\s*\n/', $text);
            $tempBlocks = []; // [['title' => string, 'body_frags' => [string, ...]], ...]
            $thresholdTitle = 80; // máx. chars para considerar "título"
            $blockIndex = 0;

            foreach ($blocks as $block) {
                $block = trim($block);
                if (empty($block)) {
                    continue;
                }
                $bLines = explode("\n", $block);
                $firstLine = trim(array_shift($bLines));
                $restOfBlock = trim(implode("\n", $bLines));

                // Determinar si es continuación de cuerpo (línea larga sin body propio)
                $isContinuation = !empty($tempBlocks)
                    && mb_strlen($firstLine) > $thresholdTitle
                    && empty($restOfBlock);

                if ($isContinuation) {
                    // Acumular al bloque anterior como un nuevo párrafo de cuerpo
                    $lastIdx = count($tempBlocks) - 1;
                    $tempBlocks[$lastIdx]['body_frags'][] = $firstLine;
                } else {
                    // Nuevo bloque: primera línea es título, el resto es cuerpo
                    $bTitle = $firstLine;
                    $bBodyLines = $restOfBlock
                        ? [$restOfBlock]
                        : [];

                    if (empty($bTitle)) {
                        $bTitle = 'Desarrollo ' . ($blockIndex + 1);
                    }

                    $tempBlocks[] = [
                        'title'      => $bTitle,
                        'body_frags' => $bBodyLines,
                    ];
                    $blockIndex++;
                }
            }

            $blockCount = 0;
            $blockIndex = 0;
            foreach ($tempBlocks as $tb) {
                $bTitle = $tb['title'];
                $bBody = trim(implode("\n\n", $tb['body_frags']));

                if (empty($bTitle)) {
                    $bTitle = 'Desarrollo ' . ($blockIndex + 1);
                }
                if (empty($bBody)) {
                    continue;
                }

                $this->wizardSections[] = [
                    'id'         => 'temp_' . uniqid(),
                    'title'      => $this->sanitizeText($bTitle),
                    'is_visible' => true,
                    'contents'   => [[
                        'id'         => 'temp_' . uniqid(),
                        'type'       => 'TEXT',
                        'title'      => null,
                        'body'       => $this->sanitizeText($bBody),
                        'is_visible' => true,
                        'media'      => null,
                    ]],
                ];
                $blockIndex++;
                $blockCount++;
            }

            Log::debug('parseSectionBlock: DESARROLLO result', [
                'raw_fragments'    => count($blocks),
                'assembled_blocks' => $blockCount,
                'titles'           => collect($tempBlocks)->pluck('title')->toArray(),
            ]);
        } else {
            // INICIO o CIERRE — una sola sección
            $sectionTitle = $marker === 'INICIO' ? ($title ?: 'Inicio') : ($title ?: 'Cierre');

            $this->wizardSections[] = [
                'id'         => 'temp_' . uniqid(),
                'title'      => $this->sanitizeText($sectionTitle),
                'is_visible' => true,
                'contents'   => [[
                    'id'         => 'temp_' . uniqid(),
                    'type'       => 'TEXT',
                    'title'      => null,
                    'body'       => $this->sanitizeText($body ?: $text),
                    'is_visible' => true,
                    'media'      => null,
                ]],
            ];
        }
    }

    // ─── Wizard: Paso 3 — Recursos y Enlaces ───────────────────

    public function addWizardResource(): void
    {
        $isUpdate = $this->editingResourceIndex !== null;

        try {
            $rules = [
                'resourceName' => 'required|string|max:255',
            ];
            if ($isUpdate) {
                // En edición, el archivo es opcional
                $rules['resourceFile'] = 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,mp4,webm,mp3,wav,ogg';
            } else {
                $rules['resourceFile'] = 'required|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,mp4,webm,mp3,wav,ogg';
            }
            if (count($this->wizardSections) > 0) {
                $rules['resourceSectionId'] = 'required';
            }
            $this->validate($rules);
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $error) {
                $this->notification()->error('Campo requerido', $error);
            }
            throw $e;
        }

        // ─── Si hay un archivo nuevo, procesarlo ────────────────
        $mediaData = null;
        if ($this->resourceFile) {
            try {
                $media = app(LmsMediaUploadService::class)->upload($this->resourceFile, auth()->id());
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->notification()->error('Error al subir', $e->getMessage());
                return;
            }

            // ─── Verificar límite total de 10 MB por lección ────
            $newFileSize = $this->resourceFile->getSize();
            $totalBytes = $newFileSize;
            foreach ($this->wizardResources as $idx => $res) {
                // En actualización, excluir el recurso actual si ya tiene tamaño
                if ($isUpdate && $idx === $this->editingResourceIndex) continue;
                $totalBytes += $res['media']['size_bytes'] ?? 0;
            }
            if ($totalBytes > 10485760) { // 10 MB en bytes
                $this->notification()->error(
                    'Límite excedido',
                    'El total de recursos de la lección no puede superar los 10 MB. Este archivo excede el límite disponible.'
                );
                return;
            }

            $mediaData = [
                'id'              => $media->id,
                'original_name'   => $media->original_name,
                'mime_type'       => $media->mime_type,
                'size_bytes'      => $media->size_bytes,
                'size_for_humans' => $media->size_for_humans,
                'public_url'      => $media->public_url,
            ];
        }

        if ($isUpdate) {
            // ─── Actualizar recurso existente ───────────────────
            $existing = $this->wizardResources[$this->editingResourceIndex];
            $existing['display_name'] = $this->resourceName;
            $existing['section_id']   = $this->resourceSectionId;
            if ($mediaData) {
                $existing['media_id']  = $mediaData['id'];
                $existing['media']     = $mediaData;
            }
            $this->wizardResources[$this->editingResourceIndex] = $existing;

            $this->editingResourceIndex = null;
            $this->reset('resourceFile', 'resourceName', 'resourceSectionId');

            $this->notification()->success(
                'Recurso actualizado',
                "“{$this->resourceName}” se actualizó correctamente."
            );
            $this->dispatch('file-preview-reset');
        } else {
            // ─── Agregar nuevo recurso ──────────────────────────
            $this->wizardResources[] = [
                'id'           => 'temp_' . uniqid(),
                'section_id'   => $this->resourceSectionId,
                'media_id'     => $mediaData['id'],
                'uploaded_by'  => auth()->id(),
                'display_name' => $this->resourceName,
                'description'  => '',
                'media'        => $mediaData,
            ];

            $addedName = $this->resourceName;
            $this->reset('resourceFile', 'resourceName', 'resourceSectionId');

            $this->notification()->success(
                'Recurso agregado',
                "“{$addedName}” se agregó correctamente."
            );
            $this->dispatch('file-preview-reset');
        }
    }

    public function editWizardResource(int $index): void
    {
        if (!isset($this->wizardResources[$index])) return;

        $res = $this->wizardResources[$index];
        $this->editingResourceIndex = $index;
        $this->resourceName = $res['display_name'] ?? '';
        $this->resourceSectionId = $res['section_id'] ?? null;
    }

    public function cancelEditResource(): void
    {
        $this->editingResourceIndex = null;
        $this->reset('resourceFile', 'resourceName', 'resourceSectionId');
        $this->dispatch('file-preview-reset');
    }

    public function removeWizardResource(int $index): void
    {
        unset($this->wizardResources[$index]);
        $this->wizardResources = array_values($this->wizardResources);

        // Ajustar o cancelar edición si el recurso eliminado afecta al índice de edición
        if ($this->editingResourceIndex !== null) {
            if ($this->editingResourceIndex === $index) {
                $this->cancelEditResource();
            } elseif ($this->editingResourceIndex > $index) {
                $this->editingResourceIndex--;
            }
        }
    }

    public function removeAllWizardResources(): void
    {
        $this->wizardResources = [];
        $this->wizardLinks = [];
        $this->wizardHtmlEmbeds = [];
        $this->editingResourceIndex = null;
        $this->editingEmbedIndex = null;
        $this->resourceName = '';
        $this->resourceFile = null;
        $this->resourceSectionId = null;
        $this->linkTitle = '';
        $this->linkUrl = '';
        $this->linkSectionId = null;
        $this->linkType = 'REFERENCE';
        $this->embedTitle = '';
        $this->embedHtml = '';
        $this->embedSectionId = null;
        $this->embedDiagramType = '';
        $this->embedPromptRefinement = '';
        $this->dispatch('file-preview-reset');
        $this->notification()->success(
            title: 'Todos los recursos eliminados',
            description: 'Material descargable, HTML embeds y enlaces se eliminarán al guardar la lección.',
        );
    }

    public function addWizardLink(): void
    {
        try {
            $rules = [
                'linkTitle' => 'required|string|max:255',
                'linkUrl'   => 'required|url|max:1000',
            ];
            if (count($this->wizardSections) > 0) {
                $rules['linkSectionId'] = 'required';
            }
            $this->validate($rules);
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $error) {
                $this->notification()->error('Campo requerido', $error);
            }
            throw $e;
        }

        $this->wizardLinks[] = [
            'id'         => 'temp_' . uniqid(),
            'section_id' => $this->linkSectionId,
            'title'      => $this->linkTitle,
            'url'        => $this->linkUrl,
            'link_type'  => $this->linkType,
            'sort_order' => count($this->wizardLinks) + 1,
        ];

        $addedTitle = $this->linkTitle;
        $this->reset('linkTitle', 'linkUrl', 'linkSectionId');
        $this->linkType = 'REFERENCE';

        $this->notification()->success(
            'Enlace agregado',
            "“{$addedTitle}” se agregó correctamente."
        );
    }

    public function removeWizardLink(int $index): void
    {
        unset($this->wizardLinks[$index]);
        $this->wizardLinks = array_values($this->wizardLinks);
    }

    // ─── Wizard: HTML Embeds ────────────────────────────────────

    public function addWizardHtmlEmbed(): void
    {
        try {
            $rules = [
                'embedHtml' => 'required|string|min:1',
            ];
            if (count($this->wizardSections) > 0) {
                $rules['embedSectionId'] = 'required';
            }
            $this->validate($rules);
        } catch (ValidationException $e) {
            foreach ($e->validator->errors()->all() as $error) {
                $this->notification()->error('Campo requerido', $error);
            }
            throw $e;
        }

        $data = [
            'id'               => 'temp_' . uniqid(),
            'section_id'       => $this->embedSectionId,
            'title'            => $this->embedTitle ?: null,
            'html_content'     => $this->embedHtml,
            'render_condition' => 'ALWAYS',
            'is_visible'       => true,
        ];

        $isUpdate = $this->editingEmbedIndex !== null;

        if ($isUpdate) {
            if (isset($this->wizardHtmlEmbeds[$this->editingEmbedIndex])) {
                $data['id'] = $this->wizardHtmlEmbeds[$this->editingEmbedIndex]['id'];
                $this->wizardHtmlEmbeds[$this->editingEmbedIndex] = $data;
            }
            $this->editingEmbedIndex = null;
        } else {
            $this->wizardHtmlEmbeds[] = $data;
        }

        $this->showEmbedPreview = false;
        $this->previewEmbedIndex = null;
        $this->reset('embedTitle', 'embedHtml', 'embedSectionId', 'embedDiagramType', 'embedPromptRefinement');

        $this->notification()->success(
            $isUpdate ? 'Embed actualizado' : 'Embed agregado',
            $isUpdate
                ? 'El código HTML se actualizó correctamente.'
                : 'El código HTML se agregó a la lección correctamente.'
        );
    }

    public function editWizardHtmlEmbed(int $index): void
    {
        if (!isset($this->wizardHtmlEmbeds[$index])) return;

        $embed = $this->wizardHtmlEmbeds[$index];
        $this->editingEmbedIndex = $index;
        $this->embedTitle = $embed['title'] ?? '';
        $this->embedHtml = $embed['html_content'] ?? '';
        $this->embedSectionId = $embed['section_id'] ?? null;
        $this->embedDiagramType = '';
        $this->embedPromptRefinement = '';
    }

    public function cancelEditEmbed(): void
    {
        $this->editingEmbedIndex = null;
        $this->reset('embedTitle', 'embedHtml', 'embedSectionId', 'embedDiagramType', 'embedPromptRefinement');
    }

    public function removeWizardHtmlEmbed(int $index): void
    {
        unset($this->wizardHtmlEmbeds[$index]);
        $this->wizardHtmlEmbeds = array_values($this->wizardHtmlEmbeds);
    }

    // ─── Wizard: Agregar recurso/enlace/embed desde el panel de sección (paso 2) ──

    public function addWizardResourceSection(int $sectionIndex): void
    {
        if (!isset($this->wizardSections[$sectionIndex])) return;
        $this->resourceSectionId = is_numeric($this->wizardSections[$sectionIndex]['id'])
            ? (int) $this->wizardSections[$sectionIndex]['id']
            : null;
        $this->addWizardResource();
        $this->resourcePanelSection = null;
    }

    public function addWizardLinkSection(int $sectionIndex): void
    {
        if (!isset($this->wizardSections[$sectionIndex])) return;
        $this->linkSectionId = is_numeric($this->wizardSections[$sectionIndex]['id'])
            ? (int) $this->wizardSections[$sectionIndex]['id']
            : null;
        $this->addWizardLink();
        $this->resourcePanelSection = null;
    }

    public function addWizardHtmlEmbedSection(int $sectionIndex): void
    {
        if (!isset($this->wizardSections[$sectionIndex])) return;
        $this->embedSectionId = is_numeric($this->wizardSections[$sectionIndex]['id'])
            ? (int) $this->wizardSections[$sectionIndex]['id']
            : null;
        $this->addWizardHtmlEmbed();
        $this->resourcePanelSection = null;
    }

    // ─── Wizard: Generar diagrama Mermaid con IA ───────────────

    /**
     * Genera código HTML con diagrama Mermaid usando el contenido
     * de la sección seleccionada (embedSectionId) y el contexto
     * completo de la lección.
     */
    public function generateEmbedCard(): void
    {
        if (!$this->embedSectionId) {
            $this->notification()->warning(
                'Selecciona una sección',
                'Debes seleccionar una sección destino para generar el diagrama.'
            );
            return;
        }

        // Buscar la sección destino
        $sectionIndex = null;
        $sectionData = null;
        foreach ($this->wizardSections as $i => $sec) {
            if ((string) $sec['id'] === (string) $this->embedSectionId) {
                $sectionIndex = $i;
                $sectionData = $sec;
                break;
            }
        }

        if (!$sectionData) {
            $this->notification()->error(
                'Sección no encontrada',
                'La sección seleccionada no existe en la lección.'
            );
            return;
        }

        $this->generatingEmbedCard = true;
        $this->generationError = null;

        $activity = $this->selectedActivity;
        $pevaluacion = $activity?->pevaluacion;

        $gradeName    = $pevaluacion?->pensum?->grado?->name ?? '—';
        $subjectName  = $pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName  = $pevaluacion?->seccion?->name ?? '—';
        $lapsoName    = $pevaluacion?->lapso?->name ?? '—';

        // ─── Contexto completo de la actividad ───────────────────
        $activityContext = collect([
            'Título de la lección'   => $this->lessonTitle,
            'Descripción'            => $this->lessonDescription,
            'Tema generador'         => $activity->topic,
            'Tejido temático'        => $activity->thematic,
            'Actividad evaluativa'   => $activity->description,
            'Enseñanza'              => $activity->teaching,
            'Aprendizaje esperado'   => $activity->learning,
            'Referentes teóricos'    => $activity->references,
            'ODS/Sistematización'    => $activity->observations,
        ])->filter()->map(fn($v, $k) => "• {$k}: {$v}")->implode("\n");

        // ─── Contenido de la sección destino ─────────────────────
        $sectionTitle = $sectionData['title'];
        $sectionContents = collect($sectionData['contents'] ?? [])
            ->map(fn($c) => ($c['title'] ?? '') . ($c['title'] ? "\n" : '') . ($c['body'] ?? ''))
            ->filter()
            ->implode("\n\n");

        $sectionContentPreview = !empty($sectionContents)
            ? $sectionContents
            : '(La sección no tiene contenido aún. Genera contenido representativo basado en el nombre de la sección y el contexto de la actividad.)';

        // ─── Indicadores de logro ────────────────────────────────
        $indicators = $activity?->achievements?->pluck('name')?->filter() ?? collect();
        $indicatorsText = $indicators->isNotEmpty()
            ? $indicators->map(fn($n) => "• {$n}")->implode("\n")
            : '—';

        // ─── Referentes normativos ───────────────────────────────
        $referentsText = $this->getReferentsContext(
            $pevaluacion?->pensum?->pestudio_id,
            $pevaluacion?->pensum
        );

        // ─── Grados y secciones disponibles (contexto curricular) ─
        $allSectionTitles = collect($this->wizardSections)
            ->pluck('title')
            ->implode(', ');

        // ══════════════════════════════════════════════════════════
        //  STAFF ENGINEER PROMPT
        // ══════════════════════════════════════════════════════════

        $diagramTypeConstraint = '';
        if (!empty($this->embedDiagramType)) {
            $typeLabels = [
                'graph'    => 'graph (flowchart)',
                'sequence' => 'sequenceDiagram', 'class' => 'classDiagram',
                'pie'      => 'pie', 'mindmap' => 'mindmap', 'gantt' => 'gantt',
                'er'       => 'erDiagram', 'state' => 'stateDiagram',
                'journey'  => 'journey', 'gitgraph' => 'gitgraph', 'timeline' => 'timeline',
            ];
            $label = $typeLabels[$this->embedDiagramType] ?? $this->embedDiagramType;
            $diagramTypeConstraint = "\n\n**Tipo solicitado:** `{$label}`. Usa EXCLUSIVAMENTE este tipo de diagrama.\n";
        }

        $systemPrompt = <<<PROMPT
Eres un Staff Engineer frontend especializado en diagramas Mermaid.js y Tailwind CSS 3.
Tu tarea es generar código HTML autónomo para un diagrama Mermaid enmarcado en un card simple, que represente visualmente el contenido pedagógico de una sección de lección escolar.

## Reglas técnicas estrictas:

1. **Solo HTML + Tailwind** — etiquetas HTML estándar + clases Tailwind CSS (vía CDN). Sin Vue, React, Alpine.js ni frameworks JS.
2. **Diagrama Mermaid.js** — el contenido central debe ser un diagrama Mermaid dentro de `<div class="mermaid">`.{$diagramTypeConstraint}Elige el tipo de diagrama que mejor represente el contenido:
   - `graph TD/LR` (flowchart) — procesos, secuencias, pasos, jerarquías
   - `sequenceDiagram` — líneas de tiempo, eventos ordenados
   - `classDiagram` — clasificaciones, taxonomías
   - `pie` — proporciones, distribución
   - `mindmap` — exploración conceptual, lluvia de ideas
   - `gantt` — cronogramas, planificación
3. **Card simple, sin título** — el diagrama va dentro de un contenedor card minimalista:
   ```html
   <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200">
     <div class="p-3 sm:p-4 overflow-x-auto">
       <div class="mermaid">
         ... código del diagrama ...
       </div>
     </div>
   </div>
   ```
4. **w-full obligatorio** — el card ocupa el 100% del ancho disponible del contenedor padre. Sin max-w ni mx-auto.
5. **MOBILE-FIRST** — padding base reducido (p-3), escala con sm:p-4. El contenedor tiene overflow-x-auto para scroll horizontal si el diagrama es ancho.
6. **Diagrama pedagógico** — el contenido del diagrama debe reflejar fielmente el contenido de la sección. Usa nodos con nombres descriptivos. Si la sección está vacía, genera contenido de muestra coherente con el contexto de la lección.
7. **Sin scripts externos** — no incluyas CDN de mermaid ni de tailwind. Solo el HTML del card con el div.mermaid. Los scripts se cargan globalmente en la página.
8. **Salida limpia** — NO incluyas ```html ni markdown. Responde SOLO HTML puro desde <div class="w-full...">. No generes solo el código mermaid suelto; el código mermaid SIEMPRE debe ir dentro de <div class="mermaid">...</div>, y este a su vez dentro del card contenedor.

9. **NO incluyas explicaciones, introducciones, descripciones ni texto fuera del código HTML. Responde ÚNICAMENTE el código HTML desde <div class="w-full...">.**
	
	**Ejemplo de salida correcta:**
<div class="w-full bg-white rounded-xl shadow-sm border border-gray-200">
  <div class="p-3 sm:p-4 overflow-x-auto">
    <div class="mermaid">
graph TD
  A[Inicio] --> B[Proceso]
  B --> C[Resultado]
    </div>
  </div>
</div>
PROMPT;

        $promptRefinementText = !empty($this->embedPromptRefinement)
            ? trim($this->embedPromptRefinement)
            : '(El docente no añadió instrucciones adicionales. Genera el diagrama según el contexto.)';

        $userPrompt = <<<PROMPT
### Datos del contexto educativo

**Curso:** {$gradeName} · {$subjectName}
**Sección escolar:** {$sectionName}
**Lapso:** {$lapsoName}

### Datos de la lección

{$activityContext}

### Indicadores de logro asociados

{$indicatorsText}

### Estructura completa de la lección

Secciones: {$allSectionTitles}

### Sección destino del card

**Nombre:** {$sectionTitle}

**Contenido:**
{$sectionContentPreview}

### Referentes normativos del plan de estudio

{$referentsText}

---

Genera el código HTML del diagrama Mermaid para esta sección.
El diagrama debe representar fielmente el contenido pedagógico y ser visualmente claro para estudiantes de secundaria.

**Importante:** MOBILE-FIRST, w-full, card simple sin título. Diagrama Mermaid con nodos/relaciones que reflejen el contenido. Sin scripts — solo el card container con el div.mermaid.

### Instrucciones adicionales del docente

{$promptRefinementText}
PROMPT;

        // ─── Llamar al servicio con compactación ────────────────
        try {
            $result = $this->askWithCompaction(
                $systemPrompt,
                $userPrompt,
                [
                    'max_tokens'  => 2048,
                    'temperature' => 0.7,
                    'timeout'     => 120,
                ],
                3500
            );

            if (!$result['success']) {
                $this->generationError = $result['error'];
                $this->generatingEmbedCard = false;
                $this->notification()->error('Error al generar diagrama', $result['error'] ?? 'Error desconocido');
                return;
            }

            $raw = trim($result['content'] ?? '');

            // Extraer código dentro de ``` ``` (si la IA añade explicación antes/después)
            $html = $raw;
            if (preg_match('/```(?:html|mermaid)?\s*\n?(.*?)```/s', $raw, $m)) {
                $html = trim($m[1]);
            }

            if (empty($html)) {
                $this->generationError = 'La IA no generó contenido HTML.';
                $this->generatingEmbedCard = false;
                $this->notification()->error('Respuesta vacía', 'La IA no generó ningún código HTML.');
                return;
            }

            // Extraer solo el código Mermaid del wrapper HTML generado por la IA,
            // para que ensureMermaidWrapper() lo detecte como código plano con is_mermaid=true
            // y el renderizado use el package icehouse-ventures/laravel-mermaid (Alpine + wire:ignore)
            if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $html, $m)) {
                $inner = strip_tags(trim($m[1]));
                $mermaidPattern = '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/';
                if (preg_match($mermaidPattern, $inner)) {
                    $html = $inner;
                }
            }

            // Poner el código Mermaid plano en el textarea
            $this->embedHtml = $html;

            // Sugerir título basado en la sección
            if (empty($this->embedTitle)) {
                $this->embedTitle = 'Diagrama: ' . $sectionTitle;
            }

            $this->generatingEmbedCard = false;

            $this->notification()->success(
                'Diagrama generado',
                'El diagrama Mermaid se generó correctamente. Revisa el resultado y haz clic en "Agregar Embed" para incorporarlo.'
            );

        } catch (\Throwable $e) {
            $this->generationError = $e->getMessage();
            $this->generatingEmbedCard = false;
            $this->notification()->error('Error inesperado', $e->getMessage());
        }
    }

    // ─── Wizard: Paso 2 — Generar preguntas de repaso ──────────

    public function generateReviewQuestions(): void
    {
        if (empty($this->lessonTitle)) {
            $this->notification()->error('Sin título', 'Primero escribe un título para la lección en el paso 1.');
            return;
        }

        // Construir contexto a partir de las secciones existentes
        $sectionsSummary = '';
        foreach ($this->wizardSections as $sec) {
            $title = $sec['title'] ?? '';
            $bodyPreview = '';
            if (!empty($sec['contents'])) {
                $bodyPreview = strip_tags($sec['contents'][0]['body'] ?? '');
                $bodyPreview = mb_substr($bodyPreview, 0, 300);
            }
            if (!empty($title) || !empty($bodyPreview)) {
                $sectionsSummary .= "- {$title}: {$bodyPreview}\n";
            }
        }
        if (empty($sectionsSummary)) {
            $sectionsSummary = '(No hay secciones generadas aún)';
        }

        $gradeName = $this->selectedActivity?->pevaluacion?->seccion?->grado?->name ?? '—';
        $subjectName = $this->selectedActivity?->pevaluacion?->pensum?->asignatura?->name ?? '—';
        $sectionName = $this->selectedActivity?->pevaluacion?->seccion?->name ?? '—';

        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Genera preguntas de repaso en formato Markdown para una lección escolar.

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado, con la calidad narrativa de un best seller.

Formato obligatorio:
## Preguntas de Repaso

1. **Pregunta 1** — texto de la pregunta en negrita seguido de la respuesta explicativa en párrafo.
2. **Pregunta 2** — mismo formato.
3. **Pregunta 3** — mismo formato.

### Sección de Desarrollo
4. **Pregunta 4** — texto de la pregunta...
5. **Pregunta 5** — ...

### Verdadero o Falso (opcional)
- ✅ **Afirmación 1** → Verdadero. Explicación breve.
- ❌ **Afirmación 2** → Falso. Explicación breve.

Reglas:
- Mínimo 8 preguntas variadas (abiertas, desarrollo, V/F).
- Usa ## para título principal, ### para sub-secciones, **negritas** para destacar.
- Incluye respuestas breves después de cada pregunta.
- NO incluyas ```markdown, NO expliques lo que generas, NO añadas meta-comentarios.
- Responde SOLO con el contenido Markdown.
PROMPT;

        $userPrompt = <<<PROMPT
### Contexto

**Curso:** {$this->lessonTitle} · {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Secciones de la lección:**
{$sectionsSummary}

Genera las preguntas de repaso en Markdown siguiendo el formato indicado. Mínimo 8 preguntas variadas con sus respuestas.
PROMPT;

        $result = $this->askWithCompaction(
            $systemPrompt,
            $userPrompt,
            ['max_tokens' => 4096, 'timeout' => 180],
        );

        if (!$result['success']) {
            $this->notification()->error('Error al generar preguntas', $result['error'] ?? 'Error desconocido');
            return;
        }

        $this->reviewQuestions = $this->sanitizeText($result['content'] ?? '');

        if (empty($this->reviewQuestions)) {
            $this->notification()->error('Respuesta vacía', 'La IA no generó preguntas de repaso.');
            return;
        }

        $this->notification()->success(
            'Preguntas generadas',
            'Las preguntas de repaso se generaron correctamente en formato Markdown.'
        );
    }

    // ─── Wizard: Paso 2 — Guardado incremental ────────────────

    public function saveStep2(): void
    {
        $activityId = $this->selectedActivityId;
        $sectionIdMap = [];

        // ─── Persistir título y descripción generados (paso 1) ──
        if (!empty($this->lessonTitle) || !empty($this->lessonDescription)) {
            Activity::where('id', $activityId)->update([
                'topic'       => $this->lessonTitle,
                'description' => $this->lessonDescription,
            ]);
        }

        foreach ($this->wizardSections as $key => $sectionData) {
            $sectionTitle = $this->sanitizeText($sectionData['title'] ?? '');

            if (str_starts_with((string)$sectionData['id'], 'temp_')) {
                $tempId = $sectionData['id'];
                $section = LmsActivitySection::create([
                    'activity_id' => $activityId,
                    'title'       => $sectionTitle,
                    'sort_order'  => $sectionData['sort_order'] ?? 1,
                    'is_visible'  => $sectionData['is_visible'] ?? true,
                ]);
                $sectionIdMap[$tempId] = $section->id;
                // Reemplazar el id temporal con el real
                $this->wizardSections[$key]['id'] = $section->id;
            } else {
                $section = LmsActivitySection::find($sectionData['id']);
                if ($section) {
                    $section->update([
                        'title'      => $sectionTitle,
                        'is_visible' => $sectionData['is_visible'] ?? true,
                    ]);
                    // Limpiar contenidos previos para evitar duplicados
                    LmsActivityContent::where('section_id', $section->id)->delete();
                }
            }

            if ($section && !empty($sectionData['contents'])) {
                foreach ($sectionData['contents'] as $i => $contentData) {
                    LmsActivityContent::create([
                        'section_id' => $section->id,
                        'type'       => $contentData['type'] ?? 'TEXT',
                        'title'      => $this->sanitizeText($contentData['title'] ?? null),
                        'body'       => $this->sanitizeText($contentData['body'] ?? ''),
                        'sort_order' => $i + 1,
                        'is_visible' => $contentData['is_visible'] ?? true,
                    ]);
                }
            }
        }

        // ─── Guardar preguntas de repaso como sección final ────
        $this->saveReviewQuestionsSection($activityId);

        // ─── Guardar recursos ──────────────────────────────────
        $visibleResourceIds = [];
        foreach ($this->wizardResources as $key => $res) {
            if (str_starts_with((string)($res['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($res['section_id']) && isset($sectionIdMap[$res['section_id']])
                    ? $sectionIdMap[$res['section_id']]
                    : (!empty($res['section_id']) ? $res['section_id'] : null);

                $newRes = LmsActivityResource::create([
                    'activity_id'  => $activityId,
                    'section_id'   => $resolvedSectionId,
                    'media_id'     => $res['media_id'],
                    'uploaded_by'  => $res['uploaded_by'] ?? auth()->id(),
                    'display_name' => $res['display_name'],
                    'description'  => $res['description'] ?? '',
                    'is_visible'   => true,
                ]);
                $this->wizardResources[$key]['id'] = $newRes->id;
                $visibleResourceIds[] = $newRes->id;
            } else {
                $resourceId = (int) $res['id'];
                $visibleResourceIds[] = $resourceId;

                // Actualizar campos editables del recurso existente
                $resolvedSectionId = isset($res['section_id']) && isset($sectionIdMap[$res['section_id']])
                    ? $sectionIdMap[$res['section_id']]
                    : (!empty($res['section_id']) ? $res['section_id'] : null);

                $updateData = [
                    'display_name' => $res['display_name'],
                    'description'  => $res['description'] ?? '',
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                if (!empty($res['media_id'])) {
                    $updateData['media_id'] = $res['media_id'];
                }
                LmsActivityResource::where('id', $resourceId)->update($updateData);
            }
        }
        LmsActivityResource::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleResourceIds)
            ->update(['is_visible' => false]);

        // ─── Guardar enlaces ───────────────────────────────────
        $visibleLinkIds = [];
        foreach ($this->wizardLinks as $key => $link) {
            if (str_starts_with((string)($link['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($link['section_id']) && isset($sectionIdMap[$link['section_id']])
                    ? $sectionIdMap[$link['section_id']]
                    : (!empty($link['section_id']) ? $link['section_id'] : null);

                $newLink = LmsActivityLink::create([
                    'activity_id' => $activityId,
                    'section_id'  => $resolvedSectionId,
                    'added_by'    => auth()->id(),
                    'title'       => $link['title'],
                    'url'         => $link['url'],
                    'link_type'   => $link['link_type'] ?? 'REFERENCE',
                    'sort_order'  => $link['sort_order'] ?? 1,
                    'is_visible'  => true,
                ]);
                $this->wizardLinks[$key]['id'] = $newLink->id;
                $visibleLinkIds[] = $newLink->id;
            } else {
                $linkId = (int) $link['id'];
                $visibleLinkIds[] = $linkId;

                $resolvedSectionId = isset($link['section_id']) && isset($sectionIdMap[$link['section_id']])
                    ? $sectionIdMap[$link['section_id']]
                    : (!empty($link['section_id']) ? $link['section_id'] : null);

                $updateData = [
                    'title'     => $link['title'],
                    'url'       => $link['url'],
                    'link_type' => $link['link_type'] ?? 'REFERENCE',
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                LmsActivityLink::where('id', $linkId)->update($updateData);
            }
        }
        LmsActivityLink::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleLinkIds)
            ->update(['is_visible' => false]);

        // ─── Guardar HTML embeds ───────────────────────────────
        $visibleEmbedIds = [];
        foreach ($this->wizardHtmlEmbeds as $key => $embed) {
            if (str_starts_with((string)($embed['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($embed['section_id']) && isset($sectionIdMap[$embed['section_id']])
                    ? $sectionIdMap[$embed['section_id']]
                    : (!empty($embed['section_id']) ? $embed['section_id'] : null);

                $newEmbed = LmsHtmlEmbed::create([
                    'activity_id'      => $activityId,
                    'section_id'       => $resolvedSectionId,
                    'added_by'         => auth()->id(),
                    'title'            => $embed['title'] ?? null,
                    'html_content'     => $embed['html_content'],
                    'render_condition' => 'ALWAYS',
                    'sort_order'       => $embed['sort_order'] ?? 1,
                    'is_visible'       => true,
                ]);
                $this->wizardHtmlEmbeds[$key]['id'] = $newEmbed->id;
                $visibleEmbedIds[] = $newEmbed->id;
            } else {
                $embedId = (int) $embed['id'];
                $visibleEmbedIds[] = $embedId;

                $resolvedSectionId = isset($embed['section_id']) && isset($sectionIdMap[$embed['section_id']])
                    ? $sectionIdMap[$embed['section_id']]
                    : (!empty($embed['section_id']) ? $embed['section_id'] : null);

                $updateData = [
                    'title'        => $embed['title'] ?? null,
                    'html_content' => $embed['html_content'],
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                LmsHtmlEmbed::where('id', $embedId)->update($updateData);
            }
        }
        LmsHtmlEmbed::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleEmbedIds)
            ->update(['is_visible' => false]);

        $this->notification()->success(
            'Guardado',
            count($this->wizardSections) . ' secciones, ' . count($visibleResourceIds) . ' recursos, ' . count($visibleLinkIds) . ' enlaces y ' . count($visibleEmbedIds) . ' embeds guardados correctamente'
        );
    }

    // ─── Guarda o elimina la sección de preguntas de repaso ──────

    private function saveReviewQuestionsSection(int $activityId): void
    {
        $reviewTitle = 'Preguntas de Repaso';

        if (!empty($this->reviewQuestions)) {
            // Buscar sección existente de repaso
            $existingSection = LmsActivitySection::where('activity_id', $activityId)
                ->where('title', $reviewTitle)
                ->first();

            if ($existingSection) {
                LmsActivityContent::where('section_id', $existingSection->id)->delete();
                $section = $existingSection;
            } else {
                $maxSort = LmsActivitySection::where('activity_id', $activityId)->max('sort_order') ?? 0;
                $section = LmsActivitySection::create([
                    'activity_id' => $activityId,
                    'title'       => $reviewTitle,
                    'sort_order'  => $maxSort + 1,
                    'is_visible'  => true,
                ]);
            }

            LmsActivityContent::create([
                'section_id' => $section->id,
                'type'       => 'TEXT',
                'title'      => null,
                'body'       => $this->sanitizeText($this->reviewQuestions),
                'sort_order' => 1,
                'is_visible' => true,
            ]);
        } else {
            // Sin contenido: eliminar sección existente si la hay
            LmsActivitySection::where('activity_id', $activityId)
                ->where('title', $reviewTitle)
                ->delete();
        }
    }

    // ─── Wizard: Paso 4 — Guardar y Publicar ───────────────────

    public function previewGeneratedEmbed(): void
    {
        $this->showEmbedPreview = true;
    }

    public function closeEmbedPreview(): void
    {
        $this->showEmbedPreview = false;
    }

    public function previewExistingEmbed(int $index): void
    {
        if (isset($this->wizardHtmlEmbeds[$index])) {
            $this->previewEmbedIndex = $index;
        }
    }

    public function closeExistingEmbedPreview(): void
    {
        $this->previewEmbedIndex = null;
    }

    // ─── Wizard: Vista previa de imagen ────────────────────────

    public function previewResourceImage(int $index): void
    {
        if (isset($this->wizardResources[$index])) {
            $this->previewResourceIndex = $index;
        }
    }

    public function closeResourcePreview(): void
    {
        $this->previewResourceIndex = null;
    }

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

        // 0. Guardar título y descripción en la actividad (sanear)
        $this->selectedActivity->update([
            'topic'       => $this->sanitizeText($this->lessonTitle),
            'description' => $this->sanitizeText($this->lessonDescription),
        ]);

        // 1. Guardar secciones y construir mapa temp_ID → real_ID
        $sectionIdMap = [];
        $mermaidEmbedIds = []; // IDs de LmsHtmlEmbed creados desde secciones con Mermaid
        foreach ($this->wizardSections as $key => $sectionData) {
            $sectionTitle = $this->sanitizeText($sectionData['title'] ?? '');

            if (str_starts_with((string)$sectionData['id'], 'temp_')) {
                $tempId = $sectionData['id'];
                $section = LmsActivitySection::create([
                    'activity_id' => $activityId,
                    'title'       => $sectionTitle,
                    'sort_order'  => $sectionData['sort_order'] ?? 1,
                    'is_visible'  => $sectionData['is_visible'],
                ]);

                // Actualizar el id temporal con el id real de BD
                $this->wizardSections[$key]['id'] = $section->id;
                // Registrar el mapeo para recursos/enlaces/embeds
                $sectionIdMap[$tempId] = $section->id;
            } else {
                $section = LmsActivitySection::find($sectionData['id']);
                if ($section) {
                    $section->update([
                        'title'      => $sectionTitle,
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
                    $rawBody = $contentData['body'] ?? '';
                    $contentType = $contentData['type'] ?? 'TEXT';

                    // ─── Detectar diagramas Mermaid ────────────────────
                    // Caso 1: contenido envuelto en <div class="mermaid">
                    $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $rawBody) === 1;

                    // Caso 2: código Mermaid plano (sin HTML wrapper) — contenido
                    // guardado antes de que existiera la lógica de detección.
                    if (!$isMermaid && $rawBody !== '' && !str_contains($rawBody, '<')) {
                        $trimmed = trim(strip_tags($rawBody));
                        $isMermaid = preg_match(
                            '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/',
                            $trimmed
                        ) === 1;
                    }

                    if ($isMermaid) {
                        $mermaidCode = $rawBody;
                        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $rawBody, $m)) {
                            $mermaidCode = trim(strip_tags($m[1]));
                        } elseif (!str_contains($rawBody, '<')) {
                            // Raw Mermaid code — usar tal cual
                            $mermaidCode = $this->sanitizeText($rawBody);
                        } else {
                            // <div class="mermaid"> detectado pero no se pudo extraer — sanitizar genérico
                            $mermaidCode = trim(strip_tags($rawBody));
                        }

                        // Guardar como LmsHtmlEmbed (se renderiza con <x-mermaid::component>)
                        $mermaidEmbed = LmsHtmlEmbed::create([
                            'activity_id'      => $activityId,
                            'section_id'       => $section->id,
                            'added_by'         => auth()->id(),
                            'title'            => ($contentData['title'] ?? null) ?: 'Diagrama',
                            'html_content'     => $mermaidCode,
                            'render_condition' => 'ALWAYS',
                            'sort_order'       => $i + 1,
                            'is_visible'       => true,
                        ]);
                        $mermaidEmbedIds[] = $mermaidEmbed->id;
                    } else {
                        // Contenido normal (texto, HTML sin Mermaid)
                        $safeBody = $this->sanitizeText($rawBody);
                        LmsActivityContent::create([
                            'section_id' => $section->id,
                            'type'       => $contentType,
                            'title'      => $this->sanitizeText($contentData['title'] ?? null),
                            'body'       => $safeBody,
                            'sort_order' => $i + 1,
                            'is_visible' => true,
                        ]);
                    }
                }
            }
        }

        // 2. Limpiar secciones eliminadas en el wizard
        $existingIds = array_filter(array_map(fn($s) => is_numeric($s['id']) ? $s['id'] : null, $this->wizardSections));
        LmsActivitySection::where('activity_id', $activityId)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // ─── Verificar límite total de 10 MB por lección ────────
        $totalBytes = 0;
        foreach ($this->wizardResources as $res) {
            $totalBytes += $res['media']['size_bytes'] ?? 0;
        }
        if ($totalBytes > 10485760) {
            $this->notification()->error(
                'Límite excedido',
                'El total de recursos de la lección supera los 10 MB. Elimine algunos recursos o reduzca su tamaño antes de publicar.'
            );
            return;
        }

        // 3. Guardar recursos
        $visibleResourceIds = [];
        foreach ($this->wizardResources as $key => $res) {
            if (str_starts_with((string)($res['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($res['section_id']) && isset($sectionIdMap[$res['section_id']])
                    ? $sectionIdMap[$res['section_id']]
                    : (!empty($res['section_id']) ? $res['section_id'] : null);

                $newRes = LmsActivityResource::create([
                    'activity_id'  => $activityId,
                    'section_id'   => $resolvedSectionId,
                    'media_id'     => $res['media_id'],
                    'uploaded_by'  => $res['uploaded_by'] ?? auth()->id(),
                    'display_name' => $res['display_name'],
                    'description'  => $res['description'] ?? '',
                    'is_visible'   => true,
                ]);
                $this->wizardResources[$key]['id'] = $newRes->id;
                $visibleResourceIds[] = $newRes->id;
            } else {
                $resourceId = (int) $res['id'];
                $visibleResourceIds[] = $resourceId;

                $resolvedSectionId = isset($res['section_id']) && isset($sectionIdMap[$res['section_id']])
                    ? $sectionIdMap[$res['section_id']]
                    : (!empty($res['section_id']) ? $res['section_id'] : null);

                $updateData = [
                    'display_name' => $res['display_name'],
                    'description'  => $res['description'] ?? '',
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                if (!empty($res['media_id'])) {
                    $updateData['media_id'] = $res['media_id'];
                }
                LmsActivityResource::where('id', $resourceId)->update($updateData);
            }
        }
        LmsActivityResource::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleResourceIds)
            ->update(['is_visible' => false]);

        // 4. Guardar enlaces
        $visibleLinkIds = [];
        foreach ($this->wizardLinks as $key => $link) {
            if (str_starts_with((string)($link['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($link['section_id']) && isset($sectionIdMap[$link['section_id']])
                    ? $sectionIdMap[$link['section_id']]
                    : (!empty($link['section_id']) ? $link['section_id'] : null);

                $newLink = LmsActivityLink::create([
                    'activity_id' => $activityId,
                    'section_id'  => $resolvedSectionId,
                    'added_by'    => auth()->id(),
                    'title'       => $link['title'],
                    'url'         => $link['url'],
                    'link_type'   => $link['link_type'] ?? 'REFERENCE',
                    'sort_order'  => $link['sort_order'] ?? 1,
                    'is_visible'  => true,
                ]);
                $this->wizardLinks[$key]['id'] = $newLink->id;
                $visibleLinkIds[] = $newLink->id;
            } else {
                $linkId = (int) $link['id'];
                $visibleLinkIds[] = $linkId;

                $resolvedSectionId = isset($link['section_id']) && isset($sectionIdMap[$link['section_id']])
                    ? $sectionIdMap[$link['section_id']]
                    : (!empty($link['section_id']) ? $link['section_id'] : null);

                $updateData = [
                    'title'     => $link['title'],
                    'url'       => $link['url'],
                    'link_type' => $link['link_type'] ?? 'REFERENCE',
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                LmsActivityLink::where('id', $linkId)->update($updateData);
            }
        }
        LmsActivityLink::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleLinkIds)
            ->update(['is_visible' => false]);

        // 5. Guardar HTML embeds
        $visibleEmbedIds = $mermaidEmbedIds; // Incluir embeds creados desde secciones Mermaid
        foreach ($this->wizardHtmlEmbeds as $key => $embed) {
            if (str_starts_with((string)($embed['id'] ?? ''), 'temp_')) {
                $resolvedSectionId = isset($embed['section_id']) && isset($sectionIdMap[$embed['section_id']])
                    ? $sectionIdMap[$embed['section_id']]
                    : (!empty($embed['section_id']) ? $embed['section_id'] : null);

                $newEmbed = LmsHtmlEmbed::create([
                    'activity_id'      => $activityId,
                    'section_id'       => $resolvedSectionId,
                    'added_by'         => auth()->id(),
                    'title'            => $embed['title'] ?? null,
                    'html_content'     => $embed['html_content'],
                    'render_condition' => 'ALWAYS',
                    'sort_order'       => $embed['sort_order'] ?? 1,
                    'is_visible'       => true,
                ]);
                $this->wizardHtmlEmbeds[$key]['id'] = $newEmbed->id;
                $visibleEmbedIds[] = $newEmbed->id;
            } else {
                $embedId = (int) $embed['id'];
                $visibleEmbedIds[] = $embedId;

                $resolvedSectionId = isset($embed['section_id']) && isset($sectionIdMap[$embed['section_id']])
                    ? $sectionIdMap[$embed['section_id']]
                    : (!empty($embed['section_id']) ? $embed['section_id'] : null);

                $updateData = [
                    'title'        => $embed['title'] ?? null,
                    'html_content' => $embed['html_content'],
                ];
                if ($resolvedSectionId) {
                    $updateData['section_id'] = $resolvedSectionId;
                }
                LmsHtmlEmbed::where('id', $embedId)->update($updateData);
            }
        }
        LmsHtmlEmbed::where('activity_id', $activityId)
            ->whereNotIn('id', $visibleEmbedIds)
            ->update(['is_visible' => false]);

        // 6. Guardar preguntas de repaso
        $this->saveReviewQuestionsSection($activityId);

        // 7. Publicar
        app(LmsPublicationService::class)->publish(
            $this->selectedActivity,
            [
                'publish_at'      => $this->publishAt,
                'allow_downloads' => $this->allowDownloads,
            ],
            auth()->id()
        );

        LmsActivityLog::record($activityId, auth()->id(), 'PUBLISH');

        $this->showPublishConfirm = false;
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
            'lmsHtmlEmbeds' => fn($q) => $q->where('is_visible', true),
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
            'html_embeds'   => $activity->lmsHtmlEmbeds
                ->map(fn($e) => $this->ensureMermaidWrapper($e->toArray()))
                ->values()
                ->toArray(),
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
            'lmsHtmlEmbeds' => fn($q) => $q->where('is_visible', true),
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
            'html_embeds'   => $activity->lmsHtmlEmbeds
                ->map(fn($e) => $this->ensureMermaidWrapper($e->toArray()))
                ->values()
                ->toArray(),
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

        // 3b. Copiar HTML embeds visibles
        $embeds = LmsHtmlEmbed::where('activity_id', $sourceActivityId)
            ->where('is_visible', true)
            ->get();

        foreach ($embeds as $embed) {
            $newEmbed = $embed->replicate();
            $newEmbed->activity_id = $targetActivityId;
            $newEmbed->added_by = auth()->id();
            $newEmbed->save();
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
     * Prueba hasta 3 modelos de OpenRouter en cascada con 60s de timeout
     * cada uno. Si todos fallan, muestra un mensaje amigable pidiendo
     * reintento manual.
     *
     * Si se proporciona un $contentValidator, el contenido devuelto por
     * cada modelo se valida con ese callable. Si retorna false, se considera
     * que el modelo falló (contenido inválido) y se pasa al siguiente.
     *
     * @param  string       $systemPrompt     Instrucción del sistema.
     * @param  string       $userPrompt       Mensaje del usuario.
     * @param  array        $overrides        Overrides base para el LLM.
     * @param  int          $tokenBudget      Máx. tokens del user prompt antes de compactar.
     * @param  callable|null $contentValidator Recibe (string $content): bool.
     *                                         true = válido, false = inválido (pasar al sig. modelo).
     * @param  array|null    $customChain      Cadena custom de modelos [['model','label'],...].
     *                                         null = usa la cadena por defecto (lesson wizard).
     * @return array{success: bool, content: ?string, model: ?string, usage: ?array, error: ?string}
     */
    private function askWithCompaction(
        string    $systemPrompt,
        string    $userPrompt,
        array     $overrides = [],
        int       $tokenBudget = 2000,
        ?callable $contentValidator = null,
        ?array    $customChain = null
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

        // ─── Cadena de modelos OpenRouter (desde config/openrouter.php) ──
        $modelChain = $customChain ?? [
            ['model' => config('openrouter.model_primary'),   'label' => 'Qwen 3.1 32B primario'],
            ['model' => config('openrouter.model_fallback1'), 'label' => 'Mistral Large fallback 1'],
            ['model' => config('openrouter.model_fallback2'), 'label' => 'Ling 2.6 Flash fallback 2'],
            ['model' => config('openrouter.model_fallback3'), 'label' => 'Nemotron 3 Nano fallback 3'],
            ['model' => config('openrouter.model_fallback4'), 'label' => 'Claude Sonnet 4 fallback 4'],
        ];

        /** @var OpenRouterService $llm */
        $llm = app(OpenRouterService::class);
        $lastError = null;

        // Instrucciones adicionales para modelos de respaldo. Se añaden limpias
        // en cada intento (sin acumulación) para reforzar las reglas.

        foreach ($modelChain as $i => $attempt) {
            // Reconstruir prompt fresco con refuerzo (sin acumulación entre intentos)
            $attemptUserPrompt = $i > 0 ? $userPrompt . self::FALLBACK_REINFORCEMENT : $userPrompt;

            $attemptOverrides = array_merge($overrides, [
                'model'   => $attempt['model'],
                'timeout' => max($overrides['timeout'] ?? 120, 120),
            ]);

            $result = $llm->ask($systemPrompt, $attemptUserPrompt, $attemptOverrides);

            if ($result['success']) {
                // Validar contenido si hay un validador
                $content = $result['content'] ?? '';
                if ($contentValidator !== null && (empty($content) || !$contentValidator($content))) {
                    // Guardar respuesta para depuración aunque sea inválida
                    $this->debugRawContent = $content;
                    $lastError = 'Contenido inválido: no superó la validación de estructura.';

                    // Inspeccionar por qué falló la validación
                    $vHasInicio    = preg_match('/^\/\/INICIO\s*$/m', $content) === 1;
                    $vHasDesarrollo = preg_match('/^\/\/DESARROLLO\s*$/m', $content) === 1;
                    $vHasCierre    = preg_match('/^\/\/CIERRE\s*$/m', $content) === 1;
                    $vDevBlocks = 0;
                    if ($vHasInicio && $vHasDesarrollo && $vHasCierre) {
                        $vDevMatch = null;
                        preg_match('/^\/\/DESARROLLO\s*$(.*?)^\/\/CIERRE\s*$/ms', $content, $vDevMatch);
                        if (!empty($vDevMatch[1])) {
                            $vBlocks = preg_split('/\n\s*\n/', trim($vDevMatch[1]));
                            $vValidBlocks = array_filter($vBlocks, fn(string $b): bool => !empty(trim($b)));
                            $vDevBlocks = count($vValidBlocks);
                        }
                    }

                    Log::warning("askWithCompaction: {$attempt['label']} contenido inválido", [
                        'model'         => $attempt['model'],
                        'length'        => mb_strlen($content),
                        'validation'    => [
                            'has_inicio'    => $vHasInicio,
                            'has_desarrollo' => $vHasDesarrollo,
                            'has_cierre'    => $vHasCierre,
                            'dev_blocks'    => $vDevBlocks,
                        ],
                        'content_preview' => mb_substr(preg_replace('/\s+/', ' ', $content), 0, 500),
                    ]);
                    $this->notification()->warning(
                        "{$attempt['label']} contenido inválido",
                        'El contenido generado no cumple la estructura requerida. Cambiando al siguiente modelo...'
                    );
                    continue;
                }

                // Éxito: registrar qué modelo de la cadena respondió
                Log::info("askWithCompaction: {$attempt['label']} generó contenido válido", [
                    'model'  => $attempt['model'],
                    'length' => mb_strlen($content),
                    'chain_index' => $i,
                ]);

                return $result;
            }

            // Falló este modelo — registrar y notificar
            $lastError = $result['error'] ?? 'Error desconocido';
            $reason = $this->describeModelError($lastError);
            Log::warning("askWithCompaction: {$attempt['label']} falló", [
                'model' => $attempt['model'],
                'error' => $lastError,
                'reason' => $reason,
            ]);

            $this->notification()->warning(
                "{$attempt['label']} no respondió",
                "Cambiando al siguiente modelo... ({$reason})"
            );
        }

        // ─── Todos los modelos fallaron ──────────────────────────
        Log::error('askWithCompaction: los 3 modelos OpenRouter fallaron', [
            'last_error' => $lastError,
        ]);
        $this->notification()->error(
            'Generación interrumpida',
            'Los modelos de IA no pudieron completar la generación. Verifica tu conexión a Internet y el saldo en OpenRouter, luego intenta de nuevo pulsando el botón de generar.'
        );

        return [
            'success' => false,
            'content' => null,
            'model'   => null,
            'usage'   => null,
            'error'   => $lastError,
        ];
    }

    /**
     * Extrae una descripción legible del error del modelo para mostrarla
     * en la notificación de fallback.
     */
    private function describeModelError(string $errorMsg): string
    {
        if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Rate limit')) {
            return 'límite de requests excedido';
        }
        if (str_contains($errorMsg, '402') || str_contains($errorMsg, 'Insufficient credits')) {
            return 'créditos insuficientes';
        }
        if (str_contains($errorMsg, '28') || str_contains($errorMsg, 'timed out') || str_contains($errorMsg, 'timeout')) {
            return 'tiempo de espera agotado (60s)';
        }
        if (str_contains($errorMsg, '52') || str_contains($errorMsg, 'Empty reply') || str_contains($errorMsg, 'Connection refused')) {
            return 'el servidor cerró la conexión';
        }
        if (str_contains($errorMsg, '404') || str_contains($errorMsg, '500') || str_contains($errorMsg, '503')) {
            return 'error del modelo (' . $errorMsg . ')';
        }
        if (str_contains($errorMsg, 'excedió el límite de tokens') || str_contains($errorMsg, 'content_filter')) {
            return 'el modelo rechazó la solicitud por seguridad o longitud';
        }
        if (str_contains($errorMsg, 'sin contenido') || str_contains($errorMsg, 'finalizó sin contenido')) {
            return 'el modelo finalizó sin generar contenido';
        }

        // Genérico
        $truncated = mb_strlen($errorMsg) > 60 ? mb_substr($errorMsg, 0, 57) . '...' : $errorMsg;
        return $truncated;
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

    /**
     * Asegura que el contenido de un embed con código Mermaid esté
     * envuelto en el contenedor HTML + div.mermaid más CDN.
     * Útil para embeds existentes guardados antes del wrapper automático.
     */
    private function ensureMermaidWrapper(array $embed): array
    {
        $content = trim($embed['html_content'] ?? '');
        $mermaidPattern = '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/';

        // 1. Ya procesado (flag is_mermaid)
        if (!empty($embed['is_mermaid'])) {
            return $embed;
        }

        // 2. Código Mermaid suelto (empieza con keyword)
        if (preg_match($mermaidPattern, $content) === 1) {
            $embed['is_mermaid'] = true;
            // html_content se deja como está (código plano)
            return $embed;
        }

        // 3. Formato legacy: tenía data-mermaid-code o div.mermaid con wrapper HTML
        // Extraer el código de data-mermaid-code
        if (preg_match('/data-mermaid-code="([^"]*)"/', $content, $m)) {
            $code = htmlspecialchars_decode($m[1], ENT_QUOTES);
            $embed['html_content'] = $code;
            $embed['is_mermaid'] = true;
            return $embed;
        }

        // 4. Formato legacy: div.mermaid con código inline
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $content, $m)) {
            $innerCode = strip_tags(trim($m[1]));
            if (preg_match($mermaidPattern, $innerCode)) {
                $embed['html_content'] = $innerCode;
                $embed['is_mermaid'] = true;
                return $embed;
            }
        }

        // 5. No es mermaid → dejar intacto
        $embed['is_mermaid'] = false;
        return $embed;
    }

    /**
     * Renderiza el contenido de la diapositiva actual para la vista previa.
     * Renderiza todos los bloques de contenido de la diapositiva actual.
     */
    public function slidePreviewContent(): string
    {
        $currentSlide = $this->wizardSections[$this->currentSlideIndex] ?? null;
        if (!$currentSlide) {
            return '';
        }

        $blocks = collect($currentSlide['contents'] ?? [])
            ->filter(fn($c) => !empty($c['body']))
            ->values();

        $rendered = $blocks->map(function (array $block, int $idx): string {
            $body  = $block['body'] ?? '';
            $type  = $block['type'] ?? 'TEXT';
            $title = $block['title'] ?? '';

            $wrapperClass = 'slide-block slide-block-' . ($idx % 2 === 0 ? 'even' : 'odd');

            // ─── IMAGE: SVG/ilustración — render raw, sin mathContent ──
            // Se detecta por type (contenido nuevo) o por body (contenido
            // existente guardado con type TEXT antes de esta clasificación).
            if ($type === 'IMAGE' || preg_match('/<svg\b/', $body)) {
                return '<div class="' . $wrapperClass . '">'
                    . "\n" . $body . "\n"
                    . '</div>';
            }

            // ─── MERMAID: detectar por clase CSS o keyword ─────────────
            $isMermaid = preg_match('/class="[^"]*\bmermaid\b[^"]*"/', $body) === 1;
            if (!$isMermaid) {
                $isMermaid = preg_match('/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/m', trim($body)) === 1;
            }

            if ($isMermaid) {
                preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $body, $m);
                $mermaidCode = trim(strip_tags($m[1] ?? ''));
                if (empty($mermaidCode)) {
                    $mermaidCode = trim(strip_tags($body));
                }
                return '<div class="' . $wrapperClass . '">'
                    . "\n"
                    . '<div wire:ignore x-data="mermaidEmbed()"'
                    . ' data-mermaid-code="' . htmlspecialchars($mermaidCode, ENT_QUOTES, 'UTF-8') . '"'
                    . ' class="w-full bg-white rounded-xl p-4 overflow-x-auto border border-slate-200">'
                    . '<div x-ref="target" class="w-full"></div>'
                    . '</div>'
                    . "\n"
                    . '</div>';
            }

            // ─── TEXT / MATH / default: render con mathContent ─────────
            $html = $this->renderContentBody($body);
            return '<div class="' . $wrapperClass . '">'
                . "\n"
                . '<div x-data="mathContent()" data-math-content="' . htmlspecialchars($html, ENT_QUOTES, 'UTF-8') . '">'
                . '<div wire:ignore><div x-ref="target"></div></div>'
                . '</div>'
                . "\n"
                . '</div>';
        });

        return $rendered->implode("\n");
    }

    /**
     * Renderiza el body de un bloque de contenido.
     *
     * Si el body contiene etiquetas HTML (<) se renderiza raw.
     * En caso contrario se asume Markdown y se convierte a HTML.
     */
    public function renderContentBody(?string $body): string
    {
        if (empty($body)) {
            return '';
        }
        if (\Illuminate\Support\Str::contains($body, '<')) {
            // Defense-in-depth: sanitizar HTML server-side antes de renderizar.
            // El contenido viene del AI (OpenRouter) o del profesor — aunque
            // confiable, un prompt malicioso podría inyectar HTML dañino.
            // DOMPurify (client-side) es la defensa primaria; este sanitizador
            // es la capa secundaria.
            return app(\App\Services\Lms\LmsHtmlSanitizerService::class)->sanitize($body);
        }
        return \Illuminate\Support\Str::markdown($body);
    }

    /**
     * Renderiza el body de un bloque de contenido para el modal de previsualización.
     *
     * Si el body contiene un diagrama Mermaid (<div class="mermaid">),
     * lo envuelve en el componente Alpine mermaidEmbed() para que se renderice
     * correctamente sin que Mermaid haga auto-scan del DOM.
     *
     * En caso contrario delega en renderContentBody().
     */
    public function renderPreviewContent(string $body): string
    {
        // Detectar si el body contiene un div.mermaid
        if (preg_match('/<div[^>]*class="[^"]*\bmermaid\b[^"]*"[^>]*>\s*(.*?)\s*<\/div>/s', $body, $m)) {
            $mermaidCode = trim(strip_tags($m[1]));
            if (!empty($mermaidCode)) {
                // Envolver en mermaidEmbed() como lo hace slidePreviewContent()
                return '<div class="slide-block">'
                    . "\n"
                    . '<div wire:ignore x-data="mermaidEmbed()"'
                    . ' data-mermaid-code="' . htmlspecialchars($mermaidCode, ENT_QUOTES, 'UTF-8') . '"'
                    . ' class="w-full bg-white rounded-xl p-4 overflow-x-auto border border-slate-200">'
                    . '<div x-ref="target" class="w-full"></div>'
                    . '</div>'
                    . "\n"
                    . '</div>';
            }
        }
        return $this->renderContentBody($body);
    }

    /**
     * Renderiza las preguntas de repaso con formato visual mejorado.
     * Procesa el markdown, extrae el título principal si existe,
     * y envuelve cada pregunta/respuesta en un diseño tipo card.
     */
    public function renderReviewQuestions(string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Eliminar el título "## Preguntas de Repaso" si existe (lo mostramos como heading de sección)
        $body = preg_replace('/^##\s+Preguntas?\s+de\s+Repaso\s*\n+/im', '', $markdown);
        $body = trim($body);

        if (empty($body)) {
            return '';
        }

        // Convertir markdown a HTML
        $html = \Illuminate\Support\Str::markdown($body);

        // Envolver en un contenedor con estilos dedicados
        return '<div class="review-questions">' . "\n" . $html . "\n" . '</div>';
    }

    /**
     * Sanitiza texto delegando en LmsTextSanitizerService.
     * Elimina espacios múltiples, saltos de línea excesivos,
     * ** markdown (común en respuestas de IA) y espacios al inicio/final.
     */
    private function sanitizeText(?string $text, string $level = 'standard'): ?string
    {
        return app(\App\Services\Lms\LmsTextSanitizerService::class)->sanitize($text, $level);
    }
}
