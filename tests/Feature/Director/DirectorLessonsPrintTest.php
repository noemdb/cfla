<?php

namespace Tests\Feature\Director;

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

/**
 * Página de impresión de lecciones LMS de la DIRECCIÓN.
 *
 * A diferencia del profesor (acotado a su propia carga), la dirección supervisa
 * TODA la institución: la página muestra las lecciones de TODOS los profesores
 * y el filtro `profesor` permite acotar el universo. Todo el módulo es SOLO
 * LECTURA (misma semántica de filtros que LessonList).
 */
class DirectorLessonsPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $director;
    private int $profesorAId;
    private int $lapsoId;
    private int $pestudioId;
    private int $gradoId;
    private int $seccionId;

    protected function setUp(): void
    {
        parent::setUp();

        // El rol de dirección no necesita ser profesor: supervisa la institución.
        $this->director = User::factory()->director()->create();

        // ─── Profesor A: Carlos Méndez — lección principal (contenido completo)
        $userA = User::factory()->create();
        $profesorA = Profesor::create([
            'user_id'       => $userA->id,
            'name'          => 'Carlos',
            'lastname'      => 'Méndez',
            'ci_profesor'   => '12345678',
            'status_active' => 'true',
        ]);
        $this->profesorAId = $profesorA->id;

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
            'profesor_id' => $profesorA->id,
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
            'added_by'     => $userA->id,
            'title'        => 'Embed HTML',
            'html_content' => '<p>Contenido embebido</p>',
            'sort_order'   => 1,
            'is_visible'   => true,
        ]);
        LmsHtmlEmbed::create([
            'activity_id'  => $activity->id,
            'section_id'   => $section->id,
            'added_by'     => $userA->id,
            'title'        => 'Embed Mermaid',
            'html_content' => "graph TB\nX[Entrada] --> Y[Salida]",
            'sort_order'   => 2,
            'is_visible'   => true,
        ]);
    }

    /**
     * Lección de OTRO profesor (Ana Gómez) con contexto totalmente distinto,
     * de modo que quede fuera de cualquier filtro del set del setUp.
     */
    private function createSecondLesson(string $topic = 'Lección ajena a los filtros'): int
    {
        $userB = User::factory()->create();
        $profesorB = Profesor::create([
            'user_id'       => $userB->id,
            'name'          => 'Ana',
            'lastname'      => 'Gómez',
            'ci_profesor'   => '87654321',
            'status_active' => 'true',
        ]);

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
            'profesor_id' => $profesorB->id,
            'pensum_id'   => $pensum->id,
            'seccion_id'  => $seccion->id,
            'lapso_id'    => $lapso->id,
        ]);

        Activity::factory()->create([
            'pevaluacion_id' => $pevaluacion->id,
            'topic'          => $topic,
        ]);

        return $profesorB->id;
    }

    // ─── Visión global: TODOS los profesores ────────────────────────────

    /** @test */
    public function director_sees_lessons_from_all_profesors(): void
    {
        $this->createSecondLesson('Lección de Ana Gómez');

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print')
            ->assertOk()
            ->getContent();

        // Sin filtros: la dirección ve el universo completo de la institución.
        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringContainsString('Lección de Ana Gómez', $html);
    }

    /** @test */
    public function director_print_shows_profesor_name_in_lesson_meta(): void
    {
        $this->createSecondLesson('Lección de Ana Gómez');

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print')
            ->assertOk()
            ->getContent();

        // Cada lección muestra su responsable (la dirección no tiene profesor propio).
        $this->assertStringContainsString('Méndez, Carlos', $html);
        $this->assertStringContainsString('Gómez, Ana', $html);
    }

    // ─── Render de contenidos (idéntico al profesor, adaptado a la ruta) ─

    /** @test */
    public function director_print_renders_svg_image_blocks(): void
    {
        $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            // El SVG va crudo al DOM (branch IMAGE) — el sanitizador lo borraría.
            ->assertSee('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">', false)
            ->assertSee('<circle cx="50" cy="50" r="40"', false);
    }

    /** @test */
    public function director_print_renders_html_blocks_without_markdown_mangling(): void
    {
        $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->assertSee('<div class="nota"><strong>Importante:</strong> leer capítulo 3.</div>', false);
    }

    /** @test */
    public function director_print_wraps_mermaid_blocks_and_preserves_text_markdown(): void
    {
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // Mermaid → wrapper Alpine (data-mermaid-code) sobre el código extraído.
        $this->assertStringContainsString('data-mermaid-code="graph LR', $html);
        $this->assertStringContainsString('A[Inicio] --&gt; B[Fin]', $html);
        $this->assertStringContainsString('data-mermaid-code="graph TB', $html);

        // TEXT → markdown renderizado y entregado a KaTeX vía data-math-content.
        $this->assertStringContainsString('data-math-content="&lt;p&gt;&lt;strong&gt;Hola&lt;/strong&gt; mundo&lt;/p&gt;', $html);

        // El embed HTML se conserva (type HTML → sanitizado directo, no markdown).
        $this->assertStringContainsString('Contenido embebido', $html);
    }

    /** @test */
    public function director_print_bounds_tall_mermaid_diagrams_to_one_page(): void
    {
        // Bug 2.7.3: un diagrama grande podía ocupar más de una página en
        // vertical. El CSS de impresión lo acota por ALTURA dentro de su
        // columna (escala por max-height pt/vh + overflow:hidden como red de
        // seguridad). Como el diagrama nunca sale del flujo de 2 columnas
        // (2.7.5), el tope de columna es el único que aplica.
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // Tope de altura en columna (fallback pt + vh adaptativo).
        $this->assertStringContainsString('max-height:430pt !important;max-height:70vh !important;', $html);
        // Red de seguridad + marco reforzado (recorta si el escalado no llega).
        $this->assertStringContainsString('.mermaid-wrap{position:relative;overflow:hidden;border-color:#94a3b8;}', $html);
    }

    /** @test */
    public function director_print_frames_mermaid_diagrams_within_column(): void
    {
        // Bug 2.7.5: los diagramas ocupaban demasiado espacio porque la capa E1
        // (column-span:all) los sacaba del flujo de 2 columnas. Ahora el marco
        // .mermaid-wrap y max-width:100% los ENMARCAN dentro de su columna: el
        // diagrama nunca cruza a la otra columna ni ocupa la página completa.
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        // El ancho del SVG queda limitado al ancho de la columna (el
        // !important gana al style inline "max-width:<naturalWidth>px" que
        // mermaid.render() incrusta).
        $this->assertStringContainsString('.mermaid-wrap svg{display:block;max-width:100% !important;height:auto !important;margin:0 auto;overflow:visible !important;}', $html);
        // El marco enmarca el diagrama dentro de la columna.
        $this->assertStringContainsString('.mermaid-wrap{margin:6px 0;padding:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;break-inside:avoid;page-break-inside:avoid;}', $html);

        // SIN spanner: no existe column-span:all, ni la clase mermaid-wide, ni
        // su etiqueta/altura/ancho asociados.
        $this->assertStringNotContainsString('column-span:all', $html);
        $this->assertStringNotContainsString('mermaid-wide', $html);
        $this->assertStringNotContainsString('DIAGRAMA · VISTA AMPLIA', $html);
    }

    /** @test */
    public function director_print_has_simple_print_button(): void
    {
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('id="btn-print"', $html);
        $this->assertStringContainsString('onclick="handlePrint()"', $html);
        $this->assertStringContainsString('aria-label="Imprimir o guardar PDF"', $html);
        $this->assertStringContainsString('function handlePrint()', $html);
        $this->assertStringContainsString('<body class="lms-print">', $html);
    }

    /** @test */
    public function director_print_letterhead_opens_the_first_column_of_the_book_layout(): void
    {
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        $columnsPos = strpos($html, 'class="lessons-columns"');
        $docHeadPos = strpos($html, 'class="doc-head"', $columnsPos);
        $lessonPos = strpos($html, 'class="lesson"', $columnsPos);
        $this->assertNotFalse($columnsPos, 'El contenedor .lessons-columns debe existir.');
        $this->assertNotFalse($docHeadPos, 'El membrete debe ir DENTRO de .lessons-columns.');
        $this->assertNotFalse($lessonPos, 'Debe existir al menos una .lesson.');
        $this->assertLessThan($lessonPos, $docHeadPos, 'El membrete abre la columna 1, antes de la primera lección.');

        $this->assertStringContainsString('size: landscape', $html);
        $this->assertStringContainsString('column-count: 2', $html);
        $this->assertStringContainsString('column-fill: auto', $html);
        $this->assertStringNotContainsString('column-fill: balance', $html);
    }

    /** @test */
    public function director_print_shows_no_content_when_no_lessons_match(): void
    {
        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso=999999')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('No hay lecciones que coincidan', $html);
        $this->assertStringNotContainsString('class="lessons-columns"', $html);
    }

    // ─── Filtros (el director puede acotar el universo con los mismos
    //     filtros del listado, incluido el nuevo filtro por profesor) ─────

    /** @test */
    public function director_print_respects_profesor_filter(): void
    {
        $other = 'Lección excluida por el filtro de profesor';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?profesor='.$this->profesorAId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
        // El profesor filtrado se refleja en la cabecera del documento.
        $this->assertStringContainsString('Profesor Méndez, Carlos', $html);
    }

    /** @test */
    public function director_print_respects_lapso_filter(): void
    {
        $other = 'Lección del otro lapso';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?lapso='.$this->lapsoId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    /** @test */
    public function director_print_respects_pestudio_filter(): void
    {
        $other = 'Lección del otro plan de estudio';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?pestudio='.$this->pestudioId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    /** @test */
    public function director_print_respects_grado_filter(): void
    {
        $other = 'Lección del otro grado';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?grado='.$this->gradoId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    /** @test */
    public function director_print_respects_seccion_filter(): void
    {
        $other = 'Lección de la otra sección';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?seccion='.$this->seccionId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    /** @test */
    public function director_print_respects_search_filter(): void
    {
        $other = 'Álgebra vectorial avanzada';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?search=Lecci%C3%B3n%20de%20prueba')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    /** @test */
    public function director_print_respects_all_filters_combined(): void
    {
        $other = 'Lección excluida por todo';
        $this->createSecondLesson($other);

        $html = $this->actingAs($this->director)
            ->get('/app/director/lecciones/print?'.http_build_query([
                'lapso'    => $this->lapsoId,
                'pestudio' => $this->pestudioId,
                'grado'    => $this->gradoId,
                'seccion'  => $this->seccionId,
                'profesor' => $this->profesorAId,
            ]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString($other, $html);
    }

    // ─── La vista de impresión respeta el invariante de SOLO LECTURA ────

    /** @test */
    public function director_print_view_has_no_write_controls(): void
    {
        $source = file_get_contents(
            resource_path('views/director/lessons-print.blade.php')
        );

        $this->assertStringNotContainsString('<form', $source);
        $this->assertStringNotContainsString('</form>', $source);
        $this->assertStringNotContainsString('wire:submit', $source);
        $this->assertStringNotContainsString('wire:click', $source);
        $this->assertStringNotContainsString('method="post"', $source);
        $this->assertStringNotContainsString('@csrf', $source);
    }
}
