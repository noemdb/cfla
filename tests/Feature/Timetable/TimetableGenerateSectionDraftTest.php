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
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * El botón "Draft sección (solver)" genera un draft acotado a la sección activa
 * usando el solver (sin IA).
 */
class TimetableGenerateSectionDraftTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_generate_section_draft_runs_solver_for_active_section(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1001', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $lessonIds = [];
        foreach ([1, 2] as $n) {
            $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);
            $lessonIds[] = TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
            ])->id;
        }

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $seccion->id)
            ->call('generateSectionDraft');

        $preview = $component->get('preview');

        $this->assertNotNull($preview);
        $this->assertSame('preview_ready', $component->get('generationState'));
        foreach ($lessonIds as $id) {
            $this->assertArrayHasKey((string) $id, $preview['assignment'], "la lección {$id} debe quedar asignada en el draft");
        }
    }
}
