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
 * Paso 4 · Disponibilidad: el select de profesores solo muestra activos y al
 * seleccionar uno carga su grilla day × período (con checked por defecto).
 */
class TimetableStep4Test extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_select_only_shows_active_profesors(): void
    {
        [$active, $inactive] = $this->setupStep4();

        Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->call('goToStep', 4)
            ->assertSee($active->lastname.', '.$active->name, false)
            ->assertDontSee($inactive->lastname.', '.$inactive->name, false);
    }

    public function test_selecting_profesor_loads_availability(): void
    {
        $active = $this->setupStep4()[0];

        $c = Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->call('goToStep', 4)
            ->set('selectedProfesorId', $active->id);

        $c->assertSet('selectedProfesorId', $active->id);
        $this->assertNotEmpty($c->get('availability'));
        // Por defecto, todos los períodos están disponibles (checked).
        $this->assertTrue(collect($c->get('availability')[$active->id])->flatten()->every(fn ($v) => $v === true));
    }

    public function test_select_only_shows_profesors_associated_with_selected_step3_lessons(): void
    {
        [$active, $inactive] = $this->setupStep4();
        $other = Profesor::create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Luis',
            'lastname' => 'Otro',
            'ci_profesor' => '2003',
            'status_active' => 'true',
        ]);
        $pev = Pevaluacion::where('profesor_id', $active->id)->first();
        $otherPev = $pev->replicate();
        $otherPev->profesor_id = $other->id;
        $otherPev->save();

        Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->set('selectedPevs', [$pev->id])
            ->call('goToStep', 4)
            ->assertSee($active->lastname.', '.$active->name, false)
            ->assertDontSee($other->lastname.', '.$other->name, false)
            ->assertDontSee($inactive->lastname.', '.$inactive->name, false);
    }

    public function test_uncheck_all_marks_profesor_unavailable(): void
    {
        $active = $this->setupStep4()[0];

        $c = Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->call('goToStep', 4)
            ->set('selectedProfesorId', $active->id)
            ->call('uncheckAllAvailability');

        $this->assertTrue(collect($c->get('availability')[$active->id])->flatten()->every(fn ($v) => $v === false));
    }

    public function test_mark_all_available_sets_profesor_back_available(): void
    {
        $active = $this->setupStep4()[0];

        $c = Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->call('goToStep', 4)
            ->set('selectedProfesorId', $active->id)
            ->call('markAllAvailable');

        $this->assertTrue(collect($c->get('availability')[$active->id])->flatten()->every(fn ($v) => $v === true));
    }

    public function test_fill_availability_from_lessons_marks_taught_blocks(): void
    {
        [$active] = $this->setupStep4();
        $pev = Pevaluacion::where('profesor_id', $active->id)->first();
        $period = TimetablePeriod::where('calendar_id', $this->calendarId)->first();
        $lesson = TimetableLesson::create([
            'calendar_id' => $this->calendarId, 'pevaluacion_id' => $pev->id, 'shift_id' => $period->shift_id,
        ]);
        TimetableSlot::create([
            'calendar_id' => $this->calendarId, 'lesson_id' => $lesson->id, 'period_id' => $period->id,
            'profesor_id' => $active->id, 'seccion_id' => $pev->seccion_id,
        ]);

        $c = Livewire::actingAs($this->coordinator())
            ->test(TimetableWizard::class)
            ->set('calendarId', $this->calendarId)
            ->call('goToStep', 4)
            ->set('selectedProfesorId', $active->id)
            ->call('fillAvailabilityFromLessons');

        // Solo el bloque donde dicta (período 07:00 → bloque 1 de la rejilla) queda disponible.
        $this->assertTrue(collect($c->get('availability')[$active->id])->flatten()->contains(true));
        $this->assertSame(1, collect($c->get('availability')[$active->id])->flatten()->filter(fn ($v) => $v === true)->count());
    }

    // ─── Fixtures ──────────────────────────────────────────────

    private ?int $calendarId = null;

    private function coordinator(): User
    {
        return User::factory()->create(['is_coordinacion' => true]);
    }

    /** @return array{0: Profesor, 1: Profesor} [activo, inactivo] */
    private function setupStep4(): array
    {
        $lapso = Lapso::factory()->create();
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $this->calendarId = $calendar->id;
        $shift = $this->makeShift();

        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id]);
        $asig = Asignatura::factory()->create(['hour_t_week' => 3, 'hour_p_week' => 2]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asig->id]);

        $active = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '2001', 'status_active' => 'true',
        ]);
        $inactive = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Karla', 'lastname' => 'Díaz',
            'ci_profesor' => '2002', 'status_active' => 'false',
        ]);

        Pevaluacion::factory()->create([
            'profesor_id' => $active->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        Pevaluacion::factory()->create([
            'profesor_id' => $inactive->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        return [$active, $inactive];
    }
}
