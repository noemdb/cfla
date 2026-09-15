<?php

namespace Tests\Feature\Menu;

use App\Models\User;
use App\Services\MenuBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * El menú de Coordinación debe exponer «Horario» como enlace activo al wizard
 * de horarios (/app/coordinacion/timetable), no como ítem «Próximamente».
 */
class MenuCoordinacionHorarioTest extends TestCase
{
    use DatabaseTransactions;

    public function test_horario_es_un_enlace_activo_en_el_menu_de_coordinacion(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $this->actingAs($user);

        $html = app(MenuBuilder::class, ['layout' => 'coordinacion'])->renderDesktop();

        // El ítem enlaza a la ruta del wizard de horarios.
        $this->assertStringContainsString('Horario', $html);
        $this->assertStringContainsString(route('app.coordinacion.timetable'), $html);

        // Solo «Suplencias» queda como «Próximamente» (antes eran dos).
        $this->assertSame(1, substr_count($html, 'title="Próximamente"'));
    }
}
