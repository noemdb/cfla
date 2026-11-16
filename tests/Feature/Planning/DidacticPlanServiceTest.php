<?php

namespace Tests\Feature\Planning;

use App\Livewire\Planning\Activities\IndexComponent as ActivitiesIndex;
use App\Livewire\Planning\DidacticPlans\Wizard;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\DidacticPlan;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use App\Services\Planning\DidacticPlanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ciclo de vida del plan de clases: borrador, aprobación, versionado y
 * apertura del wizard con el universo filtrado.
 */
class DidacticPlanServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<string, mixed>
     */
    private function fixture(): array
    {
        $planner = User::factory()->create(['is_planner' => true]);
        $lapso = Lapso::factory()->create([
            'finicial' => '2035-01-01',
            'ffinal' => '2035-01-31',
        ]);

        $pestudio = Pestudio::factory()->create([
            'peducativo_id' => Peducativo::factory(),
            'status_active' => 'true',
            'planning_module' => true,
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
            'name' => 'Cultura alimentaria',
        ]);
        $pensum = Pensum::factory()->create([
            'pestudio_id' => $pestudio->id,
            'grado_id' => $grado->id,
            'asignatura_id' => $asignatura->id,
            'status_active' => true,
        ]);
        $profesor = Profesor::factory()->create(['name' => 'Docente de prueba']);
        $pevaluacion = Pevaluacion::create([
            'profesor_id' => $profesor->id,
            'pensum_id' => $pensum->id,
            'seccion_id' => $seccion->id,
            'lapso_id' => $lapso->id,
        ]);

        $activity = Activity::create([
            'pevaluacion_id' => $pevaluacion->id,
            'finicial' => '2035-01-08',
            'ffinal' => '2035-01-09',
            'topic' => 'Los sabores de mi familia',
            'thematic' => 'Alimentación y cultura',
            'references' => 'Recetario familiar',
            'teaching' => 'INICIO: Pregunta detonadora. DESARROLLO: Entrevista familiar. CIERRE: Socialización.',
            'learning' => 'Reconoce alimentos tradicionales de su entorno.',
            'description' => 'Lista de cotejo del relato oral.',
            'observations' => 'ODS 4: educación de calidad.',
            'status' => false,
        ]);
        $activity->achievements()->create([
            'name' => 'Reconoce alimentos tradicionales',
            'weighting' => 40,
            'status_quantitative_weighting' => true,
        ]);

        return compact(
            'planner', 'lapso', 'pestudio', 'grado', 'seccion',
            'asignatura', 'pensum', 'profesor', 'pevaluacion', 'activity'
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        $payload = [
            'pestudio_id' => $data['pestudio']->id,
            'grado_id' => $data['grado']->id,
            'seccion_id' => $data['seccion']->id,
            'lapso_id' => $data['lapso']->id,
            'period_start' => '2035-01-08',
            'period_end' => '2035-01-14',
            'axis' => 'Alimentación, cultura e identidad',
            'project_title' => 'Sabores que nos identifican',
            'objective' => 'Reconocer la alimentación como parte de la cultura.',
            'ods' => ['4'],
            'filter_snapshot' => ['seccion_id' => $data['seccion']->id],
            'filter_hash' => hash('sha256', 'filtros'),
            'universe_hash' => hash('sha256', (string) $data['activity']->id),
            'filtered_count' => 1,
            'selected_count' => 1,
            'sessions' => [[
                'date' => '2035-01-08',
                'day_index' => 1,
                'sort_order' => 0,
                'title' => 'Los sabores de mi familia',
                'thematic' => 'Alimentación y cultura',
                'objective' => 'Reconoce alimentos tradicionales.',
                'start' => 'Pregunta detonadora.',
                'development' => 'Entrevista familiar.',
                'close' => 'Socialización.',
                'resources' => 'Recetario familiar',
                'evaluation_instrument' => 'Lista de cotejo',
                'evaluation_description' => 'Lista de cotejo del relato oral.',
                'ods' => ['4'],
                'indicators' => [[
                    'name' => 'Reconoce alimentos tradicionales',
                    'weighting' => 40,
                    'is_quantitative' => true,
                    'origin' => 'activity',
                ]],
            ]],
            'sources' => [[
                'activity_id' => $data['activity']->id,
                'pevaluacion_id' => $data['pevaluacion']->id,
                'activity_checksum' => hash('sha256', (string) $data['activity']->id),
                'fields_used' => ['topic', 'teaching'],
                'excerpt' => 'Los sabores de mi familia',
                'included' => true,
            ]],
        ];

        return $payload;
    }

    public function test_save_draft_persists_sessions_indicators_sources_and_checksum(): void
    {
        $data = $this->fixture();
        $service = app(DidacticPlanService::class);

        $plan = $service->saveDraft($this->payload($data), $data['planner']);

        $this->assertSame(DidacticPlan::STATUS_DRAFT, $plan->status);
        $this->assertSame(0, $plan->lock_version);
        $this->assertNotNull($plan->checksum);
        $this->assertCount(1, $plan->sessions);
        $this->assertCount(1, $plan->sessions->first()->indicators);
        $this->assertCount(1, $plan->sources);
        $this->assertSame('2035-01-08', $plan->period_start->toDateString());
    }

    public function test_approving_a_draft_marks_it_and_a_new_version_supersedes_it(): void
    {
        $data = $this->fixture();
        $service = app(DidacticPlanService::class);

        $first = $service->saveDraft($this->payload($data), $data['planner']);
        $first = $service->approve($first, $data['planner']);

        $this->assertTrue($first->isApproved());
        $this->assertSame($data['planner']->id, $first->approved_by);
        $this->assertNotNull($first->approved_at);
        $this->assertSame(
            DidacticPlan::approvedKeyFor(
                $data['seccion']->id,
                $data['lapso']->id,
                '2035-01-08',
                '2035-01-14'
            ),
            $first->approved_key
        );

        $second = $service->newVersion($first, $data['planner']);
        $this->assertSame(DidacticPlan::STATUS_DRAFT, $second->status);
        $this->assertSame(2, $second->version);

        $second = $service->approve($second, $data['planner']);

        $first->refresh();

        $this->assertSame(DidacticPlan::STATUS_SUPERSEDED, $first->status);
        $this->assertNull($first->approved_key);
        $this->assertSame($second->id, $first->superseded_by_id);
        $this->assertTrue($second->isApproved());
    }

    public function test_approval_requires_content_or_justification(): void
    {
        $data = $this->fixture();
        $service = app(DidacticPlanService::class);

        $incomplete = $this->payload($data);
        $incomplete['notes'] = null;
        $incomplete['ods'] = [];
        $incomplete['sessions'][0]['start'] = '';
        $incomplete['sessions'][0]['development'] = '';
        $incomplete['sessions'][0]['close'] = '';
        $incomplete['sessions'][0]['evaluation_description'] = '';
        $incomplete['sessions'][0]['indicators'] = [];
        $incomplete['sessions'][0]['ods'] = [];

        $plan = $service->saveDraft($incomplete, $data['planner']);

        try {
            $service->approve($plan, $data['planner']);
            $this->fail('La aprobación debió fallar sin contenido ni justificación.');
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }

        $this->assertSame(DidacticPlan::STATUS_DRAFT, $plan->fresh()->status);

        $justified = $incomplete;
        $justified['id'] = $plan->id;
        $justified['lock_version'] = $plan->lock_version;
        $justified['notes'] = 'Se justifica la ausencia de momentos por ajuste de calendario.';

        $draft = $service->saveDraft($justified, $data['planner']);
        $approved = $service->approve($draft, $data['planner']);

        $this->assertTrue($approved->isApproved());
    }

    public function test_wizard_opens_with_the_filtered_universe_and_builds_sessions(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $filters = [
            'pestudio_id' => $data['pestudio']->id,
            'seccion_id' => $data['seccion']->id,
            'lapso_id' => $data['lapso']->id,
        ];

        $component = Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', $filters)
            ->assertSet('open', true)
            ->assertSet('seccionIds', [$data['seccion']->id])
            ->assertCount('activities', 1)
            ->assertCount('weekOptions', 1)
            ->assertSet('periodStart', '2035-01-08')
            ->assertSet('periodEnd', '2035-01-09')
            ->call('goToActivities')
            ->assertSet('step', 2)
            ->call('toggleActivity', $data['activity']->id)
            ->assertSet('selectedIds', [$data['activity']->id])
            ->call('buildSessions')
            ->assertSet('step', 3)
            ->assertCount('sessions', 1)
            ->call('goToReview')
            ->assertSet('step', 4)
            ->assertSee('Checklist')
            ->call('saveDraft')
            ->assertSet('step', 5)
            ->assertSet('planResults.0.status', DidacticPlan::STATUS_DRAFT)
            ->assertSee('Borradores guardados')
            ->call('approve')
            ->assertSet('planResults.0.status', DidacticPlan::STATUS_APPROVED)
            ->assertSet('approved', true)
            ->assertSee('Descargar PDF');

        $this->assertSame('Los sabores de mi familia', $component->get('sessions')[0]['title']);
        $this->assertSame([
            '4',
        ], $component->get('sessions')[0]['ods']);
    }

    public function test_step_two_only_shows_activities_inside_the_selected_period(): void
    {
        $data = $this->fixture();
        $outsideActivity = Activity::create([
            'pevaluacion_id' => $data['pevaluacion']->id,
            'finicial' => '2035-01-20',
            'ffinal' => '2035-01-20',
            'topic' => 'Actividad fuera del periodo seleccionado',
            'teaching' => 'INICIO: Inicio. DESARROLLO: Desarrollo. CIERRE: Cierre.',
            'description' => 'Lista de cotejo.',
            'status' => false,
        ]);

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->assertCount('activities', 2)
            ->call('goToActivities')
            ->assertSee('Los sabores de mi familia')
            ->assertDontSee('Actividad fuera del periodo seleccionado')
            ->call('toggleActivity', $outsideActivity->id)
            ->assertSet('selectedIds', []);
    }

    public function test_wizard_allows_a_custom_period_within_the_filtered_range(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $component = Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->assertSee('Periodo del plan')
            ->set('periodEnd', '2035-01-08')
            ->assertSet('selectedWeek', null)
            ->assertSet('periodStart', '2035-01-08')
            ->assertSet('periodEnd', '2035-01-08')
            ->call('goToActivities')
            ->assertSet('step', 2)
            ->call('toggleActivity', $data['activity']->id)
            ->call('buildSessions')
            ->call('goToReview')
            ->call('saveDraft')
            ->assertSet('step', 5)
            ->assertSet('planResults.0.status', DidacticPlan::STATUS_DRAFT);

        $plan = DidacticPlan::query()
            ->where('seccion_id', $data['seccion']->id)
            ->sole();
        $this->assertSame('2035-01-08', $plan->period_start->toDateString());
        $this->assertSame('2035-01-08', $plan->period_end->toDateString());
    }

    public function test_wizard_accepts_null_when_selecting_a_custom_period(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->set('selectedWeek', null)
            ->assertSet('selectedWeek', null)
            ->assertSet('periodStart', '2035-01-08')
            ->assertSet('periodEnd', '2035-01-09');
    }

    public function test_wizard_allows_editing_and_resetting_the_available_range(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->assertSet('rangeStart', '2035-01-08')
            ->assertSet('rangeEnd', '2035-01-09')
            ->assertSet('periodStart', '2035-01-08')
            ->set('rangeStart', '2035-01-01')
            ->set('rangeEnd', '2035-01-28')
            ->assertSet('rangeStart', '2035-01-01')
            ->assertSet('rangeEnd', '2035-01-28')
            ->assertCount('weekOptions', 4)
            ->assertSet('periodStart', '2035-01-08')
            ->call('resetDerivedPeriod')
            ->assertSet('rangeStart', '2035-01-08')
            ->assertSet('rangeEnd', '2035-01-09')
            ->assertSet('periodStart', '2035-01-08')
            ->assertSet('periodEnd', '2035-01-09');
    }

    public function test_failed_approval_keeps_the_draft_and_can_be_justified(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $data['activity']->achievements()->delete();
        $data['activity']->update(['observations' => '']);

        $component = Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->call('goToActivities')
            ->call('toggleActivity', $data['activity']->id)
            ->call('buildSessions')
            ->call('goToReview')
            ->call('approve')
            ->assertSet('step', 5)
            ->assertSet('planResults.0.status', DidacticPlan::STATUS_DRAFT)
            ->assertSet('approved', false);

        $this->assertNotNull($component->get('planResults')[0]['plan_id']);
        $this->assertStringContainsString('indicador', strtolower($component->get('planResults')[0]['error']));
        $this->assertStringNotContainsString('Sesión 1:', $component->get('planResults')[0]['error']);

        $component->call('justifyAndRetry')
            ->assertSet('planResults.0.status', DidacticPlan::STATUS_APPROVED)
            ->assertSet('approved', true);

        $plan = DidacticPlan::query()->where('seccion_id', $data['seccion']->id)->sole();

        $this->assertTrue($plan->isApproved());
        $this->assertNotSame('', trim((string) $plan->notes));
    }

    public function test_wizard_rejects_a_custom_period_outside_the_range(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->set('periodStart', '2035-01-01')
            ->set('periodEnd', '2035-01-02')
            ->call('goToActivities')
            ->assertSet('step', 1)
            ->assertSet('stepError', 'El periodo seleccionado debe estar dentro del rango definido.');
    }

    public function test_wizard_accepts_a_period_longer_than_seven_days(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'seccion_id' => $data['seccion']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->set('rangeStart', '2035-01-01')
            ->set('rangeEnd', '2035-01-28')
            ->set('periodStart', '2035-01-08')
            ->set('periodEnd', '2035-01-16')
            ->assertSet('stepError', '')
            ->call('goToActivities')
            ->assertSet('step', 2)
            ->assertSet('stepError', '');
    }

    public function test_approved_plan_pdf_is_downloadable(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $service = app(DidacticPlanService::class);
        $plan = $service->saveDraft($this->payload($data), $data['planner']);
        $plan = $service->approve($plan, $data['planner']);

        $response = $this->actingAs($data['planner'])
            ->get(route('app.planning.activities.didactic-plans.pdf', $plan->id));

        $response->assertOk();
        $this->assertStringContainsString(
            'pdf',
            strtolower($response->headers->get('Content-Type') ?? '')
        );
    }

    public function test_wizard_generates_one_plan_per_selected_section(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $secondSection = Seccion::factory()->create([
            'grado_id' => $data['grado']->id,
            'status_active' => 'true',
        ]);
        $secondPevaluacion = Pevaluacion::create([
            'profesor_id' => $data['profesor']->id,
            'pensum_id' => $data['pensum']->id,
            'seccion_id' => $secondSection->id,
            'lapso_id' => $data['lapso']->id,
        ]);
        $secondActivity = Activity::create([
            'pevaluacion_id' => $secondPevaluacion->id,
            'finicial' => '2035-01-08',
            'ffinal' => '2035-01-09',
            'topic' => 'Saberes de la comunidad',
            'thematic' => 'Alimentación y cultura',
            'teaching' => 'INICIO: Saberes previos. DESARROLLO: Entrevista. CIERRE: Socialización.',
            'description' => 'Rúbrica del relato.',
            'status' => false,
        ]);

        $component = Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'lapso_id' => $data['lapso']->id,
            ])
            ->assertCount('sectionOptions', 2)
            ->assertCount('seccionIds', 2)
            ->call('goToActivities')
            ->call('toggleActivity', $data['activity']->id)
            ->call('focusSection', $secondSection->id)
            ->call('toggleActivity', $secondActivity->id)
            ->call('buildSessions')
            ->assertCount('sessions', 2)
            ->call('goToReview')
            ->assertSet('step', 4)
            ->set('notes', 'Justificación de prueba para el lote.')
            ->call('saveDraft')
            ->assertCount('planResults', 2);

        $this->assertEqualsCanonicalizing(
            [$data['seccion']->id, $secondSection->id],
            $component->get('seccionIds')
        );

        $this->assertSame(
            [$data['seccion']->id, $secondSection->id],
            DidacticPlan::query()
                ->whereIn('seccion_id', [$data['seccion']->id, $secondSection->id])
                ->orderBy('seccion_id')
                ->pluck('seccion_id')
                ->all()
        );

        $component->call('approveDrafts')
            ->assertSet('approved', true);

        $this->assertSame(
            2,
            DidacticPlan::query()
                ->whereIn('seccion_id', [$data['seccion']->id, $secondSection->id])
                ->where('status', DidacticPlan::STATUS_APPROVED)
                ->count()
        );
    }

    public function test_wizard_excludes_sections_that_do_not_match_the_pensum_level(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        // Pevaluación inconsistente: pensum de primaria con sección de media.
        $media = Pestudio::factory()->create([
            'peducativo_id' => Peducativo::factory(),
            'status_active' => 'true',
            'planning_module' => true,
        ]);
        $mediaGrado = Grado::factory()->create([
            'pestudio_id' => $media->id,
            'status_active' => 'true',
        ]);
        $mediaSeccion = Seccion::factory()->create([
            'grado_id' => $mediaGrado->id,
            'status_active' => 'true',
        ]);
        Pevaluacion::create([
            'profesor_id' => $data['profesor']->id,
            'pensum_id' => $data['pensum']->id,
            'seccion_id' => $mediaSeccion->id,
            'lapso_id' => $data['lapso']->id,
        ]);

        $component = Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', [
                'pestudio_id' => $data['pestudio']->id,
                'lapso_id' => $data['lapso']->id,
            ]);

        $sectionIds = collect($component->get('sectionOptions'))->pluck('id')->all();

        $this->assertSame([$data['seccion']->id], $sectionIds);
        $this->assertSame([$data['seccion']->id], $component->get('seccionIds'));
        $this->assertNotContains($mediaSeccion->id, $sectionIds);
    }

    public function test_plan_pdf_is_not_available_when_the_flag_is_off(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        $service = app(DidacticPlanService::class);
        $plan = $service->saveDraft($this->payload($data), $data['planner']);
        $plan = $service->approve($plan, $data['planner']);

        config()->set('planning.didactic_plans_enabled', false);

        $this->actingAs($data['planner'])
            ->get(route('app.planning.activities.didactic-plans.pdf', $plan->id))
            ->assertNotFound();
    }

    public function test_activities_page_shows_the_wizard_entry_point_only_when_enabled(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', true);

        Livewire::actingAs($data['planner'])
            ->test(ActivitiesIndex::class)
            ->set('pestudio_id', $data['pestudio']->id)
            ->set('lapso_id', $data['lapso']->id)
            ->assertSee('Plan de clases')
            ->call('openDidacticPlanWizard')
            ->assertDispatched('open-didactic-plan-wizard');

        config()->set('planning.didactic_plans_enabled', false);

        Livewire::actingAs($data['planner'])
            ->test(ActivitiesIndex::class)
            ->assertDontSee('Plan de clases')
            ->call('openDidacticPlanWizard')
            ->assertNotDispatched('open-didactic-plan-wizard');
    }

    public function test_wizard_is_hidden_when_the_flag_is_off(): void
    {
        $data = $this->fixture();

        config()->set('planning.didactic_plans_enabled', false);

        Livewire::actingAs($data['planner'])
            ->test(Wizard::class)
            ->call('open', ['seccion_id' => $data['seccion']->id])
            ->assertNotFound();
    }
}
