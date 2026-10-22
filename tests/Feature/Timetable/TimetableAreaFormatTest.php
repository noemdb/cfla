<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableRoom;
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
        $slot = TimetableSlot::factory()->create([
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
            'pensum', 'lapso', 'pev', 'calendar', 'shift', 'period', 'lesson', 'slot', 'area',
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

    public function test_area_preview_uses_published_slots_and_badge_for_active_calendar(): void
    {
        $fixture = $this->fixture();

        // Un preview_payload viejo no debe convertir en borrador un calendario
        // que ya está activo ni sustituir los slots publicados.
        $fixture['calendar']->update([
            'preview_payload' => [
                'dry_run' => true,
                'assignment' => [
                    '999999' => [[
                        'period_id' => (int) $fixture['period']->id,
                        'room_id' => 0,
                        'half' => null,
                    ]],
                ],
                'unassigned' => [],
                'assignment_source' => 'manual_preview',
            ],
        ]);

        $captured = null;
        $pdfMock = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('stream')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf']));
        Pdf::shouldReceive('loadView')->once()->andReturnUsing(function ($view, $data) use (&$captured, $pdfMock) {
            $captured = $data;

            return $pdfMock;
        });

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.area-preview', [
                $fixture['calendar']->id,
                $fixture['area']->id,
            ]))
            ->assertOk();

        $this->assertTrue($captured['isPublishedSchedule']);

        $html = view('pdfs.timetable.preview-area', $captured)->render();
        $this->assertStringContainsString('HORARIO PUBLICADO', $html);
        $this->assertStringNotContainsString('BORRADOR — NO PUBLICADO', $html);
        $this->assertStringContainsString($fixture['asignatura']->name, $html);
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

    public function test_preview_area_shows_grupo_estable_below_subject(): void
    {
        $fixture = $this->fixture();
        $grupo = GrupoEstable::factory()->create(['name' => 'Grupo Azul']);
        $fixture['pev']->update(['grupo_estable_id' => $grupo->id]);

        $captured = null;
        $pdfMock = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('stream')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf']));
        Pdf::shouldReceive('loadView')->once()->andReturnUsing(function ($view, $data) use (&$captured, $pdfMock) {
            $captured = $data;

            return $pdfMock;
        });

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.area-preview', [
                $fixture['calendar']->id,
                $fixture['area']->id,
            ]))
            ->assertOk();

        $cell = $captured['shiftGrids'][0]['rows'][0]['days'][1]['assignments'];
        $this->assertCount(1, $cell);
        $this->assertSame('Grupo Azul', $cell[0]['grupo']);

        $html = view('pdfs.timetable.preview-area', [
            'calendar' => $fixture['calendar'],
            'area' => $fixture['area'],
            'shiftGrids' => $captured['shiftGrids'],
            'asignaturasCount' => 1,
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();

        $subject = preg_quote(e($fixture['asignatura']->name), '/');
        $this->assertMatchesRegularExpression(
            '/<div class="subject">'.$subject.'<\/div>\s*<div class="group">Grupo Azul<\/div>/',
            $html,
        );
    }

    public function test_section_pdf_shows_grupo_estable_below_subject(): void
    {
        $fixture = $this->fixture();
        $grupo = GrupoEstable::factory()->create(['name' => 'Grupo Azul']);
        $fixture['pev']->update(['grupo_estable_id' => $grupo->id]);

        $slot = $fixture['slot']->fresh([
            'lesson.pevaluacion.pensum.asignatura',
            'lesson.pevaluacion.profesor',
            'lesson.pevaluacion.grupoEstable',
        ]);

        $grid = collect([
            1 => collect([
                1 => collect([$slot]),
                2 => collect(),
                3 => collect(),
                4 => collect(),
                5 => collect(),
            ]),
        ]);

        $html = view('pdfs.timetable.section', [
            'calendar' => $fixture['calendar'],
            'seccion' => $fixture['seccion'],
            'grid' => $grid,
            'institucion' => null,
            'fecha' => 'hoy',
        ])->render();

        $subject = preg_quote(e($fixture['asignatura']->name), '/');
        $this->assertMatchesRegularExpression(
            '/<div class="subject">'.$subject.'<\/div>\s*<div class="group">Grupo Azul<\/div>/',
            $html,
        );
    }

    public function test_area_preview_route_hides_teacher_names_when_calendar_flag_is_false(): void
    {
        $fixture = $this->fixture();
        $fixture['calendar']->update(['status_profesor_show' => false]);

        $captured = null;
        $pdfMock = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('setPaper')->andReturnSelf();
        $pdfMock->shouldReceive('stream')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf']));
        Pdf::shouldReceive('loadView')->once()->andReturnUsing(function ($view, $data) use (&$captured, $pdfMock) {
            $captured = $data;

            return $pdfMock;
        });

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.area-preview', [
                $fixture['calendar']->id,
                $fixture['area']->id,
            ]))
            ->assertOk();

        $this->assertNotNull($captured);
        $this->assertFalse($captured['calendar']->status_profesor_show);

        $html = view('pdfs.timetable.preview-area', $captured)->render();
        $this->assertStringNotContainsString($fixture['profesor']->lastname, $html);
    }

    public function test_printable_grids_hide_teacher_names_when_calendar_flag_is_false(): void
    {
        $fixture = $this->fixture();
        $fixture['calendar']->update(['status_profesor_show' => false]);
        $fixture['calendar']->refresh();

        $slot = $fixture['slot']->fresh([
            'lesson.pevaluacion.pensum.asignatura',
            'lesson.pevaluacion.profesor',
            'lesson.pevaluacion.grupoEstable',
            'lesson.pevaluacion.seccion',
        ]);

        $grid = collect([
            1 => collect([
                1 => collect([$slot]),
                2 => collect(),
                3 => collect(),
                4 => collect(),
                5 => collect(),
            ]),
        ]);

        $teacherLastname = $fixture['profesor']->lastname;
        $sectionView = fn () => view('pdfs.timetable.section', [
            'calendar' => $fixture['calendar'],
            'seccion' => $fixture['seccion'],
            'grid' => $grid,
            'institucion' => null,
            'fecha' => 'hoy',
        ])->render();

        $this->assertStringNotContainsString($teacherLastname, $sectionView());

        $roomHtml = view('pdfs.timetable.room', [
            'calendar' => $fixture['calendar'],
            'room' => TimetableRoom::factory()->create(),
            'grid' => $grid,
            'institucion' => null,
            'fecha' => 'hoy',
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $roomHtml);

        $publicHtml = view('timetable.public', [
            'calendar' => $fixture['calendar'],
            'subjectLabel' => 'Sección '.$fixture['seccion']->name,
            'grid' => $grid,
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $publicHtml);

        $days = collect(range(1, 5))->mapWithKeys(fn (int $day): array => [
            $day => [
                'period' => $day === 1 ? $fixture['period'] : null,
                'assignments' => $day === 1 ? [[
                    'asignatura' => $fixture['asignatura']->name,
                    'profesor' => $fixture['profesor']->lastname.' '.$fixture['profesor']->name,
                    'grupo' => null,
                    'grado' => $fixture['grado']->name,
                    'seccion' => $fixture['seccion']->name,
                    'room' => null,
                    'half' => null,
                ]] : [],
            ],
        ])->all();

        $shiftGrids = [[
            'shift' => $fixture['shift'],
            'rows' => [[
                'period' => $fixture['period'],
                'days' => $days,
            ]],
        ]];

        $areaHtml = view('pdfs.timetable.preview-area', [
            'calendar' => $fixture['calendar'],
            'area' => $fixture['area'],
            'shiftGrids' => $shiftGrids,
            'asignaturasCount' => 1,
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $areaHtml);

        $previewSectionHtml = view('pdfs.timetable.preview-section', [
            'calendar' => $fixture['calendar'],
            'seccion' => $fixture['seccion'],
            'shiftGrids' => $shiftGrids,
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
            'teacherSummary' => [],
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $previewSectionHtml);

        $previewGradeHtml = view('pdfs.timetable.preview-grade', [
            'calendar' => $fixture['calendar'],
            'grado' => $fixture['grado'],
            'sectionSchedules' => [[
                'section' => $fixture['seccion'],
                'shiftGrids' => $shiftGrids,
                'teacherSummary' => [],
            ]],
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $previewGradeHtml);

        $allPestudiosHtml = view('pdfs.timetable.preview-all-pestudios', [
            'lapso' => $fixture['lapso'],
            'pestudiosData' => [[
                'calendar' => $fixture['calendar'],
                'pestudio' => $fixture['pestudio'],
                'isPublishedSchedule' => false,
                'gradeSchedules' => [[
                    'grado' => $fixture['grado'],
                    'sectionSchedules' => [[
                        'section' => $fixture['seccion'],
                        'shiftGrids' => $shiftGrids,
                        'teacherSummary' => [],
                    ]],
                ]],
            ]],
            'institucion' => null,
            'fecha' => 'hoy',
        ])->render();
        $this->assertStringNotContainsString($teacherLastname, $allPestudiosHtml);

        // Al reactivar el flag, la grilla vuelve a mostrar el apellido.
        $fixture['calendar']->update(['status_profesor_show' => true]);
        $fixture['calendar']->refresh();

        $this->assertStringContainsString($teacherLastname, $sectionView());
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
