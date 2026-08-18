<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use Database\Seeders\TimetableTestSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * SPEC-TIMETABLE-001 §18 / ticket 001i — TimetableTestSeeder genera un
 * dataset sintético reproducible (seed fijo) y factible para el solver.
 */
class TimetableTestSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_seeder_creates_expected_entities(): void
    {
        $this->seed(TimetableTestSeeder::class);

        $calendar = TimetableCalendar::query()->latest('id')->first();

        $this->assertNotNull($calendar);
        $this->assertSame(30, TimetablePeriod::query()->where('calendar_id', $calendar->id)->count());
        $this->assertGreaterThan(0, TimetableShift::query()->count());
        $this->assertGreaterThan(0, TimetableRoom::query()->count());

        // 8 asignaturas × 3 secciones → 24 lecciones.
        $this->assertSame(24, TimetableLesson::query()->where('calendar_id', $calendar->id)->count());
    }

    public function test_seeder_uses_fixed_seed_and_is_reproducible(): void
    {
        $seeder = new TimetableTestSeeder;

        // Seed fijo + catálogo fijo → mismo dataset cada corrida.
        $this->assertSame(20260818, $seeder->seed);
        $this->assertCount(8, $seeder->catalog());

        // Dos corridas producen la misma estructura (un calendario por lapso).
        $this->seed(TimetableTestSeeder::class);
        $first = TimetableCalendar::query()->latest('id')->first();

        $this->seed(TimetableTestSeeder::class);
        $second = TimetableCalendar::query()->latest('id')->first();

        $this->assertNotSame($first->id, $second->id);
        $this->assertSame(
            TimetableLesson::query()->where('calendar_id', $first->id)->count(),
            TimetableLesson::query()->where('calendar_id', $second->id)->count(),
        );
    }

    public function test_seeder_dataset_solves_without_unassigned_lessons(): void
    {
        $this->seed(TimetableTestSeeder::class);

        $calendar = TimetableCalendar::query()->latest('id')->first();

        GenerateTimetableJob::dispatchSync($calendar->id, dryRun: true);

        $payload = $calendar->fresh()->preview_payload;

        $this->assertNotNull($payload);
        $this->assertSame([], $payload['unassigned'], 'El dataset sintético debe ser factible');
        $this->assertCount(24, $payload['assignment']);
    }

    public function test_seeder_lessons_derive_blocks_from_asignatura_hours(): void
    {
        $this->seed(TimetableTestSeeder::class);

        $calendar = TimetableCalendar::query()->latest('id')->first();

        $lesson = TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->with('pevaluacion.pensum.asignatura')
            ->first();

        $asignatura = $lesson->pevaluacion->pensum->asignatura;

        // Con period_minutes=45: ceil(horas*60/45).
        $expectedT = max(1, (int) ceil(((int) $asignatura->hour_t_week) * 60 / 45));
        $expectedP = (int) ceil(((int) $asignatura->hour_p_week) * 60 / 45);

        $this->assertSame($expectedT, (int) $lesson->weekly_blocks_t);
        $this->assertSame($expectedP, (int) $lesson->weekly_blocks_p);

        // Bloques prácticos exigen aula del tipo pedido.
        if ($expectedP > 0) {
            $this->assertSame('laboratorio', $lesson->room_type_required);
        }
    }
}
