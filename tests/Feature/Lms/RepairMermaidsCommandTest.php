<?php

namespace Tests\Feature\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Cobertura del comando `lms:repair-mermaid` (revisión/reparación
 * determinista de diagramas Mermaid en la BD, apto para producción).
 *
 * @group lesson-wizard
 * @group lms-repair-mermaid
 */
class RepairMermaidsCommandTest extends TestCase
{
    use DatabaseTransactions;

    private function createProfesorUser(): array
    {
        $user = User::factory()->create(['is_admin' => false]);

        $profesorId = DB::table('profesors')->insertGetId([
            'ti_teacher' => 'V-12345678',
            'ci_profesor' => '12345678',
            'name' => 'Profesor Test',
            'lastname' => 'Test',
            'user_id' => $user->id,
            'status_active' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['user' => $user, 'profesor_id' => $profesorId];
    }

    private function createActivity(int $profesorId): Activity
    {
        $lapsoId = DB::table('lapsos')->insertGetId([
            'code' => 'LAP-TEST-'.uniqid(), 'code_sm' => 'LT', 'name' => 'Test Lapso',
            'finicial' => now(), 'ffinal' => now()->addMonths(3), 'status_last' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $escalaId = DB::table('escalas')->insertGetId([
            'tipo' => 'NUMÉRICA', 'name' => 'Test Scale', 'minimo' => '1', 'maximo' => '20', 'aprobacion' => '10',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $institucionId = DB::table('institucions')->insertGetId([
            'name' => 'Test Institution', 'legalname' => 'Test Institution Legal',
            'rif_institution' => 'J-'.uniqid(), 'email_institution' => 'test-'.uniqid().'@institution.test',
            'status_dont_allow_registration_if_insolvency' => 'false',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pescolarId = DB::table('pescolars')->insertGetId([
            'institucion_id' => $institucionId, 'name' => 'Test P.Escolar', 'description' => 'Test',
            'finicial' => now(), 'ffinal' => now()->addYear(),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $peducativoId = DB::table('peducativos')->insertGetId([
            'name' => 'Test P.Educativo', 'description' => 'Test', 'pescolar_id' => $pescolarId, 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pestudioId = DB::table('pestudios')->insertGetId([
            'code' => 'PE-'.uniqid(), 'name' => 'Test P.Estudio', 'peducativo_id' => $peducativoId,
            'scale' => $escalaId, 'planning_module' => 'true', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $gradoId = DB::table('grados')->insertGetId([
            'pestudio_id' => $pestudioId, 'name' => 'Test Grado', 'code' => 'GR-TEST', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $seccionId = DB::table('seccions')->insertGetId([
            'grado_id' => $gradoId, 'name' => 'A', 'description' => 'Sección A', 'status_active' => 'true',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $asignaturaId = DB::table('asignaturas')->insertGetId([
            'pestudio_id' => $pestudioId, 'code' => 'ASIG-TEST', 'name' => 'Test Asignatura', 'tescala' => $escalaId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pensumId = DB::table('pensums')->insertGetId([
            'pestudio_id' => $pestudioId, 'grado_id' => $gradoId, 'asignatura_id' => $asignaturaId,
            'status_component' => true, 'status_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pevaluacionId = DB::table('pevaluacions')->insertGetId([
            'pensum_id' => $pensumId, 'profesor_id' => $profesorId, 'lapso_id' => $lapsoId, 'seccion_id' => $seccionId,
            'objetivo' => 'Test objetivo', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Activity::create([
            'pevaluacion_id' => $pevaluacionId,
            'finicial' => now(),
            'ffinal' => now()->addDays(7),
            'topic' => 'Tema Generador Test',
            'thematic' => 'Tejido Temático Test',
            'description' => 'Actividad Evaluativa Test',
            'teaching' => 'Enseñanza Test',
            'learning' => 'Aprendizaje Esperado Test',
            'references' => 'Referencias Test',
            'observations' => 'Observaciones/ODS Test',
        ]);
    }

    private function brokenMermaid(): string
    {
        return "graph TD\n"
            ."A[\"AutoconocimientoVocacional\"] --> B[\"InteresesVocacionales\"]\n"
            ."B --> C[\"Curiosidad yMotivaciónInterna\"]\n"
            ."A --> D[\"HabilidadesPersonales\"]\n"
            ."D --> E[\"CapacidadesNaturalesyDesarrolladas\"]\n"
            ."A --> F[\"ValoresFundamentales\"]\n"
            ."F --> G[\"Fortalezas yÁreasdeMejora\"]\n"
            ."C --> H[\"DecisionesVocacionalesyAcertadas\"]\n"
            ."E --> H\n"
            ."G --> H";
    }

    private function brokenMermaidCard(): string
    {
        return '<div class="w-full bg-white rounded-xl border border-gray-200">'
            .'<div class="p-3 overflow-x-auto"><div class="mermaid">'
            ."graph TD\nA[\"AutoconocimientoVocacional\"] --> B[\"InteresesVocacionales\"]"
            .'</div></div></div>';
    }

    /** @test */
    public function dry_run_reporta_pero_no_persiste(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $embedId = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $data['user']->id,
            'title' => 'Diagrama roto',
            'html_content' => $this->brokenMermaid(),
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ])->id;

        $before = LmsHtmlEmbed::find($embedId)->html_content;

        $exit = Artisan::call('lms:repair-mermaid', ['--dry-run' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('REPARARÍA', $output);
        $this->assertStringContainsString('concatenadas', $output);
        // Dry-run: la BD no cambia
        $this->assertSame($before, LmsHtmlEmbed::find($embedId)->html_content);
    }

    /** @test */
    public function repara_embeds_y_contents_y_es_idempotente(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $embedId = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $data['user']->id,
            'title' => 'Diagrama roto',
            'html_content' => $this->brokenMermaid(),
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ])->id;

        $sectionId = LmsActivitySection::create([
            'activity_id' => $activity->id,
            'title' => 'Sección 1',
            'sort_order' => 1,
            'is_visible' => true,
        ])->id;

        $contentId = LmsActivityContent::create([
            'section_id' => $sectionId,
            'type' => 'HTML',
            'title' => 'Bloque',
            'body' => $this->brokenMermaidCard(),
            'sort_order' => 1,
            'is_visible' => true,
        ])->id;

        // 1ª ejecución: repara y persiste
        $exit = Artisan::call('lms:repair-mermaid');
        $output = Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('reparado', $output);

        $embed = LmsHtmlEmbed::find($embedId);
        $content = LmsActivityContent::find($contentId);

        $this->assertStringContainsString('Autoconocimiento Vocacional', $embed->html_content);
        $this->assertStringNotContainsString('AutoconocimientoVocacional', $embed->html_content);
        $this->assertStringContainsString('Intereses Vocacionales', $content->body);
        $this->assertStringNotContainsString('InteresesVocacionales', $content->body);
        $this->assertStringContainsString('class="mermaid"', $content->body);

        // 2ª ejecución: idempotente, nada que reparar
        $exit2 = Artisan::call('lms:repair-mermaid', ['--dry-run' => true]);
        $output2 = Artisan::output();

        $this->assertSame(0, $exit2);
        $this->assertStringNotContainsString('REPARARÍA', $output2);
    }

    /** @test */
    public function respeta_filtros_ids_y_only(): void
    {
        $data = $this->createProfesorUser();
        $activity = $this->createActivity($data['profesor_id']);

        $embedId = LmsHtmlEmbed::create([
            'activity_id' => $activity->id,
            'added_by' => $data['user']->id,
            'title' => 'Diagrama roto',
            'html_content' => $this->brokenMermaid(),
            'render_condition' => 'ALWAYS',
            'sort_order' => 1,
            'is_visible' => true,
        ])->id;

        // --only=contents: no toca los embeds
        Artisan::call('lms:repair-mermaid', ['--only' => 'contents', '--dry-run' => true]);
        $this->assertStringNotContainsString('embed', Artisan::output());
        $this->assertStringContainsString('AutoconocimientoVocacional', LmsHtmlEmbed::find($embedId)->html_content);

        // --ids=inexistente: no procesa nada
        Artisan::call('lms:repair-mermaid', ['--ids' => [999999], '--dry-run' => true]);
        $this->assertStringContainsString('No se encontraron registros', Artisan::output());
    }
}
