<?php

namespace Tests\Feature\Livewire\Planning\Lms;

use App\Livewire\Planning\Lms\LessonPendingCount;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class LessonPendingCountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_renders_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(LessonPendingCount::class)
            ->assertStatus(200);
    }

    public function test_echo_listener_includes_event_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(LessonPendingCount::class);

        $listeners = (new ReflectionMethod(LessonPendingCount::class, 'getListeners'))
            ->invoke($component->instance());

        $expected = 'echo-private:App.Models.User.'.$user->id.',.lesson.scheduled';
        $this->assertArrayHasKey($expected, $listeners);
        $this->assertSame('refreshCountFromEcho', $listeners[$expected]);
        $this->assertArrayHasKey('lesson-scheduled', $listeners);
        $this->assertSame('refreshCount', $listeners['lesson-scheduled']);
        $this->assertArrayNotHasKey('lesson-scheduled-toast', $listeners);
    }

    /** @test */
    public function toast_se_muestra_una_sola_vez_por_leccion_programada(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'activity_id' => 999,
            'teacher_name' => 'Prof. Carlos',
            'lesson_title' => 'Lección de álgebra',
            'message' => 'Prof. Carlos ha programado la lección para aprobación.',
            'url' => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        ];

        $component = Livewire::test(LessonPendingCount::class);

        // Primer broadcast → toast
        $component->call('refreshCountFromEcho', $payload)
            ->assertDispatched('wireui:notification');

        // Mismo broadcast de nuevo (varios badges en la página) → NO se repite
        $component->call('refreshCountFromEcho', $payload)
            ->assertNotDispatched('wireui:notification');

        // Lección distinta → toast de nuevo
        $payload['activity_id'] = 1000;
        $component->call('refreshCountFromEcho', $payload)
            ->assertDispatched('wireui:notification');
    }
}
