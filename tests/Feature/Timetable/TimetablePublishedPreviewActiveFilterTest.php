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
 * El preview de slots publicados (loadPublishedPreview) no debe incluir
 * lecciones de grados/secciones inactivos en `unassigned`.
 */
class TimetablePublishedPreviewActiveFilterTest extends TestCase
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

    public function test_published_preview_excludes_inactive_grade_unassigned(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9501', 'status_active' => 'true',
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
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $lessonActiva = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevActiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        $lessonInactiva = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevInactiva->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        // Solo la lección activa tiene un slot publicado.
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lessonActiva->id,
            'period_id' => $period->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccionActiva->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->call('selectCalendar', $calendar->id);

        $preview = $component->get('preview');

        $this->assertNotContains($lessonInactiva->id, $preview['unassigned'], 'la lección del grado inactivo no debe estar en unassigned');
        $this->assertArrayHasKey($lessonActiva->id, $preview['assignment'], 'la lección activa debe estar en la asignación');
    }
}
