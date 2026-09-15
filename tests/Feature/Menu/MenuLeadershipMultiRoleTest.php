<?php

namespace Tests\Feature\Menu;

use App\Models\User;
use App\Services\MenuBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Un usuario multi-rol (admin + leadership, etc.) cuyo layout primario es
 * «admin» debe ver el dropdown de Seguimiento (is_leadership).
 */
class MenuLeadershipMultiRoleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_multi_role_user_sees_seguimiento_dropdown(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'is_planner' => true,
            'is_diagnostic' => true,
            'is_profesor' => true,
            'is_coordinacion' => true,
            'is_leadership' => true,
            'is_director' => true,
        ]);

        $this->actingAs($user);

        $layout = MenuBuilder::resolveLayoutForUser($user);
        $this->assertSame('admin', $layout);

        $html = app(MenuBuilder::class, ['layout' => $layout])->renderDesktop();

        $this->assertStringContainsString('Seguimiento', $html);
        $this->assertStringContainsString(route('app.leadership.dashboard'), $html);
    }

    public function test_user_without_leadership_does_not_see_seguimiento(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'is_planner' => false,
            'is_diagnostic' => false,
            'is_profesor' => false,
            'is_coordinacion' => false,
            'is_leadership' => false,
            'is_director' => false,
            'is_student' => false,
        ]);

        $this->actingAs($user);

        $html = app(MenuBuilder::class, ['layout' => 'admin'])->renderDesktop();

        $this->assertStringNotContainsString('Seguimiento', $html);
    }
}
