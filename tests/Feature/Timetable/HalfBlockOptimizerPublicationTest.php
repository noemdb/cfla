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
use App\Models\User;
use App\Services\Timetable\SectionTimetableOptimizer;
use App\Services\Timetable\TimetablePublicationReadinessService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * ADR-TT-017 — Fase 6: el optimizador por sección y el chequeo de publicación
 * evalúan la ocupación por sub-franja (mitad superior/inferior).
 *
 *  - Dos mitades secuenciales del mismo período NO colisionan.
 *  - Un bloque completo (half null) excluye cualquier mitad del período.
 *  - Dos mitades sobre la MISMA sub-franja sí colisionan.
 */
class HalfBlockOptimizerPublicationTest extends TestCase
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
        bool $halfBlock = false,
        bool $halfGroup = false,
        ?Seccion $seccion = null,
    ): TimetableLesson {
        $seccion ??= $this->seccion;

        $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $this->pestudio->id,
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
            'calendar_id' => $this->calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $this->shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'is_half_group' => $halfGroup,
            'half_block' => $halfBlock,
        ]);
    }

    /**
     * @param  list<int>  $days
     * @return array<int, TimetablePeriod>
     */
    private function makePeriods(array $days): array
    {
        $periods = [];
        foreach ($days as $day) {
            $periods[$day] = TimetablePeriod::factory()->create([
                'calendar_id' => $this->calendar->id,
                'shift_id' => $this->shift->id,
                'day_of_week' => $day,
                'order_in_day' => 1,
                'is_break' => false,
            ]);
        }

        return $periods;
    }

    /**
     * @return array{period_id:int, room_id:int|null, is_practical:bool, locked:bool, half:int|null}
     */
    private function slot(int $periodId, ?int $half = null, bool $locked = false): array
    {
        return [
            'period_id' => $periodId,
            'room_id' => null,
            'is_practical' => false,
            'locked' => $locked,
            'half' => $half,
        ];
    }

    // ─── Optimizador por sección ────────────────────────────────────────────

    public function test_optimizer_allows_two_sequential_halves_in_same_period(): void
    {
        $a = $this->makeLesson($this->makeProfesor('9001'), halfBlock: true);
        $b = $this->makeLesson($this->makeProfesor('9002'), halfBlock: true);
        $periods = $this->makePeriods([1, 2]);

        $assignment = [
            (int) $a->id => [$this->slot($periods[1]->id, half: 0)],
            (int) $b->id => [$this->slot($periods[1]->id, half: 1)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertSame(0, $result['collisions_before'], 'dos mitades secuenciales no deben colisionar');
        $this->assertSame(0, $result['hard_violations']);
        $this->assertSame(0, $result['assignment'][(int) $a->id][0]['half']);
        $this->assertSame(1, $result['assignment'][(int) $b->id][0]['half']);
    }

    public function test_optimizer_full_block_excludes_half_in_same_period(): void
    {
        $half = $this->makeLesson($this->makeProfesor('9011'), halfBlock: true);
        $full = $this->makeLesson($this->makeProfesor('9012'));
        $periods = $this->makePeriods([1, 2]);

        $assignment = [
            (int) $half->id => [$this->slot($periods[1]->id, half: 0)],
            (int) $full->id => [$this->slot($periods[1]->id)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertGreaterThan(0, $result['collisions_before'], 'un bloque completo choca con la mitad');
        $this->assertSame(0, $result['hard_violations'], 'el optimizador debe resolver la colisión');
        $this->assertSame(0, $result['assignment'][(int) $half->id][0]['half']);
    }

    public function test_optimizer_reports_two_halves_on_same_subslot_as_conflict(): void
    {
        $a = $this->makeLesson($this->makeProfesor('9021'), halfBlock: true);
        $b = $this->makeLesson($this->makeProfesor('9022'), halfBlock: true);
        $periods = $this->makePeriods([1, 2]);

        $assignment = [
            (int) $a->id => [$this->slot($periods[1]->id, half: 0)],
            (int) $b->id => [$this->slot($periods[1]->id, half: 0)],
        ];

        $result = (new SectionTimetableOptimizer)->optimize($this->calendar, (int) $this->seccion->id, $assignment);

        $this->assertGreaterThan(0, $result['collisions_before']);
        $this->assertSame(0, $result['hard_violations']);
    }

    // ─── Capacidad / publicación ────────────────────────────────────────────

    public function test_readiness_allows_two_sequential_halves_in_same_period(): void
    {
        $a = $this->makeLesson($this->makeProfesor('9101'), halfBlock: true);
        $b = $this->makeLesson($this->makeProfesor('9102'), halfBlock: true);
        $period = $this->makePeriods([1])[1];

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($this->calendar, [
            'assignment' => [
                (string) $a->id => [['period_id' => $period->id, 'half' => 0]],
                (string) $b->id => [['period_id' => $period->id, 'half' => 1]],
            ],
        ]);

        $this->assertTrue($readiness['ready'], 'dos mitades secuenciales deben ser publicables');
        $this->assertSame(2, $readiness['assigned']);
        $this->assertSame([], $readiness['hard_conflicts']);
    }

    public function test_readiness_flags_two_halves_on_same_subslot(): void
    {
        $a = $this->makeLesson($this->makeProfesor('9111'), halfBlock: true);
        $b = $this->makeLesson($this->makeProfesor('9112'), halfBlock: true);
        $period = $this->makePeriods([1])[1];

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($this->calendar, [
            'assignment' => [
                (string) $a->id => [['period_id' => $period->id, 'half' => 0]],
                (string) $b->id => [['period_id' => $period->id, 'half' => 0]],
            ],
        ]);

        $this->assertFalse($readiness['ready']);
        $this->assertContains('section_double_booked', array_column($readiness['hard_conflicts'], 'type'));
    }

    public function test_readiness_allows_same_teacher_sequential_halves_across_sections(): void
    {
        $seccionB = Seccion::factory()->create([
            'grado_id' => $this->grado->id,
            'name' => 'B',
            'status_active' => 'true',
        ]);

        $teacher = $this->makeProfesor('9121');
        $a = $this->makeLesson($teacher, halfBlock: true);
        $b = $this->makeLesson($teacher, halfBlock: true, seccion: $seccionB);
        $period = $this->makePeriods([1])[1];

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($this->calendar, [
            'assignment' => [
                (string) $a->id => [['period_id' => $period->id, 'half' => 0]],
                (string) $b->id => [['period_id' => $period->id, 'half' => 1]],
            ],
        ]);

        $this->assertTrue($readiness['ready'], 'un docente con dos mitades secuenciales no está duplicado');
        $this->assertNotContains('teacher_double_booked', array_column($readiness['hard_conflicts'], 'type'));
    }

    public function test_readiness_flags_same_teacher_same_subslot_across_sections(): void
    {
        $seccionB = Seccion::factory()->create([
            'grado_id' => $this->grado->id,
            'name' => 'B',
            'status_active' => 'true',
        ]);

        $teacher = $this->makeProfesor('9131');
        $a = $this->makeLesson($teacher, halfBlock: true);
        $b = $this->makeLesson($teacher, halfBlock: true, seccion: $seccionB);
        $period = $this->makePeriods([1])[1];

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($this->calendar, [
            'assignment' => [
                (string) $a->id => [['period_id' => $period->id, 'half' => 0]],
                (string) $b->id => [['period_id' => $period->id, 'half' => 0]],
            ],
        ]);

        $this->assertFalse($readiness['ready']);
        $this->assertContains('teacher_double_booked', array_column($readiness['hard_conflicts'], 'type'));
    }

    public function test_readiness_full_block_conflicts_with_half_in_same_period(): void
    {
        $half = $this->makeLesson($this->makeProfesor('9141'), halfBlock: true);
        $full = $this->makeLesson($this->makeProfesor('9142'));
        $period = $this->makePeriods([1])[1];

        $readiness = app(TimetablePublicationReadinessService::class)->evaluate($this->calendar, [
            'assignment' => [
                (string) $half->id => [['period_id' => $period->id, 'half' => 0]],
                (string) $full->id => [['period_id' => $period->id]],
            ],
        ]);

        $this->assertFalse($readiness['ready']);
        $this->assertContains('section_double_booked', array_column($readiness['hard_conflicts'], 'type'));
    }
}
