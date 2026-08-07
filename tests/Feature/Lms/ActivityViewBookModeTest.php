<?php

namespace Tests\Feature\Lms;

use App\Livewire\Student\Lms\ActivityView;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Modo libro (flipbook) de una lección LMS — blueprint/estudiant/page-flip-adapted.md §5–6.
 *
 * El toggle "Libro" es opt-in y queda GATEADO por $flipEnabled (ActivityView::mount):
 * se ofrece solo con ≥2 secciones visibles, publicación completa (no preview) y
 * fuera de la franja de lectura asistida (5–8 años, modoLectura).
 */
class ActivityViewBookModeTest extends TestCase
{
    use DatabaseTransactions;

    /* ------------------------------------------------------------------
     * §5.2 · Gate del toggle — ausencia del modo libro
     * ------------------------------------------------------------------ */

    public function test_book_toggle_hidden_with_single_visible_section(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        // 1 sola sección visible → modo libro no aplica (requiere ≥2).
        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Inicio',
            'sort_order' => 1,
        ]);
        $this->publishFull($activity);

        $component = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity]);

        $this->assertFalse($component->get('flipEnabled'));
        $html = $component->html();
        // El script lessonBook() se renderiza siempre y cita 'lms-flipbook-root'
        // en getElementById: la ausencia se sondea sobre el DOM (id="..." y x-data).
        $this->assertStringNotContainsString('aria-label="Modo de lectura"', $html);
        $this->assertStringNotContainsString('id="lms-flipbook-root"', $html);
        $this->assertStringNotContainsString('x-data="lessonBook()"', $html);
    }

    public function test_book_toggle_hidden_in_preview_mode(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        // 2 secciones en BD, pero publicación en vista previa (publish_at futuro):
        // el mount recorta a la 1ª sección → flipEnabled=false.
        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishPreview($activity);

        $component = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity]);

        $this->assertTrue($component->get('isPreview'));
        $this->assertFalse($component->get('flipEnabled'));
        $html = $component->html();
        $this->assertStringNotContainsString('aria-label="Modo de lectura"', $html);
        $this->assertStringNotContainsString('id="lms-flipbook-root"', $html);
        $this->assertStringNotContainsString('x-data="lessonBook()"', $html);
    }

    public function test_book_toggle_hidden_in_modo_lectura(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento6(),
        ]);

        // 2 secciones y publicación completa, pero estudiante de 6 años → modo lectura.
        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishFull($activity);

        $component = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity]);

        $this->assertTrue($component->get('modoLectura'));
        $this->assertFalse($component->get('flipEnabled'));
        $html = $component->html();
        $this->assertStringNotContainsString('aria-label="Modo de lectura"', $html);
        $this->assertStringNotContainsString('id="lms-flipbook-root"', $html);
        $this->assertStringNotContainsString('x-data="lessonBook()"', $html);
    }

    /* ------------------------------------------------------------------
     * §5.2 · Gate del toggle — presencia del modo libro
     * ------------------------------------------------------------------ */

    public function test_book_toggle_shown_for_full_publication_with_two_sections(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishFull($activity);

        $component = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity]);

        $this->assertTrue($component->get('flipEnabled'));
        $html = $component->html();
        $this->assertStringContainsString('aria-label="Modo de lectura"', $html);
        $this->assertStringContainsString('Libro', $html);
        $this->assertStringContainsString('id="lms-flipbook-root"', $html);
        $this->assertStringContainsString('x-data="lessonBook()"', $html);
    }

    /* ------------------------------------------------------------------
     * §5.1 · Páginas del libro = secciones visibles (ordenadas por sort_order)
     * ------------------------------------------------------------------ */

    public function test_book_pages_match_visible_sections(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $titles = ['Inicio', 'Desarrollo', 'Cierre'];
        $this->addSections($activity, $titles);
        $this->publishFull($activity);

        $html = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity])
            ->html();

        // Una página por sección visible, cada una con su <h2> (títulos ASCII:
        // loadHTML() sin prefijo XML trata el input como ISO-8859-1).
        $this->assertEquals($titles, $this->bookSectionTitles($html));
    }

    /* ------------------------------------------------------------------
     * §5.1 · Contenedor del libro: wire:ignore plano (no .self)
     * ------------------------------------------------------------------ */

    public function test_flipbook_root_uses_plain_wire_ignore(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishFull($activity);

        $html = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity])
            ->html();

        // StPageFlip reescribe el DOM interno: el contenedor debe usar wire:ignore
        // plano (no .self, que solo blindaría el nodo raíz y Livewire diffearía el interior).
        $this->assertStringContainsString('wire:ignore x-show="!loadError"', $html);
        $this->assertStringContainsString('id="lms-flipbook-root"', $html);
        $this->assertStringNotContainsString('wire:ignore.self', $html);
    }

    /* ------------------------------------------------------------------
     * §6.1 · Mermaid en modo libro: placeholder enlazado (no el diagrama)
     * ------------------------------------------------------------------ */

    public function test_mermaid_shows_placeholder_in_book_mode(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $section = LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Desarrollo',
            'sort_order' => 1,
        ]);
        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Cierre',
            'sort_order' => 2,
        ]);
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'title' => 'Diagrama',
            'body' => '<div class="mermaid">graph TD' . "\n" . 'A-->B</div>',
            'sort_order' => 1,
        ]);
        $this->publishFull($activity);

        $html = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity])
            ->html();

        // En modo libro se ofrece el placeholder en ámbar con enlace a la sección,
        // no el canvas de Mermaid (el embed wire:ignore sí aparece en modo scroll).
        $this->assertStringContainsString('📊 Este diagrama se ve mejor en modo deslizar.', $html);
        $this->assertStringContainsString('Ir a la sección', $html);
        $this->assertStringContainsString('@click="openSection(' . $section->id . ')"', $html);
    }

    /* ------------------------------------------------------------------
     * §6.2 · Completado → el libro conmuta su CTA a "✓ Completada"
     * ------------------------------------------------------------------ */

    public function test_completing_activity_flips_book_cta(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishFull($activity);

        $component = Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity]);

        // Antes de completar: la barra del libro arranca con data-completed="0"
        // (Alpine conmuta el botón "Marcar…" ↔ "✓ Completada" mediante x-show).
        $this->assertStringContainsString('data-completed="0"', $component->html());

        // Completar la lección → re-render → el libro recibe data-completed="1",
        // que en el navegador muestra "✓ Completada" en la barra del libro (§6.2).
        $component->call('markComplete')
            ->assertSet('completed', true);

        $html = $component->html();
        $this->assertStringContainsString('data-completed="1"', $html);
        $this->assertStringNotContainsString('data-completed="0"', $html);
        $this->assertStringContainsString('✓ Completada', $html);
    }

    /* ------------------------------------------------------------------
     * §6.3 · Completar dispara el evento 'activity-completed'
     * ------------------------------------------------------------------ */

    public function test_mark_complete_dispatches_activity_completed(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => $this->fechaNacimiento14(),
        ]);

        $this->addSections($activity, ['Inicio', 'Desarrollo']);
        $this->publishFull($activity);

        Livewire::actingAs($student)
            ->test(ActivityView::class, ['activity' => $activity])
            ->call('markComplete')
            ->assertSet('completed', true)
            ->assertDispatched('activity-completed');
    }

    /* ------------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------------ */

    /** Fecha de nacimiento de un estudiante de ~14 años (fuera de modo lectura). */
    private function fechaNacimiento14(): string
    {
        return now()->subYears(14)->subMonths(1)->toDateString();
    }

    /** Fecha de nacimiento de un estudiante de ~6 años (franja 5–8 → modo lectura). */
    private function fechaNacimiento6(): string
    {
        return now()->subYears(6)->subMonths(1)->toDateString();
    }

    /** Publica la actividad completa (PUBLISHED y publish_at en el pasado). */
    private function publishFull(Activity $activity): void
    {
        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);
    }

    /** Publicación en vista previa: PUBLISHED pero con publish_at en el futuro. */
    private function publishPreview(Activity $activity): void
    {
        LmsActivityPublication::create([
            'activity_id' => $activity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => now()->addHour(),
            'published_at' => now(),
        ]);
    }

    /** Crea las secciones visibles dadas (por orden) y devuelve sus ids. */
    private function addSections(Activity $activity, array $titles): array
    {
        $ids = [];
        foreach ($titles as $i => $title) {
            $ids[] = LmsActivitySection::create([
                'activity_id' => $activity->id,
                'title' => $title,
                'sort_order' => $i + 1,
            ])->id;
        }

        return $ids;
    }

    /** Títulos de sección renderizados como páginas dentro de #lms-flipbook-root. */
    private function bookSectionTitles(string $html): array
    {
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $root = $xpath->query('//div[@id="lms-flipbook-root"]')->item(0);
        if (! $root) {
            return [];
        }

        $titles = [];
        foreach ($xpath->query('.//div[contains(@class, "stf__item")]//h2', $root) as $h2) {
            $titles[] = trim($h2->textContent);
        }

        return $titles;
    }

    /**
     * Create a student User with Estudiant + Inscripcion in the given seccion.
     *
     * $overrides se fusionan en el factory de Estudiant (p. ej. date_birth para
     * controlar la franja etaria / modo lectura).
     */
    private function createStudentInSeccion(int $seccionId, array $overrides = []): User
    {
        $user = User::factory()->create(['is_student' => true]);

        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan',
            'description' => 'Test plan description',
            'observations' => 'Test observations',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estudiant = \App\Models\app\Learner\Estudiant::factory()->create(array_merge([
            'user_id' => $user->id,
            'planpago_id' => $planpagoId,
        ], $overrides));

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionId,
        ]);

        return $user;
    }

    /**
     * Create a minimal activity with the entire FK chain.
     */
    private function createMinimalActivity(array $overrides = []): Activity
    {
        // Build the FK chain manually since the deep factory chain has missing factories.
        // pevaluacion -> pensum -> pestudio -> peducativo -> pescolar
        //              -> grado       -> escala
        //              -> seccion
        //              -> lapso

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST',
            'code_sm' => 'LT',
            'name' => 'Test Lapso',
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale',
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution',
            'legalname' => 'Test Institution Legal',
            'rif_institution' => 'J-12345678-9',
            'email_institution' => 'test@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar',
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE',
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => 'PEST-TEST',
            'name' => 'Test Plan de Estudio',
            'scale' => $escalaId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado',
            'code' => 'GR-TEST',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-TEST',
            'name' => 'Test Asignatura',
            'tescala' => $escalaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId,
            'grado_id' => $gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678',
            'ci_profesor' => '12345678',
            'name' => 'Profesor Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Test objetivo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Activity::create(array_merge([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Test Activity',
            'references' => 'Refs',
            'teaching' => 'Teaching',
            'learning' => 'Learning',
            'observations' => 'Obs',
        ], $overrides));
    }
}
