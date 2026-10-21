<?php

namespace Tests\Feature\Timetable;

use App\Models\app\Academy\AreaConocimiento;
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
use App\Models\app\Timetable\TimetableRoom;
use App\Models\app\Timetable\TimetableShift;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\User;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * ADR-TT-017 — Fase 7: las vistas de solo lectura y los PDFs parten la celda en
 * dos sub-franjas (½ sup. / ½ inf.) cuando contiene mitades, sin alterar el
 * layout de una celda normal.
 */
class HalfBlockReadOnlyTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private Lapso $lapso;

    private TimetableShift $shift;

    private User $user;

    private Pestudio $pestudio;

    private Grado $grado;

    private Seccion $seccion;

    private TimetableCalendar $calendar;

    private TimetablePeriod $period;

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
        ]);

        $this->period = TimetablePeriod::factory()->create([
            'calendar_id' => $this->calendar->id,
            'shift_id' => $this->shift->id,
            'day_of_week' => 1,
            'order_in_day' => 1,
            'is_break' => false,
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

    /**
     * @return array{lesson: TimetableLesson, slot: TimetableSlot, asignatura: Asignatura, profesor: Profesor}
     */
    private function makeHalfSlot(Profesor $profesor, ?int $half, bool $halfBlock = true): array
    {
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $this->pestudio->id,
            'grado_id' => $this->grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id,
            'seccion_id' => $this->seccion->id,
            'pensum_id' => $pensum->id,
            'lapso_id' => $this->lapso->id,
        ]);

        $lesson = TimetableLesson::factory()->create([
            'calendar_id' => $this->calendar->id,
            'pevaluacion_id' => $pev->id,
            'shift_id' => $this->shift->id,
            'weekly_blocks_t' => 1,
            'weekly_blocks_p' => 0,
            'half_block' => $halfBlock,
        ]);

        $slot = TimetableSlot::factory()->create([
            'calendar_id' => $this->calendar->id,
            'lesson_id' => $lesson->id,
            'period_id' => $this->period->id,
            'profesor_id' => $profesor->id,
            'seccion_id' => $this->seccion->id,
            'half' => $half,
        ]);

        return compact('lesson', 'slot', 'asignatura', 'profesor');
    }

    /** Crea una lección con dos mitades secuenciales (sup + inf) en el mismo período. */
    private function makeTwoHalves(): array
    {
        $sup = $this->makeHalfSlot($this->makeProfesor('7001'), 0);
        $inf = $this->makeHalfSlot($this->makeProfesor('7002'), 1);

        return [$sup, $inf];
    }

    /**
     * Estructura mínima de una grilla de preview con dos mitades el lunes.
     *
     * @return array<int, array<string, mixed>>
     */
    private function previewShiftGrids(): array
    {
        return [[
            'shift' => $this->shift,
            'rows' => [[
                'period' => $this->period,
                'days' => collect(range(1, 5))->mapWithKeys(fn (int $day): array => [
                    $day => [
                        'period' => $this->period,
                        'assignments' => $day === 1
                            ? [
                                ['asignatura' => 'Matemática', 'profesor' => 'Pérez', 'grupo' => null, 'half' => 0],
                                ['asignatura' => 'Física', 'profesor' => 'Gómez', 'grupo' => null, 'half' => 1],
                            ]
                            : [],
                    ],
                ])->all(),
            ]],
        ]];
    }

    // ─── Vista pública firmada (modelos) ────────────────────────────────────

    public function test_public_section_view_renders_both_halves(): void
    {
        [$sup, $inf] = $this->makeTwoHalves();

        $url = URL::temporarySignedRoute('timetable.public.section', now()->addHour(), [
            'calendar' => $this->calendar->id,
            'seccion' => $this->seccion->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('½ sup.')
            ->assertSee('½ inf.')
            ->assertSee($sup['asignatura']->name)
            ->assertSee($inf['asignatura']->name);
    }

    public function test_public_section_view_keeps_normal_cell_without_half_bands(): void
    {
        $full = $this->makeHalfSlot($this->makeProfesor('7011'), null, halfBlock: false);
        $this->makeProfesor('7012');

        $url = URL::temporarySignedRoute('timetable.public.section', now()->addHour(), [
            'calendar' => $this->calendar->id,
            'seccion' => $this->seccion->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee($full['asignatura']->name)
            ->assertDontSee('½ sup.')
            ->assertDontSee('½ inf.');
    }

    // ─── PDFs por modelo (section / teacher) ────────────────────────────────

    public function test_section_pdf_view_renders_both_halves(): void
    {
        [$sup, $inf] = $this->makeTwoHalves();

        $grid = app(TimetableViewService::class)->gridForSection($this->calendar, (int) $this->seccion->id);

        $html = view('pdfs.timetable.section', [
            'calendar' => $this->calendar,
            'seccion' => $this->seccion,
            'grid' => $grid,
            'institucion' => null,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString($sup['asignatura']->name, $html);
        $this->assertStringContainsString($inf['asignatura']->name, $html);
    }

    public function test_teacher_pdf_view_renders_both_halves(): void
    {
        // Un mismo docente con dos mitades secuenciales en secciones distintas:
        // la grilla del docente debe partir la celda igualmente.
        $profesor = $this->makeProfesor('7021');
        $this->makeHalfSlot($profesor, 0);
        $this->makeHalfSlot($profesor, 1);

        $grid = app(TimetableViewService::class)->gridForTeacher($this->calendar, (int) $profesor->id);

        $html = view('pdfs.timetable.teacher', [
            'calendar' => $this->calendar,
            'profesor' => $profesor,
            'grid' => $grid,
            'institucion' => null,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
    }

    // ─── PDFs de vista previa (arrays de preview con `half`) ─────────────────

    public function test_preview_section_pdf_renders_both_halves(): void
    {
        $shiftGrids = [[
            'shift' => $this->shift,
            'rows' => [[
                'period' => $this->period,
                'days' => collect(range(1, 5))->mapWithKeys(fn (int $day): array => [
                    $day => [
                        'period' => $this->period,
                        'assignments' => $day === 1
                            ? [
                                ['asignatura' => 'Matemática', 'profesor' => 'Pérez', 'grupo' => null, 'half' => 0],
                                ['asignatura' => 'Física', 'profesor' => 'Gómez', 'grupo' => null, 'half' => 1],
                            ]
                            : [],
                    ],
                ])->all(),
            ]],
        ]];

        $html = view('pdfs.timetable.preview-section', [
            'calendar' => $this->calendar,
            'seccion' => $this->seccion,
            'shiftGrids' => $shiftGrids,
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
            'teacherSummary' => [],
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString('Matemática', $html);
        $this->assertStringContainsString('Física', $html);
    }

    public function test_preview_area_pdf_renders_both_halves(): void
    {
        $area = AreaConocimiento::create([
            'pestudio_id' => $this->pestudio->id,
            'name' => 'CIENCIAS NATURALES',
            'code' => 'CN',
        ]);

        $shiftGrids = [[
            'shift' => $this->shift,
            'rows' => [[
                'period' => $this->period,
                'days' => collect(range(1, 5))->mapWithKeys(fn (int $day): array => [
                    $day => [
                        'period' => $this->period,
                        'assignments' => $day === 1
                            ? [
                                ['asignatura' => 'Biología', 'profesor' => 'Ruiz', 'grado' => '1º', 'seccion' => 'A', 'grupo' => null, 'room' => 'L1', 'half' => 0],
                                ['asignatura' => 'Química', 'profesor' => 'Lara', 'grado' => '2º', 'seccion' => 'B', 'grupo' => null, 'room' => null, 'half' => 1],
                            ]
                            : [],
                    ],
                ])->all(),
            ]],
        ]];

        $html = view('pdfs.timetable.preview-area', [
            'calendar' => $this->calendar,
            'area' => $area,
            'shiftGrids' => $shiftGrids,
            'asignaturasCount' => 2,
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString('Biología', $html);
        $this->assertStringContainsString('Química', $html);
    }

    public function test_preview_grade_pdf_renders_both_halves_and_fractional_summary(): void
    {
        $html = view('pdfs.timetable.preview-grade', [
            'calendar' => $this->calendar,
            'grado' => $this->grado,
            'sectionSchedules' => [[
                'section' => $this->seccion,
                'shiftGrids' => $this->previewShiftGrids(),
                'teacherSummary' => [['name' => 'Docente Media', 'blocks' => 0.5]],
            ]],
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString('Matemática', $html);
        $this->assertStringContainsString('Física', $html);
        $this->assertStringContainsString('<td>0.5</td>', $html);
    }

    public function test_preview_pestudio_pdf_renders_both_halves(): void
    {
        $html = view('pdfs.timetable.preview-pestudio', [
            'calendar' => $this->calendar,
            'pestudio' => $this->pestudio,
            'gradeSchedules' => [[
                'grado' => $this->grado,
                'sectionSchedules' => [[
                    'section' => $this->seccion,
                    'shiftGrids' => $this->previewShiftGrids(),
                    'teacherSummary' => [],
                ]],
            ]],
            'institucion' => null,
            'fecha' => 'hoy',
            'isPublishedSchedule' => false,
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString('Matemática', $html);
        $this->assertStringContainsString('Física', $html);
    }

    public function test_preview_all_pestudios_pdf_renders_both_halves_and_fractional_summary(): void
    {
        $html = view('pdfs.timetable.preview-all-pestudios', [
            'lapso' => $this->lapso,
            'pestudiosData' => [[
                'calendar' => $this->calendar,
                'pestudio' => $this->pestudio,
                'isPublishedSchedule' => false,
                'gradeSchedules' => [[
                    'grado' => $this->grado,
                    'sectionSchedules' => [[
                        'section' => $this->seccion,
                        'shiftGrids' => $this->previewShiftGrids(),
                        'teacherSummary' => [['name' => 'Docente Media', 'blocks' => 0.5]],
                    ]],
                ]],
            ]],
            'institucion' => null,
            'fecha' => 'hoy',
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString('Matemática', $html);
        $this->assertStringContainsString('Física', $html);
        $this->assertStringContainsString('<td class="blq">0.5</td>', $html);
    }

    public function test_room_pdf_view_renders_both_halves(): void
    {
        $room = TimetableRoom::factory()->create();
        [$sup, $inf] = $this->makeTwoHalves();
        $sup['slot']->update(['room_id' => $room->id]);
        $inf['slot']->update(['room_id' => $room->id]);

        $grid = app(TimetableViewService::class)->gridForRoom($this->calendar, (int) $room->id);

        $html = view('pdfs.timetable.room', [
            'calendar' => $this->calendar,
            'room' => $room,
            'grid' => $grid,
            'institucion' => null,
            'fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY'),
        ])->render();

        $this->assertStringContainsString('½ sup.', $html);
        $this->assertStringContainsString('½ inf.', $html);
        $this->assertStringContainsString($sup['asignatura']->name, $html);
        $this->assertStringContainsString($inf['asignatura']->name, $html);
    }

    public function test_all_grid_timetable_reports_use_the_shared_half_cell_partial(): void
    {
        $views = [
            'pdfs.timetable.section',
            'pdfs.timetable.teacher',
            'pdfs.timetable.room',
            'pdfs.timetable.teachers',
            'pdfs.timetable.teachers-all',
            'pdfs.timetable.preview-section',
            'pdfs.timetable.preview-grade',
            'pdfs.timetable.preview-pestudio',
            'pdfs.timetable.preview-area',
            'pdfs.timetable.preview-all-pestudios',
            'timetable.public',
            'timetable.teachers-all',
            'livewire.timetable.role-view',
            'livewire.profesor.timetable.my-timetable',
        ];

        foreach ($views as $view) {
            $path = resource_path('views/'.str_replace('.', '/', $view).'.blade.php');

            $this->assertFileExists($path);
            $this->assertStringContainsString(
                'timetable.partials.half-cell',
                (string) file_get_contents($path),
                "{$view} debe usar la celda compartida de medias franjas.",
            );
        }
    }
}
