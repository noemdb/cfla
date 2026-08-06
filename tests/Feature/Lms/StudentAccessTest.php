<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_can_see_home(): void
    {
        $student = User::factory()->create(['is_student' => true]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.home'));

        $response->assertStatus(200);
    }

    public function test_non_student_cannot_access_student_routes(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_unpublished_activity(): void
    {
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(404);
    }

    public function test_student_can_access_published_activity(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(200);
    }

    public function test_published_activity_has_single_root_element(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity]);

        $html = $component->html();

        // Replicar la detección de Livewire SupportMultipleRootElementDetection
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);

        $count = 0;
        foreach ($body->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                if ($child->tagName === 'script') {
                    continue;
                }
                $count++;
            }
        }

        $this->assertEquals(1, $count, 'El componente debe tener exactamente un (1) root element HTML');
    }

    public function test_mark_complete_renders_clean_celebration_overlay(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->call('markComplete');

        $html = $component->html();

        // C3: mensaje de celebración limpio (sin caracteres de reemplazo U+FFFD)
        $this->assertStringContainsString('¡Lo lograste!', $html);
        $this->assertStringContainsString('Has completado esta lección.', $html);
        $this->assertStringNotContainsString("\u{FFFD}", $html);

        // C3: el overlay usa x-show="visible" para poder animarse y auto-ocultarse
        $this->assertStringContainsString('x-show="visible"', $html);
    }

    public function test_celebration_script_auto_dismisses_and_respects_reduced_motion(): void
    {
        $source = file_get_contents(resource_path('views/livewire/student/lms/activity-view.blade.php'));

        // C3 + E2: el confeti JS no debe generarse bajo prefers-reduced-motion
        $this->assertStringContainsString("'(prefers-reduced-motion: reduce)'", $source);

        // C3: el overlay comienza oculto (visible: false) y se auto-oculta (setTimeout)
        $this->assertStringContainsString('visible: false', $source);
        $this->assertStringContainsString('this.visible = true', $source);
        $this->assertStringContainsString('setTimeout', $source);
    }

    /*
     * ------------------------------------------------------------------
     * C4 · Mascota/avatar compañero — celebración por franja etaria (F1)
     * ------------------------------------------------------------------
     */

    public function test_celebration_mascot_shows_for_5_8(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(), // 6 años
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->call('markComplete')
            ->html();

        // C4: la mascota celebra en el overlay y la franja 5–8 usa "oro puro"
        $this->assertStringContainsString('lms-mascot-body', $html);
        $this->assertStringContainsString('M 38 44.5 L 39.8 48.2', $html);
    }

    public function test_celebration_mascot_hidden_for_13_15(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => now()->subYears(14)->subMonths(1)->toDateString(), // 14 años
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->call('markComplete')
            ->html();

        // Franja 13–15: sin mascota ni en el detalle ni en el overlay
        $this->assertStringNotContainsString('lms-mascot-body', $html);
    }

    public function test_mascot_component_idle_floats_and_emphasis_is_gold(): void
    {
        $source = file_get_contents(resource_path('views/components/lms/mascot.blade.php'));

        // C4: todas las variantes flotan (idle "anima en el vacío")
        $this->assertStringContainsString('$float = true;', $source);
        // C4: "Para 5–8 es oro puro" — ojos de estrella dorados
        $this->assertStringContainsString('fill="#fbbf24"', $source);
    }

    /*
     * ------------------------------------------------------------------
     * C1 · Progreso por lección con estrellas — detalle de la lección
     * ------------------------------------------------------------------
     * El header del detalle replica las 3 estrellas de logro (C1). Con la
     * lección completada, comentario aprobado y recurso descargado: badge
     * "Completada" + 3 estrellas verdes + accesibilidad "3 de 3 logros".
     */

    public function test_activity_detail_lights_3_stars_when_all_achievements_met(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'COMPLETE',
            'created_at' => now(),
        ]);
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'RESOURCE_DOWNLOAD',
            'created_at' => now(),
        ]);
        DB::table('activity_comments')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'body' => 'Lección muy útil.',
            'is_approved' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->html();

        // Badge "Completada" + accesibilidad de las estrellas
        $this->assertStringContainsString('Completada', $html);
        $this->assertStringContainsString('3 de 3 logros', $html);

        // Las 3 estrellas del detalle (clase única w-4 h-4 text-emerald-500)
        $this->assertSame(3, substr_count($html, 'M9.049 2.927'));
        $this->assertSame(3, substr_count($html, 'class="w-4 h-4 text-emerald-500"'));
        $this->assertSame(0, substr_count($html, 'class="w-4 h-4 text-gray-300 dark:text-gray-600"'));
    }

    /*
     * ------------------------------------------------------------------
     * D1 · Pan rallado — miga de pan en el detalle de la lección
     * ------------------------------------------------------------------
     * Debajo del navbar el detalle orienta con `Inicio › Lecciones ›
     * {Materia} › {Lección}`: enlaces a Home y Lecciones, la materia como
     * texto intermedio (no hay ruta por materia) y la lección actual
     * marcada con aria-current="page". La materia sólo aparece aquí
     * (el hint del back-nav se eliminó por redundante).
     */

    public function test_activity_detail_shows_breadcrumb_trail(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->html();

        // Miga completa: Inicio › Lecciones › Test Asignatura › Test Activity
        $this->assertStringContainsString('aria-label="Ruta de navegación"', $html);
        $this->assertStringContainsString(route('student.lms.home'), $html);
        $this->assertStringContainsString(route('student.lms.lessons'), $html);
        $this->assertStringContainsString('aria-current="page"', $html);

        // La materia aparece una sola vez: en la miga (el back-nav ya no repite el hint)
        $this->assertSame(1, substr_count($html, 'Test Asignatura'));

        // Orden en el documento: Inicio < materia < lección actual
        $this->assertLessThan(
            strpos($html, 'Test Asignatura'),
            strpos($html, route('student.lms.home'))
        );
        $this->assertLessThan(
            strpos($html, 'aria-current="page"'),
            strpos($html, 'Test Asignatura')
        );
    }

    /*
     * ------------------------------------------------------------------
     * D2 · Color por materia — miga de la lección y encabezado del TOC
     * ------------------------------------------------------------------
     */

    public function test_activity_detail_colors_the_breadcrumb_subject(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->html();

        // Test Asignatura → rose. El punto de materia de la miga lleva su color.
        $key = Asignatura::colorKey('Test Asignatura');
        $this->assertStringContainsString("bg-{$key}-400", $html);

        // D2 no debe duplicar el nombre de la materia: sigue apareciendo
        // exactamente una vez (en la miga), como exige el test D1.
        $this->assertSame(1, substr_count($html, 'Test Asignatura'));
    }

    /*
     * ------------------------------------------------------------------
     * F3 · Micro-copia en lenguaje infantil — detalle de la lección
     * ------------------------------------------------------------------
     */

    public function test_activity_modo_lectura_shows_child_copy(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(), // 6 años
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        // F3 requiere secciones visibles: la CTA de inicio desplaza a la 1ª
        // sección y el empujón final aparece tras la última ($loop->last).
        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Inicio',
            'description' => null,
            'sort_order' => 1,
        ]);
        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Cierre',
            'description' => null,
            'sort_order' => 2,
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->html();

        // F1: el <body> lleva la clase modo-lectura — se verifica sobre la
        // página completa (GET), porque html() de Livewire no incluye el layout.
        $page = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity))
            ->getContent();
        $this->assertStringContainsString('flex flex-col modo-lectura', $page);

        // F3: CTA de inicio infantil, empujón final y botón "¡Lo terminé!"
        $this->assertStringContainsString('Pulsa para empezar', $html);
        $this->assertStringContainsString('¡Ya casi terminas!', $html);
        $this->assertStringContainsString('¡Lo terminé!', $html);

        // La micro-copia infantil sustituye a la etiqueta adulta
        $this->assertStringNotContainsString('Marcar como completada', $html);

        // D1: la materia sigue apareciendo exactamente una vez (en la miga)
        $this->assertSame(1, substr_count($html, 'Test Asignatura'));

        // Sin caracteres corruptos (U+FFFD)
        $this->assertStringNotContainsString("\u{FFFD}", $html);
    }

    public function test_activity_adult_mode_keeps_full_copy(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentInSeccion($activity->pevaluacion->seccion_id, [
            'date_birth' => now()->subYears(14)->subMonths(1)->toDateString(), // 14 años
        ]);

        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);

        // Aun con secciones presentes, la micro-copia infantil NO aparece en
        // la franja 13–15: depende del modo lectura, no del contenido.
        LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Introducción',
            'description' => null,
            'sort_order' => 1,
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\ActivityView::class, ['activity' => $activity])
            ->html();

        // F1: sin modo lectura — se verifica sobre la página completa (GET),
        // porque html() de Livewire no incluye el <body> del layout.
        $page = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity))
            ->getContent();
        $this->assertStringNotContainsString('flex flex-col modo-lectura', $page);

        // F3: se mantiene la etiqueta adulta y no hay micro-copia infantil
        $this->assertStringContainsString('Marcar como completada', $html);
        $this->assertStringNotContainsString('Pulsa para empezar', $html);
        $this->assertStringNotContainsString('¡Ya casi terminas!', $html);
        $this->assertStringNotContainsString('¡Lo terminé!', $html);

        // D1: la materia sigue apareciendo exactamente una vez (en la miga)
        $this->assertSame(1, substr_count($html, 'Test Asignatura'));
    }

    public function test_breadcrumb_sits_below_navbar_and_before_back_nav(): void
    {
        $source = file_get_contents(resource_path('views/livewire/student/lms/activity-view.blade.php'));

        // D1: la miga se apoya en un <ol> semántico con la lección actual marcada
        $this->assertStringContainsString('<ol class="flex items-center flex-wrap', $source);
        $this->assertStringContainsString('aria-current="page"', $source);

        // D1: está debajo del navbar (progreso de lectura) y encima del back-nav
        $progress = strpos($source, 'READING PROGRESS');
        $crumb = strpos($source, 'Ruta de navegación');
        $backNav = strpos($source, 'BACK NAV');
        $this->assertLessThan($crumb, $progress, 'La miga debe ir después del progreso (debajo del navbar)');
        $this->assertLessThan($backNav, $crumb, 'La miga debe ir antes del back-nav');
    }

    /**
     * Create a student User with Estudiant + Inscripcion in the given seccion.
     *
     * $overrides se fusionan en el factory de Estudiant (p. ej. date_birth para
     * controlar la franja etaria de la mascota, C4).
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

        // Only create records if they don't already exist (for transaction safety)
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
