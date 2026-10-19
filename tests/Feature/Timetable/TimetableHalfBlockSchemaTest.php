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
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * ADR-TT-017 — esquema e índices de la mitad de bloque.
 *
 * Verifica que las claves generadas con sufijo de mitad permiten dos mitades
 * secuenciales del mismo período y siguen bloqueando la misma mitad.
 */
class TimetableHalfBlockSchemaTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{0: TimetableCalendar, 1: TimetablePeriod, 2: Profesor, 3: Seccion, 4: TimetableRoom, 5: TimetableLesson, 6: TimetableLesson}
     */
    private function makeContext(): array
    {
        $user = User::factory()->create(['is_planner' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Ana',
            'lastname' => 'Mitad',
            'ci_profesor' => '9477',
            'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'H', 'status_active' => 'true']);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'Horario Half Block',
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

        return [$calendar, $period, $profesor, $seccion, $room, $lessonA, $lessonB];
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

    public function test_half_block_defaults_and_cast(): void
    {
        [, , , , , $lessonA] = $this->makeContext();

        $this->assertTrue((bool) $lessonA->fresh()->half_block);
    }

    public function test_two_halves_of_same_period_coexist(): void
    {
        [$calendar, $period, $profesor, $seccion, , $lessonA, $lessonB] = $this->makeContext();

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 1,
        ]);

        $this->assertSame(2, TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('period_id', $period->id)
            ->count());
    }

    public function test_same_half_collides(): void
    {
        [$calendar, $period, $profesor, $seccion, , $lessonA, $lessonB] = $this->makeContext();

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 0,
        ]);

        $this->expectException(QueryException::class);

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'half' => 0,
        ]);
    }

    public function test_room_can_be_reused_across_halves(): void
    {
        [$calendar, $period, $profesor, $seccion, $room, $lessonA, $lessonB] = $this->makeContext();

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'room_id' => $room->id,
            'half' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'room_id' => $room->id,
            'half' => 1,
        ]);

        $this->assertSame(2, TimetableSlot::query()
            ->where('calendar_id', $calendar->id)
            ->where('room_id', $room->id)
            ->count());
    }
}
