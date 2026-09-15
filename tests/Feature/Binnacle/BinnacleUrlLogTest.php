<?php

namespace Tests\Feature\Binnacle;

use App\Livewire\Admin\Binnacle\UrlLogComponent;
use App\Models\BinnacleEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Log de URLs visitadas (binnacle_entries, event_type = access).
 */
class BinnacleUrlLogTest extends TestCase
{
    use DatabaseTransactions;

    private function accessEntry(User $user, string $url, ?string $method = 'GET'): BinnacleEntry
    {
        return BinnacleEntry::forceCreate([
            'uuid' => fake()->uuid(),
            'event_type' => 'access',
            'event_category' => 'user_action',
            'event_severity' => 'info',
            'title' => 'Acceso a ruta protegida',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'subject_identifier' => $user->username,
            'request_url' => $url,
            'request_method' => $method,
            'created_at' => now(),
        ]);
    }

    public function test_url_log_page_loads(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/binnacle/log/url')->assertOk();
    }

    public function test_url_log_aggregates_visits_by_path(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $token = 'probeagg'.uniqid();

        $this->accessEntry($userA, "https://probe.test/{$token}/timetable");
        $this->accessEntry($userA, "https://probe.test/{$token}/timetable?x=1");
        $this->accessEntry($userB, "https://probe.test/{$token}/binnacle");

        $component = Livewire::actingAs($admin)
            ->test(UrlLogComponent::class)
            ->set('search', $token);

        $urls = $component->viewData('urls')->getCollection();

        $timetable = $urls->firstWhere('path', "/{$token}/timetable");
        $this->assertNotNull($timetable);
        $this->assertSame(2, $timetable->visits);
        $this->assertSame(1, $timetable->users);

        $this->assertSame(3, $component->viewData('summary')['visits']);
        $this->assertSame(2, $component->viewData('summary')['urls']);

        // El chart usa las rutas agregadas.
        $chart = collect($component->get('chartData'))->pluck('x')->all();
        $this->assertContains("/{$token}/timetable", $chart);
    }

    public function test_url_log_search_filters_paths(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $token = 'probesearch'.uniqid();

        $this->accessEntry($user, "https://probe.test/{$token}/timetable");
        $this->accessEntry($user, "https://probe.test/{$token}/binnacle");

        $component = Livewire::actingAs($admin)
            ->test(UrlLogComponent::class)
            ->set('search', $token.'/timetable');

        $urls = $component->viewData('urls')->getCollection();
        $this->assertCount(1, $urls);
        $this->assertSame("/{$token}/timetable", $urls->first()->path);
    }

    public function test_url_log_paginates_listing(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $token = 'probepage'.uniqid();

        foreach (['a', 'b', 'c'] as $suffix) {
            $this->accessEntry($user, "https://probe.test/{$token}/{$suffix}");
        }

        $component = Livewire::actingAs($admin)
            ->test(UrlLogComponent::class)
            ->set('search', $token)
            ->set('paginate', 2);

        $this->assertSame(3, $component->viewData('urls')->total());
        $this->assertCount(2, $component->viewData('urls')->getCollection());

        $component->call('gotoPage', 2);
        $this->assertCount(1, $component->viewData('urls')->getCollection());
    }

    public function test_live_toggle_controls_polling(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $component = Livewire::actingAs($admin)->test(UrlLogComponent::class);

        $component->assertSet('live', true);
        $this->assertStringContainsString('wire:poll', $component->html());

        $component->set('live', false);
        $this->assertStringNotContainsString('wire:poll', $component->html());
    }
}
