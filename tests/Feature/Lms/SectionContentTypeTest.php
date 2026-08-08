<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivitySection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Spec "Campo content_type en lms_activity_sections" — F2/F5.
 *
 * Verifica:
 *   - El observer recalcula el tipo de la sección al crear/editar/ocultar/
 *     eliminar contenidos.
 *   - El comando lms:sync-section-types es idempotente y persiste.
 *   - El acceso al tipo funciona en el modelo (columna + accesor).
 */
class SectionContentTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_observer_sets_type_when_content_is_created(): void
    {
        $section = $this->makeSection();

        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'body' => '<p>Prosa simple de la sección.</p>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $this->assertSame('text', $section->fresh()->content_type);
    }

    public function test_observer_updates_type_when_svg_content_added(): void
    {
        $section = $this->makeSection();

        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'body' => '<p>Prosa simple.</p>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        $this->assertSame('text', $section->fresh()->content_type);

        // Añadir una ilustración SVG → sección mixta.
        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'IMAGE',
            'body' => '<svg viewBox="0 0 100 100"><rect width="100" height="100" fill="#f8f9fa"/></svg>',
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        $this->assertSame('mixed', $section->fresh()->content_type);
    }

    public function test_observer_recomputes_when_content_hidden(): void
    {
        $section = $this->makeSection();

        $svg = LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'IMAGE',
            'body' => '<svg viewBox="0 0 100 100"><rect width="100" height="100" fill="#f8f9fa"/></svg>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        $this->assertSame('svg', $section->fresh()->content_type);

        // Ocultar el contenido → sección sin contenidos visibles.
        $svg->update(['is_visible' => false]);

        $this->assertSame('none', $section->fresh()->content_type);
    }

    public function test_observer_sets_none_when_all_contents_deleted(): void
    {
        $section = $this->makeSection();

        $content = LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'body' => '<p>Único contenido.</p>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);
        $this->assertSame('text', $section->fresh()->content_type);

        $content->delete();

        $this->assertSame('none', $section->fresh()->content_type);
    }

    public function test_accessor_falls_back_to_live_computation_when_column_null(): void
    {
        $section = $this->makeSection();

        // Sin backfill: la columna queda null pero el accesor calcula en vivo.
        $this->assertNull($section->fresh()->getAttributes()['content_type'] ?? null);
        $this->assertSame('none', $section->fresh()->content_type);
    }

    public function test_sync_command_is_idempotent_and_persists(): void
    {
        $section = $this->makeSection();

        LmsActivityContent::create([
            'section_id' => $section->id,
            'type' => 'TEXT',
            'body' => '<p>Prosa.</p>',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        // Primera corrida: persiste el tipo.
        Artisan::call('lms:sync-section-types', ['--activity' => $section->activity_id]);
        $this->assertSame('text', $section->fresh()->content_type);

        // Segunda corrida en dry-run: sin cambios → idempotente.
        Artisan::call('lms:sync-section-types', ['--activity' => $section->activity_id, '--dry-run' => true]);
        $output = Artisan::output();
        $this->assertStringContainsString('Cambios: 0', $output);
    }

    public function test_section_type_label_is_human_readable(): void
    {
        $section = $this->makeSection(['content_type' => 'mermaid']);
        $this->assertSame('Mermaid', $section->fresh()->content_type_label);
    }

    // ─── Fixtures ─────────────────────────────────────────────────────────

    private function makeSection(array $overrides = []): LmsActivitySection
    {
        $activity = $this->createMinimalActivity();

        return LmsActivitySection::create(array_merge([
            'activity_id' => $activity->id,
            'title' => 'Sección de prueba',
            'sort_order' => 1,
            'is_visible' => true,
        ], $overrides));
    }

    private function createMinimalActivity(array $overrides = []): Activity
    {
        // Cadena de FKs construida manualmente (mismo patrón que
        // StudentAccessTest / StudentLessonsPrintTest): pevaluacion → pensum
        // → pestudio → peducativo → pescolar, grado, escala, seccion, lapso.
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST', 'code_sm' => 'LT', 'name' => 'Test Lapso',
            'finicial' => now(), 'ffinal' => now()->addMonths(3),
            'status_last' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA', 'name' => 'Test Scale', 'minimo' => '1',
            'maximo' => '20', 'aprobacion' => '10', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution', 'legalname' => 'Test Institution Legal',
            'rif_institution' => 'J-12345678-9', 'email_institution' => 'test@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId, 'name' => 'Test Año Escolar',
            'description' => 'Test', 'finicial' => now(), 'ffinal' => now()->addYear(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId, 'name' => 'Test PE', 'description' => 'Test',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId, 'code' => 'PEST-TEST', 'name' => 'Test Plan de Estudio',
            'scale' => $escalaId, 'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => 'Test Grado', 'code' => 'GR-TEST',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId, 'code' => 'ASIG-TEST', 'name' => 'Test Asignatura',
            'tescala' => $escalaId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId, 'grado_id' => $gradoId, 'asignatura_id' => $asignaturaId,
            'status_component' => true, 'status_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678', 'ci_profesor' => '12345678', 'name' => 'Profesor Test',
            'status_active' => 'true', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId, 'profesor_id' => $profesorId, 'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId, 'objetivo' => 'Test objetivo',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return Activity::create(array_merge([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Test Activity',
            'references' => 'Refs',
            'teaching' => 'Teaching',
            'learning' => 'Learning',
            'observations' => 'Obs',
        ], $overrides));
    }
}
