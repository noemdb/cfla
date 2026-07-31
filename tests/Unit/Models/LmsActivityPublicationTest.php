<?php

namespace Tests\Unit\Models;

use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LmsActivityPublicationTest extends TestCase
{
    use DatabaseTransactions;

    private function makePublication(array $overrides = []): LmsActivityPublication
    {
        // Creamos la publicación directamente sin usar la cadena de factories
        // porque las factories de los modelos anidados están dispersas y faltan
        // los `$model` en varias de ellas para namespaces profundos.
        $attrs = array_merge([
            'activity_id'    => 1,    // FK, no se valida en make() pero sí en create()
            'published_by'   => 1,
            'status'         => 'DRAFT',
            'allow_comments' => true,
            'allow_downloads' => true,
        ], $overrides);

        return new LmsActivityPublication($attrs);
    }

    // ─── studentVisibility() / helpers ────────────────────────────

    public function test_is_hidden_when_published_without_publish_at(): void
    {
        // El ciclo de vida garantiza publish_at (los botones "Publicar ahora"
        // ahora pasan por un modal con fecha). Si igualmente llega un registro
        // PUBLISHED con publish_at nulo, NO se muestra al estudiante.
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => null,
            'unpublish_at' => null,
        ]);

        $this->assertSame('hidden', $publication->studentVisibility());
        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_full_visible_when_publish_at_is_in_the_past(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->subHour(),
            'unpublish_at' => null,
        ]);

        $this->assertSame('full', $publication->studentVisibility());
        $this->assertTrue($publication->isVisibleToStudents());
        $this->assertFalse($publication->isPreviewToStudents());
        $this->assertTrue($publication->isFullVisibleToStudents());
    }

    public function test_is_preview_when_publish_at_is_in_the_future(): void
    {
        // now() < publish_at → solo la 1ª sección visible.
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->addDay(),
            'unpublish_at' => null,
        ]);

        $this->assertSame('preview', $publication->studentVisibility());
        $this->assertTrue($publication->isVisibleToStudents());
        $this->assertTrue($publication->isPreviewToStudents());
        $this->assertFalse($publication->isFullVisibleToStudents());
    }

    public function test_is_preview_when_scheduled_with_future_publish_at(): void
    {
        // SCHEDULED con fecha futura es visible como vista previa hasta la fecha.
        $publication = $this->makePublication([
            'status' => 'SCHEDULED',
            'publish_at' => now()->addHour(),
            'unpublish_at' => null,
        ]);

        $this->assertSame('preview', $publication->studentVisibility());
        $this->assertTrue($publication->isVisibleToStudents());
    }

    public function test_is_hidden_when_scheduled_without_publish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'SCHEDULED',
            'publish_at' => null,
        ]);

        $this->assertSame('hidden', $publication->studentVisibility());
        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_draft(): void
    {
        $publication = $this->makePublication(['status' => 'DRAFT']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_archived(): void
    {
        $publication = $this->makePublication(['status' => 'ARCHIVED']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_after_unpublish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->subDay(),
            'unpublish_at' => now()->subHour(),
        ]);

        $this->assertSame('hidden', $publication->studentVisibility());
        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_visible_when_unpublish_at_is_in_the_future(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->subHour(),
            'unpublish_at' => now()->addDay(),
        ]);

        $this->assertSame('full', $publication->studentVisibility());
        $this->assertTrue($publication->isVisibleToStudents());
    }

    // ─── scopeVisibleNow() ────────────────────────────────────────

    public function test_scope_visible_now_filters_correctly(): void
    {
        // Insertar registros mínimos necesarios con todas las FKs de la cadena
        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test',
            'legalname' => 'Test',
            'rif_institution' => 'J-00000000-0',
            'email_institution' => 'test@test.com',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId,
            'name' => 'Test Año Escolar',
            'description' => 'Test',
            'finicial' => now(), 'ffinal' => now()->addYear(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $peducativoId = DB::table('peducativos')->insertGetId([
            'pescolar_id' => $pescolarId,
            'name' => 'Test PE',
            'description' => 'Test',
            'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA', 'name' => 'Test',
            'minimo' => '1', 'maximo' => '20', 'aprobacion' => '10',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $pestudioId = DB::table('pestudios')->insertGetId([
            'peducativo_id' => $peducativoId,
            'code' => 'PEST-T', 'name' => 'Test',
            'scale' => $escalaId, 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId,
            'name' => 'Test', 'code' => 'GR-T',
            'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A',
            'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId,
            'code' => 'ASIG-T', 'name' => 'Test',
            'tescala' => $escalaId,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-T', 'code_sm' => 'LT',
            'name' => 'Test', 'status_last' => 'true',
            'finicial' => now(), 'ffinal' => now()->addMonths(3),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId,
            'grado_id' => $gradoId,
            'asignatura_id' => $asignaturaId,
            'status_component' => true, 'status_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-1', 'ci_profesor' => '1',
            'name' => 'Prof', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId,
            'profesor_id' => $profesorId,
            'lapso_id' => $lapsoId,
            'seccion_id' => $seccionId,
            'objetivo' => 'Test',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'username' => 'testpub',
            'email' => 'testpub@test.com',
            'password' => bcrypt('password'),
            'is_active' => 'enable',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Helper para crear una activity rápidamente
        $makeActivity = function () use ($pevaluacionId) {
            return DB::table('activities')->insertGetId([
                'pevaluacion_id' => $pevaluacionId,
                'finicial' => now(), 'ffinal' => now()->addDays(7),
                'topic' => 'Test', 'references' => 'Ref',
                'teaching' => 'T', 'learning' => 'L',
                'observations' => 'O',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        };

        // Cada publicación usa su propia activity (unique constraint en activity_id)
        $now = now();

        // Baseline: el test puede correr contra una BD que ya tenga datos reales
        // (phpunit.xml tiene sqlite :memory: comentado → corre contra MySQL de desarrollo).
        $baseline = LmsActivityPublication::visibleNow()->count();

        DB::table('lms_activity_publications')->insert([
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'PUBLISHED', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'DRAFT', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'PUBLISHED', 'publish_at' => now()->addDay(), 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'ARCHIVED', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $visible = LmsActivityPublication::visibleNow()->get();

        // Solo la fila 3 (PUBLISHED con publish_at futuro) debe sumarse al baseline:
        // la fila 1 (PUBLISHED con publish_at nulo) NO es visible (whereNotNull publish_at).
        $this->assertCount($baseline + 1, $visible);
    }
}
