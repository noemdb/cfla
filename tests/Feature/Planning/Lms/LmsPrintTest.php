<?php

namespace Tests\Feature\Planning\Lms;

use App\Livewire\Planning\Lms\LmsMonitor;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Botón "Ver / Imprimir" del monitor LMS de PLANIFICACIÓN y su página de
 * impresión (/app/planning/lms/print).
 *
 * Reutiliza Director\LessonsPrintController (misma semántica de filtros y el
 * mismo scope global de supervisión de la institución), pero el membrete se
 * adapta al módulo de origen y el grupo de rutas exige rol de planificador
 * (IsPlanner), NO director: un planificador no puede llegar a la ruta de la
 * Dirección y viceversa.
 */
class LmsPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $planner;
    private int $profesorAId;
    private int $asignaturaId;
    private int $lapsoId;
    private int $pestudioId;
    private int $gradoId;
    private int $seccionId;

    protected function setUp(): void
    {
        parent::setUp();

        // El rol de planificador supervisa la institución (no es profesor).
        $this->planner = User::factory()->create(['is_planner' => true]);

        // ─── Profesor A: Carlos Méndez — lección PUBLICADA (contenido) ────
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
        $this->asignaturaId = $asignatura->id;
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

        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title'       => 'Sección 1',
            'sort_order'  => 1,
            'is_visible'  => true,
        ]);

        // Publicación PUBLISHED → cubre el filtro `status` del monitor.
        LmsActivityPublication::factory()->create([
            'activity_id'  => $activity->id,
            'published_by' => $this->planner->id,
            'status'       => 'PUBLISHED',
            'publish_at'   => now(),
            'published_at' => now(),
        ]);
    }

    /**
     * Lección de OTRO profesor (Ana Gómez) con contexto totalmente distinto y
     * SIN publicación, de modo que quede fuera de los filtros asignatura/status.
     */
    private function createSecondLesson(string $topic = 'Lección ajena a los filtros'): void
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
    }

    // ─── Acceso y contexto del membrete ─────────────────────────────────

    /** @test */
    public function planner_can_access_lms_print_and_sees_all_lessons(): void
    {
        $this->createSecondLesson('Lección de Ana Gómez');

        $html = $this->actingAs($this->planner)
            ->get('/app/planning/lms/print')
            ->assertOk()
            ->getContent();

        // Scope global: el monitor supervisa TODA la institución.
        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringContainsString('Lección de Ana Gómez', $html);

        // Membrete adaptado al módulo de Planificación.
        $this->assertStringContainsString('PLANIFICACIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringContainsString('Planificación · Monitor LMS', $html);
    }

    /** @test */
    public function lms_print_requires_planner_role(): void
    {
        $user = User::factory()->create(['is_planner' => false]);

        $this->actingAs($user)
            ->get('/app/planning/lms/print')
            ->assertForbidden();
    }

    /** @test */
    public function director_print_keeps_director_letterhead(): void
    {
        $director = User::factory()->director()->create();

        $html = $this->actingAs($director)
            ->get('/app/director/lecciones/print')
            ->assertOk()
            ->getContent();

        // El membrete de la Dirección no cambia (el contexto se deduce de la ruta).
        $this->assertStringContainsString('DIRECCIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringNotContainsString('PLANIFICACIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
    }

    /** @test */
    public function lms_print_respects_asignatura_filter(): void
    {
        $this->createSecondLesson('Lección de otra asignatura');

        $html = $this->actingAs($this->planner)
            ->get('/app/planning/lms/print?asignatura='.$this->asignaturaId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección de otra asignatura', $html);
        // El filtro se refleja en la cabecera del documento.
        $this->assertStringContainsString('Asignatura', $html);
    }

    /** @test */
    public function lms_print_respects_status_filter(): void
    {
        $this->createSecondLesson('Lección sin publicación');

        $html = $this->actingAs($this->planner)
            ->get('/app/planning/lms/print?status=PUBLISHED')
            ->assertOk()
            ->getContent();

        // Solo la lección PUBLICADA queda; la segunda (sin publicación) no.
        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección sin publicación', $html);
        $this->assertStringContainsString('Estado Publicado', $html);
    }

    // ─── Botón "Ver / Imprimir" en el monitor ──────────────────────────

    /** @test */
    public function monitor_has_ver_imprimir_button_carrying_active_filters(): void
    {
        // URL esperada construida con el mismo helper que la vista: robusta al
        // APP_URL del entorno (localhost vs. cfla.local).
        $expected = route('app.planning.lms.print', [
            'asignatura' => (string) $this->asignaturaId,
            'status'     => 'PUBLISHED',
        ]);
        // En el atributo HTML el `&` sale escapado como `&amp;`.
        $expected = str_replace('&', '&amp;', $expected);

        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->set('filterAsignatura', (string) $this->asignaturaId)
            ->set('filterStatus', 'PUBLISHED')
            ->assertSee('Ver / Imprimir', false)
            // El href lleva los filtros activos como query string.
            ->assertSee($expected, false);
    }

    /** @test */
    public function monitor_ver_imprimir_button_targets_print_in_new_tab(): void
    {
        Livewire::actingAs($this->planner)
            ->test(LmsMonitor::class)
            ->assertSee('target="_blank"', false)
            ->assertSee('app/planning/lms/print', false);
    }

    // ─── La vista de impresión respeta el invariante de SOLO LECTURA ────

    /** @test */
    public function lms_print_view_has_no_write_controls(): void
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
