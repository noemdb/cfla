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
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * CRUD del Paso 3 (Clases). Los cambios son en memoria: impactan la base de
 * datos recién al pulsar «Guardar clases y continuar» (saveLessons).
 */
class TimetableLessonCrudTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{user: User, calendar: TimetableCalendar, seccion: Seccion, pev: Pevaluacion}
     */
    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '8801', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'draft',
        ]);
        $this->makeShift();

        return compact('user', 'calendar', 'seccion', 'pev');
    }

    public function test_add_lesson_only_persists_on_save(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->call('addLesson', $f['pev']->id);

        // En memoria: agregada, sin tocar la base de datos todavía.
        $this->assertTrue((bool) ($component->get('selectedPevs')[$f['pev']->id] ?? false));
        $this->assertDatabaseMissing('timetable_lessons', [
            'calendar_id' => $f['calendar']->id,
            'pevaluacion_id' => $f['pev']->id,
        ]);

        $component->call('saveLessons');

        $this->assertDatabaseHas('timetable_lessons', [
            'calendar_id' => $f['calendar']->id,
            'pevaluacion_id' => $f['pev']->id,
        ]);
    }

    public function test_inline_edit_only_persists_on_save(): void
    {
        $f = $this->fixture();
        $shiftId = (int) TimetableShift::query()->value('id');

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $f['calendar']->id, 'pevaluacion_id' => $f['pev']->id,
            'shift_id' => $shiftId, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('selectedPevs', [$f['pev']->id => true])
            ->call('loadLessons');

        // Edición en memoria (como haría wire:model diferido).
        $component->set('lessons.'.$f['pev']->id.'.weekly_blocks_t', 5);

        $this->assertSame(2, (int) $lesson->fresh()->weekly_blocks_t, 'sin guardar, la BD no cambia');

        $component->call('saveLessons');

        $this->assertSame(5, (int) $lesson->fresh()->weekly_blocks_t);
    }

    public function test_delete_lesson_removes_lesson_and_slots_on_save(): void
    {
        $f = $this->fixture();

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $f['calendar']->id, 'pevaluacion_id' => $f['pev']->id,
            'shift_id' => TimetableShift::query()->value('id'),
            'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $f['calendar']->id, 'shift_id' => $lesson->shift_id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $slot = TimetableSlot::factory()->create([
            'calendar_id' => $f['calendar']->id, 'lesson_id' => $lesson->id,
            'period_id' => $period->id, 'profesor_id' => $f['pev']->profesor_id,
            'seccion_id' => $f['seccion']->id,
        ]);

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('selectedPevs', [$f['pev']->id => true])
            ->call('loadLessons')
            ->call('deleteLesson', $f['pev']->id);

        // Retirada en memoria: la BD sigue intacta hasta guardar.
        $this->assertDatabaseHas('timetable_lessons', ['id' => $lesson->id]);
        $this->assertDatabaseHas('timetable_slots', ['id' => $slot->id]);

        $component->call('saveLessons');

        $this->assertDatabaseMissing('timetable_lessons', ['id' => $lesson->id]);
        $this->assertDatabaseMissing('timetable_slots', ['id' => $slot->id]);
    }

    public function test_switcher_renders_section_lock_button(): void
    {
        $f = $this->fixture();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('activeSeccionId', $f['seccion']->id);

        $component->assertSee('section-lock-switcher-'.$f['seccion']->id, false)
            ->assertSee('Bloquear el horario de esta sección');

        $component->call('toggleSectionTimetableLock', $f['seccion']->id);

        $this->assertTrue((bool) $f['seccion']->fresh()->timetable_locked);
        $component->assertSee('Desbloquear el horario de esta sección');
    }

    public function test_locked_section_blocks_add_and_delete(): void
    {
        $f = $this->fixture();
        $f['seccion']->update(['timetable_locked' => true]);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $f['calendar']->id, 'pevaluacion_id' => $f['pev']->id,
            'shift_id' => TimetableShift::query()->value('id'),
            'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('selectedPevs', [$f['pev']->id => true])
            ->call('loadLessons');

        $component->call('deleteLesson', $f['pev']->id);
        $this->assertDatabaseHas('timetable_lessons', ['id' => $lesson->id]);

        $component->call('addLesson', $f['pev']->id);
        $this->assertSame(
            1,
            TimetableLesson::query()
                ->where('calendar_id', $f['calendar']->id)
                ->where('pevaluacion_id', $f['pev']->id)
                ->count(),
        );
    }
}
