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
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 ticket 001a: migraciones + modelos + factories.
 */
class TimetableModelsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_calendar_has_expected_schema_columns(): void
    {
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => Lapso::factory(),
        ]);

        $this->assertDatabaseHas('timetable_calendars', [
            'id' => $calendar->id,
            'status' => 'draft',
            'version' => 0,
        ]);

        $this->assertTrue($calendar->is_editable);
        $this->assertNotNull($calendar->lapso);
    }

    public function test_shift_factory_creates_morning_and_afternoon(): void
    {
        $morning = TimetableShift::factory()->create();
        $afternoon = TimetableShift::factory()->afternoon()->create();

        $this->assertSame('M', $morning->code);
        $this->assertSame('T', $afternoon->code);
    }

    public function test_lesson_wraps_pevaluacion_and_derives_blocks(): void
    {
        $fixture = $this->lessonFixture();

        $lesson = $fixture['lesson'];
        $lesson->update(['weekly_blocks_t' => 3, 'weekly_blocks_p' => 2]);

        $this->assertSame(5, $lesson->blocks_needed);
        $this->assertNotNull($lesson->pevaluacion);
        $this->assertSame($fixture['pev']->id, $lesson->pevaluacion->id);
    }

    public function test_slot_persists_denormalized_teacher_and_section(): void
    {
        $fixture = $this->lessonFixture();

        $slot = TimetableSlot::factory()->create([
            'calendar_id' => $fixture['calendar']->id,
            'lesson_id' => $fixture['lesson']->id,
            'period_id' => $fixture['period']->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $fixture['seccion']->id,
        ]);

        $this->assertDatabaseHas('timetable_slots', [
            'id' => $slot->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $fixture['seccion']->id,
        ]);
    }

    public function test_room_factory_creates_laboratory(): void
    {
        $room = TimetableRoom::factory()->laboratory()->create();

        $this->assertSame('laboratorio', $room->type);
    }

    public function test_duplicate_slot_for_same_teacher_in_same_period_is_rejected_by_db(): void
    {
        $fixture = $this->lessonFixture();

        $seccionB = Seccion::factory()->create(['grado_id' => $fixture['grado']->id]);
        $pevB = Pevaluacion::factory()->create([
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $seccionB->id,
            'lapso_id' => $fixture['lapso']->id,
        ]);
        $lessonB = TimetableLesson::factory()->create([
            'calendar_id' => $fixture['calendar']->id,
            'pevaluacion_id' => $pevB->id,
            'shift_id' => $fixture['shift']->id,
        ]);

        TimetableSlot::factory()->create([
            'calendar_id' => $fixture['calendar']->id,
            'lesson_id' => $fixture['lesson']->id,
            'period_id' => $fixture['period']->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $fixture['seccion']->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        TimetableSlot::factory()->create([
            'calendar_id' => $fixture['calendar']->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $fixture['period']->id,
            'profesor_id' => $fixture['profesor']->id,
            'seccion_id' => $seccionB->id,
        ]);
    }

    /**
     * Cadena de fixtures: Profesor → Pestudio → Grado → Seccion → Asignatura →
     * Pensum → Lapso → Pevaluacion → TimetableCalendar/Shift/Period/Lesson.
     */
    private function lessonFixture(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Carlos',
            'lastname' => 'Méndez',
            'ci_profesor' => '12345678',
            'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 2]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = TimetableShift::factory()->create();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
        ]);

        return compact(
            'user', 'profesor', 'pestudio', 'grado', 'seccion',
            'asignatura', 'pensum', 'lapso', 'pev',
            'calendar', 'shift', 'period', 'lesson',
        );
    }
}
