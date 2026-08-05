<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class StudentHomeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Solo las lecciones con publish_at futuro aparecen en "Próximas
     * Publicaciones". Las ya publicadas (publish_at pasado) salen de la sección.
     */
    public function test_published_activity_is_excluded_and_preview_shows_countdown(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        // Actividad ya publicada (publish_at en el pasado) → NO debe aparecer en la sección
        $publishedActivity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(3),
            'topic' => 'Actividad ya publicada',
            'status' => true,
        ]);

        // Actividad programada para publicarse en 2 días (mediodía → día estable)
        $publishAt = now()->startOfDay()->addDays(2)->addHours(12);

        $previewActivity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => 'Actividad por publicar',
            'status' => true,
        ]);

        LmsActivityPublication::create([
            'activity_id' => $publishedActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => now()->subHour(),
            'published_at' => now(),
        ]);

        LmsActivityPublication::create([
            'activity_id' => $previewActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->assertSee('Próximas Publicaciones')
            ->assertSee('Actividad por publicar')
            ->assertSee('Se publica en 2 días')
            // La publicada ya salió de la sección
            ->assertDontSee('Actividad ya publicada')
            // El countdown de ffinal desapareció del panel
            ->assertDontSee('días rest.');
    }

    /**
     * Publicación programada para hoy: muestra la hora exacta.
     */
    public function test_preview_publishing_today_shows_time(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $publishAt = now()->addHours(3);

        $previewActivity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => 'Se publica hoy',
            'status' => true,
        ]);

        LmsActivityPublication::create([
            'activity_id' => $previewActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->assertSee('Se publica hoy a las '.$publishAt->format('H:i'))
            ->assertDontSee('días rest.');
    }

    /**
     * Publicación programada para mañana: badge "Se publica mañana".
     */
    public function test_preview_publishing_tomorrow_shows_manana(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $publishAt = now()->startOfDay()->addDay()->addHours(12);

        $previewActivity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => 'Se publica mañana',
            'status' => true,
        ]);

        LmsActivityPublication::create([
            'activity_id' => $previewActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->assertSee('Se publica mañana');
    }

    /**
     * Publicación lejana (más de 7 días): badge con la fecha.
     */
    public function test_far_future_preview_shows_date(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $publishAt = now()->startOfDay()->addDays(9)->addHours(12);

        $previewActivity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(30),
            'topic' => 'Se publica en 9 días',
            'status' => true,
        ]);

        LmsActivityPublication::create([
            'activity_id' => $previewActivity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
        ]);

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->assertSee('Se publica el '.$publishAt->translatedFormat('j M'));
    }

    /**
     * Create a student User with Estudiant + Inscripcion in the given seccion.
     */
    private function createStudentInSeccion(int $seccionId): User
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

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionId,
        ]);

        return $user;
    }

    /**
     * Build the FK chain once (pevaluacion -> pensum -> ... -> seccion)
     * and return [seccion_id, pevaluacion_id] so multiple activities
     * can share the same seccion (visible to the same student).
     */
    private function createEvaluacionChain(): array
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

        return [$seccionId, $pevaluacionId];
    }
}
