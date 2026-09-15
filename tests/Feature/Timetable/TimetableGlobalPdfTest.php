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
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * PDFs consolidados del toolbar: todos los P.Estudios y todos los profesores
 * de los calendarios activos del lapso vigente.
 */
class TimetableGlobalPdfTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function fixture(): array
    {
        // Usa el lapso vigente real para que los consolidados lo incluyan.
        $lapso = Lapso::current() ?? Lapso::factory()->create();

        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '8801', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active',
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $period->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);

        return compact('user', 'lapso', 'pestudio', 'grado', 'seccion', 'profesor', 'calendar');
    }

    public function test_all_pestudios_pdf_streams(): void
    {
        $fixture = $this->fixture();

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-pestudios'))
            ->assertOk();
    }

    public function test_all_teachers_pdf_streams(): void
    {
        $fixture = $this->fixture();

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers'))
            ->assertOk();
    }

    public function test_all_pestudios_returns_404_without_active_calendars(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        // Sin calendarios activos en el lapso vigente.
        TimetableCalendar::query()->update(['status' => 'archived']);

        $this->actingAs($user)
            ->get(route('app.coordinacion.timetable.pdf.all-pestudios'))
            ->assertNotFound();
    }
}
