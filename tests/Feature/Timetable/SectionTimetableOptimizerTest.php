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
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use App\Services\Timetable\SectionTimetableOptimizer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

class SectionTimetableOptimizerTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private Lapso $lapso;

    private TimetableShift $shift;

    private User $user;

    private Pestudio $pestudio;

    private Grado $grado;

    private Seccion $seccion;

    private TimetableCalendar $calendar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lapso = Lapso::factory()->create();
        $this->shift = $this->makeShift();
        $this->user = User::factory()->create();

        $this->pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $this->grado = Grado::factory()->create(['pestudio_id' => $this->pestudio->id, 'status_active' => 'true']);
        $this->seccion = Seccion::factory()->create(['grado_id' => $this->grado->id, 'name' => 'A', 'status_active' => 'true']);
        $this->calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $this->lapso->id,
            'pestudio_id' => $this->pestudio->id,
            'status' => 'active',
            'max_subjects_per_period' => 2,
        ]);
    }

    private function makeProfesor(string $ci): Profesor
    {
        return Profesor::create([
            'user_id' => $this->user->id,
            'name' => 'Doc '.$ci,
            'lastname' => 'Test',
            'ci_profesor' => $ci,
            'status_active' => 'true',
        ]);
    }

    private function makeLesson(
        Profesor $profesor,
        int $blocks = 1,
        bool $halfGroup = false,
        bool $shared = false,
        bool $locked = false,
        ?TimetableCalendar $calendar = null,
        ?Seccion $seccion = null,
        ?Pestudio $pestudio = null,
    ): TimetableLesson {
        $calendar ??= $this->calendar;
        $seccion ??= $this->seccion;
        $pestudio ??= $this->pestudio;

        $asignatura = Asignatura::factory()->create(['hour_t_week' => $blocks, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $this->lapso->id,
        ]);

        return TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $this->shift->id,
            'weekly_blocks_t' => $blocks,
            'weekly_blocks_p' => 0,
            'is_half_group' => $halfGroup,
            'allow_shared_teacher' => $shared,
            'locked' => $locked,
        ]);
    }

    private function makePeriods(TimetableCalendar $calendar, array $days): array
    {
        $periods = [];
        foreach ($days as $day) {
            $periods[$day] = TimetablePeriod::factory()->create([
                'calendar_id' => $calendar->id,
                'shift_id' => $this->shift->id,
                'day_of_week' => $day,
                'order_in_day' => 1,
                'is_break' => false,
            ]);
        }

        return $periods;
    }

    /**
     * @param  array<int, array{period_id:int, room_id?:int|null, is_practical?:bool, locked?:bool}>  $slots
     * @return array<int, array{period_id:int, room_id:int|null, is_practical:bool, locked:bool}>
     */
    private function slot(int $periodId, bool $locked = false): array
    {
        return ['period_id' => $periodId, 'room_id' => null, 'is_practical' => false, 'locked' => $locked];
    }

    public function test_graph_coloring_resolves_cross_studio_teacher_collisions(): void
    {
        $teachers = [$this->makeProfesor('8001'), $this->makeProfesor('8002'), $this->makeProfesor('8003')];
        $lessons = array_map(fn (Profesor $t): TimetableLesson => $this->makeLesson($t), $teachers);

        $periods = $this->makePeriods($this->calendar, [1, 2, 3]);

        // Otro P.Estudio: cada docente ocupado justo en el período del bloque.
        $pestB = Pestudio::factory()->create(['status_active' => 'true']);
        $gradoB = Grado::factory()->create(['pestudio_id' => $pestB->id, 'status_active' => 'true']);
        $secB = Seccion::factory()->create(['grado_id' => $gradoB->id, 'name' => 'B', 'status_active' => 'true']);
        $calB = TimetableCalendar::factory()->create(['lapso_id' => $this->lapso->id, 'pestudio_id' => $pestB->id, 'status' => 'active']);

        foreach ($teachers as $i => $teacher) {
            $lessonB = $this->makeLesson($teacher, 1, false, false, false, $calB, $secB, $pestB);
            $periodB = TimetablePeriod::factory()->create([
                'calendar_id' => $calB->id,
                'shift_id' => $this->shift->id,
                'day_of_week' => $i + 1,
                'order_in_day' => 1,
                'is_break' => false,
            ]);
            TimetableSlot::factory()->create([
                'calendar_id' => $calB->id,
                'lesson_id' => $lessonB->id,
                'period_id' => $periodB->id,
                'profesor_id' => $teacher->id,
                'seccion_id' => $secB->id,
            ]);
        }

        $assignment = [];
        foreach ($lessons as $i => $lesson) {
            $assignment[(int) $lesson->id] = [$this->slot($periods[$i + 1]->id)];
        }

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertSame(3, $result['collisions_before']);
        $this->assertSame(0, $result['collisions_after']);
        $this->assertSame(0, $result['hard_violations']);
        $this->assertGreaterThanOrEqual(1, $result['moved']);
    }

    public function test_csp_separates_same_teacher_hard_conflict(): void
    {
        $teacher = $this->makeProfesor('8101');
        $l1 = $this->makeLesson($teacher);
        $l2 = $this->makeLesson($teacher);

        $periods = $this->makePeriods($this->calendar, [1, 2, 3]);

        $assignment = [
            (int) $l1->id => [$this->slot($periods[1]->id)],
            (int) $l2->id => [$this->slot($periods[1]->id)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertGreaterThan(0, $result['collisions_before']);
        $this->assertSame(0, $result['hard_violations']);
        $this->assertGreaterThanOrEqual(1, $result['moved']);
    }

    public function test_half_group_lessons_may_share_a_period(): void
    {
        $l1 = $this->makeLesson($this->makeProfesor('8201'), 1, halfGroup: true);
        $l2 = $this->makeLesson($this->makeProfesor('8202'), 1, halfGroup: true);

        $periods = $this->makePeriods($this->calendar, [1, 2]);

        $assignment = [
            (int) $l1->id => [$this->slot($periods[1]->id)],
            (int) $l2->id => [$this->slot($periods[1]->id)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertSame(0, $result['hard_violations']);
        $this->assertSame(0, $result['collisions_before']);
    }

    public function test_locked_slot_is_never_moved(): void
    {
        $l1 = $this->makeLesson($this->makeProfesor('8301'));
        $l2 = $this->makeLesson($this->makeProfesor('8302'));

        $periods = $this->makePeriods($this->calendar, [1, 2, 3]);

        $assignment = [
            (int) $l1->id => [$this->slot($periods[1]->id, locked: true)],
            (int) $l2->id => [$this->slot($periods[1]->id)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertSame(0, $result['hard_violations']);
        $this->assertSame($periods[1]->id, $result['assignment'][(int) $l1->id][0]['period_id']);
    }

    public function test_soft_objective_spreads_blocks_across_days(): void
    {
        $lesson = $this->makeLesson($this->makeProfesor('8401'), 2);

        $periods = $this->makePeriods($this->calendar, [1, 2, 3]);

        // Dos bloques el mismo día (lunes) → concentración penalizada.
        $assignment = [
            (int) $lesson->id => [
                $this->slot($periods[1]->id),
                $this->slot($periods[2]->id),
            ],
        ];

        $optimizer = new SectionTimetableOptimizer;
        $result = $optimizer->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertSame(0, $result['hard_violations']);

        $days = array_map(
            fn (array $slot): int => (int) TimetablePeriod::query()->find($slot['period_id'])->day_of_week,
            $result['assignment'][(int) $lesson->id],
        );

        $this->assertCount(2, array_unique($days), 'los bloques deben quedar en días distintos');
        $this->assertSame(0, $result['report']['soft']['distribution']);
    }
}
