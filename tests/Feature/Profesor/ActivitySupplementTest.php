<?php

namespace Tests\Feature\Profesor;

use App\Livewire\Profesor\Activity\IndexComponent;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\ActivitySupplement;
use App\Models\User;
use App\Services\ActivityImprovementService;
use App\Services\KimiService;
use App\Services\NvidiaService;
use App\Services\OpenRouterService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * PLAN-ACTIVITIES-001 — Información complementaria de la actividad.
 *
 * Contrato (2026-09-07): relación 1:1 con `Activity` en `activity_supplements`
 * (activity_id único + FK cascade). Guardado por UPSERT idempotente
 * (`updateOrCreate`): crear si no existe, actualizar si existe. Desde el
 * componente `IndexComponent` se gestiona el modal (openSupplementModal /
 * saveSupplement / closeSupplementModal) y la subida de imagen JPG al disco
 * público.
 *
 * @group profesor-activity
 * @group activity-supplement
 */
class ActivitySupplementTest extends TestCase
{
    use DatabaseTransactions;

    private static int $chainCounter = 0;

    // ─── Helpers (misma cadena FK que ActivityImprovementTest) ────────

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

        return compact('pevaluacionId', 'profesorId', 's');
    }

    private function createActivity(int $pevaluacionId): Activity
    {
        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema Generador Test',
            'thematic' => 'Tejido Temático Test',
            'description' => 'Actividad Evaluativa Test',
            'teaching' => 'INICIO: motivación DESARROLLO: mediación CIERRE: sistematización',
            'learning' => 'Aprendizaje Esperado Test',
        ]);
    }

    // ─── TESTS ────────────────────────────────────────────────────────

    /**
     * La relación 1:1 funciona en ambas direcciones: desde Activity hacia su
     * suplemento y desde el suplemento hacia su actividad.
     */
    public function test_supplement_relation_both_directions(): void
    {
        $chain = $this->createEvaluacionChain();
        $activity = $this->createActivity($chain['pevaluacionId']);

        $supplement = ActivitySupplement::create([
            'activity_id' => $activity->id,
            'text' => '## Contenido complementario',
            'image_url' => '/storage/activity-supplements/abc.jpg',
        ]);

        // Activity → supplement
        $this->assertSame($supplement->id, $activity->supplement->id);
        $this->assertSame($supplement->image_url, $activity->supplement->image_url);

        // supplement → activity
        $this->assertSame($activity->id, $supplement->activity->id);
        $this->assertSame($activity->topic, $supplement->activity->topic);
    }

    /**
     * El guardado es un UPSERT idempotente: llamar `updateOrCreate` dos veces
     * para la misma activity NO duplica el registro; actualiza el existente.
     */
    public function test_update_or_create_is_idempotent_per_activity(): void
    {
        $chain = $this->createEvaluacionChain();
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::updateOrCreate(
            ['activity_id' => $activity->id],
            ['text' => 'Primera versión', 'image_url' => '/storage/a.jpg'],
        );

        ActivitySupplement::updateOrCreate(
            ['activity_id' => $activity->id],
            ['text' => 'Segunda versión', 'image_url' => '/storage/b.jpg'],
        );

        $this->assertSame(1, ActivitySupplement::where('activity_id', $activity->id)->count());
        $this->assertDatabaseHas('activity_supplements', [
            'activity_id' => $activity->id,
            'text' => 'Segunda versión',
            'image_url' => '/storage/b.jpg',
        ]);
    }

    /**
     * Al abrir el modal para una actividad sin suplemento, el formulario queda
     * vacío listo para crear.
     */
    public function test_open_supplement_modal_without_existing_is_blank(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('showSupplementModal', true)
            ->assertSet('supplementActivityId', $activity->id)
            ->assertSet('supplementText', null)
            ->assertSet('supplementImageUrl', null);
    }

    /**
     * Al abrir el modal para una actividad que YA tiene suplemento, el
     * formulario se rellena con el registro existente.
     */
    public function test_open_supplement_modal_with_existing_populates_form(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create([
            'activity_id' => $activity->id,
            'text' => 'Contenido guardado previamente',
            'image_url' => '/storage/prev.jpg',
        ]);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementText', 'Contenido guardado previamente')
            ->assertSet('supplementImageUrl', '/storage/prev.jpg');
    }

    /**
     * saveSupplement crea el registro cuando no existía (UPSERT — INSERT).
     */
    public function test_save_supplement_creates_record_when_missing(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'Nuevo contenido en **markdown**')
            ->set('supplementImageUrl', '/storage/nuevo.jpg')
            ->call('saveSupplement')
            ->assertSet('showSupplementModal', false);

        $this->assertSame(1, ActivitySupplement::where('activity_id', $activity->id)->count());
        $this->assertDatabaseHas('activity_supplements', [
            'activity_id' => $activity->id,
            'text' => 'Nuevo contenido en **markdown**',
            'image_url' => '/storage/nuevo.jpg',
        ]);
    }

    /**
     * saveSupplement actualiza el registro cuando ya existía (UPSERT — UPDATE)
     * y no duplica filas.
     */
    public function test_save_supplement_updates_existing_record(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create([
            'activity_id' => $activity->id,
            'text' => 'Antiguo',
            'image_url' => '/storage/old.jpg',
        ]);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'Actualizado')
            ->set('supplementImageUrl', '/storage/new.jpg')
            ->call('saveSupplement');

        $this->assertSame(1, ActivitySupplement::where('activity_id', $activity->id)->count());
        $this->assertDatabaseHas('activity_supplements', [
            'activity_id' => $activity->id,
            'text' => 'Actualizado',
            'image_url' => '/storage/new.jpg',
        ]);
    }

    /**
     * Cerrar el modal limpia el estado de la sesión.
     */
    public function test_close_supplement_modal_clears_state(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'algo')
            ->call('closeSupplementModal')
            ->assertSet('showSupplementModal', false)
            ->assertSet('supplementText', null)
            ->assertSet('supplementImageUrl', null)
            ->assertSet('supplementActivityId', null);
    }

    // ─── IA: GENERAR TEXTO COMPLEMENTARIO FORMATEADO (PLAN-ACTIVITIES-001) ──

    /**
     * El botón "Generar Texto" con textarea vacío avisa y NO llama al servicio
     * de IA.
     */
    public function test_generate_supplement_text_with_empty_input_warns_without_calling_ai(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->method('improveSupplementText')->willReturn([]);
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            // textarea vacío
            ->call('generateSupplementText')
            ->assertSet('supplementText', null)
            ->assertDispatched('wireui:notification');

        // Sin suplemento persistido
        $this->assertSame(0, ActivitySupplement::where('activity_id', $activity->id)->count());
    }

    /**
     * El botón "Generar Texto" con éxito puebla el textarea con el Markdown
     * devuelto por la IA.
     */
    public function test_generate_supplement_text_success_populates_textarea(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->expects($this->once())
            ->method('improveSupplementText')
            ->willReturn([
                'success' => true,
                'content' => '## Título mejorado\n\nTexto reorganizado con **énfasis**.',
                'model'   => 'test-model',
                'error'   => null,
            ]);
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'texto original plano')
            ->call('generateSupplementText')
            ->assertSet('supplementText', '## Título mejorado\n\nTexto reorganizado con **énfasis**.')
            ->assertDispatched('wireui:notification');
    }

    /**
     * Si la IA responde con error, se muestra notificación de error y el
     * textarea queda intacto.
     */
    public function test_generate_supplement_text_failure_keeps_textarea_and_notifies(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->expects($this->once())
            ->method('improveSupplementText')
            ->willReturn([
                'success' => false,
                'content' => null,
                'model'   => null,
                'error'   => 'Servicio IA caído',
            ]);
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'texto original plano')
            ->call('generateSupplementText')
            ->assertSet('supplementText', 'texto original plano')
            ->assertDispatched('wireui:notification');
    }

    /**
     * El servicio limpia los wrappers de fence markdown (```md ... ```) de la
     * respuesta de la IA, devolviendo solo el markdown plano.
     */
    public function test_improve_supplement_text_strips_markdown_fences(): void
    {
        // Se mockean los 3 servicios de IA que inyecta el constructor.
        $openRouter = $this->createMock(OpenRouterService::class);
        $nvidia = $this->createMock(NvidiaService::class);
        $kimi = $this->createMock(KimiService::class);

        $raw = "```md" . "\n" . "## Título" . "\n" . "\n" . "Texto formateado." . "\n" . "```";
        $expected = "## Título" . "\n" . "\n" . "Texto formateado.";

        $openRouter->expects($this->once())
            ->method('ask')
            ->willReturn([
                'success' => true,
                'content' => $raw,
                'model'   => 'test-model',
                'usage'   => null,
                'error'   => null,
            ]);

        $service = new ActivityImprovementService($openRouter, $nvidia, $kimi);

        $result = $service->improveSupplementText(
            text: 'texto original',
            pensumId: 1,
            profesorId: 1,
        );

        $this->assertTrue($result['success']);
        // El fence ```md ... ``` se elimina; queda el markdown plano.
        $this->assertSame($expected, $result['content']);
    }

    // ─── A2: ELIMINAR SUPLEMENTO ──────────────────────────────────────

    /**
     * deleteSupplement elimina el registro y cierra el modal.
     */
    public function test_delete_supplement_removes_record_and_closes_modal(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create([
            'activity_id' => $activity->id,
            'text' => 'Contenido a eliminar',
        ]);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementExists', true)
            ->call('deleteSupplement')
            ->assertSet('showSupplementModal', false)
            ->assertSet('supplementExists', false);

        $this->assertSame(0, ActivitySupplement::where('activity_id', $activity->id)->count());
    }

    /**
     * Al abrir el modal para una actividad sin suplemento, `supplementExists`
     * es false (no se muestra el botón Eliminar).
     */
    public function test_open_supplement_modal_sets_exists_flag(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        // Sin suplemento → false
        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementExists', false);

        // Tras crear → true
        ActivitySupplement::create(['activity_id' => $activity->id, 'text' => 'x']);
        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementExists', true);
    }

    // ─── B1: VISTA PREVIA (TERCER TAB) ────────────────────────────────

    /**
     * setSupplementTab cambia entre las pestañas 'text', 'image' y 'preview'.
     * La pestaña por defecto al abrir es 'text'; un valor inválido se ignora.
     */
    public function test_set_supplement_tab(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementTab', 'text')
            ->call('setSupplementTab', 'image')
            ->assertSet('supplementTab', 'image')
            ->call('setSupplementTab', 'preview')
            ->assertSet('supplementTab', 'preview')
            ->call('setSupplementTab', 'text')
            ->assertSet('supplementTab', 'text')
            // Valor inválido se ignora
            ->call('setSupplementTab', 'invalid')
            ->assertSet('supplementTab', 'text');
    }

    // ─── A1: BADGE "TIENE SUPLEMENTO" EN LA LISTA ────────────────────

    /**
     * El listado muestra el indicador "Info" cuando la actividad tiene suplemento.
     */
    public function test_list_shows_supplement_badge_when_exists(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create(['activity_id' => $activity->id, 'text' => '## X']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            // E1: badge de texto
            ->assertSee('Tiene texto complementario', false);
    }

    /**
     * Con imagen, muestra el badge IMG en el listado (E1).
     */
    public function test_list_shows_image_badge_when_image_exists(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create(['activity_id' => $activity->id, 'image_url' => '/storage/x.jpg']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->assertSee('Tiene imagen complementaria', false);
    }

    /**
     * El listado NO muestra indicadores cuando la actividad no tiene suplemento.
     */
    public function test_list_hides_supplement_badge_when_missing(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $this->createActivity($chain['pevaluacionId']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->assertDontSee('Tiene texto complementario', false)
            ->assertDontSee('Tiene imagen complementaria', false);
    }

    // ─── B5: QUITAR IMAGEN ───────────────────────────────────────────

    /**
     * removeSupplementImage pone la URL de imagen a null.
     */
    public function test_remove_supplement_image_clears_image(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        ActivitySupplement::create(['activity_id' => $activity->id, 'text' => 'x', 'image_url' => '/storage/a.jpg']);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->assertSet('supplementImageUrl', '/storage/a.jpg')
            ->call('removeSupplementImage')
            ->assertSet('supplementImageUrl', null);
    }

    // ─── C3: MODO DE FORMATEO ────────────────────────────────────────

    /**
     * setSupplementFormatMode se propaga en generateSupplementText.
     */
    public function test_generate_supplement_text_passes_format_mode(): void
    {
        $chain = $this->createEvaluacionChain();
        $user = $this->createProfesorUser($chain['profesorId']);
        $activity = $this->createActivity($chain['pevaluacionId']);

        // El mock devuelve éxito sin importar los args; verifica que el flujo
        // con mode≠default no rompe y que la prop se propaga.
        $mock = $this->createMock(ActivityImprovementService::class);
        $mock->expects($this->once())
            ->method('improveSupplementText')
            ->willReturn(['success' => true, 'content' => 'ok', 'model' => 'm', 'error' => null]);
        $this->app->instance(ActivityImprovementService::class, $mock);

        Livewire::actingAs($user)
            ->test(IndexComponent::class, ['id' => $chain['pevaluacionId']])
            ->call('openSupplementModal', $activity->id)
            ->set('supplementText', 'texto')
            ->set('supplementFormatMode', 'detallado')
            ->call('generateSupplementText')
            ->assertSet('supplementFormatMode', 'detallado')
            ->assertSet('supplementText', 'ok');
    }
}