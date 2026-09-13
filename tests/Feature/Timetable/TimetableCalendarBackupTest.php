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
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Backup/restore de lessons de TODO el calendario (barra superior), sin
 * depender de la sección activa.
 */
class TimetableCalendarBackupTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

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
}
