<?php

namespace Tests\Feature\Livewire\Profesor\Lms;

use App\Livewire\Profesor\Lms\LessonWizard;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El «Listado de Lecciones» (/app/profesors/lms/activity/lesson/new) debe
 * mostrarse en orden cronológico según `activities.finicial`; las actividades
 * sin fecha inicial quedan al final.
 */
class LessonWizardListOrderTest extends TestCase
{
    use DatabaseTransactions;

    private static int $counter = 0;

    /**
     * @return array{user: User, lapso: Lapso, pev: Pevaluacion}
     */
    private function fixture(): array
    {
        self::$counter++;
        $n = self::$counter;

        $user = User::factory()->create(['is_profesor' => true]);
        $lapso = Lapso::factory()->create();
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $profesor = Profesor::create([
            'user_id' => $user->id, 'name' => 'Ana', 'lastname' => 'López',
            'ci_profesor' => '77'.$n, 'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create(['hour_t_week' => 2, 'hour_p_week' => 0]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
        ]);
        $pev = Pevaluacion::factory()->create([
            'profesor_id' => $profesor->id, 'seccion_id' => $seccion->id,
            'pensum_id' => $pensum->id, 'lapso_id' => $lapso->id,
        ]);

        return compact('user', 'lapso', 'pev');
    }

    private function makeActivity(Pevaluacion $pev, string $topic, $finicial): Activity
    {
        return Activity::create([
            'pevaluacion_id' => $pev->id,
            'finicial' => $finicial,
            'ffinal' => $finicial ? \Carbon\Carbon::parse($finicial)->addDays(7) : now()->addDays(7),
            'topic' => $topic,
            'thematic' => 'Tejido '.$topic,
            'description' => 'Desc '.$topic,
            'teaching' => 'INICIO DESARROLLO CIERRE',
            'learning' => 'Aprendizaje '.$topic,
        ]);
    }

    public function test_listado_de_lecciones_ordenado_por_activities_finicial(): void
    {
        $f = $this->fixture();

        // Se crean en orden NO cronológico a propósito.
        $this->makeActivity($f['pev'], 'Tardía', now()->addDays(20));
        $this->makeActivity($f['pev'], 'Temprana', now()->addDays(1));
        $this->makeActivity($f['pev'], 'Media', now()->addDays(10));

        $component = Livewire::actingAs($f['user'])
            ->test(LessonWizard::class)
            ->set('lapsoId', $f['lapso']->id);

        $topics = $component->viewData('activities')->getCollection()->pluck('topic')->all();

        $this->assertSame(['Temprana', 'Media', 'Tardía'], $topics);
    }
}
