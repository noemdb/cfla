<?php

namespace Tests\Feature\Navbar;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminNavbarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_navbar_shows_admin_items(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Dashboard')
            ->assertSee('Usuarios');
    }

    public function test_admin_navbar_shows_admin_only_items_for_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Votaciones')
            ->assertSee('Logs')
            ->assertSee('Bitácora');
    }

    public function test_admin_navbar_hides_admin_only_items_for_diagnostic(): void
    {
        $user = User::factory()->diagnostic()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk()->assertSee('Admin');

        // Los ítems admin-only viven en el navbar (desktop). El contenido de la
        // página admin/index legitima a 'Votaciones' para is_admin o is_diagnostic
        // (rutas bajo middleware isAdminOrDiagnostic), así que acotamos la aserción
        // al menú principal.
        $navbar = $this->extractNavbar($response->getContent());

        $this->assertStringNotContainsString('Votaciones', $navbar);
        $this->assertStringNotContainsString('Logs', $navbar);
        $this->assertStringNotContainsString('Bitácora', $navbar);
        $this->assertStringNotContainsString('Métricas de Auditoría', $navbar);
        $this->assertStringNotContainsString('Línea de Actividad', $navbar);
    }

    private function extractNavbar(string $html): string
    {
        if (preg_match('/<nav role="navigation" aria-label="Menú principal"[^>]*>(.*?)<\/nav>/s', $html, $match)) {
            return $match[1];
        }

        return $html;
    }
}
