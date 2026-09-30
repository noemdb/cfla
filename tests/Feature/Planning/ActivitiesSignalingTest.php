<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Activities\IndexComponent;
use App\Models\app\Academy\ActivitySupplement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Señalización E/Ev + información complementaria en /app/planning/activities
 * (réplica de /app/profesors/activities).
 */
class ActivitiesSignalingTest extends TestCase
{
    use DatabaseTransactions;

    private function planner(): User
    {
        return User::factory()->create(['is_planner' => true]);
    }

    public function test_shows_teaching_and_evaluative_badges(): void
    {
        Livewire::actingAs($this->planner())
            ->test(IndexComponent::class)
            ->set('paginate', 9999)
            ->assertOk()
            ->assertSee('Contiene planificación de enseñanza', false)
            ->assertSee('Contiene actividad evaluativa', false);
    }

    public function test_supplement_button_only_when_supplement_exists(): void
    {
        ActivitySupplement::whereIn('activity_id', [139, 479])->delete();
        ActivitySupplement::create(['activity_id' => 139, 'text' => 'Texto complementario de prueba']);

        Livewire::actingAs($this->planner())
            ->test(IndexComponent::class)
            ->set('paginate', 9999)
            ->assertOk()
            ->assertSee('openSupplementModal(139)', false)
            ->assertDontSee('openSupplementModal(479)', false);
    }

    public function test_open_supplement_modal_loads_readonly_content(): void
    {
        ActivitySupplement::where('activity_id', 139)->delete();
        ActivitySupplement::create(['activity_id' => 139, 'text' => 'Texto complementario de prueba']);

        Livewire::actingAs($this->planner())
            ->test(IndexComponent::class)
            ->call('openSupplementModal', 139)
            ->assertSet('modeSupplement', true)
            ->assertSet('supplementActivity.id', 139)
            ->assertSee('Texto complementario de prueba', false)
            ->assertSee('Información Complementaria', false)
            ->call('closeSupplementModal')
            ->assertSet('modeSupplement', false);
    }
}
