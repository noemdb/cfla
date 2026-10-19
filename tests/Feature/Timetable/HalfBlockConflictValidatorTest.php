<?php

namespace Tests\Feature\Timetable;

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
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use App\Services\Timetable\ConflictValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * ADR-TT-017 — ConflictValidator por sub-franja.
 */
class HalfBlockConflictValidatorTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{calendar: TimetableCalendar, period: TimetablePeriod, profesor: Profesor, seccion: Seccion, room: TimetableRoom, lessonA: TimetableLesson, lessonB: TimetableLesson}
     */
    private function makeContext(): array
    {
        $user = User::factory()->create(['is_planner' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Luis',
            'lastname' => 'Mitad',
            'ci_profesor' => '9488',
            'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'M', 'status_active' => 'true']);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'Horario Conflict Half Block',
            'status' => 'active',
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);
        $room = TimetableRoom::factory()->create();

        $lessonA = $this->makeLesson($calendar, $shift, $profesor, $seccion, $pestudio, $grado, $lapso, 'A');
        $lessonB = $this->makeLesson($calendar, $shift, $profesor, $seccion, $pestudio, $grado, $lapso, 'B');

        return compact('calendar', 'period', 'profesor', 'seccion', 'room', 'lessonA', 'lessonB');
    }

    private function makeLesson(
        TimetableCalendar $calendar,
        $shift,
        Profesor $profesor,
        Seccion $seccion,
        Pestudio $pestudio,
        Grado $grado,
        Lapso $lapso,
        string $suffix,
    ): TimetableLesson {
        $asignatura = Asignatura::factory()->create([
            'name' => "Materia $suffix",
            'hour_t_week' => 1,
            'hour_p_week' => 0,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapso->id,
        ]);

        return TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'half_block' => true,
        ]);
    }

    private function occupy(array $ctx, TimetableLesson $lesson, ?int $half, ?int $roomId = null): void
    {
        TimetableSlot::factory()->create([
            'calendar_id' => $ctx['calendar']->id,
            'lesson_id' => $lesson->id,
            'period_id' => $ctx['period']->id,
            'profesor_id' => $ctx['profesor']->id,
            'seccion_id' => $ctx['seccion']->id,
            'room_id' => $roomId,
            'half' => $half,
        ]);
    }

    private function validate(array $ctx, TimetableLesson $lesson, ?int $half, ?int $roomId = null): array
    {
        return app(ConflictValidator::class)->validate(
            $ctx['calendar']->id,
            $lesson->id,
            $ctx['period']->id,
            $ctx['profesor']->id,
            $ctx['seccion']->id,
            $roomId,
            null,
            null,
            $half,
        );
    }

    public function test_sequential_halves_do_not_collide(): void
    {
        $ctx = $this->makeContext();
        $this->occupy($ctx, $ctx['lessonA'], 0);

        $result = $this->validate($ctx, $ctx['lessonB'], 1);

        $this->assertTrue($result['valid'], implode(' | ', $result['reasons']));
    }

    public function test_same_half_collides(): void
    {
        $ctx = $this->makeContext();
        $this->occupy($ctx, $ctx['lessonA'], 0);

        $result = $this->validate($ctx, $ctx['lessonB'], 0);

        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_full_block_blocks_any_half(): void
    {
        $ctx = $this->makeContext();
        $this->occupy($ctx, $ctx['lessonA'], null);

        $this->assertFalse($this->validate($ctx, $ctx['lessonB'], 0)['valid']);
        $this->assertFalse($this->validate($ctx, $ctx['lessonB'], 1)['valid']);
    }

    public function test_room_reusable_across_halves(): void
    {
        $ctx = $this->makeContext();
        $this->occupy($ctx, $ctx['lessonA'], 0, $ctx['room']->id);

        $result = $this->validate($ctx, $ctx['lessonB'], 1, $ctx['room']->id);

        $this->assertTrue($result['valid'], implode(' | ', $result['reasons']));
    }
}
