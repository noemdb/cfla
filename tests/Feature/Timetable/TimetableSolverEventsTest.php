<?php

namespace Tests\Feature\Timetable;

use App\Events\Timetable\SolverAttemptCompleted;
use App\Events\Timetable\SolverCompleted;
use App\Events\Timetable\SolverStarted;
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
use Illuminate\Support\Facades\Event;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * F5 (TT-CFP-13) — El job emite eventos de ciclo de vida del solver para que la
 * UI actualice el progreso en tiempo real (Reverb).
 */
class TimetableSolverEventsTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_job_broadcasts_solver_lifecycle_events(): void
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9301', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
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

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Event::fake([SolverStarted::class, SolverAttemptCompleted::class, SolverCompleted::class]);

        GenerateTimetableJob::dispatchSync($calendar->id, dryRun: true);

        Event::assertDispatched(SolverStarted::class);
        Event::assertDispatched(SolverAttemptCompleted::class);
        Event::assertDispatched(SolverCompleted::class);
    }
}
