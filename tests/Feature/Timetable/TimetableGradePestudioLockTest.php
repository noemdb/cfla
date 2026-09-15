<?php

namespace Tests\Feature\Timetable;

use App\Livewire\Coordinacion\Timetable\TimetableWizard;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Toggle de bloqueo del horario por grado y por P.Estudio: aplica a todas las
 * secciones activas del grado / de los grados del P.Estudio.
 */
class TimetableGradePestudioLockTest extends TestCase
{
    use DatabaseTransactions;

    private function makeSeccion(int $gradoId): Seccion
    {
        return Seccion::factory()->create([
            'grado_id' => $gradoId,
            'status_active' => 'true',
        ]);
    }

    public function test_toggle_grade_lock_applies_to_all_active_sections(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $otroGrado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);

        $seccionA = $this->makeSeccion($grado->id);
        $seccionB = $this->makeSeccion($grado->id);
        $seccionOtroGrado = $this->makeSeccion($otroGrado->id);

        $component = Livewire::actingAs($user)->test(TimetableWizard::class);

        $component->call('toggleGradeTimetableLock', $grado->id);
        $this->assertTrue((bool) $seccionA->fresh()->timetable_locked);
        $this->assertTrue((bool) $seccionB->fresh()->timetable_locked);
        $this->assertFalse((bool) $seccionOtroGrado->fresh()->timetable_locked, 'otro grado no debe afectarse');

        $component->call('toggleGradeTimetableLock', $grado->id);
        $this->assertFalse((bool) $seccionA->fresh()->timetable_locked);
        $this->assertFalse((bool) $seccionB->fresh()->timetable_locked);
    }

    public function test_toggle_pestudio_lock_applies_to_all_active_sections(): void
    {
        $user = User::factory()->create(['is_coordinacion' => true]);
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $otroPestudio = Pestudio::factory()->create(['status_active' => 'true']);

        $gradoA = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $gradoB = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $gradoOtro = Grado::factory()->create(['pestudio_id' => $otroPestudio->id, 'status_active' => 'true']);

        $seccionA = $this->makeSeccion($gradoA->id);
        $seccionB = $this->makeSeccion($gradoB->id);
        $seccionOtroPestudio = $this->makeSeccion($gradoOtro->id);

        $component = Livewire::actingAs($user)->test(TimetableWizard::class);

        $component->call('togglePestudioTimetableLock', $pestudio->id);
        $this->assertTrue((bool) $seccionA->fresh()->timetable_locked);
        $this->assertTrue((bool) $seccionB->fresh()->timetable_locked);
        $this->assertFalse((bool) $seccionOtroPestudio->fresh()->timetable_locked, 'otro P.Estudio no debe afectarse');

        $component->call('togglePestudioTimetableLock', $pestudio->id);
        $this->assertFalse((bool) $seccionA->fresh()->timetable_locked);
        $this->assertFalse((bool) $seccionB->fresh()->timetable_locked);
    }
}
