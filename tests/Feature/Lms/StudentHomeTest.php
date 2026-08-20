<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class StudentHomeTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithTime;

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
        // Congela el reloj: now()->addHours(3) debe seguir siendo "hoy".
        // Sin esto, si se ejecuta de noche la suma cruza la medianoche y el
        // badge pasa a "Se publica mañana" (test intermitente por hora).
        $this->travelTo('2026-01-15 10:00:00');
        try {
            [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

            $publishAt = now()->addHours(3); // 13:00 del mismo día

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
        } finally {
            $this->travelBack();
        }
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
     * D2 · Color por materia — el home aplica el color de la asignatura
     * (punto de materia, badge del hero y barra de distribución), y la
     * barra de distribución deja de pintarse siempre de esmeralda.
     */
    public function test_home_applies_subject_color_dots_badge_and_distribution(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        // Edad fija 14 años: el D2 verifica la variante adulta (barra de
        // distribución con gradiente de materia). Sin date_birth la fábrica
        // genera una fecha aleatoria y ~14% de las veces el estudiante cae en
        // modo lectura (5–8), donde la barra de progreso F2 usa el gradiente
        // esmeralda fijo y rompería el assert de abajo.
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(14)->subMonths(1)->toDateString(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Test Asignatura → rose (ver AsignaturaColorKeyTest). Computamos la
        // clave desde el modelo para que el test siga siendo autoconsistente.
        $key = Asignatura::colorKey('Test Asignatura');
        $this->assertSame('rose', $key);

        // Punto de materia en las tarjetas de lección
        $this->assertStringContainsString("bg-{$key}-400", $html);

        // Badge del hero (la lección siguiente publicada sin completar)
        $this->assertStringContainsString("bg-{$key}-100 text-{$key}-700", $html);

        // Distribución: la barra de Test Asignatura usa el gradiente rose,
        // ya no el esmeralda fijo de antes del D2.
        $this->assertStringNotContainsString('linear-gradient(90deg, #10b981, #34d399)', $html);
    }

    /**
     * NavTabs · El home renderiza 4 pestañas visibles (Continuar, Lecciones,
     * Distribución, Actividad) siguiendo el patrón del listado del profesor
     * (nav reactiva con border-b). Cada pestaña enlaza su panel con x-show
     * sobre activeTab; el estado se vincula a Livewire con el binding
     * bidireccional @entangle('activeTab').live (la pestaña activa por defecto
     * es 'continuar').
     */
    public function test_home_renders_visible_navtabs_with_four_sections(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        $student = $this->createStudentInSeccion($seccionId);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Tablist visible con 4 pestañas (patrón profesor: role="tablist")
        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('id="tab-continuar"', $html);
        $this->assertStringContainsString('id="tab-lecciones"', $html);
        $this->assertStringContainsString('id="tab-distribucion"', $html);
        $this->assertStringContainsString('id="tab-actividad"', $html);

        // Cada pestaña enlaza su panel vía aria-controls
        $this->assertStringContainsString('aria-controls="panel-continuar"', $html);
        $this->assertStringContainsString('aria-controls="panel-lecciones"', $html);
        $this->assertStringContainsString('aria-controls="panel-distribucion"', $html);
        $this->assertStringContainsString('aria-controls="panel-actividad"', $html);

        // Los 4 paneles existen y se muestran según activeTab (x-show),
        // conservando el contenido en el DOM (los tests cortan por substring)
        $this->assertStringContainsString('x-show="activeTab === \'continuar\'"', $html);
        $this->assertStringContainsString('x-show="activeTab === \'lecciones\'"', $html);
        $this->assertStringContainsString('x-show="activeTab === \'distribucion\'"', $html);
        $this->assertStringContainsString('x-show="activeTab === \'actividad\'"', $html);

        // El estado Alpine se vincula a Livewire con @entangle bidireccional
        // ('.live') y el botón sincroniza vía setActiveTab local
        $this->assertStringContainsString("entangle('activeTab').live", $html);
        $this->assertStringContainsString("@click=\"setActiveTab('continuar')\"", $html);

        // La pestaña activa por defecto es 'continuar': su botón lleva el
        // estado activo via :class (ternario Alpine)
        $this->assertStringContainsString(":class=\"activeTab === 'continuar'", $html);
    }

    /**
     * NavTabs · Cambiar de pestaña actualiza activeTab en el servidor y el
     * query string lo persiste (deep link / refresh mantienen la sección).
     */
    public function test_navtab_set_active_tab_persists_choice(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        $student = $this->createStudentInSeccion($seccionId);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        // Por defecto 'continuar'
        $this->assertSame('continuar', $component->get('activeTab'));

        // Cambiamos a 'lecciones' → se sincroniza en el servidor
        $component->call('setActiveTab', 'lecciones');
        $this->assertSame('lecciones', $component->get('activeTab'));

        // El query string lo persiste (configurable en $queryString) — al
        // recargar con ?activeTab=lecciones, el componente arranca en esa pestaña
        $refreshed = Livewire::actingAs($student)
            ->withQueryParams(['activeTab' => 'lecciones'])
            ->test(\App\Livewire\Student\Lms\StudentHome::class);
        $this->assertSame('lecciones', $refreshed->get('activeTab'));
    }

    /**
     * F1+F2+F3 · Home en modo lectura (franja 5–8): el <body> lleva la clase
     * modo-lectura, la sección de estadísticas se reduce a una única barra de
     * progreso accesible (role="progressbar"), y el CTA del hero usa la
     * micro-copia infantil "Pulsa para empezar" en vez del tema de la lección.
     */
    public function test_home_modo_lectura_shows_progress_bar_and_child_cta(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        // 6 años → modo lectura activo (misma base etaria que la mascota C4)
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // F1: el layout aplica la clase modo-lectura al <body>. El html() de
        // Livewire devuelve solo el componente; la clase del layout se verifica
        // con una petición HTTP real (GET renderiza layout + componente).
        $page = $this->actingAs($student)->get(route('student.lms.home'))->getContent();
        $this->assertStringContainsString('flex flex-col modo-lectura', $page);

        // F2: barra de progreso en lugar de las 4 tarjetas adultas
        $this->assertStringContainsString('Tu progreso', $html);
        $this->assertStringContainsString('role="progressbar"', $html);
        $this->assertStringContainsString('lecciones completadas', $html);
        $this->assertStringContainsString('linear-gradient(90deg, #10b981, #34d399)', $html);
        $this->assertStringNotContainsString('Disponibles para ti', $html);
        $this->assertStringNotContainsString('Que has dejado', $html);
        $this->assertStringNotContainsString('Recursos descargados', $html);

        // F3: el CTA del hero usa micro-copia infantil
        $this->assertStringContainsString('Pulsa para empezar', $html);

        // El CTA del hero comparte el destino de la lección siguiente
        $this->assertStringContainsString('Continuar', $html);
    }

    /**
     * F1+F2+F3 · Home en modo adulto (13–15): sin clase modo-lectura, se
     * mantienen las 4 tarjetas de estadísticas y el CTA del hero muestra el
     * tema de la lección, no la micro-copia infantil.
     */
    public function test_home_adult_mode_keeps_full_grid_and_topic_cta(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $this->createPublishedActivity($pevaluacionId, 'Lección de Test Asignatura', now()->subDay());

        // 14 años → modo lectura inactivo
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(14)->subMonths(1)->toDateString(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // F1: sin modo lectura — se verifica sobre la página completa (GET),
        // porque html() de Livewire no incluye el <body> del layout.
        $page = $this->actingAs($student)->get(route('student.lms.home'))->getContent();
        $this->assertStringNotContainsString('flex flex-col modo-lectura', $page);

        // F2: las 4 tarjetas adultas, sin la barra de progreso infantil
        $this->assertStringContainsString('Disponibles para ti', $html);
        $this->assertStringContainsString('Que has dejado', $html);
        $this->assertStringContainsString('Recursos descargados', $html);
        $this->assertStringNotContainsString('Tu progreso', $html);
        $this->assertStringNotContainsString('role="progressbar"', $html);

        // F3: el CTA adulto muestra el tema de la lección
        $this->assertStringContainsString('Lección de Test Asignatura', $html);
        $this->assertStringNotContainsString('Pulsa para empezar', $html);

        // El CTA del hero comparte el destino de la lección siguiente
        $this->assertStringContainsString('Continuar', $html);
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
     * (a diferencia del fallback de "Continuar Aprendiendo" que usa take(4)),
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
     * hay coincidencias, muestra el estado vacío con "Ver todas".
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

        // Filtro de otra asignatura: sin resultados → estado vacío con "Ver todas"
        $component->set('subjectFilter', 'Matemática');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringNotContainsString('Lección de Test Asignatura', $section);
        $this->assertStringContainsString('Ver todas', $section);
    }

    /**
     * Una búsqueda sin coincidencias muestra el estado vacío y "Ver todas"
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
        $this->assertStringContainsString('Ver todas', $section);
        $this->assertStringNotContainsString('Lección de búsqueda', $section);

        // Al limpiar, el listado completo vuelve
        $component->call('resetFilters');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringContainsString('Lección de búsqueda', $section);
        $this->assertStringNotContainsString('Ver todas', $section);
    }

    /*
     * ------------------------------------------------------------------
     * C4 · Mascota/avatar compañero — franja etaria (F1) y empty state
     * ------------------------------------------------------------------
     * Se muestra ≤12 años y se oculta para 13–15; "oro puro" (ojos de
     * estrella dorados) solo en la franja 5–8. La variante idle "anima en
     * el vacío" (flota y aparece en el empty state del listado).
     */

    public function test_home_mascot_shows_for_5_8_with_gold_emphasis(): void
    {
        [$seccionId] = $this->createEvaluacionChain();

        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(), // 6 años
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Mascota en el hero (variante greet: brazo alzado) y flota
        $this->assertStringContainsString('lms-mascot-body', $html);
        $this->assertStringContainsString('M26 66 Q18 76 20 84', $html);
        $this->assertStringContainsString('animate-mascot-float', $html);
        // "Para 5–8 es oro puro": ojos de estrella dorados
        $this->assertStringContainsString('M 38 44.5 L 39.8 48.2', $html);
        $this->assertStringContainsString('fill="#fbbf24"', $html);
    }

    public function test_home_mascot_shows_for_9_12_without_emphasis(): void
    {
        [$seccionId] = $this->createEvaluacionChain();

        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(10)->subMonths(1)->toDateString(), // 10 años
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Mascota presente con ojos de punto (sin énfasis dorado)
        $this->assertStringContainsString('lms-mascot-body', $html);
        $this->assertStringContainsString('cx="38" cy="50" r="4"', $html);
        $this->assertStringNotContainsString('M 38 44.5 L 39.8 48.2', $html);
    }

    public function test_home_mascot_shown_for_null_age_without_emphasis(): void
    {
        [$seccionId] = $this->createEvaluacionChain();

        // date_birth sin cargar ('0000-00-00') → getAgeAttribute() = '-'
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => '0000-00-00',
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        $this->assertStringContainsString('lms-mascot-body', $html);
        $this->assertStringContainsString('cx="38" cy="50" r="4"', $html);
        $this->assertStringNotContainsString('M 38 44.5 L 39.8 48.2', $html);
    }

    public function test_home_mascot_hidden_for_13_15(): void
    {
        [$seccionId] = $this->createEvaluacionChain();

        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(14)->subMonths(1)->toDateString(), // 14 años
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Franja 13–15: la mascota se oculta por completo (ni hero ni empty state)
        $this->assertStringNotContainsString('lms-mascot-body', $html);
    }

    public function test_home_mascot_idle_in_empty_state(): void
    {
        [$seccionId] = $this->createEvaluacionChain();

        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(),
        ]);

        // La sección "Todas las Lecciones" (con su empty state) solo se
        // renderiza si hay resultados, búsqueda o filtro activo.
        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $component->set('search', 'inexistente');
        $html = $component->html();

        // Variante idle: lupa de búsqueda en el empty state ("anima en el vacío")
        $this->assertStringContainsString('lms-mascot-body', $html);
        $this->assertStringContainsString('M89 28 L96 35', $html);
        $this->assertStringContainsString('No encontramos lecciones', $html);
    }

    /*
     * ------------------------------------------------------------------
     * C5 · Empty state ilustrado — visual + CTA clara
     * ------------------------------------------------------------------
     * Sin resultados: ilustración (mascota idle), mensaje contextual con el
     * término buscado, micro-copia de apoyo y dos CTAs: "Vuelve a intentarlo"
     * (limpia la búsqueda) y "Ver todas" (restaura el listado completo).
     */

    public function test_all_lessons_empty_state_illustrated_with_ctas(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección visible', now()->subDay());

        // Franja con mascota (≤12): la ilustración del empty state es la mascota idle
        $student = $this->createStudentInSeccion($seccionId, [
            'date_birth' => now()->subYears(6)->subMonths(1)->toDateString(), // 6 años
        ]);
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

        // Visual: ilustración (mascota idle con lupa) dentro del empty state
        $this->assertStringContainsString('lms-mascot-body', $section);
        $this->assertStringContainsString('M89 28 L96 35', $section);

        // Mensaje contextual con el término (dentro de un <span>) + micro-copia
        $this->assertStringContainsString('No encontramos lecciones', $section);
        $this->assertStringContainsString('>inexistente</span>”.', $section);
        $this->assertStringContainsString('Prueba con otra búsqueda o limpia los filtros.', $section);

        // CTA clara: "Vuelve a intentarlo" (limpia búsqueda) y "Ver todas"
        $this->assertStringContainsString('Vuelve a intentarlo', $section);
        $this->assertStringContainsString('Ver todas', $section);

        // "Vuelve a intentarlo" ($set('search','')) restaura el listado
        $component->set('search', '');
        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);
        $this->assertStringContainsString('Lección visible', $section);
        $this->assertStringNotContainsString('Vuelve a intentarlo', $section);
        $this->assertStringNotContainsString('Ver todas', $section);
    }

    /*
     * ------------------------------------------------------------------
     * C1 · Progreso por lección con estrellas — filas del catálogo
     * ------------------------------------------------------------------
     * Cada lección del catálogo ("Todas las Lecciones") muestra 3 estrellas
     * de logro (completada / comentario aprobado / recurso descargado) y una
     * barra de progreso visual. Con los tres logros: 3 estrellas verdes y
     * barra al 100%. Sin interacción: 3 estrellas grises y barra al 0%.
     */

    public function test_all_lessons_row_lights_3_stars_with_full_progress(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección con logros', now()->subDay());
        $student = $this->createStudentInSeccion($seccionId);

        // Los 3 logros de la lección (C1): completada + comentario aprobado + descarga
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
            'body' => 'Me encantó esta lección.',
            'is_approved' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);

        // Aislar la única fila del catálogo (una sola lección publicada)
        $liStart = strpos($section, '<li');
        $liEnd = strpos($section, '</li>') + strlen('</li>');
        $row = substr($section, $liStart, $liEnd - $liStart);

        // 3 estrellas verdes (todas ganadas) + barra de progreso al 100%
        $this->assertStringContainsString('3 de 3 logros', $row);
        $this->assertSame(3, substr_count($row, 'text-emerald-500'));
        $this->assertSame(0, substr_count($row, 'text-gray-300 dark:text-gray-600'));
        $this->assertStringContainsString('style="width: 100%"', $row);
    }

    public function test_all_lessons_row_shows_gray_stars_without_interaction(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección recién publicada', now()->subDay());
        $student = $this->createStudentInSeccion($seccionId);

        $component = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class);

        $html = $component->html();
        $start = strpos($html, 'Todas las Lecciones');
        $this->assertNotFalse($start);
        $section = substr($html, $start);

        $liStart = strpos($section, '<li');
        $liEnd = strpos($section, '</li>') + strlen('</li>');
        $row = substr($section, $liStart, $liEnd - $liStart);

        // Sin interacción: 0 estrellas ganadas (3 grises) y barra al 0%
        $this->assertStringContainsString('0 de 3 logros', $row);
        $this->assertSame(0, substr_count($row, 'text-emerald-500'));
        $this->assertSame(3, substr_count($row, 'text-gray-300 dark:text-gray-600'));
        $this->assertStringContainsString('style="width: 0%"', $row);
    }

    /*
     * ------------------------------------------------------------------
     * C2 · Racha de días (streak) — hero del home
     * ------------------------------------------------------------------
     * La racha cuenta los días consecutivos con actividad (VIEW/COMPLETE/
     * RESOURCE_DOWNLOAD) desde hoy (o ayer si hoy aún no hay actividad) y
     * se muestra como píldora ámbar — la familia del countdown del hero —
     * con un "pop" de celebración al cargar (login).
     */

    public function test_hero_lights_streak_badge_in_countdown_family(): void
    {
        [$seccionId, $pevaluacionId] = $this->createEvaluacionChain();

        $activity = $this->createPublishedActivity($pevaluacionId, 'Lección para racha', now()->subDay());
        $student = $this->createStudentInSeccion($seccionId);

        // Actividad hoy y ayer → racha de 2 días consecutivos
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now(),
        ]);
        DB::table('lms_activity_logs')->insert([
            'activity_id' => $activity->id,
            'user_id' => $student->id,
            'event' => 'VIEW',
            'created_at' => now()->subDay(),
        ]);

        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        // Contador + micro-copia de racha
        $this->assertStringContainsString('2 días de racha', $html);

        // Familia del countdown del hero (ámbar) + celebración "pop" al login
        $this->assertStringContainsString(
            'text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 animate-streak-pop',
            $html
        );
        $this->assertStringNotContainsString('text-orange-700', $html);
    }

    public function test_hero_hides_streak_badge_without_activity(): void
    {
        [$seccionId] = $this->createEvaluacionChain();
        $student = $this->createStudentInSeccion($seccionId);

        // Sin logs de actividad → sin racha → sin píldora
        $html = Livewire::actingAs($student)
            ->test(\App\Livewire\Student\Lms\StudentHome::class)
            ->html();

        $this->assertStringNotContainsString('de racha', $html);
        $this->assertStringNotContainsString('animate-streak-pop', $html);
    }

    public function test_streak_celebration_pop_respects_reduced_motion(): void
    {
        $source = file_get_contents(resource_path('views/student/layouts/app.blade.php'));

        // C2: keyframe "pop" definido junto a mascot-float, un solo disparo
        $this->assertStringContainsString('@keyframes streak-pop', $source);
        $this->assertStringContainsString('animation: streak-pop 0.5s', $source);

        // C2 + E2: bajo prefers-reduced-motion el "pop" se desactiva
        $this->assertStringContainsString('.animate-streak-pop { animation: none; }', $source);
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
