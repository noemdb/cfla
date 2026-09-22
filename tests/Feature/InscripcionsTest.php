<?php

namespace Tests\Feature;

use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class InscripcionsTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_planner' => true,
        ]);
    }

    /**
     * Creates a minimal Estudiant record with all required FK references.
     */
    private function createMinimalEstudiant(array $extra = []): Estudiant
    {
        $representant = Representant::factory()->create();
        $planpagoId = DB::table('planpagos')->insertGetId([
            'name' => 'Plan de Prueba',
            'description' => 'Plan de pago de prueba',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Estudiant::create(array_merge([
            'ci_estudiant' => fake()->unique()->numerify('########'),
            'name' => $extra['name'] ?? fake()->firstName(),
            'lastname' => fake()->lastName(),
            'representant_id' => $representant->id,
            'planpago_id' => $planpagoId,
            'type_ci_id' => 1,
            'gender' => 'Masculino',
            'date_birth' => '2010-01-15',
            'city_birth' => 'Ciudad de Prueba',
            'state_birth' => 'Estado de Prueba',
            'country_birth' => 'VENEZUELA',
            'status_active' => 'true',
            'cellphone' => '0412-1234567',
            'email' => fake()->unique()->safeEmail(),
        ], $extra));
    }

    // ─── HTTP Tests ─────────────────────────────────────────────

    /** @test */
    public function the_page_loads_successfully(): void
    {
        $this->actingAs($this->user)
            ->get('/app/planning/inscripcions')
            ->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_users_are_redirected(): void
    {
        $this->get('/app/planning/inscripcions')
            ->assertRedirect('/login');
    }

    /** @test */
    public function non_planner_users_get_403(): void
    {
        $nonPlanner = User::factory()->create([
            'is_planner' => false,
            'is_admin' => false,
        ]);

        $this->actingAs($nonPlanner)
            ->get('/app/planning/inscripcions')
            ->assertForbidden();
    }

    // ─── Livewire CRUD Tests ────────────────────────────────────

    /** @test */
    public function it_lists_existing_inscripcions(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $estudiant = $this->createMinimalEstudiant();
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->assertSee($estudiant->name)
            ->assertSee($estudiant->lastname);
    }

    /** @test */
    public function it_creates_an_inscripcion(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $estudiant = $this->createMinimalEstudiant();
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('create')
            ->set('pestudio_id', $pestudio->id)
            ->set('grado_id', $grado->id)
            ->set('seccion_id', $seccion->id)
            ->set('estudiant_id', $estudiant->id)
            ->set('tipo_id', $tipo->id)
            ->set('escolaridad_id', $escolaridad->id)
            ->set('programacion_id', $programacion->id)
            ->set('grupo_estable_id', $grupo->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inscripcions', [
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
        ]);
    }

    /** @test */
    public function it_edits_an_inscripcion(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $estudiant = $this->createMinimalEstudiant();
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        $inscripcion = Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        $newObservations = 'Observación editada';

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('edit', $inscripcion->id)
            ->set('observations', $newObservations)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inscripcions', [
            'id' => $inscripcion->id,
            'observations' => $newObservations,
        ]);
    }

    /** @test */
    public function it_soft_deletes_an_inscripcion(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $estudiant = $this->createMinimalEstudiant();
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        $inscripcion = Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('confirmDelete', $inscripcion->id)
            ->call('destroy')
            ->assertHasNoErrors();

        $this->assertSoftDeleted('inscripcions', [
            'id' => $inscripcion->id,
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_estudiant_id_on_create(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        $estudiant = $this->createMinimalEstudiant();

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('create')
            ->set('pestudio_id', $pestudio->id)
            ->set('grado_id', $grado->id)
            ->set('seccion_id', $seccion->id)
            ->set('estudiant_id', $estudiant->id)
            ->set('tipo_id', $tipo->id)
            ->set('escolaridad_id', $escolaridad->id)
            ->set('programacion_id', $programacion->id)
            ->set('grupo_estable_id', $grupo->id)
            ->call('save')
            ->assertHasErrors('estudiant_id');
    }

    /** @test */
    public function it_filters_by_search(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'status_active' => 'true']);
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        $juan = $this->createMinimalEstudiant(['name' => 'Juan', 'lastname' => 'Pérez']);
        $maria = $this->createMinimalEstudiant(['name' => 'María', 'lastname' => 'García']);

        Inscripcion::factory()->create([
            'estudiant_id' => $juan->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Inscripcion::factory()->create([
            'estudiant_id' => $maria->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->set('search', 'Juan')
            ->assertSee('Juan')
            ->assertDontSee('María');
    }

    /** @test */
    public function it_filters_by_pestudio(): void
    {
        $pestudioA = Pestudio::factory()->create(['name' => 'Primaria', 'status_active' => 'true']);
        $pestudioB = Pestudio::factory()->create(['name' => 'Secundaria', 'status_active' => 'true']);
        $gradoA = Grado::factory()->create(['pestudio_id' => $pestudioA->id, 'status_active' => 'true']);
        $gradoB = Grado::factory()->create(['pestudio_id' => $pestudioB->id, 'status_active' => 'true']);
        $seccionA = Seccion::factory()->create(['grado_id' => $gradoA->id, 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $gradoB->id, 'status_active' => 'true']);
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();
        $grupo = GrupoEstable::factory()->create();

        $estA = $this->createMinimalEstudiant(['name' => 'AlumnoA']);
        $estB = $this->createMinimalEstudiant(['name' => 'AlumnoB']);

        Inscripcion::factory()->create([
            'estudiant_id' => $estA->id,
            'seccion_id' => $seccionA->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);
        Inscripcion::factory()->create([
            'estudiant_id' => $estB->id,
            'seccion_id' => $seccionB->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
            'grupo_estable_id' => $grupo->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->set('filterPestudio', $pestudioA->id)
            ->assertSee('AlumnoA')
            ->assertDontSee('AlumnoB');
    }

    // ─── CSV import Tests ───────────────────────────────────────

    /** @test */
    public function it_opens_the_csv_import_dialog(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->assertSee('Plan de Pago (estudiantes nuevos)')
            ->assertSee('CI del Representante (opcional)')
            ->call('openImportModal')
            ->assertDispatched('wireui:dialog:csv-import');
    }

    /** @test */
    public function it_imports_inscriptions_from_csv_creating_missing_students(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => 'GRADO CSV',
            'status_active' => 'true',
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'name' => 'X',
            'status_active' => 'true',
        ]);
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();

        $existing = $this->createMinimalEstudiant([
            'ci_estudiant' => '55500001',
            'name' => 'EXISTENTE',
            'lastname' => 'CSV',
        ]);

        $csv = "ci_estudiant,lastname,name,grado,seccion\n"
            ."55500001,CSV,EXISTENTE,GRADO CSV,X\n"
            ."55500002,CSV,NUEVO,GRADO CSV,X\n";

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('openImportModal')
            ->set('importCsvFile', UploadedFile::fake()->createWithContent('inscripciones.csv', $csv))
            ->set('importTipoId', $tipo->id)
            ->set('importEscolaridadId', $escolaridad->id)
            ->set('importProgramacionId', $programacion->id)
            ->call('importInscriptions')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inscripcions', [
            'estudiant_id' => $existing->id,
            'seccion_id' => $seccion->id,
        ]);

        $newStudent = Estudiant::where('ci_estudiant', '55500002')->first();
        $this->assertNotNull($newStudent);
        $this->assertDatabaseHas('inscripcions', [
            'estudiant_id' => $newStudent->id,
            'seccion_id' => $seccion->id,
        ]);
    }

    /** @test */
    public function it_updates_the_section_when_student_is_already_inscribed(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => 'GRADO CSV',
            'status_active' => 'true',
        ]);
        $seccionA = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'name' => 'X',
            'status_active' => 'true',
        ]);
        $seccionB = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'name' => 'Y',
            'status_active' => 'true',
        ]);
        $tipo = Tinscripcion::factory()->create();
        $escolaridad = Escolaridad::factory()->create();
        $programacion = Programacion::factory()->create();

        $estudiant = $this->createMinimalEstudiant([
            'ci_estudiant' => '55500003',
            'name' => 'INSCRITO',
            'lastname' => 'CSV',
        ]);

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionA->id,
            'tipo_id' => $tipo->id,
            'escolaridad_id' => $escolaridad->id,
            'programacion_id' => $programacion->id,
        ]);

        $csv = "ci_estudiant,lastname,name,grado,seccion\n"
            ."55500003,CSV,INSCRITO,GRADO CSV,Y\n";

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('openImportModal')
            ->set('importCsvFile', UploadedFile::fake()->createWithContent('inscripciones.csv', $csv))
            ->set('importTipoId', $tipo->id)
            ->set('importEscolaridadId', $escolaridad->id)
            ->set('importProgramacionId', $programacion->id)
            ->call('importInscriptions')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inscripcions', [
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionB->id,
        ]);
        $this->assertDatabaseMissing('inscripcions', [
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionA->id,
        ]);
    }

    /** @test */
    public function it_updates_academic_data_through_the_dialog_option(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => 'GRADO CSV',
            'status_active' => 'true',
        ]);
        $seccionA = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'X', 'status_active' => 'true']);
        $seccionB = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'Y', 'status_active' => 'true']);

        $tipoOld = Tinscripcion::factory()->create();
        $tipoNew = Tinscripcion::factory()->create();
        $escolaridadOld = Escolaridad::factory()->create();
        $escolaridadNew = Escolaridad::factory()->create();
        $programacionOld = Programacion::factory()->create();
        $programacionNew = Programacion::factory()->create();

        $estudiant = $this->createMinimalEstudiant(['ci_estudiant' => '55500004']);
        $inscripcion = Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccionA->id,
            'tipo_id' => $tipoOld->id,
            'escolaridad_id' => $escolaridadOld->id,
            'programacion_id' => $programacionOld->id,
        ]);

        $csv = "ci_estudiant,lastname,name,grado,seccion\n"
            ."55500004,CSV,NOMBRE,GRADO CSV,Y\n";

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->call('openImportModal')
            ->set('importCsvFile', UploadedFile::fake()->createWithContent('inscripciones.csv', $csv))
            ->set('importUpdateAcademicData', true)
            ->set('importTipoId', $tipoNew->id)
            ->set('importEscolaridadId', $escolaridadNew->id)
            ->set('importProgramacionId', $programacionNew->id)
            ->call('importInscriptions')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inscripcions', [
            'id' => $inscripcion->id,
            'seccion_id' => $seccionB->id,
            'tipo_id' => $tipoNew->id,
            'escolaridad_id' => $escolaridadNew->id,
            'programacion_id' => $programacionNew->id,
        ]);
    }

    // ─── PDF export Tests ───────────────────────────────────────

    /** @test */
    public function it_exports_the_section_listing_to_pdf(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => 'GRADO PDF',
            'status_active' => 'true',
        ]);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'X', 'status_active' => 'true']);
        $estudiant = $this->createMinimalEstudiant(['ci_estudiant' => '55500099']);

        Inscripcion::factory()->create([
            'estudiant_id' => $estudiant->id,
            'seccion_id' => $seccion->id,
            'tipo_id' => Tinscripcion::factory()->create()->id,
            'escolaridad_id' => Escolaridad::factory()->create()->id,
            'programacion_id' => Programacion::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('app.planning.inscripcions.export-pdf', [
            'grado_id' => $grado->id,
            'seccion_id' => $seccion->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    /** @test */
    public function it_rejects_pdf_export_when_section_does_not_belong_to_grado(): void
    {
        $pestudio = Pestudio::factory()->create(['status_active' => 'true']);
        $grado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $otroGrado = Grado::factory()->create(['pestudio_id' => $pestudio->id, 'status_active' => 'true']);
        $seccion = Seccion::factory()->create(['grado_id' => $grado->id, 'name' => 'X', 'status_active' => 'true']);

        $response = $this->actingAs($this->user)->get(route('app.planning.inscripcions.export-pdf', [
            'grado_id' => $otroGrado->id,
            'seccion_id' => $seccion->id,
        ]));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_shows_the_export_pdf_button(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Planning\Inscripcion\IndexComponent::class)
            ->assertSee('Exportar PDF');
    }
}
