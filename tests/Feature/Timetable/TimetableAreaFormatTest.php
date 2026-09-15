<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Formato tipo horario por asignaturas asociadas a un área de conocimiento
 * (toolbar del wizard + PDF por asignatura).
 */
class TimetableAreaFormatTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '7701', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $lapso = Lapso::factory()->create();
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active',
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);
        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
            'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
        ]);
        TimetableSlot::factory()->create([
            'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
            'period_id' => $period->id, 'profesor_id' => $profesor->id,
            'seccion_id' => $seccion->id,
        ]);

        $area = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id, 'name' => 'CIENCIAS NATURALES', 'code' => 'CN',
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id, 'asignatura_id' => $asignatura->id,
        ]);

        return compact(
            'user', 'profesor', 'pestudio', 'grado', 'seccion', 'asignatura',
            'pensum', 'lapso', 'pev', 'calendar', 'shift', 'period', 'lesson', 'area',
        );
    }

    public function test_preview_area_pdf_streams(): void
    {
        $fixture = $this->fixture();

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.area-preview', [
                $fixture['calendar']->id,
                $fixture['area']->id,
            ]))
            ->assertOk();
    }

    public function test_area_format_options_and_url(): void
    {
        $fixture = $this->fixture();

        $component = Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $fixture['calendar']->id)
            ->call('openAreaFormatModal')
            ->assertSet('showAreaFormatModal', true)
            ->assertSee('Formato por área de conocimiento')
            // El dropdown enriquecido muestra nombre, código y conteo de asignaturas.
            ->assertSee('CIENCIAS NATURALES')
            ->assertSee('CN')
            ->assertSee('asignatura(s)');

        // Otra área de un P.Estudio distinto también debe listarse (sin filtro).
        $otroPestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $otraArea = AreaConocimiento::create([
            'pestudio_id' => $otroPestudio->id, 'name' => 'ÁREA DE OTRO PESTUDIO', 'code' => 'OTRO',
        ]);

        $options = $component->instance()->areaFormatOptions();
        $this->assertTrue($options->contains('id', $fixture['area']->id));
        $this->assertTrue($options->contains('id', $otraArea->id));

        $component->set('areaFormatId', (string) $fixture['area']->id);

        $url = $component->instance()->areaFormatUrl();
        $this->assertNotNull($url);
        $this->assertStringContainsString(
            'timetable/pdf/area-preview/'.$fixture['calendar']->id.'/'.$fixture['area']->id,
            $url,
        );
    }

    public function test_preview_area_consolidates_subjects_in_one_grid(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '8802', 'status_active' => 'true',
        ]);
        $profesorB = Profesor::create([
            'user_id' => $user->id, 'name' => 'Beto', 'lastname' => 'Pérez',
            'ci_profesor' => '8803', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $lapso = Lapso::factory()->create();

        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active',
        ]);
        $shift = $this->makeShift();
        $period = TimetablePeriod::factory()->create([
            'calendar_id' => $calendar->id, 'shift_id' => $shift->id,
            'day_of_week' => 1, 'order_in_day' => 1, 'is_break' => false,
        ]);

        $area = AreaConocimiento::create([
            'pestudio_id' => $pestudio->id, 'name' => 'AREA CONSOLIDADA', 'code' => 'AC',
        ]);

        foreach (['MATEMÁTICA', 'FÍSICA'] as $index => $nombre) {
            $seccion = $index === 0 ? $seccionA : $seccionB;
            $docente = $index === 0 ? $profesor : $profesorB;
            $asignatura = Asignatura::factory()->create(['name' => $nombre, 'hour_t_week' => 2, 'hour_p_week' => 0]);
            CampoConocimiento::create([
                'area_conocimiento_id' => $area->id, 'asignatura_id' => $asignatura->id,
            ]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $docente->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);
            $lesson = TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
                'shift_id' => $shift->id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            ]);
            TimetableSlot::factory()->create([
                'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
                'period_id' => $period->id, 'profesor_id' => $docente->id,
                'seccion_id' => $seccion->id,
            ]);
        }

        $captured = null;
        $pdfMock = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('stream')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf']));
        Pdf::shouldReceive('loadView')->once()->andReturnUsing(function ($view, $data) use (&$captured, $pdfMock) {
            $captured = $data;

            return $pdfMock;
        });

        $this->actingAs($user)
            ->get(route('app.coordinacion.timetable.pdf.area-preview', [$calendar->id, $area->id]))
            ->assertOk();

        $this->assertIsArray($captured);
        $this->assertArrayHasKey('shiftGrids', $captured);

        // Una sola grilla (no una por asignatura) con las dos apiladas en la celda.
        $cell = $captured['shiftGrids'][0]['rows'][0]['days'][1]['assignments'];
        $this->assertCount(2, $cell);
        $this->assertEqualsCanonicalizing(
            ['MATEMÁTICA', 'FÍSICA'],
            array_column($cell, 'asignatura'),
        );
        $profesores = array_column($cell, 'profesor');
        $this->assertContains($profesor->lastname.' '.$profesor->name, $profesores);
        $this->assertContains($profesorB->lastname.' '.$profesorB->name, $profesores);
        $this->assertNotSame('', $cell[0]['grado']);
        $this->assertNotSame('', $cell[0]['seccion']);
    }

    public function test_area_format_resolves_calendar_without_wizard_selection(): void
    {
        $fixture = $this->fixture();

        // Sin calendarId en el wizard: se resuelve el activo del P.Estudio del área.
        $component = Livewire::actingAs($fixture['user'])
            ->test(TimetableWizard::class)
            ->call('openAreaFormatModal')
            ->set('areaFormatId', (string) $fixture['area']->id);

        $calendar = $component->instance()->areaFormatCalendar();
        $this->assertNotNull($calendar);
        $this->assertSame($fixture['calendar']->id, $calendar->id);

        $url = $component->instance()->areaFormatUrl();
        $this->assertStringContainsString(
            'timetable/pdf/area-preview/'.$fixture['calendar']->id.'/'.$fixture['area']->id,
            $url,
        );
    }
}
