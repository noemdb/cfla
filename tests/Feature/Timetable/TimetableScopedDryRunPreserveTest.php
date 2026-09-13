<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
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
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * En dry-runs acotados, el `unassigned` preservado de lecciones fuera de alcance
 * no debe incluir lecciones de grados/secciones inactivos.
 */
class TimetableScopedDryRunPreserveTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function makePev(Seccion $seccion, Profesor $profesor, Lapso $lapso, Pestudio $pestudio): Pevaluacion
    {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
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

    public function test_scoped_dry_run_drops_inactive_grade_from_preserved_unassigned(): void
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9401', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();

        $gradoActivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionActiva = Seccion::factory()->create(['grado_id' => $gradoActivo->id, 'status_active' => 'true']);
        $pevActiva = $this->makePev($seccionActiva, $profesor, $lapso, $pestudio);

        $gradoInactivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'false']);
        $seccionInactiva = Seccion::factory()->create(['grado_id' => $gradoInactivo->id, 'status_active' => 'true']);
        $pevInactiva = $this->makePev($seccionInactiva, $profesor, $lapso, $pestudio);

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

        $lessonActiva = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevActiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonInactiva = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevInactiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        // Preview previo que tenía como no asignada la lección del grado inactivo.
        $calendar->update([
            'preview_payload' => [
                'dry_run' => true,
                'assignment' => [],
                'unassigned' => [$lessonInactiva->id],
            ],
        ]);

        GenerateTimetableJob::dispatchSync($calendar->id, dryRun: true, pevaluacionIds: [$pevActiva->id]);

        $payload = $calendar->fresh()->preview_payload;

        $this->assertNotContains($lessonInactiva->id, $payload['unassigned'], 'la lección del grado inactivo no debe preservarse');
        $this->assertNotContains($lessonActiva->id, $payload['unassigned'], 'la lección activa debe quedar asignada');
    }
}
