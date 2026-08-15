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
use App\Services\Timetable\TimetableViewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §8 — Publicación y exportación (vista firmada + PDF).
 */
class TimetablePublicationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_signed_public_section_view_works_without_auth(): void
    {
        $fixture = $this->publicationFixture();

        $url = URL::temporarySignedRoute('timetable.public.section', now()->addHour(), [
            'calendar' => $fixture['calendar']->id,
            'seccion' => $fixture['seccionA']->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Sección')
            ->assertSee($fixture['asignaturaA']->name);
    }

    public function test_signed_public_view_rejects_invalid_signature(): void
    {
        $fixture = $this->publicationFixture();

        // Ruta sin firma válida.
        $this->get("/timetable/section/{$fixture['calendar']->id}/{$fixture['seccionA']->id}")
            ->assertForbidden();
    }

    public function test_public_view_requires_active_calendar(): void
    {
        $fixture = $this->publicationFixture();
        $fixture['calendar']->update(['status' => 'draft']);

        $url = URL::temporarySignedRoute('timetable.public.section', now()->addHour(), [
            'calendar' => $fixture['calendar']->id,
            'seccion' => $fixture['seccionA']->id,
        ]);

        $this->get($url)->assertNotFound();
    }

    public function test_section_pdf_streams(): void
    {
        $fixture = $this->publicationFixture();
        $user = User::factory()->create(['is_coordinacion' => true]);

        $this->actingAs($user)
            ->get(route('app.coordinacion.timetable.pdf.section', [
                $fixture['calendar']->id,
                $fixture['seccionA']->id,
            ]))
            ->assertOk();
    }

    public function test_view_service_builds_grid_by_day_and_order(): void
    {
        $fixture = $this->publicationFixture();

        $service = app(TimetableViewService::class);
        $grid = $service->gridForSection($fixture['calendar'], $fixture['seccionA']->id);

        $this->assertTrue($grid->has(1));
        $this->assertNotNull($grid->get(1)->get(1)); // lunes, primer bloque
        $this->assertNull($grid->get(1)->get(2)); // martes, primer bloque (sin slot)
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private function publicationFixture(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '4001', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id]);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id]);

        $asignaturaA = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 0]);
        $pensumA = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignaturaA->id,
        ]);

        $lapso = Lapso::factory()->create();
        $pevA = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccionA->id,
            'pensum_id' => $pensumA->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'status' => 'active']);
        $shift = TimetableShift::factory()->create();

        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $room = TimetableRoom::factory()->create();

        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccionA->id,
            'room_id' => $room->id,
        ]);

        return compact('calendar', 'shift', 'period', 'lesson', 'profesor', 'seccionA', 'seccionB', 'asignaturaA', 'room');
    }
}
