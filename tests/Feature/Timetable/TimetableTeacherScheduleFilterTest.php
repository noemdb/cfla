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
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * El select "Horario docente" solo debe listar profesores asociados a lessons
 * de secciones/grados ACTIVOS.
 */
class TimetableTeacherScheduleFilterTest extends TestCase
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

    public function test_teacher_schedule_select_only_lists_active_lesson_professors(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);

        $profesorActivo = Profesor::create([
            'user_id' => $user->id, 'name' => 'UNO', 'lastname' => 'ACTIVO',
            'ci_profesor' => '9701', 'status_active' => 'true',
        ]);
        $profesorInactivo = Profesor::create([
            'user_id' => $user->id, 'name' => 'DOS', 'lastname' => 'INACTIVO',
            'ci_profesor' => '9702', 'status_active' => 'true',
        ]);

        $gradoActivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionActiva = Seccion::factory()->create(['grado_id' => $gradoActivo->id, 'status_active' => 'true']);
        $pevActiva = $this->makePev($seccionActiva, $profesorActivo, $lapso, $pestudio);

        $gradoInactivo = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'false']);
        $seccionInactiva = Seccion::factory()->create(['grado_id' => $gradoInactivo->id, 'status_active' => 'true']);
        $pevInactiva = $this->makePev($seccionInactiva, $profesorInactivo, $lapso, $pestudio);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevActiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevInactiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('openTeacherScheduleDialog')
            ->assertSeeHtml('<option value="'.$profesorActivo->id.'">ACTIVO UNO</option>')
            ->assertDontSeeHtml('<option value="'.$profesorInactivo->id.'">INACTIVO DOS</option>');
    }
}
