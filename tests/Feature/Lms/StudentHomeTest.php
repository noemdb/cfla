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
     * Las lecciones con publish_at futuro aparecen en "Próximas Publicaciones".
     * Las ya publicadas salen de esa sección, pero al no haber historial de
     * interacción aparecen en el fallback de "Publicaciones Recientes"
     * (lecciones publicadas más recientes) — nunca dentro de Próximas.
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
            // La publicada ya salió de "Próximas": aparece antes del heading,
            // dentro del fallback de "Continuar Aprendiendo"
            ->assertSeeInOrder(['Actividad ya publicada', 'Próximas Publicaciones'])
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
     * Sin historial de interacción, el fallback "Publicaciones Recientes"
     * muestra las lecciones ya publicadas (publish_at <= ahora), de la más
     * reciente a la más lejana (publish_at DESC), con el hint "hace X".
     */
    public function test_no_activity_logs_shows_recently_published_fallback(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $recent = $this->createPublishedActivity($pevaluacionId, 'Reciente hace 2 días', now()->subDays(2));
        $older = $this->createPublishedActivity($pevaluacionId, 'Reciente hace 6 días', now()->subDays(6));

        $student = $this->createStudentInSeccion($seccionId);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->assertSee('Publicaciones Recientes')
            // Más reciente primero (publish_at DESC)
            ->assertSeeInOrder([$recent->topic, $older->topic])
            // Hint derecho con diffForHumans ("hace 2 días")
            ->assertSee($recent->lmsPublication->publish_at->diffForHumans());
    }

    /**
     * Cuando el estudiante tiene historial de interacción, el fallback NO se
     * muestra: solo las lecciones con logs recientes aparecen en la sección.
     */
    public function test_fallback_hidden_when_student_has_history(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Sin historial', now()->subDays(3));
        $withLog = $this->createPublishedActivity($pevaluacionId, 'Con historial', now()->subDays(1));

        $student = $this->createStudentInSeccion($seccionId);

        DB::table('lms_activity_logs')->insert([
            'activity_id' => $withLog->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now(),
        ]);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            // Aparece vía el historial reciente
            ->assertSee('Con historial')
            // El fallback "Publicaciones Recientes" no se muestra (hay historial).
            // "Sin historial" sí aparece, pero dentro del listado global
            // "Todas las Lecciones" (sección 4), no en el fallback.
            ->assertDontSee('Publicaciones Recientes');
    }

    /**
     * El fallback solo incluye lecciones YA publicadas (publish_at <= ahora).
     * Las programadas (preview, publish_at futuro) quedan fuera del fallback
     * y se ven en "Próximas Publicaciones".
     */
    public function test_fallback_excludes_preview_lessons(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Publicada hace 2 días', now()->subDays(2));
        $preview = $this->createPublishedActivity($pevaluacionId, 'Programada en 3 días', now()->addDays(3));

        $student = $this->createStudentInSeccion($seccionId);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Solo la publicada vive dentro de "Publicaciones Recientes"
        $start = strpos($html, 'Publicaciones Recientes');
        $end = strpos($html, 'Próximas Publicaciones');
        $this->assertNotFalse($start);
        $this->assertNotFalse($end);
        $fallback = substr($html, $start, $end - $start);

        $this->assertStringContainsString('Publicada hace 2 días', $fallback);
        $this->assertStringNotContainsString($preview->topic, $fallback);

        // La preview sí aparece en el resto del panel: en "Próximas Publicaciones"
        // y en el listado global "Todas las Lecciones" (sección 4)
        $this->assertStringContainsString($preview->topic, $html);
    }

    /**
     * El listado "Todas las Lecciones" muestra TODAS las lecciones visibles
     * (publicadas y previews con publish_at futuro) con paginación de 5 por
     * página, de la más reciente a la más antigua según publish_at DESC
     * (a diferencia del fallback de "Continuar Aprendiendo" que usa take(5)),
     * e independiente del historial de interacción. La preview (publish_at
     * futuro) se ordena PRIMERO por tener el publish_at más alto.
     */
    public function test_all_lessons_listed_desc_by_publish_at(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        // 6 lecciones ya publicadas con fechas escalonadas (más de 5 a propósito
        // para verificar la paginación de 5 por página)
        $topics = [];
        for ($i = 0; $i < 6; $i++) {
            $topics[] = $this->createPublishedActivity($pevaluacionId, "Lección {$i}", now()->subDays($i + 1));
        }
        // Publicación futura → también en el listado, arriba de todo
        $preview = $this->createPublishedActivity($pevaluacionId, 'Futura en 3 días', now()->addDays(3));

        // Con historial de interacción: el listado NO es el fallback de la sección 2
        $student = $this->createStudentInSeccion($seccionId);
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $topics[0]->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $expectedOrder = [$preview, ...$topics]; // publish_at DESC

        // Página 1: 5 lecciones visibles (de 7 totales), en orden DESC
        $html = $component->html();
        $this->assertStringContainsString('Todas las Lecciones', $html);
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $page1 = substr($html, $start);

        $page1Expected = array_slice($expectedOrder, 0, 5);
        foreach ($page1Expected as $activity) {
            $this->assertStringContainsString($activity->topic, $page1);
        }

        $positions = array_map(fn ($a) => strpos($page1, $a->topic), $page1Expected);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);

        // Las dos más antiguas no caben en la página 1 → página 2
        $page2Expected = array_slice($expectedOrder, 5);
        foreach ($page2Expected as $activity) {
            $this->assertStringNotContainsString($activity->topic, $page1);
        }

        // Página 2: las 2 restantes, también en orden DESC
        $component->call('gotoPage', 2);
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $page2 = substr($html, $start);

        foreach ($page2Expected as $activity) {
            $this->assertStringContainsString($activity->topic, $page2);
        }

        $positions2 = array_map(fn ($a) => strpos($page2, $a->topic), $page2Expected);
        $sorted2 = $positions2;
        sort($sorted2);
        $this->assertSame($sorted2, $positions2);
    }

    /**
     * La búsqueda en vivo ("Todas las Lecciones") filtra por texto del topic.
     */
    public function test_all_lessons_search_filters_by_topic(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Álgebra lineal', now()->subDays(1));
        $this->createPublishedActivity($pevaluacionId, 'Historia antigua', now()->subDays(2));

        $student = $this->createStudentInSeccion($seccionId);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $component->set('search', 'Álgebra');

        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);

        $this->assertStringContainsString('Álgebra lineal', $section);
        $this->assertStringNotContainsString('Historia antigua', $section);
    }

    /**
     * El filtro por asignatura excluye lecciones de otras asignaturas y, si no
     * hay coincidencias, muestra el estado vacío con "Limpiar filtros".
     */
    public function test_all_lessons_subject_filter_excludes_other_subjects(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        $student = $this->createStudentInSeccion($seccionId);
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        // Filtro que coincide: la lección sigue en el listado global
        $component->set('subjectFilter', 'Test Asignatura');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringContainsString('Lección de Test Asignatura', $section);

        // Filtro de otra asignatura: sin resultados → estado vacío con limpiar
        $component->set('subjectFilter', 'Matemática');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringNotContainsString('Lección de Test Asignatura', $section);
        $this->assertStringContainsString('Limpiar filtros', $section);
    }

    /**
     * Una búsqueda sin coincidencias muestra el estado vacío y "Limpiar filtros"
     * restaura el listado completo.
     */
    public function test_all_lessons_search_no_results_shows_clear_and_reset(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección de búsqueda', now()->subDay());

        $student = $this->createStudentInSeccion($seccionId);
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $component->set('search', 'inexistente');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringContainsString('Limpiar filtros', $section);
        $this->assertStringNotContainsString('Lección de búsqueda', $section);

        // Al limpiar, el listado completo vuelve
        $component->call('resetFilters');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringContainsString('Lección de búsqueda', $section);
        $this->assertStringNotContainsString('Limpiar filtros', $section);
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

    /**
     * Create a visible Activity with a PUBLISHED LmsActivityPublication
     * whose publish_at is the given instant.
     */
    private function createPublishedActivity(int $pevaluacionId, string $topic, \Illuminate\Support\Carbon $publishAt): Activity
    {
        $activity = Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now()->subDays(2),
            'ffinal' => now()->addDays(7),
            'topic' => $topic,
            'status' => true,
        ]);

        LmsActivityPublication::create([
            'activity_id' => $activity->id,
            'published_by' => User::factory()->create()->id,
            'status' => 'PUBLISHED',
            'publish_at' => $publishAt,
        ]);

        return $activity;
    }
}
