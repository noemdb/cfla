<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Activities\IndexComponent as ActivitiesIndex;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
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
 * Exportación Markdown de /app/planning/activities: snapshot completo para
 * análisis con LLM respetando los filtros activos.
 */
class ActivityMarkdownExportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_planner_can_download_a_complete_markdown_snapshot(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create(['name' => '1.er Momento']);
        $data = $this->makePlan($lapso, 'Matemática de prueba', 'Profesora de prueba');

        $data['pevaluacion']->update([
            'observations' => 'Falta profundizar en los referentes teóricos.',
            'objetivo' => 'Consolidar el pensamiento algebraico.',
        ]);

        $activity = Activity::create([
            'pevaluacion_id' => $data['pevaluacion']->id,
            'finicial' => '2026-09-20',
            'ffinal' => '2026-09-27',
            'topic' => 'Función lineal',
            'thematic' => 'Pendiente, dominio y rango.',
            'references' => 'Stewart, Cálculo.',
            'teaching' => 'INICIO: Preguntas previas. DESARROLLO: Modelado. CIERRE: Metacognición.',
            'learning' => 'Identifica pendiente y dominio.',
            'description' => 'Prueba escrita con ejercicios.',
            'observations' => 'ODS 4: educación de calidad.',
            'comments' => 'Ajustar el cierre.',
            'status' => false,
        ]);
        $activity->achievements()->create([
            'name' => 'Indicador de prueba',
            'weighting' => 40,
            'status_quantitative_weighting' => true,
        ]);

        $component = Livewire::actingAs($planner)
            ->test(ActivitiesIndex::class)
            ->assertSee('Exportar MD')
            ->set('pestudio_id', $data['pestudio']->id)
            ->set('lapso_id', $lapso->id)
            ->call('downloadMarkdownReport')
            ->assertFileDownloaded();

        $download = $component->effects['download'];
        $markdown = base64_decode($download['content']);

        self::assertSame('text/markdown; charset=UTF-8', $download['contentType']);
        self::assertStringStartsWith('actividades-planificacion-', $download['name']);
        self::assertStringEndsWith('.md', $download['name']);
        self::assertStringContainsString('document_type: "dataset_snapshot_for_llm"', $markdown);
        self::assertStringContainsString('## 1. Cómo usar este documento', $markdown);
        self::assertStringContainsString('Matemática de prueba', $markdown);
        self::assertStringContainsString('Profesora de prueba', $markdown);
        self::assertStringContainsString('1.er Momento', $markdown);
        self::assertStringContainsString('Función lineal', $markdown);
        self::assertStringContainsString('**Sección INICIO:**', $markdown);
        self::assertStringContainsString('Indicador de prueba', $markdown);
        self::assertStringContainsString('Falta profundizar en los referentes teóricos.', $markdown);
        self::assertStringContainsString('## 7. Actividades en revisión', $markdown);
        self::assertStringContainsString('## 12. Trazabilidad técnica', $markdown);
    }

    public function test_markdown_respects_the_active_pestudio_filter(): void
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create();

        $included = $this->makePlan($lapso, 'ASIG-INCLUIDA-'.uniqid(), 'PROF-UNO-'.uniqid());
        $excluded = $this->makePlan($lapso, 'ASIG-EXCLUIDA-'.uniqid(), 'PROF-DOS-'.uniqid());

        $component = Livewire::actingAs($planner)
            ->test(ActivitiesIndex::class)
            ->set('pestudio_id', $included['pestudio']->id)
            ->set('lapso_id', $lapso->id)
            ->call('downloadMarkdownReport')
            ->assertFileDownloaded();

        $markdown = base64_decode($component->effects['download']['content']);

        self::assertStringContainsString($included['asignatura']->name, $markdown);
        self::assertStringNotContainsString($excluded['asignatura']->name, $markdown);
        self::assertStringNotContainsString($excluded['profesor']->lastname, $markdown);
    }

    /**
     * @return array<string, mixed>
     */
    private function makePlan(Lapso $lapso, string $asignaturaName, string $profesorName): array
    {
        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => Peducativo::factory(),
            'status_active' => 'true',
            'planning_module' => true,
            'activities_avr' => 100,
        ]);
        $grado = Grado::factory()->create([
            'pestudio_id' => $pestudio->id,
            'status_active' => 'true',
        ]);
        $seccion = Seccion::factory()->create([
            'grado_id' => $grado->id,
            'status_active' => 'true',
        ]);
        $asignatura = Asignatura::factory()->create([
            'pestudio_id' => $pestudio->id,
            'name' => $asignaturaName,
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
        ]);
        $profesor = Profesor::factory()->create([
            'name' => $profesorName,
            'lastname' => 'Apellido-'.uniqid(),
        ]);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        return compact('pestudio', 'grado', 'seccion', 'asignatura', 'pensum', 'profesor', 'pevaluacion');
    }
}
