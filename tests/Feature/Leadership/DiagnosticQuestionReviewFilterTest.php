<?php

namespace Tests\Feature\Leadership;

use App\Livewire\Leadership\DiagnosticQuestionReview;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regresión: la cascada de filtros (pestudio → grado → pensum → profesor)
 * del revisor de preguntas no debe lanzar PropertyNotFoundException
 * al actualizar cada filtro (render() lee todas las props).
 */
class DiagnosticQuestionReviewFilterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_filter_cascade_renders_without_property_errors(): void
    {
        $user = User::factory()->create(['is_leadership' => true, 'is_active' => 'enable']);

        $this->actingAs($user);

        Livewire::test(DiagnosticQuestionReview::class)
            ->set('filterPestudioId', '1')
            ->assertOk()
            ->set('filterGradoId', '1')
            ->assertOk()
            ->set('filterPensumId', '1')
            ->assertOk()
            ->set('filterProfesorId', '1')
            ->assertOk();
    }
}
