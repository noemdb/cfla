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
 * Los checkboxes de bloqueo del Paso 5 deben reflejar el estado real de
 * `timetable_slots.locked` (cargado en `mount`), no un valor por defecto.
 */
class TimetablePreviewSlotLockTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{0: TimetableWizard, 1: int, 2: int, 3: int, 4: int}
     */
    private function makeCalendarWithSlots(bool $firstLocked, bool $secondLocked): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create(['user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'L', 'ci_profesor' => '9701', 'status_active' => 'true']);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $lapso = Lapso::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);

        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create(['profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active']);
        $shift = $this->makeShift();
        $p1 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false]);
        $p2 = TimetablePeriod::factory()->create(['calendar_id' => $calendar->id, 'shift_id' => $shift->id, 'day_of_week' => 2, 'order_in_day' => 1, 'is_break' => false]);

        $lesson = TimetableLesson::factory()->create(['calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id, 'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0]);

        TimetableSlot::factory()->create(['calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $p1->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'locked' => $firstLocked]);
        TimetableSlot::factory()->create(['calendar_id' => $calendar->id, 'lesson_id' => $lesson->id, 'period_id' => $p2->id, 'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id, 'locked' => $secondLocked]);

        return [$user, $calendar, $seccion, $lesson, $p1, $p2];
    }

    /**
     * @return array<int, bool> locked por period_id para la lección dada
     */
    private function previewLocksByPeriod(TimetableWizard $instance, int $lessonId): array
    {
        return collect($instance->preview['assignment'][$lessonId] ?? [])
            ->mapWithKeys(fn (array $slot): array => [(int) $slot['period_id'] => (bool) ($slot['locked'] ?? false)])
            ->all();
    }

    public function test_mount_syncs_preview_lock_state_from_database(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeCalendarWithSlots(true, false);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class, ['calendarId' => $calendar->id])
            ->set('activeSeccionId', $seccion->id)
            ->set('currentStep', 5)
            ->call('$refresh');

        $locks = $this->previewLocksByPeriod($component->instance(), $lesson->id);

        $this->assertTrue($locks[$p1->id] ?? false, 'el slot bloqueado debe reflejar locked=true');
        $this->assertFalse($locks[$p2->id] ?? false, 'el slot desbloqueado debe reflejar locked=false');
    }

    public function test_preview_lock_sync_overrides_stale_preview(): void
    {
        [$user, $calendar, $seccion, $lesson, $p1, $p2] = $this->makeCalendarWithSlots(false, false);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class, ['calendarId' => $calendar->id]);

        $instance = $component->instance();

        // Simula un preview obsoleto con todos los slots bloqueados.
        $instance->preview['assignment'][$lesson->id] = [
            ['period_id' => $p1->id, 'room_id' => null, 'is_practical' => false, 'locked' => true],
            ['period_id' => $p2->id, 'room_id' => null, 'is_practical' => false, 'locked' => true],
        ];

        $method = new \ReflectionMethod($instance, 'syncPreviewSlotLocksFromDatabase');
        $method->setAccessible(true);
        $method->invoke($instance);

        $this->assertFalse((bool) $instance->preview['assignment'][$lesson->id][0]['locked']);
        $this->assertFalse((bool) $instance->preview['assignment'][$lesson->id][1]['locked']);
    }
}
