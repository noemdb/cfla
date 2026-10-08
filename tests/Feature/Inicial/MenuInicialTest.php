<?php

namespace Tests\Feature\Inicial;

use App\Http\Middleware\IsInicial;
use App\Models\User;
use App\Services\MenuBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Entrada del módulo de Educación Inicial en el menú global.
 *
 * El módulo es funcional pero antes no tenía ningún punto de entrada en la UI:
 * el docente con `is_inicial` solo podía entrar escribiendo `/app/inicials` a
 * mano. Aquí se comprueba que ahora el ítem "Educación Inicial" aparece en el
 * menú para quien tiene el flag y NO para el resto.
 *
 * @group inicial
 */
class MenuInicialTest extends TestCase
{
    use DatabaseTransactions;

    private function saltarSiFaltaLaMigracion(): void
    {
        if (IsInicial::migracionPendiente()) {
            $this->markTestSkipped('Requiere la columna users.is_inicial.');
        }
    }

    /** @return array<string, mixed> */
    private function itemsDelGrupo(string $layout, string $label): array
    {
        return collect((new MenuBuilder($layout))->resolveGroups())
            ->firstWhere('label', $label)['items'] ?? [];
    }

    /** @return array<string, mixed> */
    private function itemsDelGrupoInicial(string $layout): array
    {
        return $this->itemsDelGrupo($layout, 'Educación Inicial');
    }

    /** @test */
    public function el_grupo_expone_todas_las_secciones_del_modulo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = User::factory()->create(['is_profesor' => true, 'is_admin' => false]);
        if (Schema::hasColumn('users', 'is_inicial')) {
            DB::table('users')->where('id', $docente->id)->update(['is_inicial' => true]);
        }

        $this->actingAs($docente->fresh());

        $items = $this->itemsDelGrupoInicial('profesor');

        $labels = collect($items)->pluck('label')->all();

        $this->assertEquals([
            'Inicio',
            'Planificación semanal',
            'Planificación quincenal',
            'Proyecto de aula',
            'Plan especial',
            'Plan de evaluación',
            'Informe final',
            'Casos de uso',
        ], $labels);

        // Cada sección apunta a su propia ruta, no a la portada.
        $rutas = collect($items)->pluck('href');
        $href = fn (string $fragmento) => $rutas->first(fn ($h) => str_contains($h, $fragmento));
        $this->assertStringContainsString('/app/inicials/eiplanningwks', $href('eiplanningwks'));
        $this->assertStringContainsString('/app/inicials/eifinalks', $href('eifinalks'));
        $this->assertStringContainsString('/app/inicials/use-cases', $href('use-cases'));
    }

    /** @test */
    public function un_docente_con_is_inicial_ve_la_entrada_al_modulo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = User::factory()->create([
            'is_profesor' => true,
            'is_admin' => false,
        ]);

        if (Schema::hasColumn('users', 'is_inicial')) {
            DB::table('users')->where('id', $docente->id)->update(['is_inicial' => true]);
        }

        $this->actingAs($docente->fresh());

        $items = $this->itemsDelGrupoInicial('profesor');

        $labels = collect($items)->pluck('label');

        $this->assertContains('Inicio', $labels);
        $this->assertNotEmpty($items);
    }

    /**
     * EL CASO QUE MOTIVA EL CAMBIO: `is_inicial` NO implica `is_profesor`.
     *
     * Un usuario con solo el flag debe ver el grupo 'inicial' aunque no sea
     * profesor (antes colgaba del grupo Profesor y no aparecía).
     *
     * @test
     */
    public function un_usuario_solo_con_is_inicial_ve_el_grupo_sin_ser_profesor(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $usuario = User::factory()->create([
            'is_profesor' => false,
            'is_admin' => false,
            'is_planner' => false,
        ]);

        if (Schema::hasColumn('users', 'is_inicial')) {
            DB::table('users')->where('id', $usuario->id)->update(['is_inicial' => true]);
        }

        $this->actingAs($usuario->fresh());

        // El layout se resuelve a 'inicial' y ahí sí está el grupo.
        $this->assertSame('inicial', MenuBuilder::resolveLayoutForUser());

        $items = $this->itemsDelGrupoInicial('inicial');

        $this->assertNotEmpty($items, 'El grupo Educación Inicial debe aparecer.');
        $this->assertContains('Inicio', collect($items)->pluck('label'));
    }

    /** @test */
    public function un_docente_sin_el_flag_no_ve_la_entrada(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = User::factory()->create([
            'is_profesor' => true,
            'is_admin' => false,
        ]);

        if (Schema::hasColumn('users', 'is_inicial')) {
            DB::table('users')->where('id', $docente->id)->update(['is_inicial' => false]);
        }

        $this->actingAs($docente->fresh());

        $items = $this->itemsDelGrupoInicial('profesor');

        $labels = collect($items)->pluck('label');

        $this->assertNotContains('Educación Inicial', $labels);
    }

    /** @test */
    public function un_admin_tambien_ve_la_entrada(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $admin = User::factory()->create([
            'is_profesor' => true,
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        // El admin accede al módulo: el grupo 'inicial' está en el layout admin
        // y su permiso (is_admin || is_inicial) lo deja entrar.
        $items = $this->itemsDelGrupoInicial('admin');

        $this->assertNotEmpty($items, 'El grupo Educación Inicial debe aparecer en el layout admin.');
        $this->assertContains('Inicio', collect($items)->pluck('label'));
    }

    /** @test */
    public function la_entrada_resuelve_a_la_portada_del_modulo(): void
    {
        $this->saltarSiFaltaLaMigracion();

        $docente = User::factory()->create([
            'is_profesor' => true,
            'is_admin' => false,
        ]);

        if (Schema::hasColumn('users', 'is_inicial')) {
            DB::table('users')->where('id', $docente->id)->update(['is_inicial' => true]);
        }

        $this->actingAs($docente->fresh());

        $item = collect($this->itemsDelGrupoInicial('profesor'))
            ->firstWhere('label', 'Inicio');

        $this->assertNotNull($item, 'La entrada debe existir.');
        $this->assertStringContainsString('/app/inicials', $item['href']);
    }
}
