<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\LessonMonitor;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
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
 * Botón "Ver / Imprimir" del monitor de LIDERAZGO y su página de impresión
 * (/app/leadership/lessons/print).
 *
 * Reutiliza Director\LessonsPrintController, pero el scope NO es global: la
 * impresión del líder solo muestra las asignaturas de las áreas con
 * leader_id = usuario (LeadershipService::scopeActivities). El membrete se
 * adapta al módulo de origen y el grupo de rutas exige rol de liderazgo
 * (IsLeadership), NO director.
 */
class LeadershipLessonsPrintTest extends TestCase
{
    use DatabaseTransactions;

    private User $leader;
    private int $profesorId;
    private int $asignaturaId;
    private int $lapsoId;
    private int $pestudioId;
    private int $gradoId;
    private int $seccionId;

    protected function setUp(): void
    {
        parent::setUp();

        // El líder supervisa SOLO las asignaturas de sus áreas asignadas.
        $this->leader = User::factory()->leadership()->create();

        $ids = $this->createLesson('Lección de prueba', true, 'PUBLISHED');
        $this->profesorId = $ids['profesor_id'];
        $this->asignaturaId = $ids['asignatura_id'];
        $this->lapsoId = $ids['lapso_id'];
        $this->pestudioId = $ids['pestudio_id'];
        $this->gradoId = $ids['grado_id'];
        $this->seccionId = $ids['seccion_id'];
    }

    /**
     * Cadena completa de fixtures (Profesor → Pestudio → Grado → Seccion →
     * Asignatura → [Área del líder + pivot] → Pensum → Lapso → Pevaluacion →
     * Activity → LmsActivitySection → [publicación LMS]).
     *
     * Cuando $attachToLeaderArea es true, la asignatura queda enlazada a un
     * ÁREA con leader_id = líder (scope de Liderazgo). Devuelve los ids por si
     * el test necesita varias lecciones (cada llamada crea su propio contexto).
     */
    private function createLesson(
        string $topic,
        bool $attachToLeaderArea = false,
        ?string $publication = 'PUBLISHED'
    ): array {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id'       => $user->id,
            'name'          => 'Carlos',
            'lastname'      => 'Méndez',
            'ci_profesor'   => '12345678',
            'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();

        if ($attachToLeaderArea) {
            // Área asignada al líder; la asignatura entra al scope del módulo
            // vía el pivot campo_conocimientos.
            $area = AreaConocimiento::create([
                'leader_id'     => $this->leader->id,
                'name'          => 'Área de Ciencias',
                'code'          => 'CIEN',
                'peducativo_id' => $pestudio->peducativo_id,
                'pestudio_id'   => $pestudio->id,
                'order'         => 1,
            ]);
            $asignatura->areasConocimiento()->attach($area->id);
        }

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
                'published_by' => $this->leader->id,
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

    // ─── Acceso, scope y membrete ───────────────────────────────────────

    /** @test */
    public function leadership_can_access_print_and_sees_only_assigned_area_lessons(): void
    {
        // Asignatura NO enlazada a un área del líder, pero PUBLICADA: el
        // aislamiento queda probado por ÁREA y no por publicación.
        $this->createLesson('Lección fuera del alcance', false, 'PUBLISHED');

        $html = $this->actingAs($this->leader)
            ->get('/app/leadership/lessons/print')
            ->assertOk()
            ->getContent();

        // Scope del módulo: solo la lección del área asignada.
        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección fuera del alcance', $html);

        // Membrete adaptado al módulo de Liderazgo.
        $this->assertStringContainsString('LIDERAZGO · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringContainsString('Liderazgo · Seguimiento de lecciones', $html);
        $this->assertStringNotContainsString('DIRECCIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
        $this->assertStringNotContainsString('COORDINACIÓN · LECCIONES LMS · CONTENIDO COMPLETO', $html);
    }

    /** @test */
    public function leadership_print_requires_leadership_role(): void
    {
        $user = User::factory()->create(['is_leadership' => false]);

        $this->actingAs($user)
            ->get('/app/leadership/lessons/print')
            ->assertForbidden();
    }

    /** @test */
    public function leadership_print_respects_asignatura_filter(): void
    {
        // Otra asignatura DENTRO del scope (área del líder), para que el
        // filtro `asignatura` sea quien decida la exclusión.
        $this->createLesson('Lección de otra asignatura', true, 'PUBLISHED');

        $html = $this->actingAs($this->leader)
            ->get('/app/leadership/lessons/print?asignatura='.$this->asignaturaId)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección de otra asignatura', $html);
        // El filtro se refleja en la cabecera del documento.
        $this->assertStringContainsString('Asignatura', $html);
    }

    /** @test */
    public function leadership_print_respects_status_filter(): void
    {
        // Lección del scope (área del líder) pero SIN publicación: la excluye
        // el filtro `status=PUBLISHED`, no el scope.
        $this->createLesson('Lección sin publicar', true, null);

        $html = $this->actingAs($this->leader)
            ->get('/app/leadership/lessons/print?status=PUBLISHED')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Lección de prueba', $html);
        $this->assertStringNotContainsString('Lección sin publicar', $html);
        $this->assertStringContainsString('Estado Publicado', $html);
    }

    // ─── Botón "Ver / Imprimir" en el monitor ──────────────────────────

    /** @test */
    public function monitor_has_ver_imprimir_button_carrying_active_filters(): void
    {
        // URL esperada construida con el mismo helper que la vista: robusta al
        // APP_URL del entorno. El orden de set() respeta la cascada del
        // componente (updatedPestudioId/updatedGradoId limpian grado/sección).
        $expected = route('app.leadership.lessons.print', [
            'lapso'    => (string) $this->lapsoId,
            'pestudio' => (string) $this->pestudioId,
            'grado'    => (string) $this->gradoId,
            'seccion'  => (string) $this->seccionId,
            'profesor' => (string) $this->profesorId,
            'status'   => 'PUBLISHED',
            'search'   => 'Lección de prueba',
        ]);
        // En el atributo HTML el `&` sale escapado como `&amp;`.
        $expected = str_replace('&', '&amp;', $expected);

        Livewire::actingAs($this->leader)
            ->test(LessonMonitor::class)
            ->set('lapso_id', $this->lapsoId)
            ->set('pestudio_id', $this->pestudioId)
            ->set('grado_id', $this->gradoId)
            ->set('seccion_id', $this->seccionId)
            ->set('profesor_id', $this->profesorId)
            ->set('filter_published', true)
            ->set('search', 'Lección de prueba')
            ->assertSee('Ver / Imprimir', false)
            // El href lleva los filtros activos como query string.
            ->assertSee($expected, false);
    }

    /** @test */
    public function monitor_ver_imprimir_button_targets_print_in_new_tab(): void
    {
        Livewire::actingAs($this->leader)
            ->test(LessonMonitor::class)
            ->assertSee('target="_blank"', false)
            ->assertSee('app/leadership/lessons/print', false);
    }

    // ─── La vista de impresión respeta el invariante de SOLO LECTURA ────

    /** @test */
    public function leadership_print_view_has_no_write_controls(): void
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
