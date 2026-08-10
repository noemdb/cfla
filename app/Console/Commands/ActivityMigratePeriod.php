<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Migra activities (y relaciones) de un grado a otro dentro del mismo pestudio.
 *
 * Flujo:
 *   1. Crea pensums destino si faltan (clonando de fuente, misma asignatura).
 *   2. Crea/finds pevaluaciones destino (mismo pensum-mapeado, lapso, sección-mapeada por nombre).
 *   3. Copia activities + relaciones (achievements, LMS, publicación, logs).
 *
 * Uso:
 *   php8.2 artisan activity:migrate-period --from-grado=10 --to-grado=15 --pestudio=2 --dry-run
 *   php8.2 artisan activity:migrate-period --from-grado=10 --to-grado=15 --pestudio=2 --force
 *   php8.2 artisan activity:migrate-period --rollback
 */
class ActivityMigratePeriod extends Command
{
    protected $signature = 'activity:migrate-period
                          {--from-grado=10 : Grado fuente (cuarto año anterior)}
                          {--to-grado=15 : Grado destino (cuarto año actual)}
                          {--pestudio=2 : Plan de estudio}
                          {--planning-only : Copiar solo activities + achievements (sin LMS)}
                          {--dry-run : Mostrar cambios sin persistir}
                          {--rollback : Revertir la migración más reciente para este rango}
                          {--force : Ejecutar sin confirmación}';

    protected $description = 'Copia activities de un grado a otro (misma asignatura, misma estructura)';

    /** @var array<int, array{source:int, target:int}> Pensums creados (source_id → target_id) */
    private array $pensumMap = [];

    /** @var array<int, array{source:int, target:int}> Pevaluaciones creadas */
    private array $pevMap = [];

    /** @var int Contador de activities copiadas */
    private int $copiedActivities = 0;

    /** @var int Contador de achievements copiadas */
    private int $copiedAchievements = 0;

    /** @var int Contador de elementos LMS copiados */
    private int $copiedLmsItems = 0;

