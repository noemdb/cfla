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

    public function test_is_visible_to_students_returns_true_when_published_without_dates(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => null,
            'unpublish_at' => null,
        ]);

        $this->assertTrue($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_draft(): void
    {
        $publication = $this->makePublication(['status' => 'DRAFT']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_scheduled(): void
    {
        $publication = $this->makePublication(['status' => 'SCHEDULED']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_before_publish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'publish_at' => now()->addDay(),
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_after_unpublish_at(): void
    {
        $publication = $this->makePublication([
            'status' => 'PUBLISHED',
            'unpublish_at' => now()->subDay(),
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_archived(): void
    {
        $publication = $this->makePublication(['status' => 'ARCHIVED']);

        $this->assertFalse($publication->isVisibleToStudents());
    }

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
        $activityId = DB::table('activities')->insertGetId([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(), 'ffinal' => now()->addDays(7),
            'topic' => 'Test', 'references' => 'Ref',
            'teaching' => 'T', 'learning' => 'L',
            'observations' => 'O',
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
        DB::table('lms_activity_publications')->insert([
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'PUBLISHED', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'DRAFT', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'PUBLISHED', 'publish_at' => now()->addDay(), 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
            ['activity_id' => $makeActivity(), 'published_by' => $userId, 'status' => 'ARCHIVED', 'publish_at' => null, 'unpublish_at' => null, 'allow_comments' => true, 'allow_downloads' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $visible = LmsActivityPublication::visibleNow()->get();

        $this->assertCount(1, $visible);
    }
}
