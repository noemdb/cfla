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
use App\Models\app\Timetable\TimetableConflict;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableTeacherAvailability;
use App\Models\User;
use App\Services\Timetable\Solver\AttemptResult;
use App\Services\Timetable\Solver\SolverAttemptConfig;
use App\Services\Timetable\Solver\SolverOutcome;
use App\Services\Timetable\Solver\SolverResult;
use App\Services\Timetable\Solver\TimetableSolverPlaybook;
use App\Services\Timetable\Solver\UnassignedReason;
use App\Services\Timetable\TimetableCalendarSnapshotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use RuntimeException;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Backup/restore de lessons de TODO el calendario (barra superior), sin
 * depender de la sección activa.
 *
 * A partir de SPEC-TIMETABLE-SNAPSHOT-001 §14 se cubren además los casos 1–21
 * del snapshot de calendario: export con checksum semántico, preview sin
 * escrituras, replace vs. additivo, auto-backup re-aplicable, rollback,
 * concurrencia (D5), medio-grupos (§9.6) y la suite anti-deriva del playbook.
 */
class TimetableCalendarBackupTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function snapshotService(): TimetableCalendarSnapshotService
    {
        return app(TimetableCalendarSnapshotService::class);
    }

    private function makePev(Seccion $seccion, Profesor $profesor, Lapso $lapso, Pestudio $pestudio, int $n): Pevaluacion
    {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2 + $n, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $seccion->grado_id,
            'asignatura_id' => $asignatura->id,
        ]);

        return Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $lapso->id,
        ]);
    }

    /**
     * @return array{user: User, lapso: Lapso, pestudio: Pestudio, calendar: TimetableCalendar, shift: \App\Models\app\Timetable\TimetableShift, pevA: Pevaluacion, pevB: Pevaluacion}
     */
    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'A', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'B', 'status_active' => 'true']);

        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9201', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id,
            'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        $pevA = $this->makePev($seccionA, $profesor, $lapso, $pestudio, 0);
        $pevB = $this->makePev($seccionB, $profesor, $lapso, $pestudio, 1);

        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevA->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 2, 'weekly_blocks_p' => 0,
        ]);
        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pevB->id, 'shift_id' => $shift->id,
            'weekly_blocks_t' => 3, 'weekly_blocks_p' => 0,
        ]);

        return compact('user', 'lapso', 'pestudio', 'calendar', 'shift', 'pevA', 'pevB');
    }

    private function lessonFor(TimetableCalendar $calendar, Pevaluacion $pev): TimetableLesson
    {
        return TimetableLesson::query()
            ->where('calendar_id', $calendar->id)
            ->where('pevaluacion_id', $pev->id)
            ->firstOrFail();
    }

    private function slotCount(TimetableCalendar $calendar): int
    {
        return TimetableSlot::query()->where('calendar_id', $calendar->id)->count();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeSlot(
        TimetableCalendar $calendar,
        TimetableLesson $lesson,
        TimetablePeriod $period,
        int $profesorId,
        int $seccionId,
        array $overrides = [],
    ): TimetableSlot {
        return TimetableSlot::factory()->create(array_merge([
            'calendar_id' => $calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $period->id,
            'profesor_id' => $profesorId,
            'seccion_id' => $seccionId,
            'grupo_estable_id' => null,
            'room_id' => null,
            'is_manual_override' => false,
            'locked' => false,
            'is_half_group' => false,
            'allow_shared_teacher' => false,
        ], $overrides));
    }

    /**
     * Escenario rico para los casos del snapshot: 4 períodos del turno M
     * (Lunes, bloques 1–4), 2 slots (pevA→bloque 1, pevB→bloque 2) y una
     * disponibilidad bloqueada del docente en el bloque 3.
     *
     * @return array<string, mixed>
     */
    private function snapshotFixture(): array
    {
        $f = $this->fixture();

        $hours = [
            1 => ['07:00:00', '07:45:00'],
            2 => ['07:45:00', '08:30:00'],
            3 => ['08:30:00', '09:15:00'],
            4 => ['09:15:00', '10:00:00'],
        ];

        $periods = [];
        foreach ($hours as $order => [$start, $end]) {
            $periods[$order] = TimetablePeriod::factory()->create([
                'calendar_id' => $f['calendar']->id,
                'shift_id' => $f['shift']->id,
                'day_of_week' => 1,
                'order_in_day' => $order,
                'start_time' => $start,
                'end_time' => $end,
            ]);
        }

        $lessonA = $this->lessonFor($f['calendar'], $f['pevA']);
        $lessonB = $this->lessonFor($f['calendar'], $f['pevB']);

        $this->makeSlot($f['calendar'], $lessonA, $periods[1], (int) $f['pevA']->profesor_id, (int) $f['pevA']->seccion_id);
        $this->makeSlot($f['calendar'], $lessonB, $periods[2], (int) $f['pevB']->profesor_id, (int) $f['pevB']->seccion_id);

        TimetableTeacherAvailability::query()->create([
            'calendar_id' => $f['calendar']->id,
            'profesor_id' => $f['pevA']->profesor_id,
            'shift_id' => $f['shift']->id,
            'day_of_week' => 1,
            'order_in_day' => 3,
            'start_time' => '08:30:00',
            'end_time' => '09:15:00',
            'is_available' => false,
        ]);

        return $f + ['periods' => $periods, 'lessonA' => $lessonA, 'lessonB' => $lessonB];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function reseal(array $payload): array
    {
        $payload['checksum'] = $this->snapshotService()->checksum($payload);

        return $payload;
    }

    /**
     * Nombres de las advertencias del preview.
     *
     * @param  array<string, mixed>  $preview
     * @return list<string>
     */
    private function warningTypes(array $preview): array
    {
        return array_map(
            fn ($warning): string => (string) ($warning['type'] ?? ''),
            $preview['warnings'] ?? [],
        );
    }

    // ─── Compatibilidad con el flujo previo al snapshot ───────────────────

    public function test_calendar_backup_downloads_regardless_of_section(): void
    {
        $fixture = $this->fixture();

        Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->set('activeSeccionId', null)
            ->call('downloadCalendarLessonsBackup')
            ->assertFileDownloaded();
    }

    public function test_restore_calendar_backup_updates_lessons_of_any_section(): void
    {
        $fixture = $this->fixture();

        $payload = [
            'format' => 'cfla-timetable-lessons-backup',
            'version' => 1,
            'scope' => 'calendar',
            'calendar' => [
                'id' => $fixture['calendar']->id,
                'lapso_id' => $fixture['lapso']->id,
                'pestudio_id' => $fixture['pestudio']->id,
            ],
            'section' => null,
            'lessons' => [
                [
                    'pevaluacion_id' => $fixture['pevA']->id,
                    'academic_identity' => [
                        'seccion_id' => $fixture['pevA']->seccion_id,
                        'pensum_id' => $fixture['pevA']->pensum_id,
                        'profesor_id' => $fixture['pevA']->profesor_id,
                        'grupo_estable_id' => null,
                    ],
                    'configuration' => [
                        'shift_id' => $fixture['shift']->id,
                        'weekly_blocks_t' => 5,
                        'weekly_blocks_p' => 0,
                        'room_type_required' => null,
                        'is_half_group' => false,
                        'priority' => 0,
                        'locked' => false,
                    ],
                ],
                [
                    'pevaluacion_id' => $fixture['pevB']->id,
                    'academic_identity' => [
                        'seccion_id' => $fixture['pevB']->seccion_id,
                        'pensum_id' => $fixture['pevB']->pensum_id,
                        'profesor_id' => $fixture['pevB']->profesor_id,
                        'grupo_estable_id' => null,
                    ],
                    'configuration' => [
                        'shift_id' => $fixture['shift']->id,
                        'weekly_blocks_t' => 1,
                        'weekly_blocks_p' => 0,
                        'room_type_required' => null,
                        'is_half_group' => false,
                        'priority' => 0,
                        'locked' => false,
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('backup.json', json_encode($payload));

        Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->set('calendarLessonsBackupFile', $file)
            ->call('restoreCalendarLessonsBackup')
            ->assertHasNoErrors();

        $this->assertSame(
            5,
            (int) TimetableLesson::query()
                ->where('calendar_id', $fixture['calendar']->id)
                ->where('pevaluacion_id', $fixture['pevA']->id)
                ->value('weekly_blocks_t'),
        );
        $this->assertSame(
            1,
            (int) TimetableLesson::query()
                ->where('calendar_id', $fixture['calendar']->id)
                ->where('pevaluacion_id', $fixture['pevB']->id)
                ->value('weekly_blocks_t'),
        );
    }

    // ─── §14 · Caso 1 — forma del snapshot y checksum semántico ───────────

    public function test_case_01_el_snapshot_exporta_la_forma_esperada_con_checksum_valido(): void
    {
        $f = $this->snapshotFixture();
        $snapshot = $this->snapshotService()->build($f['calendar']);

        $this->assertSame('cfla-timetable-calendar-snapshot', $snapshot['format']);
        $this->assertSame(1, $snapshot['version']);
        $this->assertSame('calendar', $snapshot['scope']);
        $this->assertSame('sha256-semantic-v1', $snapshot['checksum_algo']);
        $this->assertSame((int) $f['calendar']->id, $snapshot['calendar']['id']);

        // Contenido del calendario: 4 períodos, 2 lessons, 2 slots, 1
        // disponibilidad y un bloqueo por cada sección presente.
        $this->assertCount(4, $snapshot['periods']);
        $this->assertCount(2, $snapshot['lessons']);
        $this->assertCount(2, $snapshot['slots']);
        $this->assertCount(1, $snapshot['availability']);
        $this->assertCount(2, $snapshot['section_locks']);

        // El snapshot es autosuficiente: incluye esquema y playbook.
        $this->assertArrayHasKey('schema', $snapshot);
        $this->assertArrayHasKey('playbook', $snapshot);

        // El checksum es reproducible y `verify()` lo acepta.
        $this->assertSame($snapshot['checksum'], $this->snapshotService()->checksum($snapshot));
        $this->assertTrue($this->snapshotService()->verify($snapshot)['checksum_verified']);

        // El bloque de configuración conserva las palancas del plan de medio-grupos.
        $this->assertArrayHasKey('allow_shared_teacher', $snapshot['lessons'][0]['configuration']);
        $this->assertArrayHasKey('is_half_group', $snapshot['lessons'][0]['configuration']);
    }

    // ─── §14 · Caso 2 — checksum alterado se rechaza sin tocar la BD ──────

    public function test_case_02_un_checksum_alterado_se_rechaza_y_no_toca_la_base(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $snapshot = $this->snapshotService()->build($f['calendar']);

        // Se altera el contenido sin recalcular el checksum.
        $snapshot['lessons'][0]['configuration']['weekly_blocks_t'] = 99;

        $file = UploadedFile::fake()->createWithContent('snapshot.json', json_encode($snapshot));

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('calendarLessonsBackupFile', $file)
            ->assertSet('snapshotPreview', null);

        $this->assertSame(
            2,
            (int) TimetableLesson::query()
                ->where('calendar_id', $f['calendar']->id)
                ->where('pevaluacion_id', $f['pevA']->id)
                ->value('weekly_blocks_t'),
        );
        $this->assertSame(2, $this->slotCount($f['calendar']));
        // Un rechazo en la validación no llega a escribir el auto-backup.
        $this->assertSame([], Storage::disk('local')->files(TimetableCalendarSnapshotService::AUTO_BACKUP_DIR));
    }

    // ─── §14 · Caso 3 — checksum estable ante metadatos y playbook ────────

    public function test_case_03_el_checksum_ignora_metadatos_y_playbook(): void
    {
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $mutated = $snapshot;
        $mutated['exported_at'] = '2000-01-01T00:00:00+00:00';
        $mutated['exported_by'] = 4242;
        $mutated['app_version'] = 'otra';
        $mutated['environment'] = 'otro';
        $mutated['checksum_algo'] = $snapshot['checksum_algo'];
        $mutated['schema'] = ['documentacion' => 'reescrita'];
        $mutated['playbook']['playbook_version'] = 999;

        $this->assertSame($snapshot['checksum'], $service->checksum($mutated));
        $this->assertTrue($service->verify($mutated)['checksum_verified']);
    }

    // ─── §14 · Caso 4 — el preview no escribe nada ────────────────────────

    public function test_case_04_el_preview_no_escribe_en_la_base(): void
    {
        $f = $this->snapshotFixture();
        $snapshot = $this->snapshotService()->build($f['calendar']);

        $lessonsBefore = TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->pluck('id')->all();
        $periodsBefore = TimetablePeriod::query()->where('calendar_id', $f['calendar']->id)->pluck('id')->all();
        $availabilityBefore = TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->count();

        $preview = $this->snapshotService()->preview($f['calendar'], $snapshot);

        $this->assertSame('replace', $preview['mode']);
        $this->assertSame(2, $preview['slots']['current']);
        $this->assertSame(2, $preview['slots']['snapshot']);
        $this->assertSame(2, $preview['slots']['insertable']);
        $this->assertSame(0, $preview['slots']['skipped']);
        $this->assertSame(2, $preview['lessons']['resolvable']);
        $this->assertSame(0, $preview['lessons']['unresolvable']);
        $this->assertSame(0, $preview['lessons']['lost']);
        $this->assertSame(['changes' => []], $preview['section_locks']);
        $this->assertSame(1, $preview['availability']['current']);
        $this->assertSame(1, $preview['availability']['snapshot']);

        // Sello de concurrencia para `apply()` (D5). El hash refleja el estado
        // real de la BD (fresh), no atributos no persistidos del modelo.
        $this->assertSame((int) $f['calendar']->fresh()->version, $preview['version']);
        $this->assertSame(
            $this->snapshotService()->checksum($this->snapshotService()->build($f['calendar']->fresh())),
            $preview['state_hash'],
        );

        // Nada cambió.
        $this->assertSame($lessonsBefore, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->pluck('id')->all());
        $this->assertSame($periodsBefore, TimetablePeriod::query()->where('calendar_id', $f['calendar']->id)->pluck('id')->all());
        $this->assertSame($availabilityBefore, TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(2, $this->slotCount($f['calendar']));
    }

    // ─── §14 · Caso 5 — replace elimina el horario sobrante ───────────────

    public function test_case_05_el_restore_reemplaza_y_elimina_slots_sobrantes(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // Un slot extra posterior al export: el replace debe eliminarlo.
        $this->makeSlot(
            $f['calendar'],
            $f['lessonB'],
            $f['periods'][3],
            (int) $f['pevB']->profesor_id,
            (int) $f['pevB']->seccion_id,
        );
        $f['calendar']->refresh();
        $this->assertSame(3, $this->slotCount($f['calendar']));

        $preview = $service->preview($f['calendar'], $snapshot);
        $this->assertSame(3, $preview['slots']['current']);
        $this->assertSame(2, $preview['slots']['snapshot']);

        $report = $service->apply($f['calendar'], $snapshot, $preview);

        $this->assertSame('replace', $report['mode']);
        $this->assertSame(2, $report['slots']);
        $this->assertSame(2, $this->slotCount($f['calendar']));
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    // ─── §14 · Caso 6 — el respaldo legacy sigue siendo aditivo ───────────

    public function test_case_06_el_respaldo_legacy_sigue_siendo_aditivo(): void
    {
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();

        // Sin la clave `slots` el payload es legacy → modo aditivo (§5.4).
        $legacy = [
            'format' => 'cfla-timetable-lessons-backup',
            'version' => 1,
            'scope' => 'calendar',
            'calendar' => ['id' => $f['calendar']->id],
            'lessons' => $service->lessonRows($f['calendar']),
        ];

        $this->assertTrue($service->verify($legacy)['legacy']);
        $this->assertFalse($service->verify($legacy)['checksum_verified']);

        $preview = $service->preview($f['calendar'], $legacy);
        $this->assertSame('additive', $preview['mode']);
        $this->assertSame(false, $preview['slots']['present']);
        $this->assertContains('additive_mode', $this->warningTypes($preview));

        $report = $service->apply($f['calendar'], $legacy, $preview);

        $this->assertSame('additive', $report['mode']);
        $this->assertSame(0, $report['slots']);
        // El horario existente no se toca en modo aditivo.
        $this->assertSame(2, $this->slotCount($f['calendar']));
    }

    // ─── §14 · Caso 7 — auto-backup del apply, re-aplicable ───────────────

    public function test_case_07_el_apply_escribe_un_auto_backup_re_aplicable(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $preview = $service->preview($f['calendar'], $snapshot);
        $report = $service->apply($f['calendar'], $snapshot, $preview);

        $this->assertIsString($report['auto_backup']);
        Storage::disk('local')->assertExists($report['auto_backup']);
        $this->assertStringStartsWith(TimetableCalendarSnapshotService::AUTO_BACKUP_DIR.'/', $report['auto_backup']);

        $auto = json_decode(Storage::disk('local')->get($report['auto_backup']), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('cfla-timetable-calendar-snapshot', $auto['format']);
        $this->assertSame($service->checksum($auto), $auto['checksum']);
        $this->assertTrue($service->verify($auto)['checksum_verified']);
        $this->assertCount(2, $auto['slots']);

        // Es un snapshot completo: permite deshacer el restore.
        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();
        $f['calendar']->refresh();

        $undoPreview = $service->preview($f['calendar'], $auto);
        $undoReport = $service->apply($f['calendar'], $auto, $undoPreview);

        $this->assertSame(2, $undoReport['slots']);
        $this->assertSame(2, $this->slotCount($f['calendar']));
    }

    // ─── §14 · Caso 8 — pevaluación irresoluble se reporta, no rompe ──────

    public function test_case_08_una_pevaluacion_irresoluble_se_reporta_en_el_preview(): void
    {
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $orphan = $snapshot['lessons'][0];
        $orphan['pevaluacion_id'] = 999999;
        $orphan['academic_identity'] = [
            'seccion_id' => 0, 'pensum_id' => 0, 'profesor_id' => 0, 'grupo_estable_id' => null,
        ];
        $snapshot['lessons'][] = $orphan;

        $preview = $service->preview($f['calendar'], $this->reseal($snapshot));

        $this->assertSame(3, $preview['lessons']['snapshot']);
        $this->assertSame(2, $preview['lessons']['resolvable']);
        $this->assertSame(1, $preview['lessons']['unresolvable']);
        $this->assertSame(999999, $preview['lessons']['skipped_detail'][0]['pevaluacion_id']);
        $this->assertSame('pevaluacion_not_found', $preview['lessons']['skipped_detail'][0]['reason']);
    }

    // ─── §14 · Caso 9 — un fallo de escritura revierte TODO el restore ────

    public function test_case_09_un_fallo_de_escritura_revierte_todo_el_restore(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // Estado divergente antes de aplicar: horario vacío y otra configuración
        // de bloques. Sirve de discriminador para detectar un rollback parcial.
        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();
        TimetableLesson::query()
            ->where('calendar_id', $f['calendar']->id)
            ->where('pevaluacion_id', $f['pevA']->id)
            ->update(['weekly_blocks_t' => 7]);
        $f['calendar']->refresh();

        $preview = $service->preview($f['calendar'], $snapshot);

        // El fallo se provoca en la ÚLTIMA escritura del apply (disponibilidad),
        // cuando slots y lessons ya se reescribieron dentro de la transacción.
        DB::listen(function ($query): void {
            if (str_starts_with($query->sql, 'insert into `timetable_teacher_availability`')) {
                throw new RuntimeException('fallo simulado de escritura');
            }
        });

        try {
            $service->apply($f['calendar'], $snapshot, $preview);
            $this->fail('El apply debió propagar el fallo simulado.');
        } catch (RuntimeException $exception) {
            $this->assertSame('fallo simulado de escritura', $exception->getMessage());
        }

        // Nada se aplicó: el horario sigue vacío, la configuración intacta y la
        // disponibilidad anterior en su sitio.
        $this->assertSame(0, $this->slotCount($f['calendar']));
        $this->assertSame(
            7,
            (int) TimetableLesson::query()
                ->where('calendar_id', $f['calendar']->id)
                ->where('pevaluacion_id', $f['pevA']->id)
                ->value('weekly_blocks_t'),
        );
        $this->assertSame(1, TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    // ─── §14 · Caso 10 — concurrencia optimista (D5) ──────────────────────

    public function test_case_10_el_apply_aborta_si_el_calendario_cambio_desde_el_preview(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $preview = $service->preview($f['calendar'], $snapshot);

        // Otra sesión toca el calendario entre el preview y el apply.
        $f['calendar']->update(['version' => (int) $f['calendar']->version + 1]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('cambió desde el preview');

        $service->apply($f['calendar'], $snapshot, $preview);
    }

    // ─── §14 · Caso 11 — `slots: []` vacía el horario con aviso ───────────

    public function test_case_11_un_slots_vacio_vacia_el_horario_y_avisa(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $snapshot['slots'] = [];
        $snapshot = $this->reseal($snapshot);

        $preview = $service->preview($f['calendar'], $snapshot);
        $this->assertSame('replace', $preview['mode']);
        $this->assertContains('empty_schedule', $this->warningTypes($preview));

        $report = $service->apply($f['calendar'], $snapshot, $preview);

        $this->assertSame(0, $report['slots']);
        $this->assertSame(0, $this->slotCount($f['calendar']));
        // Las lessons siguen restaurándose: solo se vacía el horario.
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    // ─── §14 · Caso 12 — turno desconocido: se omite, no aborta ───────────

    public function test_case_12_un_turno_desconocido_omite_el_slot_sin_abortar(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $snapshot['slots'][0]['period']['shift_code'] = 'X';
        $snapshot = $this->reseal($snapshot);

        $preview = $service->preview($f['calendar'], $snapshot);

        $this->assertSame(1, $preview['slots']['insertable']);
        $this->assertSame(1, $preview['slots']['skipped']);
        $this->assertSame('unknown_shift', $preview['slots']['skipped_detail'][0]['reason']);
        $this->assertContains('slots_skipped', $this->warningTypes($preview));

        $report = $service->apply($f['calendar'], $snapshot, $preview);

        $this->assertSame(1, $report['slots']);
        $this->assertSame(1, $report['slots_skipped']);
        $this->assertSame(1, $this->slotCount($f['calendar']));
    }

    // ─── §14 · Caso 13 — medio-grupo: clave generada por MySQL (§9.6) ─────

    public function test_case_13_los_medio_grupos_reconstruyen_su_clave_de_seccion(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // pevB pasa a ser medio-grupo, en la lesson y en su slot.
        foreach ($snapshot['lessons'] as $index => $row) {
            if ((int) $row['pevaluacion_id'] === (int) $f['pevB']->id) {
                $snapshot['lessons'][$index]['configuration']['is_half_group'] = true;
            }
        }
        foreach ($snapshot['slots'] as $index => $row) {
            if ((int) $row['pevaluacion_id'] === (int) $f['pevB']->id) {
                $snapshot['slots'][$index]['is_half_group'] = true;
            }
        }
        $snapshot = $this->reseal($snapshot);

        $preview = $service->preview($f['calendar'], $snapshot);
        $service->apply($f['calendar'], $snapshot, $preview);

        $lessonB = $this->lessonFor($f['calendar'], $f['pevB']);
        $this->assertTrue((bool) $lessonB->is_half_group);

        $slot = TimetableSlot::query()
            ->where('calendar_id', $f['calendar']->id)
            ->where('seccion_id', $f['pevB']->seccion_id)
            ->firstOrFail();

        // Las claves de unicidad las calcula MySQL: nunca se escriben desde PHP.
        $this->assertSame('S'.$f['pevB']->seccion_id.':H'.$lessonB->id, $slot->slot_section_key);
        $this->assertSame('T'.$f['pevB']->profesor_id.':S'.$lessonB->id, $slot->slot_teacher_key);
        $this->assertTrue((bool) $slot->is_half_group);
    }

    // ─── §14 · Caso 14 — disponibilidad re-vinculada por la terna ─────────

    public function test_case_14_la_disponibilidad_se_re_vincula_por_turno_dia_y_bloque(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // El destino cambió de disponibilidad: otra terna y distinto valor.
        TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->delete();
        TimetableTeacherAvailability::query()->create([
            'calendar_id' => $f['calendar']->id,
            'profesor_id' => $f['pevA']->profesor_id,
            'shift_id' => $f['shift']->id,
            'day_of_week' => 2,
            'order_in_day' => 1,
            'start_time' => '07:00:00',
            'end_time' => '07:45:00',
            'is_available' => true,
        ]);
        $f['calendar']->refresh();

        $preview = $service->preview($f['calendar'], $snapshot);
        $this->assertSame(1, $preview['availability']['current']);
        $this->assertSame(1, $preview['availability']['snapshot']);

        $report = $service->apply($f['calendar'], $snapshot, $preview);
        $this->assertSame(1, $report['availability']);

        $rows = TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->get();
        $this->assertCount(1, $rows);
        $this->assertSame(1, (int) $rows[0]->day_of_week);
        $this->assertSame(3, (int) $rows[0]->order_in_day);
        $this->assertSame((int) $f['shift']->id, (int) $rows[0]->shift_id);
        $this->assertSame((int) $f['pevA']->profesor_id, (int) $rows[0]->profesor_id);
        $this->assertFalse((bool) $rows[0]->is_available);
    }

    // ─── §14 · Caso 15 — bloqueo de sección (`seccions.timetable_locked`) ─

    public function test_case_15_el_restore_repone_el_bloqueo_de_seccion(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // El snapshot dice «sin bloquear»; el destino lo bloqueó después.
        Seccion::query()->whereKey($f['pevA']->seccion_id)->update(['timetable_locked' => true]);
        $f['calendar']->refresh();

        $preview = $service->preview($f['calendar'], $snapshot);

        $changes = $preview['section_locks']['changes'];
        $this->assertCount(1, $changes);
        $this->assertSame((int) $f['pevA']->seccion_id, (int) $changes[0]['seccion_id']);
        $this->assertTrue($changes[0]['from']);
        $this->assertFalse($changes[0]['to']);

        $report = $service->apply($f['calendar'], $snapshot, $preview);

        $this->assertSame(1, $report['section_locks']);
        $this->assertFalse((bool) Seccion::query()->find($f['pevA']->seccion_id)->timetable_locked);
        // La otra sección no cambia: no hay nada que reponer.
        $this->assertFalse((bool) Seccion::query()->find($f['pevB']->seccion_id)->timetable_locked);
    }

    // ─── §14 · Caso 16 — portabilidad entre calendarios equivalentes ──────

    public function test_case_16_el_snapshot_es_portable_entre_calendarios_equivalentes(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        // Otro calendario del mismo lapso/plan: el id del snapshot no manda.
        $destination = TimetableCalendar::factory()->create([
            'lapso_id' => $f['lapso']->id,
            'pestudio_id' => $f['pestudio']->id,
        ]);

        // El id del calendario entra en el checksum: se resella el payload.
        $snapshot['calendar']['id'] = 999999;
        $snapshot = $this->reseal($snapshot);

        $preview = $service->preview($destination, $snapshot);

        $this->assertSame(2, $preview['lessons']['resolvable']);
        $this->assertSame(0, $preview['lessons']['lost']);
        $this->assertSame(2, $preview['slots']['insertable']);
        $this->assertSame(0, $preview['slots']['current']);
        // Los períodos del snapshot no existen en el destino: se crearán.
        $this->assertSame(2, $preview['slots']['periods_to_create']);
        $this->assertContains('periods_created', $this->warningTypes($preview));

        $report = $service->apply($destination, $snapshot, $preview);

        $this->assertSame(2, $report['slots']);
        $this->assertSame(2, $report['periods_created']);
        $this->assertSame(2, $this->slotCount($destination));
        // El calendario de origen no se toca.
        $this->assertSame(2, $this->slotCount($f['calendar']));
    }

    // ─── §14 · Caso 17 — el playbook queda fuera del checksum ─────────────

    public function test_case_17_el_playbook_esta_fuera_del_checksum(): void
    {
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();
        $snapshot = $service->build($f['calendar']);

        $before = $snapshot['checksum'];
        $snapshot['playbook'] = ['playbook_version' => 999, 'levers' => [[], []]];

        $this->assertSame($before, $service->checksum($snapshot));
        $this->assertTrue($service->verify($snapshot)['checksum_verified']);
    }

    // ─── §14 · Caso 18 — diagnósticos derivados del enum ──────────────────

    public function test_case_18_los_diagnosticos_del_playbook_cubren_todos_los_enum(): void
    {
        $playbook = (new TimetableSolverPlaybook)->build();
        $codes = collect($playbook['diagnostics']['codes'])->keyBy('code');

        $this->assertCount(count(UnassignedReason::cases()), $codes);

        foreach (UnassignedReason::cases() as $reason) {
            $this->assertTrue($codes->has($reason->value), "Falta el diagnóstico «{$reason->value}» en el playbook.");
            $this->assertSame($reason->label(), $codes[$reason->value]['label']);
            $this->assertSame($reason->action(), $codes[$reason->value]['action']);
        }
    }

    // ─── §14 · Caso 19 — anti-deriva: referencias y constantes vivas ──────

    public function test_case_19_el_playbook_solo_cita_simbolos_existentes(): void
    {
        $playbook = (new TimetableSolverPlaybook)->build();

        $strings = [];
        $collect = function ($value) use (&$collect, &$strings): void {
            if (is_string($value)) {
                $strings[] = $value;

                return;
            }
            if (is_array($value)) {
                foreach ($value as $item) {
                    $collect($item);
                }
            }
        };
        $collect($playbook);

        $references = [];
        foreach ($strings as $string) {
            preg_match_all(
                '/([A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)+)::([A-Za-z_][A-Za-z0-9_]*)\(\)/',
                $string,
                $matches,
                PREG_SET_ORDER,
            );
            foreach ($matches as $match) {
                $references[$match[1].'::'.$match[2]] = $match[1];
            }
        }

        // Toda referencia `Clase::metodo()` del playbook debe existir de verdad.
        $this->assertNotEmpty($references, 'El playbook no cita ningún método: la salvaguarda anti-deriva quedó inerte.');
        foreach ($references as $reference => $class) {
            [$class, $method] = explode('::', $reference);
            $this->assertTrue(class_exists($class), "El playbook cita una clase inexistente: {$class}");
            $this->assertTrue(method_exists($class, $method), "El playbook cita un método inexistente: {$reference}()");
        }

        // Toda constante de orden del solver debe aparecer documentada. El
        // playbook usa la forma normalizada (`constraint`, no `ORDER_CONSTRAINT`).
        $constants = array_keys((new \ReflectionClass(SolverAttemptConfig::class))->getConstants());
        $orders = array_values(array_map(
            fn (string $name): string => strtolower(substr($name, strlen('ORDER_'))),
            array_filter($constants, fn (string $name): bool => str_starts_with($name, 'ORDER_')),
        ));
        $documented = array_column($playbook['soft_rules']['ordering'], 'order');

        sort($orders);
        sort($documented);

        $this->assertNotEmpty($orders);
        $this->assertSame($orders, $documented);
        $this->assertSame(
            array_map(fn (int $index): string => 'O-'.$index, range(1, count($documented))),
            array_column($playbook['soft_rules']['ordering'], 'id'),
        );
    }

    // ─── §14 · Caso 20 — la config refleja el entorno, el checksum no ─────

    public function test_case_20_las_palancas_del_playbook_siguen_la_config_y_no_el_checksum(): void
    {
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();

        config()->set('timetable.solver.half_group_bonus', 20);
        $first = $service->build($f['calendar']);

        config()->set('timetable.solver.half_group_bonus', 77);
        $second = $service->build($f['calendar']);

        $this->assertSame(20, $first['playbook']['config']['half_group_bonus']);
        $this->assertSame(77, $second['playbook']['config']['half_group_bonus']);

        // Cambiar una palanca del playbook NO invalida el snapshot.
        $this->assertSame($first['checksum'], $second['checksum']);
        $this->assertTrue($service->verify($second)['checksum_verified']);
    }

    // ─── §14 · Caso 21 — métricas derivadas con las claves reales ─────────

    public function test_case_21_las_metricas_derivadas_usan_las_claves_del_solver(): void
    {
        $probe = new SolverOutcome(
            new AttemptResult('probe', new SolverResult([], [], false, 0.0), 0, 0),
            [],
        );

        $expected = array_keys($probe->halfGroupMetrics([]));
        $playbook = (new TimetableSolverPlaybook)->build();

        $this->assertSame($expected, array_keys($playbook['derived_metrics']['keys']));
        $this->assertSame(
            [
                'half_group_lessons',
                'half_group_grouped_periods',
                'half_group_isolated',
                'half_group_unassigned',
            ],
            $expected,
        );
    }

    // ─── Reset global del módulo de horarios (barra superior) ─────────────

    public function test_clear_all_timetable_data_purges_operational_data_and_keeps_calendars(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        Seccion::query()->whereKey($f['pevA']->seccion_id)->update(['timetable_locked' => true]);
        $f['calendar']->update([
            'status' => TimetableCalendar::STATUS_ACTIVE,
            'quality_score' => 10,
        ]);

        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(1, TimetableTeacherAvailability::query()->where('calendar_id', $f['calendar']->id)->count());

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->call('clearAllTimetableData')
            ->assertDispatched('wireui:notification');

        // Datos operativos eliminados en TODOS los calendarios.
        $this->assertSame(0, TimetableLesson::query()->count());
        $this->assertSame(0, TimetableSlot::query()->count());
        $this->assertSame(0, TimetableTeacherAvailability::query()->count());
        $this->assertSame(0, TimetableConflict::query()->count());

        // Estructura conservada: calendarios, períodos, turnos y aulas.
        $this->assertTrue(TimetableCalendar::query()->whereKey($f['calendar']->id)->exists());
        $this->assertSame(4, TimetablePeriod::query()->where('calendar_id', $f['calendar']->id)->count());

        // Bloqueos de sección liberados.
        $this->assertFalse((bool) Seccion::query()->whereKey($f['pevA']->seccion_id)->value('timetable_locked'));

        // Estado derivado y publicación reiniciados.
        $calendar = TimetableCalendar::query()->findOrFail($f['calendar']->id);
        $this->assertNull($calendar->preview_payload);
        $this->assertNull($calendar->quality_score);
        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $calendar->status);
    }

    public function test_clear_all_timetable_data_requires_double_confirmation(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->call('confirmClearAllTimetableData')
            ->assertDispatched('wireui:confirm-dialog', function ($eventName, $params) {
                $options = $params[0]['options'] ?? [];

                return ($options['accept']['method'] ?? '') === 'confirmClearAllTimetableDataFinal'
                    && ($options['icon'] ?? '') === 'warning';
            });

        // El primer diálogo no borra nada.
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());

        // El segundo diálogo tampoco borra: solo confirma con el impacto real.
        $component->call('confirmClearAllTimetableDataFinal')
            ->assertDispatched('wireui:confirm-dialog', function ($eventName, $params) {
                $options = $params[0]['options'] ?? [];

                return ($options['accept']['method'] ?? '') === 'clearAllTimetableData'
                    && ($options['icon'] ?? '') === 'error';
            });
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());

        // Solo el tercer paso borra.
        $component->call('clearAllTimetableData');
        $this->assertSame(0, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    public function test_clear_all_timetable_data_writes_re_aplicable_pre_reset_backups(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->call('clearAllTimetableData');

        // El resguardo pre-reset del calendario con datos existe y es un
        // snapshot v2 completo (lessons + slots).
        $files = collect(Storage::disk('local')->files(TimetableCalendarSnapshotService::AUTO_BACKUP_DIR))
            ->filter(fn (string $path): bool => str_contains($path, 'pre-reset-'.$f['calendar']->id.'-'));
        $this->assertCount(1, $files);

        $payload = json_decode(
            Storage::disk('local')->get($files->first()),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $this->assertSame('cfla-timetable-calendar-snapshot', $payload['format']);
        $this->assertCount(2, $payload['lessons']);
        $this->assertCount(2, $payload['slots']);

        // Es re-aplicable: restaura lessons + slots desde el resguardo.
        $calendar = TimetableCalendar::query()->findOrFail($f['calendar']->id);
        $service = $this->snapshotService();
        $service->apply($calendar, $payload, $service->preview($calendar, $payload));

        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    public function test_clear_all_timetable_data_aborts_when_the_safety_backup_fails(): void
    {
        $f = $this->snapshotFixture();

        // Disco local imposible de escribir: el resguardo pre-reset falla y
        // el borrado debe abortarse sin tocar la base.
        config()->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => '/dev/null/timetable-snapshots-imposible',
        ]);

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->call('clearAllTimetableData');

        // Sin red de seguridad no se borra NADA.
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    // ─── Respaldo/restore de TODOS los calendarios (v2, snapshot completo) ──

    public function test_all_calendars_backup_v2_downloads_full_snapshots_with_slots(): void
    {
        $f = $this->snapshotFixture();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id);

        $response = $component->instance()->downloadAllCalendarsBackup();
        ob_start();
        $response->sendContent();
        $json = (string) ob_get_clean();
        $backup = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('cfla-timetable-calendars-backup', $backup['format']);
        $this->assertSame(2, $backup['version']);

        $entry = collect($backup['calendars'])->firstWhere('calendar.id', $f['calendar']->id);
        $this->assertNotNull($entry, 'El snapshot de todos debe incluir el calendario del fixture.');
        $this->assertSame(TimetableCalendar::STATUS_DRAFT, $entry['calendar']['status']);
        $this->assertArrayHasKey('slots', $entry);
        $this->assertCount(2, $entry['slots']);
        $this->assertArrayHasKey('availability', $entry);
        // Documentación no se duplica por calendario.
        $this->assertArrayNotHasKey('schema', $entry);
        $this->assertArrayNotHasKey('playbook', $entry);
    }

    public function test_all_calendars_backup_restores_published_status(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $f['calendar']->activate();

        $snapshot = $this->snapshotService()->build($f['calendar']->fresh());
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $snapshot['calendar']['status']);

        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();
        $file = UploadedFile::fake()->createWithContent('published.json', json_encode([
            'format' => 'cfla-timetable-calendars-backup',
            'version' => 2,
            'calendars' => [$snapshot],
        ], JSON_THROW_ON_ERROR));

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('allCalendarsBackupFile', $file)
            ->call('restoreAllCalendarsBackup');

        $restored = $f['calendar']->fresh();
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $restored->status);
        $this->assertSame(2, $restored->slots()->count());
    }

    public function test_all_calendars_backup_exports_draft_assignments_without_persisted_slots(): void
    {
        $f = $this->snapshotFixture();
        $f['calendar']->update([
            'preview_payload' => [
                'dry_run' => true,
                'assignment' => [
                    (string) $f['lessonA']->id => [['period_id' => $f['periods'][1]->id]],
                    (string) $f['lessonB']->id => [['period_id' => $f['periods'][2]->id]],
                ],
            ],
        ]);
        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id);
        $response = $component->instance()->downloadAllCalendarsBackup();
        ob_start();
        $response->sendContent();
        $backup = json_decode((string) ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);

        $entry = collect($backup['calendars'])->firstWhere('calendar.id', $f['calendar']->id);
        $this->assertCount(2, $entry['slots']);
    }

    public function test_all_calendars_backup_v2_restores_the_schedule(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();

        $snapshot = $this->snapshotService()->build($f['calendar']);
        unset($snapshot['schema'], $snapshot['playbook']);

        $backup = [
            'format' => 'cfla-timetable-calendars-backup',
            'version' => 2,
            'calendars' => [$snapshot],
        ];
        $json = json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Contexto limpio: sin horario ni lessons.
        TimetableSlot::query()->delete();
        TimetableLesson::query()->delete();
        $this->assertSame(0, TimetableSlot::query()->count());

        $file = UploadedFile::fake()->createWithContent('todos.json', $json);

        $wizard = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('allCalendarsBackupFile', $file)
            ->call('restoreAllCalendarsBackup')
            ->assertDispatched('wireui:notification');

        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());

        // El paso 5 debe poder pintar el horario restaurado.
        $this->assertSame(5, $wizard->get('currentStep'));
        $this->assertContains($wizard->get('generationState'), ['preview_ready', 'published']);
        $preview = $wizard->get('preview');
        $this->assertIsArray($preview);
        $this->assertNotEmpty($preview['assignment'] ?? []);
    }

    public function test_snapshot_uses_dry_run_assignment_when_slots_are_not_published(): void
    {
        $f = $this->snapshotFixture();
        $lessonA = $f['lessonA'];
        $lessonB = $f['lessonB'];

        $f['calendar']->update([
            'preview_payload' => [
                'dry_run' => true,
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $f['periods'][1]->id]],
                    (string) $lessonB->id => [['period_id' => $f['periods'][2]->id]],
                ],
            ],
        ]);
        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();

        $snapshot = $this->snapshotService()->build($f['calendar']->fresh());

        $this->assertCount(2, $snapshot['slots']);
        $this->assertSame($f['pevA']->id, $snapshot['slots'][0]['pevaluacion_id']);
        $this->assertSame($f['pevB']->id, $snapshot['slots'][1]['pevaluacion_id']);
    }

    public function test_all_calendars_backup_v1_legacy_remains_additive(): void
    {
        $f = $this->snapshotFixture();

        $backup = [
            'format' => 'cfla-timetable-calendars-backup',
            'version' => 1,
            'calendars' => [[
                'calendar' => [
                    'id' => $f['calendar']->id,
                    'lapso_id' => $f['calendar']->lapso_id,
                    'pestudio_id' => $f['calendar']->pestudio_id,
                ],
                'lessons' => [[
                    'pevaluacion_id' => $f['pevA']->id,
                    'academic_identity' => [
                        'seccion_id' => $f['pevA']->seccion_id,
                        'pensum_id' => $f['pevA']->pensum_id,
                        'profesor_id' => $f['pevA']->profesor_id,
                        'grupo_estable_id' => null,
                    ],
                    'configuration' => [
                        'shift_id' => $f['shift']->id,
                        'weekly_blocks_t' => 5,
                        'weekly_blocks_p' => 0,
                    ],
                ]],
            ]],
        ];

        $file = UploadedFile::fake()->createWithContent('legacy.json', json_encode($backup));

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('allCalendarsBackupFile', $file)
            ->call('restoreAllCalendarsBackup')
            ->assertDispatched('wireui:notification', function ($eventName, $params) {
                $description = (string) ($params[0]['options']['description'] ?? '');

                return str_contains($description, 'solo lessons')
                    && str_contains($description, 'no trae horario');
            });

        // Aditivo: los slots existentes no se tocan; la lesson se actualiza.
        $this->assertSame(2, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(
            5,
            (int) TimetableLesson::query()
                ->where('calendar_id', $f['calendar']->id)
                ->where('pevaluacion_id', $f['pevA']->id)
                ->value('weekly_blocks_t'),
        );
    }

    public function test_all_calendars_backup_v2_without_horario_warns_in_the_notification(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();
        $service = $this->snapshotService();

        // Snapshot sin horario y sin períodos: ni el restore ni el draft
        // automático pueden producir slots.
        $snapshot = $service->build($f['calendar']);
        unset($snapshot['schema'], $snapshot['playbook']);
        $snapshot['slots'] = [];
        $snapshot['periods'] = [];
        $snapshot['checksum'] = $service->checksum($snapshot);

        TimetableSlot::query()->delete();
        TimetableLesson::query()->delete();
        TimetablePeriod::query()->delete();

        $file = UploadedFile::fake()->createWithContent('sin-horario.json', json_encode([
            'format' => 'cfla-timetable-calendars-backup',
            'version' => 2,
            'calendars' => [$snapshot],
        ], JSON_THROW_ON_ERROR));

        Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('allCalendarsBackupFile', $file)
            ->call('restoreAllCalendarsBackup')
            ->assertDispatched('wireui:notification', function ($eventName, $params) {
                $description = (string) ($params[0]['options']['description'] ?? '');

                return str_contains($description, 'no contenía horario')
                    && str_contains($description, '0 slot(s)');
            });

        // Lessons restauradas; sin horario.
        $this->assertSame(2, TimetableLesson::query()->where('calendar_id', $f['calendar']->id)->count());
        $this->assertSame(0, TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->count());
    }

    public function test_confirm_and_publish_publishes_a_saved_preview_without_step3_selection(): void
    {
        Storage::fake('local');
        $f = $this->snapshotFixture();

        // Estado post-recarga: dry-run guardado en preview_payload, sin slots
        // persistidos y sin selección del paso 3 (se pierde al recargar).
        $f['lessonB']->update(['weekly_blocks_t' => 2]);
        $f['calendar']->update([
            'preview_payload' => [
                'dry_run' => true,
                'assignment' => [
                    (string) $f['lessonA']->id => [
                        ['period_id' => $f['periods'][1]->id],
                        ['period_id' => $f['periods'][2]->id],
                    ],
                    (string) $f['lessonB']->id => [
                        ['period_id' => $f['periods'][3]->id],
                        ['period_id' => $f['periods'][4]->id],
                    ],
                ],
            ],
        ]);
        TimetableSlot::query()->where('calendar_id', $f['calendar']->id)->delete();

        $component = Livewire::withQueryParams(['calendarId' => $f['calendar']->id])
            ->actingAs($f['user'])
            ->test(TimetableWizard::class);

        $this->assertSame([], $component->get('selectedPevs'));
        $this->assertNotNull($component->get('preview'));

        // Antes este paso fallaba con "Sin lessons seleccionadas".
        $component->call('confirmAndPublish');

        $calendar = $f['calendar']->fresh();
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $calendar->status);
        $this->assertSame(4, $calendar->slots()->count());
    }
}