    public function handle(): int
    {
        if ($this->option('rollback')) {
            return $this->rollback();
        }

        $fromGrado = (int) $this->option('from-grado');
        $toGrado = (int) $this->option('to-grado');
        $pestudioId = (int) $this->option('pestudio');
        $planningOnly = (bool) $this->option('planning-only');
        $dryRun = (bool) $this->option('dry-run');

        // ── 1. Validar inputs ──
        $fromGradoModel = Grado::find($fromGrado);
        $toGradoModel = Grado::find($toGrado);
        $pestudio = Pestudio::find($pestudioId);

        if (! $fromGradoModel || ! $toGradoModel || ! $pestudio) {
            $this->error('Grado fuente, destino o pestudio no encontrado.');

            return self::FAILURE;
        }

        $this->info("=== Diagnóstico ===");
        $this->info("Pestudio: {$pestudio->name} (id={$pestudioId})");
        $this->info("Grado fuente: {$fromGradoModel->name} (id={$fromGrado}) — active=" . var_export($fromGradoModel->status_active, true));
        $this->info("Grado destino: {$toGradoModel->name} (id={$toGrado}) — active=" . var_export($toGradoModel->status_active, true));
        $this->newLine();

        // ── 2. Pensums fuente ──
        $sourcePensums = Pensum::where('pestudio_id', $pestudioId)
            ->where('grado_id', $fromGrado)
            ->with('asignatura')
            ->get();

        if ($sourcePensums->isEmpty()) {
            $this->error("No hay pensums en pestudio={$pestudioId} grado={$fromGrado}.");

            return self::FAILURE;
        }

        $this->info("Pensums fuente: {$sourcePensums->count()}");

        // ── 3. Pevaluaciones fuente ──
        $sourcePevs = Pevaluacion::with('pensum.asignatura', 'seccion', 'lapso', 'profesor')
            ->whereHas('seccion', fn ($q) => $q->where('grado_id', $fromGrado))
            ->whereHas('pensum', fn ($q) => $q->where('pestudio_id', $pestudioId))
            ->get();

        $pevsWithActivities = $sourcePevs->filter(fn ($p) => $p->activities()->count() > 0);
        $this->info("Pevaluaciones fuente: {$sourcePevs->count()} (con activities: {$pevsWithActivities->count()})");

        // ── 4. Secciones destino (mapeo por nombre) ──
        $targetSeccions = \App\Models\app\Academy\Seccion::where('grado_id', $toGrado)
            ->get()
            ->keyBy(fn ($s) => $s->name);

        $this->info("Secciones destino: " . $targetSeccions->implode('name', ', '));
        $this->newLine();

        // ── 5. Diagnóstico detallado ──
        $this->table(
            ['Asignatura fuente', 'Pensum src', 'Estado destino', 'Pevs fuente'],
            $sourcePensums->map(fn ($p) => [
                $p->asignatura?->name ?? '?',
                $p->id,
                isset($this->pensumMap[$p->id]) ? 'Ya mapeado' : ($targetSeccions->isNotEmpty() ? 'Se creará' : '⚠ Sin secciones match'),
                $sourcePevs->where('pensum_id', $p->id)->count(),
            ])->toArray()
        );

        // ── 6. Resumen antes de ejecutar ──
        $lmsLabel = $planningOnly ? 'Solo planificación (sin LMS)' : 'Integral (con LMS completo)';
        $this->info("Alcance: {$lmsLabel}");

        if ($dryRun) {
            $this->warn("⚡ MODO DRY-RUN — no se escribirá nada.");
        }

        if (! $dryRun && ! $this->option('force')) {
            $targetActivities = Activity::whereIn('pevaluacion_id', $sourcePevs->pluck('id'))->count();
            $this->newLine();
            $this->warn("Esto creará {$sourcePensums->count()} pensums, {$sourcePevs->count()} pevaluaciones y {$targetActivities} activities en grado {$toGrado}/pestudio {$pestudioId}.");
            $this->warn("La fuente (grado {$fromGrado}) NO se modifica.");

            if (! $this->confirm('¿Continuar?', false)) {
                $this->info('Abortado.');

                return self::SUCCESS;
            }
        }

        $this->newLine();

        // ── EJECUCIÓN ──
        DB::beginTransaction();

        try {
            // Paso 1: Clonar pensums
            $this->clonarPensums($sourcePensums, $pestudioId, $toGrado, $dryRun);

            // Paso 2: Crear/find pevaluaciones destino
            $this->mapearPevs($sourcePevs, $pestudioId, $toGrado, $targetSeccions, $dryRun);

            // Paso 3: Copiar activities + relaciones
            $this->copiarActivities($pevsWithActivities, $planningOnly, $dryRun);

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->warn("⚡ DRY-RUN completado — cambios no persistidos.");
            } else {
                DB::commit();
                $this->newLine();
                $this->info("✓ Migración completada.");
            }

            // Resumen final
            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Pensums creados', count($this->pensumMap)],
                    ['Pevaluaciones creadas', count($this->pevMap)],
                    ['Activities copiadas', $this->copiedActivities],
                    ['Achievements copiadas', $this->copiedAchievements],
                    ['Elementos LMS copiados', $this->copiedLmsItems],
                ]
            );

            return self::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error: {$e->getMessage()}");
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    // ─── PASO 1: PENSUMS ──────────────────────────────────────────────────

    private function clonarPensums(\Illuminate\Support\Collection $sourcePensums, int $pestudioId, int $toGrado, bool $dryRun): void
    {
        $this->info('▸ Clonando pensums...');

        foreach ($sourcePensums as $src) {
            // ¿Ya existe un pensum con misma asignatura en destino?
            $exists = Pensum::where('pestudio_id', $pestudioId)
                ->where('grado_id', $toGrado)
                ->where('asignatura_id', $src->asignatura_id)
                ->first();

            if ($exists) {
                $this->pensumMap[$src->id] = ['source' => $src->id, 'target' => $exists->id];
                $this->line("  → {$src->asignatura?->name}: ya existe (pensum {$exists->id})");

                continue;
            }

            if (! $dryRun) {
                $target = Pensum::create([
                    'pestudio_id' => $pestudioId,
                    'grado_id' => $toGrado,
                    'asignatura_id' => $src->asignatura_id,
                    'status_component' => $src->status_component,
                    'status_active' => $src->status_active,
                    'status_active_diagnostic' => $src->status_active_diagnostic,
                    'observations' => $src->observations,
                ]);
                $this->pensumMap[$src->id] = ['source' => $src->id, 'target' => $target->id];
                $this->line("  ✓ {$src->asignatura?->name}: pensum {$target->id}");
            } else {
                $this->pensumMap[$src->id] = ['source' => $src->id, 'target' => 0];
                $this->line("  ○ {$src->asignatura?->name}: se crearía pensum (dry-run)");
            }
        }
    }

    // ─── PASO 2: PEVALUACIONES ────────────────────────────────────────────

    private function mapearPevs(
        \Illuminate\Support\Collection $sourcePevs,
        int $pestudioId,
        int $toGrado,
        \Illuminate\Support\Collection $targetSeccions,
        bool $dryRun
    ): void {
        $this->info('▸ Mapeando pevaluaciones...');

        foreach ($sourcePevs as $src) {
            // Pensum destino por asignatura
            $pensumEntry = $this->pensumMap[$src->pensum_id] ?? null;
            if (! $pensumEntry) {
                $this->warn("  ⚠ pev {$src->id}: sin pensum destino, skip");

                continue;
            }
            $targetPensumId = $pensumEntry['target'];

            // Sección destino por nombre
            $targetSeccion = $targetSeccions->get($src->seccion?->name);
            if (! $targetSeccion) {
                $this->warn("  ⚠ pev {$src->id}: sección '{$src->seccion?->name}' sin match, skip");

                continue;
            }

            // Find-or-create: mismo pensum + sección + lapso + profesor
            $existingPev = Pevaluacion::where('pensum_id', $targetPensumId)
                ->where('seccion_id', $targetSeccion->id)
                ->where('lapso_id', $src->lapso_id)
                ->where('profesor_id', $src->profesor_id)
                ->first();

            if ($existingPev) {
                $this->pevMap[$src->id] = ['source' => $src->id, 'target' => $existingPev->id];

                continue;
            }

            if (! $dryRun) {
                $targetPev = Pevaluacion::create([
                    'profesor_id' => $src->profesor_id,
                    'lapso_id' => $src->lapso_id,
                    'seccion_id' => $targetSeccion->id,
                    'pensum_id' => $targetPensumId,
                    'grupo_estable_id' => $src->grupo_estable_id,
                    'status_baremo' => $src->status_baremo,
                    'status_official' => $src->status_official,
                    'status_note_report' => $src->status_note_report,
                    'nota_type' => $src->nota_type,
                    'escala_id' => $src->escala_id,
                    'objetivo' => $src->objetivo,
                    'description' => $src->description,
                    'observations' => $src->observations,
                    'category' => $src->category,
                ]);
                $this->pevMap[$src->id] = ['source' => $src->id, 'target' => $targetPev->id];
                $this->line("  ✓ pev {$src->id} → {$targetPev->id}");
            } else {
                $this->pevMap[$src->id] = ['source' => $src->id, 'target' => 0];
                $this->line("  ○ pev {$src->id}: se crearía pev (dry-run)");
            }
        }
    }

    // ─── PASO 3: ACTIVITIES + RELACIONES ──────────────────────────────────

    private function copiarActivities(
        \Illuminate\Support\Collection $pevsWithActivities,
        bool $planningOnly,
        bool $dryRun
    ): void {
        $this->info('▸ Copiando activities...');

        $activities = Activity::whereIn('pevaluacion_id', $pevsWithActivities->pluck('id'))
            ->with([
                'achievements',
                'lmsSections.visibleContents.media',
                'lmsResources.media',
                'lmsLinks',
                'lmsHtmlEmbeds',
                'lmsPublication',
                'lmsLogs',
            ])
            ->get();

        foreach ($activities as $srcActivity) {
            $pevEntry = $this->pevMap[$srcActivity->pevaluacion_id] ?? null;
            if (! $pevEntry) {
                $this->warn("  ⚠ act {$srcActivity->id}: sin pev destino, skip");

                continue;
            }

            $targetPevId = $pevEntry['target'];

            // Skip si ya se copió (idempotencia)
            if (! $dryRun && DB::table('activity_migration_logs')
                ->where('activity_source_id', $srcActivity->id)
                ->exists()
            ) {
                $this->line("  → act {$srcActivity->id}: ya migrado, skip");

                continue;
            }

            if (! $dryRun) {
                // Copiar activity
                $targetActivity = Activity::create([
                    'pevaluacion_id' => $targetPevId,
                    'finicial' => $srcActivity->finicial,
                    'ffinal' => $srcActivity->ffinal,
                    'topic' => $srcActivity->topic,
                    'thematic' => $srcActivity->thematic,
                    'references' => $srcActivity->references,
                    'teaching' => $srcActivity->teaching,
                    'learning' => $srcActivity->learning,
                    'description' => $srcActivity->description,
                    'observations' => $srcActivity->observations,
                    'comments' => $srcActivity->comments,
                    'status' => $srcActivity->status,
                ]);

                // Achievements
                $this->copiarAchievements($srcActivity, $targetActivity);

                // LMS relations (si no es planning-only)
                if (! $planningOnly) {
                    $this->copiarLmsRelations($srcActivity, $targetActivity);
                }

                // Log
                DB::table('activity_migration_logs')->insert([
                    'pestudio_id' => 2, // Se infiere del contexto; se puede generalizar
                    'from_grado_id' => (int) $this->option('from-grado'),
                    'to_grado_id' => (int) $this->option('to-grado'),
                    'pensum_source_id' => $srcActivity->pevaluacion->pensum_id ?? null,
                    'pensum_target_id' => ($this->pensumMap[$srcActivity->pevaluacion->pensum_id ?? 0]['target'] ?? null),
                    'pev_source_id' => $srcActivity->pevaluacion_id,
                    'pev_target_id' => $targetPevId,
                    'activity_source_id' => $srcActivity->id,
                    'activity_target_id' => $targetActivity->id,
                    'copied_at' => now(),
                ]);

                $this->copiedActivities++;
                $this->line("  ✓ act {$srcActivity->id} → {$targetActivity->id}: {$srcActivity->topic}");
            } else {
                $this->line("  ○ act {$srcActivity->id}: se copiaría (dry-run) — {$srcActivity->topic}");
            }
        }
    }

    private function copiarAchievements(Activity $srcActivity, Activity $targetActivity): void
    {
        foreach ($srcActivity->achievements as $src) {
            \App\Models\app\Academy\Achievement::create([
                'activity_id' => $targetActivity->id,
                'name' => $src->name,
                'weighting' => $src->weighting,
                'status_quantitative_weighting' => $src->status_quantitative_weighting,
            ]);
            $this->copiedAchievements++;
        }
    }

    private function copiarLmsRelations(Activity $srcActivity, Activity $targetActivity): void
    {
        // Sections + contents
        foreach ($srcActivity->lmsSections()->get() as $srcSection) {
            $targetSection = LmsActivitySection::create([
                'activity_id' => $targetActivity->id,
                'title' => $srcSection->title,
                'description' => $srcSection->description,
                'content_type' => $srcSection->content_type,
                'sort_order' => $srcSection->sort_order,
                'is_visible' => $srcSection->is_visible,
            ]);
            $this->copiedLmsItems++;

            foreach ($srcSection->contents()->orderBy('sort_order')->get() as $srcContent) {
                DB::table('lms_activity_contents')->insert([
                    'section_id' => $targetSection->id,
                    'type' => $srcContent->type,
                    'title' => $srcContent->title,
                    'body' => $srcContent->body,
                    'media_id' => $srcContent->media_id,
                    'sort_order' => $srcContent->sort_order,
                    'is_required' => $srcContent->is_required,
                    'is_visible' => $srcContent->is_visible,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->copiedLmsItems++;
            }
        }

        // Resources (mismo media_id — librería compartida)
        foreach ($srcActivity->lmsResources()->get() as $src) {
            LmsActivityResource::create([
                'activity_id' => $targetActivity->id,
                'section_id' => null, // Secciones LMS no se mapean por id
                'media_id' => $src->media_id,
                'uploaded_by' => $src->uploaded_by,
                'display_name' => $src->display_name,
                'description' => $src->description,
                'sort_order' => $src->sort_order,
                'is_visible' => $src->is_visible,
            ]);
            $this->copiedLmsItems++;
        }

        // Links
        foreach ($srcActivity->lmsLinks()->get() as $src) {
            LmsActivityLink::create([
                'activity_id' => $targetActivity->id,
                'section_id' => null,
                'added_by' => $src->added_by,
                'title' => $src->title,
                'url' => $src->url,
                'link_type' => $src->link_type,
                'description' => $src->description,
                'sort_order' => $src->sort_order,
                'is_visible' => $src->is_visible,
            ]);
            $this->copiedLmsItems++;
        }

        // HTML Embeds
        foreach ($srcActivity->lmsHtmlEmbeds()->get() as $src) {
            LmsHtmlEmbed::create([
                'activity_id' => $targetActivity->id,
                'section_id' => null,
                'added_by' => $src->added_by,
                'title' => $src->title,
                'html_content' => $src->html_content,
                'render_condition' => $src->render_condition,
                'sort_order' => $src->sort_order,
                'is_visible' => $src->is_visible,
            ]);
            $this->copiedLmsItems++;
        }

        // Publication (única por activity)
        if ($srcPublication = $srcActivity->lmsPublication) {
            LmsActivityPublication::create([
                'activity_id' => $targetActivity->id,
                'published_by' => $srcPublication->published_by,
                'status' => $srcPublication->status,
                'publish_at' => $srcPublication->publish_at,
                'unpublish_at' => $srcPublication->unpublish_at,
                'published_at' => $srcPublication->published_at,
                'allow_comments' => $srcPublication->allow_comments,
                'allow_downloads' => $srcPublication->allow_downloads,
                'notes' => $srcPublication->notes,
            ]);
            $this->copiedLmsItems++;
        }

        // Logs (columns: activity_id, user_id, event, context_id, context_type, ip_address)
        foreach ($srcActivity->lmsLogs()->get() as $src) {
            DB::table('lms_activity_logs')->insert([
                'activity_id' => $targetActivity->id,
                'user_id' => $src->user_id,
                'event' => $src->event,
                'context_id' => $src->context_id,
                'context_type' => $src->context_type,
                'ip_address' => $src->ip_address,
                'created_at' => now(),
            ]);
            $this->copiedLmsItems++;
        }
    }

    // ─── ROLLBACK ─────────────────────────────────────────────────────────

    private function rollback(): int
    {
        $fromGrado = (int) $this->option('from-grado');
        $toGrado = (int) $this->option('to-grado');
        $pestudioId = (int) $this->option('pestudio');

        $logs = DB::table('activity_migration_logs')
            ->where('pestudio_id', $pestudioId)
            ->where('from_grado_id', $fromGrado)
            ->where('to_grado_id', $toGrado)
            ->orderByDesc('id')
            ->get();

        if ($logs->isEmpty()) {
            $this->warn('No hay registros de migración para este rango.');

            return self::SUCCESS;
        }

        $this->info("Rollback: {$logs->count()} activities a eliminar.");

        if (! $this->option('force') && ! $this->confirm('¿Eliminar las activities copiadas y sus relaciones?', false)) {
            $this->info('Abortado.');

            return self::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $targetActivityIds = $logs->pluck('activity_target_id')->filter()->values();

            // Borrar relaciones LMS (sin cascade FK en algunas tablas)
            if ($targetActivityIds->isNotEmpty()) {
                $sectionIds = DB::table('lms_activity_sections')
                    ->whereIn('activity_id', $targetActivityIds)
                    ->pluck('id');

                DB::table('lms_activity_contents')->whereIn('section_id', $sectionIds)->delete();
                DB::table('lms_activity_sections')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('lms_activity_resources')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('lms_activity_links')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('lms_html_embeds')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('lms_activity_publications')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('lms_activity_logs')->whereIn('activity_id', $targetActivityIds)->delete();
                DB::table('achievements')->whereIn('activity_id', $targetActivityIds)->delete();

                Activity::whereIn('id', $targetActivityIds)->delete();
            }

            // Borrar pevs creadas
            $targetPevIds = $logs->pluck('pev_target_id')->filter()->values();
            if ($targetPevIds->isNotEmpty()) {
                Pevaluacion::whereIn('id', $targetPevIds)->delete();
            }

            // Borrar pensums creados
            $targetPensumIds = $logs->pluck('pensum_target_id')->filter()->unique()->values();
            if ($targetPensumIds->isNotEmpty()) {
                Pensum::whereIn('id', $targetPensumIds)->delete();
            }

            // Limpiar log
            DB::table('activity_migration_logs')
                ->where('pestudio_id', $pestudioId)
                ->where('from_grado_id', $fromGrado)
                ->where('to_grado_id', $toGrado)
                ->delete();

            DB::commit();

            $this->info("✓ Rollback completado: {$targetActivityIds->count()} activities, {$targetPevIds->count()} pevs, {$targetPensumIds->count()} pensums eliminados.");

            return self::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error en rollback: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
