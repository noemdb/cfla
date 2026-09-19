<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Livewire\Planning\Timetable\TimetableWizard as PlanningTimetableWizard;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Exportación JSON del horario de un docente (dropdown «Datos» del wizard).
 */
class TimetableTeacherJsonExportTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * Escenario mínimo: un docente, una asignatura, un bloque el lunes con aula.
     *
     * @return array<string, mixed>
     */
    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);

        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9201', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'Horario Test',
        ]);
        $shift = $this->makeShift();

        $asignatura = Asignatura::factory()->create([
            'name' => 'Matemática', 'hour_t_week' => 1, 'hour_p_week' => 0,
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

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
        ]);

        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'start_time' => '07:00:00',
            'end_time' => '07:45:00',
        ]);

        $room = TimetableRoom::factory()->create([
            'code' => 'A1', 'name' => 'Aula 1', 'type' => 'aula',
        ]);

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'room_id' => $room->id,
        ]);

        return compact(
            'user', 'lapso', 'pestudio', 'grado', 'seccion',
            'profesor', 'calendar', 'shift', 'asignatura', 'pev', 'lesson', 'period', 'room',
        );
    }

    public function test_open_dialog_preselects_current_calendar_and_first_teacher(): void
    {
        $fixture = $this->fixture();

        Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->call('openTeacherJsonDialog')
            ->assertSet('showTeacherJsonDialog', true)
            ->assertSet('teacherJsonCalendarId', $fixture['calendar']->id)
            ->assertSet('teacherJsonProfesorId', $fixture['profesor']->id);
    }

    public function test_download_teacher_schedule_json_returns_identified_structure(): void
    {
        $fixture = $this->fixture();

        $component = Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->call('openTeacherJsonDialog')
            ->call('downloadTeacherScheduleJson');

        $component->assertFileDownloaded(null, null, 'application/json; charset=UTF-8');

        $content = base64_decode((string) data_get($component->effects, 'download.content'));
        $this->assertNotSame('', $content);

        $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('cfla-timetable-teacher-schedule', $payload['format']);
        $this->assertSame('López Ana', $payload['teacher']['full_name']);
        $this->assertSame('9201', $payload['teacher']['ci']);
        $this->assertSame('Horario Test', $payload['calendar']['name']);
        $this->assertSame('Lunes', $payload['schedule'][0]['day']);

        $assignment = $payload['schedule'][0]['blocks'][0]['assignments'][0];
        $this->assertSame('Matemática', $assignment['subject']['name']);
        $this->assertSame('A', $assignment['section']['name']);
        $this->assertSame('A1', $assignment['room']['code']);
        $this->assertSame(1, $payload['totals']['weekly_blocks']);
    }

    public function test_download_without_selection_does_not_emit_file(): void
    {
        $fixture = $this->fixture();

        Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('teacherJsonCalendarId', null)
            ->set('teacherJsonProfesorId', null)
            ->call('downloadTeacherScheduleJson')
            ->assertNoFileDownloaded();
    }

    public function test_planner_can_export_teacher_schedule_json(): void
    {
        $fixture = $this->fixture();
        $planner = User::factory()->create(['is_planner' => true]);

        Livewire::actingAs($planner)
            ->test(PlanningTimetableWizard::class)
            ->call('openTeacherJsonDialog')
            ->set('teacherJsonCalendarId', $fixture['calendar']->id)
            ->set('teacherJsonProfesorId', $fixture['profesor']->id)
            ->call('downloadTeacherScheduleJson')
            ->assertFileDownloaded();
    }
}
