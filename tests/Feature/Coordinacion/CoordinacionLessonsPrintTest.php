<?php

namespace Tests\Feature\Coordinacion;

use App\Livewire\Coordinacion\LessonList;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Peducativo;
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
 * Botón "Ver / Imprimir" del listado de COORDINACIÓN y su página de impresión
 * (/app/coordinacion/lecciones/print).
 *
 * Reutiliza Director\LessonsPrintController, pero el scope NO es global: la
 * impresión del coordinador solo muestra los pestudios de los peducativos con
 * manager_id = usuario y planning_module=1 (CoordinacionScopeService), y SOLO
 * actividades con publicación LMS (mismo criterio que LessonList en render,
 * replicado por el controlador con whereHas('lmsPublication')). El membrete se
 * adapta al módulo de origen y el grupo de rutas exige rol de coordinación
 * (IsCoordinacion), NO director.
 */
class CoordinacionLessonsPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $coord;
    private int $profesorId;
    private int $lapsoId;
    private int $pestudioId;
    private Pestudio $pestudio;

    protected function setUp(): void
    {
        parent::setUp();

        // El coordinador supervisa SOLO los pestudios de sus peducativos.
        $this->coord = User::factory()->coordinacion()->create();

        $peducativo = Peducativo::factory()->create([
            'manager_id'    => $this->coord->id,
            'status_active' => 'true',
        ]);
        $this->pestudio = Pestudio::factory()->create([
            'peducativo_id'   => $peducativo->id,
            'status_active'   => 'true',
            'planning_module' => 1, // la factory usa faker->boolean(); forzarlo a 1
        ]);
        $this->pestudioId = $this->pestudio->id;

        $ids = $this->createLesson('Lección de prueba', $this->pestudio, 'PUBLISHED');
        $this->profesorId = $ids['profesor_id'];
        $this->lapsoId = $ids['lapso_id'];
    }

    /**
     * Cadena completa de fixtures (Profesor → Grado → Seccion → Asignatura →
     * Pensum → Lapso → Pevaluacion → Activity → LmsActivitySection →
     * [publicación LMS]) sobre el $pestudio del scope del coordinador.
     */
    private function createLesson(string $topic, Pestudio $pestudio, ?string $publication = null): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id'       => $user->id,
            'name'          => 'Carlos',
            'lastname'      => 'Méndez',
            'ci_profesor'   => '12345678',
            'status_active' => 'true',
        ]);

        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id'   => $pestudio->id,
            'grado_id'      => $grado->id,
            'asignatura_id' => $asignatura->id,
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

        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title'       => 'Sección 1',
            'sort_order'  => 1,
            'is_visible'  => true,
        ]);

        if ($publication) {
            LmsActivityPublication::factory()->create([
                'activity_id'  => $activity->id,
                'published_by' => $this->coord->id,
                'status'       => $publication,
                'publish_at'   => now(),
                'published_at' => now(),
            ]);
        }

        return [
            'profesor_id'   => $profesor->id,
            'asignatura_id' => $asignatura->id,
            'lapso_id'      => $lapso->id,
            'pestudio_id'   => $pestudio->id,
            'grado_id'      => $grado->id,
            'seccion_id'    => $seccion->id,
        ];
    }

    /**
     * Lección PUBLISHED en un pestudio planning_module=1 cuyo peducativo NO
     * tiene manager_id = coordinador: fuera del scope, pero con publicación
     * para que el aislamiento quede probado por SCOPE y no por publicación.
     */
    private function createOutOfScopeLesson(string $topic = 'Lección fuera del alcance'): void
    {
        $peducativo = Peducativo::factory()->create(['status_active' => 'true']); // manager_id null
        $pestudio = Pestudio::factory()->create([
            'peducativo_id'   => $peducativo->id,
            'status_active'   => 'true',
            'planning_module' => 1,
        ]);

        $this->createLesson($topic, $pestudio, 'PUBLISHED');
    }

    // ─── Acceso, scope y membrete ───────────────────────────────────────

    /** @test */
    public function coordinacion_can_access_print_and_sees_only_manager_scope_lessons(): void
    {
        $this->createOutOfScopeLesson();

        $html = $this->actingAs($this->coord)
            ->get('/app/coordinacion/lecciones/print')
            ->assertOk()
            ->getContent();

        // Scope del módulo: solo la lección del pestudio gestionado.
        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección fuera del alcance', $html);

        // Membrete adaptado al módulo de Coordinación.
        $this->assertStringContainsString('COORDINACIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringContainsString('Coordinación · Lecciones LMS', $html);
        $this->assertStringNotContainsString('DIRECCIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringNotContainsString('LIDERAZGO · LECCIONES LMS · CONTENIDO COMPLETO', $html);
    }

    /** @test */
    public function coordinacion_print_requires_coordinacion_role(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/app/coordinacion/lecciones/print')
            ->assertForbidden();
    }

    /** @test */
    public function coordinacion_print_only_includes_activities_with_lms_publication(): void
    {
        // Lección DENTRO del scope (mismo pestudio del coordinador) pero SIN
        // publicación LMS: la excluye el whereHas('lmsPublication').
        $this->createLesson('Lección sin publicación LMS', $this->pestudio, null);

        $html = $this->actingAs($this->coord)
            ->get('/app/coordinacion/lecciones/print')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección sin publicación LMS', $html);
    }

    /** @test */
    public function coordinacion_print_respects_status_filter(): void
    {
        // Lección del scope con publicación SCHEDULED: la excluye el filtro
        // `status=PUBLISHED`, no el scope.
        $this->createLesson('Lección programada', $this->pestudio, 'SCHEDULED');

        $html = $this->actingAs($this->coord)
            ->get('/app/coordinacion/lecciones/print?status=PUBLISHED')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección programada', $html);
        $this->assertStringContainsString('Estado Publicado', $html);
    }

    // ─── Botón "Ver / Imprimir" en el listado ──────────────────────────

    /** @test */
    public function monitor_has_ver_imprimir_button_carrying_active_filters(): void
    {
        // URL esperada construida con el mismo helper que la vista: robusta al
        // APP_URL del entorno.
        $expected = route('app.coordinacion.lessons.print', [
            'lapso'    => (string) $this->lapsoId,
            'pestudio' => (string) $this->pestudioId,
            'profesor' => (string) $this->profesorId,
            'status'   => 'PUBLISHED',
            'search'   => 'Lección de prueba',
        ]);
        // En el atributo HTML el `&` sale escapado como `&amp;`.
        $expected = str_replace('&', '&amp;', $expected);

        Livewire::actingAs($this->coord)
            ->test(LessonList::class)
            ->set('lapsoId', $this->lapsoId)
            ->set('pestudioId', $this->pestudioId)
            ->set('profesorId', $this->profesorId)
            ->set('filterStatus', 'PUBLISHED')
            ->set('search', 'Lección de prueba')
            ->assertSee('Ver / Imprimir', false)
            // El href lleva los filtros activos como query string.
            ->assertSee($expected, false);
    }

    /** @test */
    public function monitor_ver_imprimir_button_targets_print_in_new_tab(): void
    {
        Livewire::actingAs($this->coord)
            ->test(LessonList::class)
            ->assertSee('target="_blank"', false)
            ->assertSee('app/coordinacion/lecciones/print', false);
    }

    // ─── La vista de impresión respeta el invariante de SOLO LECTURA ────

    /** @test */
    public function coordinacion_print_view_has_no_write_controls(): void
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
