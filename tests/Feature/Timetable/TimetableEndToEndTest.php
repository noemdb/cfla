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
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * Flujo completo multi-calendario end-to-end: crear calendario por pestudio →
 * generar períodos desde la estructura legacy → lecciones → dry-run → publicar →
 * los lectores resuelven el activo del lapso vigente.
 */
class TimetableEndToEndTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    public function test_full_flow_per_pestudio_calendar(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);

        // Lapso vigente + pestudio MEDIA GENERAL (estructura real del legacy).
        $lapso = Lapso::factory()->create([
            'finicial' => now()->subMonth(),
            'ffinal' => now()->addMonth(),
        ]);
        $pestudio = Pestudio::factory()->create(['name' => 'EDUCACION MEDIA GENERAL']);

        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create(['pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id]);
        $profesor = Profesor::create([
            'user_id' => User::factory()->create()->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '40001', 'status_active' => 'true',
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        // ── Paso 1: crear el calendario (con pestudio) y generar períodos del turno M.
        $shift = $this->makeShift();

        $c = Livewire::actingAs($user)
            ->test(\App\Livewire\Coordinacion\Timetable\TimetableWizard::class)
            ->set('currentStep', 1)
            ->set('lapsoId', $lapso->id)
            ->set('pestudioId', $pestudio->id)
            ->set('calendarName', 'E2E Test')
            ->call('createCalendar')
            ->assertHasNoErrors();

        $calendar = TimetableCalendar::query()->where('lapso_id', $lapso->id)->orderByDesc('id')->first();
        $this->assertNotNull($calendar);
        $this->assertSame($pestudio->id, $calendar->pestudio_id);

        $c->set('calendarId', $calendar->id)
            ->set('shiftId', $shift->id)
            ->call('generatePeriods')
            ->call('savePeriods')
            ->assertHasNoErrors();

        // Los períodos provienen de la estructura del legacy (MEDIA GENERAL · M).
        $this->assertGreaterThan(0, TimetableCalendar::find($calendar->id)->periods()->count());

        // ── Paso 3: seleccionar la lección y guardar.
        $c->set('currentStep', 3)
            ->set('selectedPevs', [$pev->id => true])
            ->call('saveLessons')
            ->assertHasNoErrors();

        // La lección existe con bloques derivados de la asignatura.
        $lesson = TimetableLesson::query()->where('calendar_id', $calendar->id)->where('pevaluacion_id', $pev->id)->first();
        $this->assertNotNull($lesson, 'La lección debe quedar registrada para el calendario.');

        // Asignar el turno correcto a la lección (bulk desde las opciones).
        $c->set('lessons.'.$pev->id.'.shift_id', $shift->id)
            ->call('saveLessons');

        // ── Paso 5: dry-run → confirmar → activo.
        $c->set('currentStep', 5)
            ->call('runDryRun')
            ->assertSet('generationState', 'preview_ready');

        $calendar->refresh();
        $this->assertNotNull($calendar->preview_payload);
        $this->assertTrue($calendar->preview_payload['dry_run']);

        $c->call('confirmAndPublish')
            ->assertSet('generationState', 'published');

        // El calendario quedó ACTIVE y es el único del lapso.
        $this->assertSame(TimetableCalendar::STATUS_ACTIVE, $calendar->fresh()->status);
        $this->assertSame(1, TimetableCalendar::query()->where('lapso_id', $lapso->id)->active()->count());

        // Los lectores resuelven el activo del lapso vigente.
        $resolved = TimetableCalendar::activeForCurrentLapso();
        $this->assertNotNull($resolved);
        $this->assertSame($calendar->id, $resolved->id);
        $this->assertSame($pestudio->id, $resolved->pestudio_id);
    }
}
