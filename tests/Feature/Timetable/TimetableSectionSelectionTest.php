<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Regresión: el "seleccionar todas" de una sección no debe arrastrar las
 * pevaluaciones de otra sección (aunque tengan slots generados).
 */
class TimetableSectionSelectionTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function makePev(Seccion $seccion, Profesor $profesor, Lapso $lapso, Pestudio $pestudio, int $n): Pevaluacion
    {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2 + $n, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);

        return Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapso->id,
        ]);
    }

    public function test_select_all_for_section_does_not_select_other_section(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);

        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9001', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
        ]);

        $pevA = $this->makePev($seccionA, $profesor, $lapso, $pestudio, 0);
        $pevB = $this->makePev($seccionB, $profesor, $lapso, $pestudio, 1);

        // La sección B tiene una lección con slot generado: antes del fix, ese
        // slot re-seleccionaba B al tocar la sección A.
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pevB->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 2,
            'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccionB->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('currentStep', 3)
            ->call('selectCalendar', $calendar->id)
            ->set('selectedPevs', []);

        $component->call('toggleSelectAllForSection', $seccionA->id);

        $selected = $component->get('selectedPevs');

        $this->assertArrayHasKey($pevA->id, $selected, 'la pevaluación de la sección A debe quedar seleccionada');
        $this->assertArrayNotHasKey($pevB->id, $selected, 'la pevaluación de la sección B no debe seleccionarse');
    }
}
