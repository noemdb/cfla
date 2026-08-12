<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\IndexComponent;
use App\Models\User;
use App\Services\ActivityImprovementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Mejora de actividad con IA (IndexComponent::improveActivity).
 *
 * Contrato de comportamiento (2026-08-11): si TODOS los servicios AI fallan
 * (p. ej. Kimi HTTP 401), el flujo NO se interrumpe: se loguea el detalle
 * técnico completo, se muestra un confirm amigable con opción de "Reintentar"
 * y el formulario queda intacto — el profesor NUNCA ve el JSON/error crudo.
 */
class ActivityImprovementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ai_failure_shows_friendly_retry_dialog_without_technical_error(): void
    {
        [$pevaluacionId, $profesorId, $s] = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($profesorId);

        // Todos los servicios AI fallan (caso real: Kimi HTTP 401)
        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->expects($this->once())
            ->method('improve')
            ->willThrowException(new \RuntimeException(
                'Todos los servicios AI fallaron. Último error: Kimi: HTTP 401: {"error":{"message":"Invalid Authentication"}}'
            ));
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $pevaluacionId])
            ->call('improveActivity')
            // El flujo NO se interrumpe: se dispara el confirm amigable con Reintentar
            ->assertDispatched('wireui:confirm-dialog', function ($eventName, $params) {
                // dispatch($event, $payload) → params[0] = ['options' => [...], 'componentId' => ...]
                $options = $params[0]['options'] ?? [];

                // El botón aceptar reintenta improveActivity; rechazar solo cierra
                return ($options['accept']['label'] ?? '') === 'Reintentar'
                    && ($options['accept']['method'] ?? '') === 'improveActivity'
                    && ($options['reject']['label'] ?? '') === 'Cerrar';
            })
            // El detalle técnico NO llega a la UI
            ->assertDontSee('Todos los servicios AI fallaron', false)
            ->assertDontSee('Invalid Authentication', false)
            ->assertDontSee('HTTP 401', false)
            // El formulario queda intacto (nada se sobreescribe con el error)
            ->assertSet('activityForm.topic', null)
            ->assertSet('activityForm.description', null);
    }

    public function test_ai_success_populates_form_and_shows_success_notification(): void
    {
        [$pevaluacionId, $profesorId, $s] = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($profesorId);

        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->expects($this->once())
            ->method('improve')
            ->willReturn([
                'description' => 'Descripción mejorada por IA',
                'topic' => 'Tema mejorado por IA',
                'thematic' => 'Tejido temático mejorado',
                'references' => 'Referencias mejoradas',
                'teachingStart' => 'INICIO mejorado',
                'teachingContent' => 'DESARROLLO mejorado',
                'teachingEnd' => 'CIERRE mejorado',
            ]);
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $pevaluacionId])
            ->call('improveActivity')
            ->assertDispatched('wireui:notification')
            ->assertSet('activityForm.topic', 'Tema mejorado por IA')
            ->assertSet('activityForm.description', 'Descripción mejorada por IA')
            ->assertSet('activityForm.teachingStart', 'INICIO mejorado')
            ->assertSet('activityForm.teachingContent', 'DESARROLLO mejorado')
            ->assertSet('activityForm.teachingEnd', 'CIERRE mejorado');
    }

    // ─── Helpers (misma cadena FK que StudentResourceTest) ────────────

    private static int $chainCounter = 0;

    private function createProfesorUser(int $profesorId): User
    {
        $user = User::factory()->create(['is_profesor' => true]);

        DB::table('profesors')->where('id', $profesorId)->update(['user_id' => $user->id]);

        return $user;
    }

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

        return [$pevaluacionId, $profesorId, $s];
    }
}
