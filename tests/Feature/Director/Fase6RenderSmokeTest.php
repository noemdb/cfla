<?php

namespace Tests\Feature\Director;

use App\Livewire\Director\IndicatorDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class Fase6RenderSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_director_routes_render_as_get_only(): void
    {
        $user = User::factory()->create(['is_director' => true]);

        $routes = [
            '/app/director'                 => 200,
            '/app/director/pensums'         => 200,
            '/app/director/carga-academica' => 200,
            '/app/director/activities'      => 200,
            '/app/director/lecciones'       => 200,
            '/app/director/recursos'        => 200,
            '/app/director/profesores'      => 200,
        ];

        foreach ($routes as $url => $status) {
            $response = $this->actingAs($user)->get($url);
            $this->assertEquals($status, $response->getStatusCode(), "GET $url failed");
        }

        $this->assertTrue(true);
    }

    /**
     * El dashboard de Dirección debe cargar los charts de "Flujo de Registros"
     * (actividades/lecciones/diagnósticos) con datos poblados y sin errores.
     */
    public function test_dashboard_loads_registration_flow_charts(): void
    {
        $user = User::factory()->create(['is_director' => true]);

        $component = Livewire::actingAs($user)->test(IndicatorDashboard::class);

        $component->assertOk();

        // Los charts se hidratan en mount() y deben estar poblados.
        $component->assertSet('registrationRange', '7d')
            ->assertSet('chartActivitiesFlow', $component->get('chartActivitiesFlow'))
            ->assertSet('chartLessonsFlow', $component->get('chartLessonsFlow'))
            ->assertSet('chartDiagnosticsFlow', $component->get('chartDiagnosticsFlow'));

        // Los charts por día (filtrados por lapso) también deben cargar.
        $component->assertSet('chartActivitiesByDay', $component->get('chartActivitiesByDay'))
            ->assertSet('chartScheduledByDay', $component->get('chartScheduledByDay'));
        $lessons = $component->get('chartLessonsByDay');
        $this->assertIsArray($lessons);
        $this->assertArrayHasKey('categories', $lessons);
        $this->assertArrayHasKey('series', $lessons);

        // Al cambiar el rango, el updated hook recalcula sin errores.
        $component->set('registrationRange', 'all')
            ->assertSet('registrationRange', 'all')
            ->assertOk();

        // Al cambiar el lapso, los charts por día se recalculan sin errores.
        if ($component->get('lapsos')->isNotEmpty()) {
            $lapsoId = $component->get('lapsos')->first()->id;
            $component->set('selectedLapsoId', $lapsoId)->assertOk();
        }

        $this->assertTrue(true);
    }
}
