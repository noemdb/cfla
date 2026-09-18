<?php

namespace Tests\Feature\Timetable;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
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
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * PDFs consolidados del toolbar: todos los P.Estudios y todos los profesores
 * de los calendarios activos del lapso vigente.
 */
class TimetableGlobalPdfTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    private function fixture(): array
    {
        // Usa el lapso vigente real para que los consolidados lo incluyan.
        $lapso = Lapso::current() ?? Lapso::factory()->create();

        $user = User::factory()->create(['is_coordinacion' => true]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '8801', 'status_active' => 'true',
        ]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
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

        return compact('user', 'lapso', 'pestudio', 'grado', 'seccion', 'profesor', 'calendar');
    }

    public function test_all_pestudios_pdf_streams(): void
    {
        $fixture = $this->fixture();

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-pestudios'))
            ->assertOk();
    }

    public function test_all_teachers_pdf_streams(): void
    {
        $fixture = $this->fixture();

        $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers'))
            ->assertOk();
    }

    /**
     * assertSee volcaría todo el HTML (muy grande) al fallar; aquí sólo se
     * compara el fragmento esperado para mantener el reporte legible.
     */
    private function assertHtmlContains(string $needle, \Illuminate\Testing\TestResponse $response): void
    {
        $this->assertStringContainsString($needle, $response->getContent(), "No se encontró «{$needle}» en el HTML generado.");
    }

    public function test_all_teachers_respects_orientation_parameter(): void
    {
        $fixture = $this->fixture();

        // Sin parámetro: portrait por defecto.
        $this->assertHtmlContains('size:letter portrait', $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers'))
            ->assertOk());

        $this->assertHtmlContains('size:letter landscape', $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers', ['orientation' => 'landscape']))
            ->assertOk());

        // Valor inválido cae al default portrait.
        $this->assertHtmlContains('size:letter portrait', $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers', ['orientation' => 'diagonal']))
            ->assertOk());
    }

    public function test_all_teachers_groups_sheets_by_per_page_and_orders_by_ci(): void
    {
        $fixture = $this->fixture();
        $calendar = $fixture['calendar'];
        $basePeriod = TimetablePeriod::query()->where('calendar_id', $calendar->id)->firstOrFail();

        // Dos docentes adicionales con CI distinta. El reporte agrupa por el
        // profesor de la Pevaluación, así que cada uno necesita su propia
        // Pevaluación + lección + slot (no basta con cambiar profesor_id).
        foreach ([['Beto', 'Zamora', '20'], ['Carlos', 'Ávila', '3']] as $i => [$name, $lastname, $ci]) {
            $extra = Profesor::create([
                'user_id' => User::factory()->create()->id,
                'name' => $name, 'lastname' => $lastname,
                'ci_profesor' => $ci, 'status_active' => 'true',
            ]);

            $seccion = Seccion::factory()->create([
                'grado_id' => $fixture['grado']->id, 'name' => 'X'.$i, 'status_active' => 'true',
            ]);
            $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
            $pensum = Pensum::factory()->create([
                'pestudio_id' => $fixture['pestudio']->id,
                'grado_id' => $fixture['grado']->id,
                'asignatura_id' => $asignatura->id,
            ]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $extra->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $fixture['lapso']->id,
            ]);
            $lesson = TimetableLesson::factory()->create([
                'calendar_id' => $calendar->id, 'pevaluacion_id' => $pev->id,
                'shift_id' => $basePeriod->shift_id, 'weekly_blocks_t' => 1, 'weekly_blocks_p' => 0,
            ]);

            TimetableSlot::factory()->create([
                'calendar_id' => $calendar->id, 'lesson_id' => $lesson->id,
                'period_id' => $basePeriod->id, 'profesor_id' => $extra->id,
                'seccion_id' => $seccion->id,
            ]);
        }

        $response = $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers', ['per_page' => 2]))
            ->assertOk();

        $html = $response->getContent();

        // Orden por CI: 3 (Ávila), 20 (Zamora), 8801 (López).
        $posCi3 = strpos($html, 'CI: 3</p>');
        $posCi20 = strpos($html, 'CI: 20</p>');
        $posCi8801 = strpos($html, 'CI: 8801</p>');
        $this->assertNotFalse($posCi3);
        $this->assertNotFalse($posCi20);
        $this->assertNotFalse($posCi8801);
        $this->assertTrue($posCi3 < $posCi20 && $posCi20 < $posCi8801, 'Los docentes deben ordenarse por CI.');

        // Hojas = ceil(docentes / per_page) y hay salto de página entre hojas.
        $teacherCount = substr_count($html, 'class="card"');
        $this->assertGreaterThan(0, $teacherCount);
        $this->assertSame((int) ceil($teacherCount / 2), substr_count($html, 'class="sheet"'));
        $this->assertStringContainsString('break-after:page', $html);

        // Con 1 docente por hoja el número de hojas coincide con los docentes.
        $htmlPerOne = $this->actingAs($fixture['user'])
            ->get(route('app.coordinacion.timetable.pdf.all-teachers', ['per_page' => 1]))
            ->assertOk()
            ->getContent();
        $this->assertSame($teacherCount, substr_count($htmlPerOne, 'class="sheet"'));
    }

    public function test_all_teachers_groups_schedules_by_peducativo(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::current() ?? Lapso::factory()->create();
        $peducativo = Peducativo::factory()->create([
            'name' => 'PEDUCATIVO '.uniqid(),
            'status_active' => 'true',
        ]);

        $profesor = Profesor::create([
            'user_id' => User::factory()->create()->id,
            'name' => 'Nestor', 'lastname' => 'Peducativo',
            'ci_profesor' => '7700'.random_int(10, 99), 'status_active' => 'true',
        ]);

        // Dos P.Estudios del MISMO P.Educativo, ambos con horario del docente.
        $asignaturas = [];
        foreach ([0, 1] as $i) {
            $pestudio = Pestudio::factory()->create(['peducativo_id' => $peducativo->id, 'status_active' => 'true']);
            $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
            $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
            $asignatura = Asignatura::factory()->create(['hour_t_week' => 1, 'hour_p_week' => 0]);
            $asignaturas[] = $asignatura->name;
            $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
            $pev = Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);

            $calendar = TimetableCalendar::factory()->create(['lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id, 'status' => 'active']);
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
        }

        $html = $this->actingAs($user)
            ->get(route('app.coordinacion.timetable.pdf.all-teachers'))
            ->assertOk()
            ->getContent();

        // El P.Educativo agrupa ambos P.Estudios en un único horario: aparece
        // una sola vez, sin subdivisiones por P.Estudio, y las asignaturas de
        // ambos P.Estudios se fusionan en la misma grilla.
        $this->assertSame(1, substr_count($html, $peducativo->name), 'el P.Educativo debe agrupar los horarios del docente');
        $this->assertStringContainsString('class="peducativo-title"', $html);
        $this->assertStringNotContainsString('pestudio-inline', $html, 'no debe subdividirse por P.Estudio');

        foreach ($asignaturas as $asignaturaName) {
            $this->assertStringContainsString($asignaturaName, $html, 'la asignatura fusionada debe aparecer en la grilla del P.Educativo');
        }
    }

    public function test_all_pestudios_returns_404_without_active_calendars(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        // Sin calendarios activos en el lapso vigente.
        TimetableCalendar::query()->update(['status' => 'archived']);

        $this->actingAs($user)
            ->get(route('app.coordinacion.timetable.pdf.all-pestudios'))
            ->assertNotFound();
    }
}
