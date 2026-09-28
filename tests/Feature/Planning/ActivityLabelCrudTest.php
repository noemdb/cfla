<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\ActivityLabel\IndexComponent;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\ActivityFieldLabel;
use App\Models\app\Academy\Peducativo;
use App\Models\User;
use App\Services\ActivityLabelResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * CRUD de etiquetas de Activity/Achievement por Peducativo.
 */
class ActivityLabelCrudTest extends TestCase
{
    use DatabaseTransactions;

    private function planner(): User
    {
        return User::factory()->create(['is_planner' => true]);
    }

    public function test_lists_seeded_labels_for_first_peducativo(): void
    {
        $user = $this->planner();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->set('paginate', 50)
            ->assertOk()
            ->assertSee('Tema generador y Énfasis');
    }

    public function test_edit_label_updates_resolver(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        $row = ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('edit', $row->id)
            ->set('label', 'Tema Generador Editado')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'Tema Generador Editado',
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic']
        );
    }

    public function test_reset_restores_default(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        $row = ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();
        $row->update(['label' => 'Personalizado']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('confirmReset', $row->id)
            ->call('resetToDefault')
            ->assertHasNoErrors();

        $this->assertSame(
            Activity::COLUMN_COMMENTS['topic'],
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic']
        );
    }

    public function test_guest_cannot_access_route(): void
    {
        $this->get(route('app.planning.activity-labels.index'))->assertRedirect('/login');
    }

    public function test_create_label(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        // Campo inexistente para poder crearlo.
        ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->delete();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('create')
            ->set('peducativo_id', $peducativo->id)
            ->set('model', 'activity')
            ->set('field', 'topic')
            ->set('label', 'Tema Nuevo')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'Tema Nuevo',
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic']
        );
    }

    public function test_create_duplicate_fails_validation(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('create')
            ->set('peducativo_id', $peducativo->id)
            ->set('model', 'activity')
            ->set('field', 'topic')
            ->set('label', 'Duplicado')
            ->call('save')
            ->assertHasErrors(['field']);
    }

    public function test_virtual_label_contenido_falls_back_and_overrides(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic_thematic_referentes')
            ->delete();
        ActivityLabelResolver::flush($peducativo->id);

        $this->assertSame(
            'Referentes teórico-prácticos',
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic_thematic_referentes']
        );

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('create')
            ->set('peducativo_id', $peducativo->id)
            ->set('model', 'activity')
            ->set('field', 'topic_thematic_referentes')
            ->set('label', 'Contenido Personalizado')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'Contenido Personalizado',
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic_thematic_referentes']
        );
    }

    public function test_delete_label_falls_back_to_default(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        $row = ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('confirmDelete', $row->id)
            ->call('destroy')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('activity_field_labels', ['id' => $row->id]);
        $this->assertSame(
            Activity::COLUMN_COMMENTS['topic'],
            ActivityLabelResolver::forPeducativo($peducativo->id)['topic']
        );
    }

    public function test_clone_copies_label_to_target_peducativo(): void
    {
        $user = $this->planner();
        $peducativos = Peducativo::where('status_active', 'true')->orderBy('order')->take(2)->get();
        $this->assertCount(2, $peducativos);
        [$source, $target] = [$peducativos[0], $peducativos[1]];

        $row = ActivityFieldLabel::where('peducativo_id', $source->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();
        $row->update(['label' => 'Tema Origen']);

        ActivityFieldLabel::where('peducativo_id', $target->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->delete();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('openClone', $row->id)
            ->set('cloneTargetPeducativoId', (string) $target->id)
            ->call('cloneToTarget')
            ->assertHasNoErrors();

        $this->assertSame(
            'Tema Origen',
            ActivityLabelResolver::forPeducativo($target->id)['topic']
        );
    }

    public function test_clone_to_same_peducativo_fails_validation(): void
    {
        $user = $this->planner();
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        $row = ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('openClone', $row->id)
            ->set('cloneTargetPeducativoId', (string) $peducativo->id)
            ->call('cloneToTarget')
            ->assertHasErrors(['cloneTargetPeducativoId']);
    }

    public function test_edit_does_not_touch_other_peducativos(): void
    {
        $user = $this->planner();
        $peducativos = Peducativo::where('status_active', 'true')->orderBy('order')->take(2)->get();
        $this->assertCount(2, $peducativos);
        [$source, $other] = [$peducativos[0], $peducativos[1]];

        $row = ActivityFieldLabel::where('peducativo_id', $source->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail();
        $otherBefore = ActivityFieldLabel::where('peducativo_id', $other->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail()
            ->toArray();

        Livewire::actingAs($user)
            ->test(IndexComponent::class)
            ->call('edit', $row->id)
            ->set('label', 'Solo Este Programa')
            ->call('save')
            ->assertHasNoErrors();

        // El otro peducativo conserva fila, label y placeholder intactos.
        $otherAfter = ActivityFieldLabel::where('peducativo_id', $other->id)
            ->where('model', 'activity')
            ->where('field', 'topic')
            ->firstOrFail()
            ->toArray();

        $this->assertSame($otherBefore['label'], $otherAfter['label']);
        $this->assertSame($otherBefore['placeholder'], $otherAfter['placeholder']);
        $this->assertSame(
            'Solo Este Programa',
            ActivityLabelResolver::forPeducativo($source->id)['topic']
        );
        $this->assertSame(
            $otherBefore['label'],
            ActivityLabelResolver::forPeducativo($other->id)['topic']
        );
    }

    public function test_virtual_label_ods_falls_back_to_default(): void
    {
        $peducativo = Peducativo::where('status_active', 'true')->orderBy('order')->firstOrFail();

        ActivityFieldLabel::where('peducativo_id', $peducativo->id)
            ->where('model', 'activity')
            ->where('field', 'ODS_sistematizacion')
            ->delete();
        ActivityLabelResolver::flush($peducativo->id);

        $this->assertSame(
            'ODS / Sistematización',
            ActivityLabelResolver::forPeducativo($peducativo->id)['ODS_sistematizacion']
        );
    }
}
