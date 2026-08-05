<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LessonsPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private int $lapsoId;
    private int $pestudioId;
    private int $gradoId;
    private int $seccionId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_profesor' => true]);
        $profesor = Profesor::create([
            'user_id'       => $this->user->id,
            'name'          => 'Carlos',
            'lastname'      => 'Méndez',
            'ci_profesor'   => '12345678',
            'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $this->pestudioId = $pestudio->id;
        $this->gradoId = $grado->id;
        $this->seccionId = $seccion->id;
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id'    => $pestudio->id,
            'grado_id'       => $grado->id,
            'asignatura_id'  => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $this->lapsoId = $lapso->id;

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic'          => 'Lección de prueba',
        ]);

        $section = LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title'       => 'Sección 1',
            'sort_order'  => 1,
            'is_visible'  => true,
        ]);

        // ─── Bloque IMAGE (SVG del botón "Generar Imagen") ─────
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type'       => 'IMAGE',
            'title'      => 'Diagrama',
            'body'       => '<figure class="my-6"><figcaption>Diagrama</figcaption>'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
                .'<circle cx="50" cy="50" r="40" fill="#0d9488"/></svg></figure>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        // ─── Bloque HTML semántico ─────────────────────────────
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type'       => 'HTML',
            'title'      => 'Nota',
            'body'       => '<div class="nota"><strong>Importante:</strong> leer capítulo 3.</div>',
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        // ─── Bloque TEXT (markdown) ────────────────────────────
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type'       => 'TEXT',
            'title'      => 'Texto',
            'body'       => '**Hola** mundo',
            'sort_order' => 3,
            'is_visible' => true,
        ]);

        // ─── Bloque Mermaid (keyword inicial) ──────────────────
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type'       => 'TEXT',
            'title'      => 'Flujo',
            'body'       => "graph LR\nA[Inicio] --> B[Fin]",
            'sort_order' => 4,
            'is_visible' => true,
        ]);

        // ─── Embeds: uno HTML, uno Mermaid ─────────────────────
        LmsHtmlEmbed::create([
            'activity_id'  => $activity->id,
            'section_id'   => $section->id,
            'added_by'     => $this->user->id,
            'title'        => 'Embed HTML',
            'html_content' => '<p>Contenido embebido</p>',
            'sort_order'   => 1,
            'is_visible'   => true,
        ]);
        LmsHtmlEmbed::create([
            'activity_id'  => $activity->id,
            'section_id'   => $section->id,
            'added_by'     => $this->user->id,
            'title'        => 'Embed Mermaid',
            'html_content' => "graph TB\nX[Entrada] --> Y[Salida]",
            'sort_order'   => 2,
            'is_visible'   => true,
        ]);
    }

    /** @test */
    public function print_page_renders_svg_image_blocks(): void
    {
        $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            // El SVG va crudo al DOM (branch IMAGE) — el sanitizador lo borraría.
            // viewBox/circle confirman que el <figure>/<svg> completo sobrevivió.
            ->assertSee('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">', false)
            ->assertSee('<circle cx="50" cy="50" r="40"', false);
    }

    /** @test */
    public function print_page_renders_html_blocks_without_markdown_mangling(): void
    {
        $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            // El HTML semántico va directo al DOM (branch HTML), sin markdown.
            ->assertSee('<div class="nota"><strong>Importante:</strong> leer capítulo 3.</div>', false);
    }

    /** @test */
    public function print_page_wraps_mermaid_blocks_and_preserves_text_markdown(): void
    {
        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // Mermaid → wrapper Alpine (data-mermaid-code) sobre el código extraído.
        // El atributo va escapado por Blade: `-->` → `--&gt;`.
        $this->assertStringContainsString('data-mermaid-code="graph LR', $html);
        $this->assertStringContainsString('A[Inicio] --&gt; B[Fin]', $html);

        // Embed Mermaid (type HTML + keyword) → también debe caer al wrapper.
        $this->assertStringContainsString('data-mermaid-code="graph TB', $html);

        // TEXT → markdown renderizado y entregado a KaTeX vía data-math-content.
        $this->assertStringContainsString('data-math-content="&lt;p&gt;&lt;strong&gt;Hola&lt;/strong&gt; mundo&lt;/p&gt;', $html);

        // El embed HTML se conserva (type HTML → sanitizado directo, no markdown).
        $this->assertStringContainsString('Contenido embebido', $html);
    }

    /** @test */
    public function print_page_has_simple_print_button(): void
    {
        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // Botón de imprimir con id para enganche JS
        $this->assertStringContainsString('id="btn-print"', $html);
        $this->assertStringContainsString('btn-print', $html);
        $this->assertStringContainsString('onclick="handlePrint()"', $html);
        $this->assertStringContainsString('aria-label="Imprimir o guardar PDF"', $html);

        // Función handlePrint definida
        $this->assertStringContainsString('function handlePrint()', $html);

        // Clase base del body
        $this->assertStringContainsString('<body class="lms-print">', $html);

        // NO debe contener elementos eliminados
        $this->assertStringNotContainsString('x-data="printGraphicsMode()"', $html);
        $this->assertStringNotContainsString('btn-preview', $html);
        $this->assertStringNotContainsString('togglePreview', $html);
        $this->assertStringNotContainsString('print-mode-info', $html);
        $this->assertStringNotContainsString('scaleGraphicsToMaxHeight', $html);
        $this->assertStringNotContainsString('body.print-preview', $html);
    }

    /** @test */
    public function print_page_letterhead_opens_the_first_column_of_the_book_layout(): void
    {
        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // El membrete (.doc-head) vive DENTRO del flujo de columnas (modo libro:
        // dos "páginas" por hoja horizontal). Debe aparecer después de que abre
        // .lessons-columns y antes de la primera .lesson.
        $columnsPos = strpos($html, 'class="lessons-columns"');
        $docHeadPos = strpos($html, 'class="doc-head"', $columnsPos);
        $lessonPos = strpos($html, 'class="lesson"', $columnsPos);
        $this->assertNotFalse($columnsPos, 'El contenedor .lessons-columns debe existir.');
        $this->assertNotFalse($docHeadPos, 'El membrete debe ir DENTRO de .lessons-columns.');
        $this->assertNotFalse($lessonPos, 'Debe existir al menos una .lesson.');
        $this->assertLessThan($lessonPos, $docHeadPos, 'El membrete abre la columna 1, antes de la primera lección.');

        // Configuración de impresión requerida: horizontal + dos páginas por hoja.
        $this->assertStringContainsString('size: landscape', $html);
        $this->assertStringContainsString('column-count: 2', $html);
        $this->assertStringContainsString('column-fill: auto', $html);
        $this->assertStringNotContainsString('column-fill: balance', $html);
    }

    /** @test */
    public function print_page_shows_no_content_when_no_lessons_match(): void
    {
        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso=999999')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('No hay lecciones que coincidan', $html);
        $this->assertStringNotContainsString('class="lessons-columns"', $html);
    }

    // ─── Los filtros del listado deben afectar la salida imprimible ──────

    /**
     * Construye una cadena completa (Pestudio→Grado→Sección→Pensum→Lapso→
     * Pevaluación→Actividad) para el MISMO profesor pero con contexto distinto
     * al del setUp, de modo que quede fuera de cualquier filtro del primero.
     */
    private function createSecondLesson(string $topic = 'Lección ajena a los filtros'): array
    {
        $profesor = Profesor::where('user_id', $this->user->id)->first();

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id'    => $pestudio->id,
            'grado_id'       => $grado->id,
            'asignatura_id'  => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();

        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        $activity = Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic'          => $topic,
        ]);

        return [
            'lapso'    => $lapso->id,
            'pestudio' => $pestudio->id,
            'grado'    => $grado->id,
            'seccion'  => $seccion->id,
            'topic'    => $topic,
        ];
    }

    /** @test */
    public function print_page_respects_lapso_filter(): void
    {
        $other = $this->createSecondLesson('Lección del otro lapso');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }

    /** @test */
    public function print_page_respects_pestudio_filter(): void
    {
        $other = $this->createSecondLesson('Lección del otro plan de estudio');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?pestudio='.$this->pestudioId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }

    /** @test */
    public function print_page_respects_grado_filter(): void
    {
        $other = $this->createSecondLesson('Lección del otro grado');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?grado='.$this->gradoId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }

    /** @test */
    public function print_page_respects_seccion_filter(): void
    {
        $other = $this->createSecondLesson('Lección de la otra sección');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?seccion='.$this->seccionId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }

    /** @test */
    public function print_page_respects_search_filter(): void
    {
        $other = $this->createSecondLesson('Álgebra vectorial avanzada');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?search=Lecci%C3%B3n%20de%20prueba')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }

    /** @test */
    public function print_page_respects_all_filters_combined(): void
    {
        $other = $this->createSecondLesson('Lección excluida por todo');

        $html = $this->actingAs($this->user)
            ->get('/app/profesors/lms/lessons/print?'.http_build_query([
                'lapso'    => $this->lapsoId,
                'pestudio' => $this->pestudioId,
                'grado'    => $this->gradoId,
                'seccion'  => $this->seccionId,
                'search'   => 'Lección de prueba',
            ]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other['topic'], $html);
    }
}
