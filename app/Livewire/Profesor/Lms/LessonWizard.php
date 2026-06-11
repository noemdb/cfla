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

    // ─── Wizard: Vista previa estudiante ──────────────────────
    public bool $showStudentPreview = false;

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

        // ─── Llamar al servicio ─────────────────────────────────
        try {
            /** @var OpenRouterService $llm */
            $llm = app(OpenRouterService::class);
            $result = $llm->ask($systemPrompt, $userPrompt, [
                'max_tokens' => 256,
            ]);

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

            // ─── Parsear resultado: título || descripción ──────
            $parts = explode('||', $content);
            $title = trim($parts[0] ?? '');
            $description = trim($parts[1] ?? '');

            if (!empty($title)) {
                $this->lessonTitle = $title;
            }
            if (!empty($description)) {
                $this->lessonDescription = $description;
            }

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

        // ─── Llamar al servicio ─────────────────────────────────
        try {
            /** @var OpenRouterService $llm */
            $llm = app(OpenRouterService::class);
            $result = $llm->ask($systemPrompt, $userPrompt, [
                'max_tokens' => 512,
            ]);

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

            $this->notification()->success('Contenido generado', 'El contenido se agregó a la sección correctamente.');
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
Eres docente venezolano. Genera estructura leccion LMS.
Debes reemplazar CADA "TITULO:" y "CONTENIDO:" por texto real.
NO incluyas las palabras TITULO ni CONTENIDO en la respuesta.

Formato:

//INICIO
TITULO: (max 10 palabras, atractivo para estudiantes)
CONTENIDO: (1-2 parrafos, 80-150 palabras)

//DESARROLLO (3 a 6 bloques)
TITULO: (max 10 palabras)
CONTENIDO: (1-2 parrafos, 100-250 palabras)

TITULO: (siguiente bloque)
CONTENIDO:

TITULO: (etc...)
CONTENIDO: (...)

//CIERRE
TITULO: (max 10 palabras)
CONTENIDO: (1 parrafo, 80-150 palabras)

Reglas: lenguaje acorde al grado, ejemplos concretos, alineado con referentes.
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

        // ─── Llamar al servicio ─────────────────────────────────
        try {
            /** @var OpenRouterService $llm */
            $llm = app(OpenRouterService::class);
            $result = $llm->ask($systemPrompt, $userPrompt, [
                'max_tokens' => 768,
            ]);

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
                $this->generatingStep2 = false;
                return;
            }

            $count = count($this->wizardSections);
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

        $this->exportAvailableActivities = Activity::whereHas('pevaluacion', function ($q) use ($value, $activity) {
            $q->where('seccion_id', $value)
              ->where('lapso_id', $activity->pevaluacion->lapso_id)
              ->where('profesor_id', $activity->pevaluacion->profesor_id);
        })->orderBy('topic')->get()->map(fn($a) => [
            'id'    => $a->id,
            'label' => $a->topic ?? 'Actividad #' . $a->id,
        ])->pluck('label', 'id')->toArray();
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

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
        $this->exportActivityId = null;
        $this->exportTargetSectionId = null;
        $this->exportTargetActivityId = null;
        $this->exportAvailableSections = [];
        $this->exportAvailableActivities = [];
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

        $this->importAvailableActivities = Activity::whereHas('pevaluacion', function ($q) use ($value, $activity) {
            $q->where('seccion_id', $value)
              ->where('lapso_id', $activity->pevaluacion->lapso_id)
              ->where('profesor_id', $activity->pevaluacion->profesor_id);
        })->where('id', '!=', $this->importActivityId)
            ->orderBy('topic')->get()->map(fn($a) => [
                'id'    => $a->id,
                'label' => $a->topic ?? 'Actividad #' . $a->id,
            ])->pluck('label', 'id')->toArray();
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

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importActivityId = null;
        $this->importSourceSectionId = null;
        $this->importSourceActivityId = null;
        $this->importAvailableSections = [];
        $this->importAvailableActivities = [];
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
    }

    // ─── Helpers para la vista previa ──────────────────────────

    public function getPreviewSectionsProperty()
    {
        return array_filter($this->wizardSections, fn($s) => $s['is_visible']);
    }

    // ─── Render ────────────────────────────────────────────────

    public function render()
    {
        if ($this->mode === 'wizard') {
            return view('livewire.profesor.lms.lesson-wizard', [
                'totalSteps' => 4,
            ])->layout('planning.layouts.app');
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
        ))->layout('planning.layouts.app');
    }
}
