<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Ficha de impresión de la lección del estudiante
 * (GET /app/estudiante/activity/{activity}/print → ActivityPrintController).
 *
 * Cubre los ajustes de fixtures reales frente a atributos inexistentes:
 *  - Fechas usan Activity.finicial/ffinal (antes pevaluacion.fechainicio,
 *    columna que NO existe → salía "Fechas: · al" vacío).
 *  - "Eje:" usa Activity.thematic (antes pevaluacion.tema, inexistente).
 *  - Membrete con el nombre real de la institución (antes genérico).
 *  - Estado traducido a español + clase CSS (antes PUBLISHED crudo).
 *  - Membrete/pie con el nombre del estudiante (antes "Estudiante").
 *  - Consistencia de acceso: solo la sección del estudiante (antes solo
 *    status de publicación → se podía imprimir la lección de otra sección).
 */
class StudentLessonsPrintTest extends TestCase
{
    use DatabaseTransactions;

    public function test_print_shows_real_dates_and_thematic(): void
    {
        $activity = $this->createMinimalActivity([
            'finicial' => now()->startOfMonth()->toDateString(),
            'ffinal' => now()->startOfMonth()->addDays(9)->toDateString(),
            'thematic' => 'Eje temático de prueba',
        ]);
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        // Fechas reales de Activity (formato d/m) — antes: campo admin inexistente.
        $this->assertStringContainsString(now()->startOfMonth()->format('d/m'), $html);
        $this->assertStringContainsString(now()->startOfMonth()->addDays(9)->format('d/m'), $html);
        $this->assertStringContainsString('Eje:</span>', $html);

        // No debe quedar el resto "al" sin fechas (antes se imprimía "al" pelado).
        $this->assertStringNotContainsString('>al<', $html);
    }

    public function test_print_uses_real_institution_name(): void
    {
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        // Renombrar la institución del chain creado en createMinimalActivity
        DB::table('institucions')->where('name', 'Test Institution')->update([
            'name' => 'COLEGIO DE PRUEBA REAL',
        ]);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('COLEGIO DE PRUEBA REAL', $html);
        $this->assertStringNotContainsString('INSTITUCIÓN EDUCATIVA', $html);
    }

    public function test_print_shows_localized_status_chip(): void
    {
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        // Estado: etiqueta en español + clase CSS mapeada (no "PUBLISHED" crudo).
        $this->assertStringContainsString('Publicado', $html);
        $this->assertStringContainsString('estado-pub', $html);
        $this->assertStringNotContainsString('>PUBLISHED<', $html);
    }

    public function test_print_denies_unapproved_activity(): void
    {
        // Una actividad en revisión (status=0) no es visible para el estudiante:
        // el guard de ActivityPrintController responde 404. El badge "Activity en
        // revisión" quedaría inalcanzable mientras el guard lo exija aprobada.
        $activity = $this->createMinimalActivity(['status' => false]);
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertStatus(404);
    }

    public function test_print_hides_in_review_alert_when_activity_approved(): void
    {
        $activity = $this->createMinimalActivity(['status' => true]);
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Activity en revisión', $html);
    }

    public function test_print_embeds_the_student_name_in_member_and_footer(): void
    {
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        // El nombre sale del perfil Estudiant (fallback al username).
        $name = $student->estudiant?->full_name ?? $student->name;
        $this->assertStringContainsString($name, $html);
        $this->assertStringContainsString('Elaborado por: '.$name, $html);
    }

    public function test_print_denies_activity_from_another_seccion(): void
    {
        $activity = $this->createMinimalActivity();
        // Estudiante de OTRA sección que no es la de la actividad.
        $otherSeccion = DB::table('seccions')->insertGetId([
            'grado_id' => $activity->pevaluacion->pensum->grado_id,
            'name' => 'B',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->publish($activity);

        $outsider = $this->createStudentIn($otherSeccion);

        $this->actingAs($outsider)
            ->get(route('student.lms.activity.print', $activity))
            ->assertStatus(404);
    }

    public function test_print_no_corrupt_tail(): void
    {
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        // El volcado de IA que truncaba la vista no debe estar en el HTML.
        $this->assertStringNotContainsString('responseelfassistant', $html);
        $this->assertStringNotContainsString('tool_call', $html);
    }

    public function test_print_repairs_truncated_svg_content(): void
    {
        // P1/P2: un contenido IMAGE cuyo SVG quedó truncado por el generador
        // (tag de apertura sin '>' que se come el '</svg>') se pintaba como un
        // rectángulo NEGRO. El render debe repararlo (LmsSvgRepairService).
        $activity = $this->createMinimalActivity();
        $student = $this->createStudentIn($activity->pevaluacion->seccion_id);
        $this->publish($activity);

        $section = \App\Models\app\Academy\Lms\LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección con diagrama roto',
            'description' => null,
            'sort_order' => 1,
        ]);

        \App\Models\app\Academy\Lms\LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'IMAGE',
            'title' => 'Diagrama truncado',
            'body' => "<figure class=\"my-6\">\n"
                . "  <figcaption>Diagrama</figcaption>\n"
                . "  <div class=\"flex justify-center rounded-xl p-2\">\n"
                . ' <svg viewBox="0 0 1000 950" xmlns="http://www.w3.org/2000/svg">' . "\n"
                . "  <rect x=\"560\" y=\"210\" width=\"380\" height=\"170\" rx=\"8\"</svg>\n"
                . "  </div>\n</figure>",
            'is_visible' => true,
        ]);

        $html = $this->actingAs($student)
            ->get(route('student.lms.activity.print', $activity))
            ->assertOk()
            ->getContent();

        // El tag roto no debe llegar al HTML impreso (se eliminó el elemento).
        $this->assertStringNotContainsString('<rect x="560"', $html);
        // El <svg> queda cerrado exactamente una vez (reparación). La portada
        // usa el logo institucional (<img>), no un SVG.
        $this->assertSame(1, substr_count($html, '</svg>'));
        // El wrapper figure se conserva en la salida.
        $this->assertStringContainsString('</figure>', $html);
    }

    // ── Helpers (replican el patrón de StudentAccessTest) ─────────────

    private function publish(Activity $activity): void
    {
        LmsActivityPublication::factory()->published()->create([
            'activity_id' => $activity->id,
            'published_by' => User::factory(),
        ]);
    }

    private function createStudentIn(int $seccionId): User
    {
        $user = User::factory()->create(['is_student' => true]);
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Test Plan',
            'description' => 'Test plan description',
            'observations' => 'Test observations',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $estudiant = \App\Models\app\Learner\Estudiant::factory()->create([
            'user_id' => $user->id,
            'planpago_id' => $planpagoId,
        ]);
        \App\Models\app\Academy\Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionId,
        ]);

        return $user;
    }

    private function createMinimalActivity(array $overrides = []): Activity
    {
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
            'status' => true,
        ], $overrides));
    }
}
