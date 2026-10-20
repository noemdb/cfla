<?php

namespace Tests\Feature\Timetable;

use App\Jobs\Timetable\GenerateTimetableJob;
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
use App\Services\Timetable\TimetableCalendarSnapshotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * ADR-TT-017 — Fase 4: persistencia y snapshot de la mitad de bloque.
 *
 * Cubre el ciclo solver → preview → slots → snapshot para lecciones
 * `half_block`: la mitad debe sobrevivir a la persistencia y las claves
 * generadas con sufijo de mitad deben replicarse en el snapshot (dos mitades
 * secuenciales del mismo período no colisionan).
 */
class HalfBlockPersistenceTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * Calendario con un único período y dos lecciones `half_block` de 1 unidad
     * en la misma sección/docente: el solver debe repartirlas en las mitades
     * superior (0) e inferior (1) del período.
     *
     * @return array{calendar: TimetableCalendar, shift: \App\Models\app\Timetable\TimetableShift, period: TimetablePeriod, profesor: Profesor, seccion: Seccion, lessons: list<TimetableLesson>}
     */
    private function halfBlockFixture(): array
    {
        $user = User::factory()->create();
        $profesor = Profesor::create([
            'user_id' => $user->id,
            'name' => 'Ana',
            'lastname' => 'Mitad',
            'ci_profesor' => '9477',
            'status_active' => 'true',
        ]);

        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'H', 'status_active' => 'true']);
        $lapso = Lapso::factory()->create();

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
            'name' => 'Horario Half Block Persistencia',
            'status' => 'draft',
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id,
            'shift_id' => $shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
        ]);

        $lessons = [];
        foreach (['A', 'B'] as $suffix) {
            $asignatura = Asignatura::factory()->create([
                'name' => "Materia $suffix",
                'hour_t_week' => 1,
                'hour_p_week' => 0,
            ]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $pestudio->id,
                'grado_id' => $grado->id,
                'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id,
                'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id,
                'lapso_id' => $lapso->id,
            ]);

            $lessons[] = TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id,
                'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id,
                'weekly_blocks_t' => 1,
                'weekly_blocks_p' => 0,
                'half_block' => true,
            ]);
        }

        return compact('calendar', 'shift', 'period', 'profesor', 'seccion', 'lessons');
    }

    public function test_solver_preview_carries_the_half_per_assignment(): void
    {
        $fx = $this->halfBlockFixture();

        GenerateTimetableJob::dispatchSync($fx['calendar']->id, dryRun: true);

        $payload = $fx['calendar']->fresh()->preview_payload;
        $this->assertSame([], $payload['unassigned']);

        // Dos mitades secuenciales dentro del único período.
        $halves = collect($payload['assignment'])
            ->flatten(1)
            ->pluck('half')
            ->map(fn ($half) => (int) $half)
            ->sort()
            ->values()
            ->all();
        $this->assertSame([0, 1], $halves);
    }

    public function test_confirmed_preview_persists_half_and_slot_half_key(): void
    {
        $fx = $this->halfBlockFixture();

        GenerateTimetableJob::dispatchSync($fx['calendar']->id, dryRun: true);
        $payload = $fx['calendar']->fresh()->preview_payload;
        $this->assertNotEmpty($payload['assignment'] ?? []);

        // Confirmar el preview → persistencia real de slots.
        GenerateTimetableJob::dispatchSync(
            $fx['calendar']->id,
            dryRun: false,
            previewPayload: $payload,
        );

        $slots = TimetableSlot::query()
            ->where('calendar_id', $fx['calendar']->id)
            ->orderBy('half')
            ->get();

        $this->assertCount(2, $slots);
        $this->assertSame([0, 1], $slots->pluck('half')->map(fn ($half) => (int) $half)->all());
        // Ambos slots comparten el período (reparto secuencial del tiempo).
        $this->assertSame(1, $slots->pluck('period_id')->unique()->count());

        // Las claves generadas reflejan la mitad (`slot_half_key`).
        $keys = DB::table('timetable_slots')
            ->where('calendar_id', $fx['calendar']->id)
            ->orderBy('half')
            ->pluck('slot_half_key')
            ->all();
        $this->assertSame(['B0', 'B1'], $keys);
    }

    public function test_snapshot_round_trip_preserves_two_sequential_halves(): void
    {
        $fx = $this->halfBlockFixture();
        [$lessonA, $lessonB] = $fx['lessons'];

        // Dos mitades del MISMO período, sección y docente (secuenciales). Si el
        // sufijo de mitad no se replicara en el snapshot, colisionarían.
        TimetableSlot::factory()->create([
            'calendar_id' => $fx['calendar']->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $fx['period']->id,
            'profesor_id' => $fx['profesor']->id,
            'seccion_id' => $fx['seccion']->id,
            'half' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $fx['calendar']->id,
            'lesson_id' => $lessonB->id,
            'period_id' => $fx['period']->id,
            'profesor_id' => $fx['profesor']->id,
            'seccion_id' => $fx['seccion']->id,
            'half' => 1,
        ]);

        $service = app(TimetableCalendarSnapshotService::class);
        $snapshot = $service->build($fx['calendar']);

        $snapshotHalves = collect($snapshot['slots'])
            ->pluck('half')
            ->map(fn ($half) => (int) $half)
            ->sort()
            ->values()
            ->all();
        $this->assertSame([0, 1], $snapshotHalves);

        // El plan del snapshot no debe descartar ni deduplicar ninguna mitad.
        $preview = $service->preview($fx['calendar'], $snapshot);
        $this->assertSame(2, $preview['slots']['insertable']);
        $this->assertSame(0, $preview['slots']['deduplicated']);
        $this->assertSame([], $preview['collisions']);

        // Round-trip completo: aplicar el snapshot reemplaza el horario
        // conservando las dos mitades.
        Storage::fake('local');
        $report = $service->apply($fx['calendar'], $snapshot, $preview);
        $this->assertSame(2, $report['slots']);

        $halves = TimetableSlot::query()
            ->where('calendar_id', $fx['calendar']->id)
            ->orderBy('half')
            ->pluck('half')
            ->map(fn ($half) => (int) $half)
            ->all();
        $this->assertSame([0, 1], $halves);

        // La lección `half_block` también sobrevive al round-trip del snapshot.
        $this->assertSame(2, TimetableLesson::query()
            ->where('calendar_id', $fx['calendar']->id)
            ->where('half_block', true)
            ->count());
    }

    public function test_locked_half_block_lesson_keeps_its_exact_half(): void
    {
        $fx = $this->halfBlockFixture();
        [$lessonA, $lessonB] = $fx['lessons'];

        // La lección A queda bloqueada en la mitad inferior (1) del período.
        // El bloqueo no debe degenerar en un bloque completo (half = null):
        // debe ocupar solo esa mitad y dejar la superior libre para B.
        TimetableSlot::factory()->create([
            'calendar_id' => $fx['calendar']->id,
            'lesson_id' => $lessonA->id,
            'period_id' => $fx['period']->id,
            'profesor_id' => $fx['profesor']->id,
            'seccion_id' => $fx['seccion']->id,
            'half' => 1,
            'locked' => true,
        ]);
        TimetableLesson::query()->whereKey($lessonA->id)->update(['locked' => true]);

        GenerateTimetableJob::dispatchSync($fx['calendar']->id, dryRun: true);

        $assignment = $fx['calendar']->fresh()->preview_payload['assignment'];

        $slotsA = $assignment[(string) $lessonA->id] ?? $assignment[$lessonA->id] ?? [];
        $this->assertCount(1, $slotsA);
        $this->assertSame($fx['period']->id, (int) $slotsA[0]['period_id']);
        $this->assertSame(1, (int) $slotsA[0]['half'], 'El bloqueo debe conservar la mitad exacta, no un bloque completo.');

        $slotsB = $assignment[(string) $lessonB->id] ?? $assignment[$lessonB->id] ?? [];
        $this->assertCount(1, $slotsB);
        $this->assertSame($fx['period']->id, (int) $slotsB[0]['period_id']);
        $this->assertSame(0, (int) $slotsB[0]['half']);
    }
}
