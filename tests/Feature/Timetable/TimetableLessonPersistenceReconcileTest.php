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
use App\Services\Timetable\TimetableLessonPersistenceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Regresión: persistir una lección con menos bloques que slots guardados debe
 * reconciliar los slots sobrantes; si no, el paso 3 (bloques) y el paso 5
 * (slots) quedan inconsistentes (p. ej. 1 bloque con 2 slots).
 */
class TimetableLessonPersistenceReconcileTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{calendar: TimetableCalendar, shift: \App\Models\app\Timetable\TimetableShift, pev: Pevaluacion, lesson: TimetableLesson}
     */
    private function fixture(int $blocksT, int $slotCount): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '6001', 'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create();
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id]);
        $shift = $this->makeShift();

        $periods = [];
        for ($i = 0; $i < $slotCount; $i++) {
            $periods[] = TimetablePeriod::factory()->create([
                'calendar_id' => $calendar->id,
                'shift_id' => $shift->id,
                'day_of_week' => $i + 1,
                'order_in_day' => 1,
                'is_break' => false,
            ]);
        }

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => $blocksT, 'weekly_blocks_p' => 0, 'locked' => true,
        ]);

        foreach ($periods as $period) {
            TimetableSlot::factory()->create([
                'calendar_id' => $calendar->id,
                'lesson_id' => $lesson->id,
                'period_id' => $period->id,
                'profesor_id' => $profesor->id,
                'seccion_id' => $seccion->id,
                'locked' => true,
            ]);
        }

        return compact('calendar', 'shift', 'pev', 'lesson');
    }

    private function persist(array $fixture, int $blocksT): void
    {
        $fixture['lesson']->update(['weekly_blocks_t' => $blocksT]);

        app(TimetableLessonPersistenceService::class)->persist($fixture['calendar']->id, [
            $fixture['pev']->id => [
                'pev_id' => $fixture['pev']->id,
                'shift_id' => $fixture['shift']->id,
                'weekly_blocks_t' => $blocksT,
                'weekly_blocks_p' => 0,
                'room_type_required' => null,
                'is_half_group' => false,
                'priority' => 0,
                'locked' => true,
            ],
        ]);
    }

    public function test_persist_trims_surplus_slots_when_blocks_decrease(): void
    {
        $fixture = $this->fixture(blocksT: 2, slotCount: 2);

        $this->persist($fixture, blocksT: 1);

        $this->assertSame(
            1,
            TimetableSlot::query()->where('lesson_id', $fixture['lesson']->id)->count(),
        );
    }

    public function test_persist_preserves_partial_slots_when_blocks_increase(): void
    {
        $fixture = $this->fixture(blocksT: 1, slotCount: 1);

        $this->persist($fixture, blocksT: 2);

        $this->assertSame(
            1,
            TimetableSlot::query()->where('lesson_id', $fixture['lesson']->id)->count(),
        );
    }

    public function test_persist_keeps_slots_when_count_matches(): void
    {
        $fixture = $this->fixture(blocksT: 2, slotCount: 2);

        $this->persist($fixture, blocksT: 2);

        $this->assertSame(
            2,
            TimetableSlot::query()->where('lesson_id', $fixture['lesson']->id)->count(),
        );
    }
}
