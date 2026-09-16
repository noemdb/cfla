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
 * El botón "Draft sección (solver)" REORGANIZA los bloques existentes de la
 * sección activa (sin agregar, quitar ni cambiar lecciones) para minimizar
 * colisiones, respetando `is_half_group` y `locked`.
 */
class TimetableGenerateSectionDraftTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_generate_section_draft_reorganizes_existing_slots(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1001', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        $periods = [];
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                $periods[] = TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }

        $lessonIds = [];
        foreach ([0, 1] as $n) {
            $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);
            $lesson = TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            ]);
            TimetableSlot::factory()->create([
                'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
                'period_id' => $periods[$n]->id, 'profesor_id' => $profesor->id,
                'seccion_id' => $seccion->id,
            ]);
            $lessonIds[] = $lesson->id;
        }

        $originalStatus = $calendar->status;

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $seccion->id)
            ->call('generateSectionDraft');

        $preview = $component->get('preview');

        $this->assertNotNull($preview);
        $this->assertSame('preview_ready', $component->get('generationState'));
        foreach ($lessonIds as $id) {
            $this->assertArrayHasKey((string) $id, $preview['assignment'], "la lección {$id} debe seguir en el horario");
            $this->assertCount(1, $preview['assignment'][(string) $id], "la lección {$id} conserva su número de bloques");
        }
        // Reorganizar no cambia el estado del calendario.
        $this->assertSame($originalStatus, $calendar->fresh()->status);
    }

    public function test_generate_section_draft_preserves_other_sections_in_preview(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1003', 'status_active' => 'true',
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 3; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }
        $period = TimetablePeriod::query()->where('calendar_id', $calendar->id)->orderBy('id')->first();

        $mk = function (Seccion $seccion) use ($pestudio, $grado, $lapso, $profesor, $calendar, $shift): TimetableLesson {
            $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);

            return TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            ]);
        };
        $lessonA = $mk($seccionA);
        $lessonB = $mk($seccionB);

        // La grilla previa del componente incluye ambas secciones (como si se
        // hubiera construido desde los slots persistidos), aunque el
        // preview_payload de la BD esté vacío.
        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $seccionA->id)
            ->set('preview', [
                'assignment' => [
                    (string) $lessonA->id => [['period_id' => $period->id]],
                    (string) $lessonB->id => [['period_id' => $period->id]],
                ],
                'unassigned' => [],
            ])
            ->call('generateSectionDraft');

        $preview = $component->get('preview');
        $this->assertArrayHasKey((string) $lessonA->id, $preview['assignment'], 'la lección de la sección activa debe estar en el draft');
        $this->assertArrayHasKey((string) $lessonB->id, $preview['assignment'], 'la lección de la otra sección no debe perderse del preview');
    }

    public function test_locked_section_blocks_generate_section_draft(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true', 'timetable_locked' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '1005', 'status_active' => 'true',
        ]);
        $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id]);
        $shift = $this->makeShift();
        for ($day = 1; $day <= 5; $day++) {
            for ($order = 1; $order <= 2; $order++) {
                TimetablePeriod::factory()->create([
                    'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
                    'day_of_week' => $day, 'order_in_day' => $order, 'is_break' => false,
                ]);
            }
        }
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(TimetableWizard::class)
            ->set('calendarId', $calendar->id)
            ->set('activeSeccionId', $seccion->id)
            ->call('generateSectionDraft');

        $this->assertNotSame('preview_ready', $component->get('generationState'));
    }
}
