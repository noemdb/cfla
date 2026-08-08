<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Services\Lms\LmsContentClassifier;
use Illuminate\Console\Command;

/**
 * Recalcula `lms_activity_sections.content_type` a partir de los contenidos
 * visibles (Spec "Campo content_type en lms_activity_sections").
 *
 * Sirve como backfill inicial, reparación de drift (ediciones manuales de BD)
 * y verificación de idempotencia en CI.
 *
 * Uso:
 *   php8.2 artisan lms:sync-section-types              # recalcula todo (persiste)
 *   php8.2 artisan lms:sync-section-types --dry-run    # reporta cambios sin guardar
 *   php8.2 artisan lms:sync-section-types --activity=42
 */
class SyncSectionTypes extends Command
{
    protected $signature = 'lms:sync-section-types
                          {--dry-run : Reportar sin persistir}
                          {--activity= : Solo las secciones de esta actividad}';

    protected $description = 'Recalcula el content_type de cada sección LMS desde sus contenidos visibles';

    public function handle(): int
    {
        /** @var LmsContentClassifier $classifier */
        $classifier = app(LmsContentClassifier::class);

        $query = LmsActivitySection::with('visibleContents.media')->orderBy('id');
        if ($activityId = $this->option('activity')) {
            $query->where('activity_id', (int) $activityId);
        }

        $dryRun = (bool) $this->option('dry-run');
        $changed = 0;
        $total = 0;
        $byType = [];

        $query->chunkById(200, function ($sections) use ($classifier, $dryRun, &$changed, &$total, &$byType) {
            foreach ($sections as $section) {
                $total++;
                $type = $classifier->classifySection($section->visibleContents);
                $byType[$type] = ($byType[$type] ?? 0) + 1;

                // Comparar contra la columna CRUDA: el accesor del modelo
                // calcula en vivo cuando la columna es null, lo que haría
                // parecer "correctas" a filas sin clasificar.
                $cached = $section->getAttributes()['content_type'] ?? null;

                if ($cached === $type) {
                    continue;
                }

                $changed++;
                $this->line(sprintf(
                    '  [%d] %s → %s (%s)',
                    $section->id,
                    $cached ?? 'null',
                    $type,
                    $section->title,
                ));

                if (! $dryRun) {
                    $section->content_type = $type;
                    $section->saveQuietly();
                }
            }
        });

        $this->newLine();
        $this->info("Secciones: {$total} · Cambios: {$changed}" . ($dryRun ? ' (dry-run)' : ''));

        ksort($byType);
        foreach ($byType as $type => $count) {
            $this->line(sprintf('  %-10s %d', $type, $count));
        }

        return Command::SUCCESS;
    }
}
