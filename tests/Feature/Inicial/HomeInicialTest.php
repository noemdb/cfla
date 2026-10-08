<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Portada del módulo de Educación Inicial (`inicials.home`) y verificación de
 * que ninguna ruta del módulo devuelve un error de servidor.
 *
 * La portada es la primera pantalla del módulo y, además, la ruta a la que
 * apuntan las migas de pan de TODAS las demás páginas: si falla, el módulo no
 * tiene entrada.
 *
 * @group inicial
 */
class HomeInicialTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array{user: User, profesor_id: int}
     */
    private function makeDocenteInicial(bool $isAdmin = false): array
    {
        $attributes = ['is_profesor' => true, 'is_admin' => $isAdmin];

        if (Schema::hasColumn('users', 'is_inicial')) {
            $attributes['is_inicial'] = ! $isAdmin;
        }

        $user = User::factory()->create($attributes);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.random_int(10_000_000, 99_999_999),
            'ci_profesor' => (string) random_int(10_000_000, 99_999_999),
            'name' => 'Docente',
            'lastname' => 'Home Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['user' => $user, 'profesor_id' => $profesorId];
    }

    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial.');
        }
    }

    /** @test */
    public function la_portada_muestra_los_seis_documentos_del_modulo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $html = $this->actingAs($this->makeDocenteInicial()['user'])
            ->get(route('inicials.home'))
            ->assertOk()
            // El layout real debe cargar Livewire y WireUI: el bug anterior era
            // que el módulo usaba el esqueleto de Jetstream y las páginas
            // quedaban sin scripts (modales/diálogos rotos).
            ->assertSee('wireui', false)
            ->assertSee('livewire', false)
            // Las seis tarjetas, con su enlace real y no un texto suelto.
            ->assertSee('Planificación semanal')
            ->assertSee('Planificación quincenal')
            ->assertSee('Proyecto de aula')
            ->assertSee('Plan especial')
            ->assertSee('Plan de evaluación')
            ->assertSee('Informe final')
            ->assertSee(route('inicials.eiplanningwks.index'))
            ->assertSee(route('inicials.eifinalks.index'));
    }

    /** @test */
    public function la_portada_exige_el_flag_de_inicial(): void
    {
        $this->actingAs(User::factory()->create(['is_profesor' => false, 'is_admin' => false]))
            ->get(route('inicials.home'))
            ->assertForbidden();
    }

    /** @test */
    public function la_portada_no_anuncia_registros_que_no_existen(): void
    {
        $this->saltarSiFaltaLaMigracion();

        // Sin filas en el módulo, la portada debe decirlo en vez de prometer
        // datos: los seis contadores salen del servicio real.
        $this->actingAs($this->makeDocenteInicial()['user'])
            ->get(route('inicials.home'))
            ->assertOk()
            ->assertSee('0 registro(s)')
            ->assertSee('Documentos registrados');
    }

    /** @test */
    public function los_casos_de_uso_documentan_los_siete_recorridos(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $this->actingAs($this->makeDocenteInicial()['user'])
            ->get(route('inicials.use-cases'))
            ->assertOk()
            ->assertSee('Autenticación y acceso')
            ->assertSee('Planificación semanal')
            ->assertSee('Planificación quincenal')
            ->assertSee('Proyectos de aula')
            ->assertSee('Planes de evaluación')
            ->assertSee('Planes especiales')
            ->assertSee('Informes finales por estudiante');
    }

    /**
     * El smoke test que encontró el bug de esta misma fase: las dos rutas de la
     * portada seguían en el esqueleto de F1 y devolvían 501 —la portada es la
     * ruta a la que apuntan las migas de pan de todas las páginas del módulo.
     *
     * @test
     */
    public function ninguna_ruta_del_modulo_devuelve_un_error_de_servidor(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $user = User::factory()->create([
            'is_admin' => true,
            'is_profesor' => true,
            'is_inicial' => true,
            'is_diagnostic' => true,
            'is_planner' => true,
        ]);

        $fallos = [];

        foreach (Route::getRoutes() as $ruta) {
            $nombre = $ruta->getName();

            if (! $nombre || ! str_contains($nombre, 'inicials.')) {
                continue;
            }

            // Se excluyen las acciones que escribirían o borrarían: no es un
            // smoke test de autorización, sino de que las páginas montan.
            if (array_intersect($ruta->methods(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                continue;
            }

            $uri = preg_replace('#\{[^}]+\}#', '1', $ruta->uri());
            $respuesta = $this->actingAs($user)->get('/'.$uri);

            if ($respuesta->getStatusCode() >= 500) {
                $fallos[] = implode('|', $ruta->methods()).' /'.$uri.' → '.$respuesta->getStatusCode();
            }
        }

        $this->assertSame([], $fallos, 'Rutas del módulo que devuelven 5xx');
    }
}
