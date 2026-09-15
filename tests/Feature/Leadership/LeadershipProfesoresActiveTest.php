<?php

namespace Tests\Feature\Leadership;

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
use App\Models\User;
use App\Services\Leadership\LeadershipService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * El listado de profesores de Jefes de Área (/app/leadership/profesores) debe
 * omitir a los profesores desactivados (status_active = 'false').
 */
class LeadershipProfesoresActiveTest extends TestCase
{
    use DatabaseTransactions;

    private function makeProfesor(string $status): Profesor
    {
        $user = User::factory()->create();

        return Profesor::create([
            'user_id' => $user->id,
            'name' => 'Nombre'.$status,
            'lastname' => 'Apellido'.$status,
            'ci_profesor' => '9'.random_int(1000, 9999),
            'status_active' => $status,
        ]);
    }

    public function test_unrestricted_admin_excludes_inactive_profesores(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $active = $this->makeProfesor('true');
        $inactive = $this->makeProfesor('false');

        $profs = (new LeadershipService($admin))->getAssignedProfesores();

        $this->assertTrue($profs->contains('id', $active->id));
        $this->assertFalse($profs->contains('id', $inactive->id));
        $this->assertSame(0, $profs->where('status_active', 'false')->count());
    }

    public function test_leadership_user_excludes_inactive_profesores(): void
    {
        $user = User::factory()->leadership()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $asignatura = Asignatura::factory()->create();

        $area = AreaConocimiento::create([
            'leader_id' => $user->id, 'pestudio_id' => $pestudio->id,
            'name' => 'ÁREA LIDERADA', 'code' => 'AL',
        ]);
        CampoConocimiento::create([
            'area_conocimiento_id' => $area->id, 'asignatura_id' => $asignatura->id,
        ]);

        $lapso = Lapso::factory()->create();
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id, 'grado_id' => $grado->id, 'asignatura_id' => $asignatura->id,
        ]);

        $active = $this->makeProfesor('true');
        $inactive = $this->makeProfesor('false');
        foreach ([$active, $inactive] as $profesor) {
            Pevaluacion::factory()->create([
                'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
                'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
            ]);
        }

        $profs = (new LeadershipService($user))->getAssignedProfesores();

        $this->assertTrue($profs->contains('id', $active->id));
        $this->assertFalse($profs->contains('id', $inactive->id));
        $this->assertSame(0, $profs->where('status_active', 'false')->count());
    }
}
