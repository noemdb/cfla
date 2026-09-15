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
use App\Models\app\Timetable\TimetableRoom;
use App\Models\User;
use App\Services\Timetable\TimetableRoomEligibilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\Concerns\TimetableShiftHelper;
use Tests\TestCase;

/**
 * La advertencia de capacidad de aula del Paso 3 debe considerar los
 * medio-grupos: un medio grupo ocupa el aula con la mitad de la sección, así
 * que la capacidad se compara contra `ceil(matrícula / 2)`.
 */
class TimetableStep3RoomCapacityHalfGroupTest extends TestCase
{
    use DatabaseTransactions, TimetableShiftHelper;

    /**
     * @return array{user: User, calendar: TimetableCalendar, pev: Pevaluacion, shift: \App\Models\app\Timetable\TimetableShift}
     */
    private function fixture(): array
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
            'amount_student' => 10,
        ]);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '9301', 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 0, 'hour_p_week' => 2]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);
        $calendar = TimetableCalendar::factory()->create([
            'lapso_id' => $lapso->id, 'pestudio_id' => $pestudio->id,
        ]);
        $shift = $this->makeShift();

        // Garantiza que exista al menos un laboratorio con capacidad >= 10.
        TimetableRoom::factory()->laboratory()->create(['capacity' => 10]);

        return compact('user', 'calendar', 'pev', 'shift');
    }

    /**
     * Capacidad máxima de laboratorio elegible para el calendario, según el
     * MISMO servicio que usa la advertencia (incluye aulas reales de la BD).
     */
    private function maxLaboratoryCapacity(TimetableCalendar $calendar): int
    {
        return (int) app(TimetableRoomEligibilityService::class)
            ->forCalendar($calendar)
            ->where('type', 'laboratorio')
            ->max('capacity');
    }

    /**
     * @param  array<string, mixed>  $f
     * @return list<string>
     */
    private function warningsFor(array $f, bool $isHalfGroup, int $students): array
    {
        $f['pev']->seccion->update(['amount_student' => $students]);

        $component = Livewire::actingAs($f['user'])
            ->test(TimetableWizard::class)
            ->set('calendarId', $f['calendar']->id)
            ->set('lessons', [
                $f['pev']->id => [
                    'pev_id' => $f['pev']->id,
                    'shift_id' => $f['shift']->id,
                    'weekly_blocks_t' => 0,
                    'weekly_blocks_p' => 2,
                    'room_type_required' => 'laboratorio',
                    'is_half_group' => $isHalfGroup,
                ],
            ]);

        return $component->viewData('step3Warnings')[$f['pev']->id] ?? [];
    }

    public function test_capacity_warning_fires_without_half_group(): void
    {
        $f = $this->fixture();
        $students = $this->maxLaboratoryCapacity($f['calendar']) + 10;

        $warnings = $this->warningsFor($f, false, $students);

        $this->assertContains(
            'La capacidad máxima del aula es menor que la matrícula de la sección',
            $warnings,
        );
    }

    public function test_capacity_warning_is_cleared_for_half_group(): void
    {
        $f = $this->fixture();
        $max = $this->maxLaboratoryCapacity($f['calendar']);
        // Matrícula completa > capacidad, pero su mitad cabe en el aula.
        $students = $max + 10;

        $warnings = $this->warningsFor($f, true, $students);

        $this->assertNotContains(
            'La capacidad máxima del aula es menor que la matrícula de la sección',
            $warnings,
        );
        $this->assertNotContains(
            'La capacidad máxima del aula es menor que la mitad de la matrícula (medio grupo)',
            $warnings,
        );
    }

    public function test_capacity_warning_fires_for_half_group_when_half_still_exceeds_capacity(): void
    {
        $f = $this->fixture();
        $max = $this->maxLaboratoryCapacity($f['calendar']);
        // La mitad de la matrícula tampoco cabe.
        $students = ($max * 2) + 10;

        $warnings = $this->warningsFor($f, true, $students);

        $this->assertContains(
            'La capacidad máxima del aula es menor que la mitad de la matrícula (medio grupo)',
            $warnings,
        );
    }
}
