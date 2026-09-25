<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\IndexComponent;
use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Confirmación de eliminación de actividades con `x-dialog`.
 *
 * El botón ya no borra directamente: pide confirmación (`askDelete`) y solo
 * borra al confirmar (`delActivity`). Además el borrado queda acotado a la
 * pevaluacion del componente, para que un id manipulado desde el navegador no
 * elimine actividades de otra pevaluacion.
 */
class ActivityDeleteConfirmTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    private function createEvaluacionChain(): array
    {
        self::$chainCounter++;
        $s = self::$chainCounter;
        $code = fn (string $base) => "{$base}-{$s}";

        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => $code('LAP-TEST'),
            'code_sm' => 'LT',
            'name' => 'Test Lapso '.$s,
            'finicial' => now(),
            'ffinal' => now()->addMonths(3),
            'status_last' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA',
            'name' => 'Test Scale '.$s,
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution '.$s,
            'legalname' => 'Test Institution Legal '.$s,
            'rif_institution' => 'J-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT).'-9',
            'email_institution' => 'test'.$s.'@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar '.$s,
            'description' => 'Test',
            'finicial' => now(),
            'ffinal' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE '.$s,
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => $code('PEST-TEST'),
            'name' => 'Test Plan de Estudio '.$s,
            'scale' => $escalaId,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test Grado '.$s,
            'code' => $code('GR-TEST'),
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId,
            'name' => 'A'.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => $code('ASIG-TEST'),
            'name' => 'Test Asignatura '.$s,
            'tescala' => $escalaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId,
            'grado_id' => $gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true,
            'status_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-'.str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'ci_profesor' => str_pad((string) $s, 8, '0', STR_PAD_LEFT),
            'name' => 'Profesor Test '.$s,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Test objetivo '.$s,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return compact('pevaluacionId', 'profesorId', 's');
    }

    private function createActivity(int $pevaluacionId, string $topic = 'Tema a eliminar'): Activity
    {
        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => $topic,
            'thematic' => 'Tejido',
            'description' => 'Descripción',
            'teaching' => 'INICIO',
            'learning' => 'Aprendizaje',
        ]);
    }

    private function profesorUser(int $profesorId): User
    {
        $user = User::factory()->create(['is_profesor' => true, 'is_active' => 'enable']);
        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

    public function test_ask_delete_arma_el_dialogo_sin_borrar(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->profesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId'], 'Álgebra基本');

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('askDelete', $activity->id)
            ->assertSet('activityToDelete', $activity->id)
            ->assertSet('activityToDeleteTopic', 'Álgebra基本')
            ->assertSee('Álgebra基本');

        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
    }

    public function test_confirmar_elimina_y_limpia_el_estado(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->profesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('askDelete', $activity->id)
            ->call('delActivity', $activity->id)
            ->assertSet('activityToDelete', null)
            ->assertSet('activityToDeleteTopic', null);

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_cancelar_no_elimina_y_limpia_el_estado(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->profesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('askDelete', $activity->id)
            ->call('cancelDelete')
            ->assertSet('activityToDelete', null);

        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
    }

    public function test_con_logros_asociados_no_abre_el_dialogo(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->profesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Achievement::create([
            'activity_id' => $activity->id,
            'name' => 'Logro test',
            'type' => 'LOGRO',
            'value' => '10',
        ]);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('askDelete', $activity->id)
            ->assertSet('activityToDelete', null);

        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
    }

    public function test_no_puede_borrar_una_actividad_de_otra_pevaluacion(): void
    {
        $chainA = $this->createEvaluacionChain();
        $chainB = $this->createEvaluacionChain();
        $user = $this->profesorUser($chainA['profesorId']);
        $activityB = $this->createActivity($chainB['pevaluacionId']);

        // El componente está en la pevaluacion A: pedir el borrado de una
        // actividad de B debe fallar, no borrarla.
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        try {
            Livewire::actingAs($user)
                ->test(IndexComponent::class, ['id' => $chainA['pevaluacionId']])
                ->call('delActivity', $activityB->id);
        } finally {
            $this->assertDatabaseHas('activities', ['id' => $activityB->id]);
        }
    }
}
