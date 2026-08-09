<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Services\Lms\LmsSvgRepairService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Normaliza (persiste) el contraste y la accesibilidad de los diagramas SVG
 * embebidos en lms_activity_contents, de forma determinista y sin IA.
 *
 * Es idempotente: solo persiste cuando renderImage() (repair + contraste +
 * accesibilidad) produce un body distinto al almacenado, por lo que puede
 * ejecutarse tantas veces como se quiera en producción sin riesgo.
 *
 * Uso:
 *   php8.2 artisan lms:normalize-svgs                  # procesa y persiste todo
 *   php8.2 artisan lms:normalize-svgs --dry-run        # solo reporta
 *   php8.2 artisan lms:normalize-svgs --ids=2232,239   # solo esos contenidos
 *   php8.2 artisan lms:normalize-svgs --limit=50       # limita el volumen
 *   php8.2 artisan lms:normalize-svgs --chunk=100      # lote de procesado
 */
class NormalizeSvgContrast extends Command
{
    protected $signature = 'lms:normalize-svgs
                          {--ids=* : Solo normalizar estos ids de lms_activity_contents}
                          {--limit= : Máximo de contenidos a procesar (0 = sin límite)}
                          {--chunk=100 : Filas por lote para no agotar memoria}
                          {--dry-run : Reportar sin persistir cambios}
                          {--since= : Solo contenidos actualizados desde esta fecha (Y-m-d)}';

    protected $description = 'Persiste la normalización determinista de contraste/accesibilidad de los SVG en BD (idempotente)';

    public function handle(): int
    {
        $svc = app(LmsSvgRepairService::class);

        $query = LmsActivityContent::where('body', 'like', '%<svg%');

        if ($ids = $this->parseIds($this->option('ids'))) {
            $query->whereIn('id', $ids);
        }
        if ($since = $this->option('since')) {
            $query->where('updated_at', '>=', $since);
        }
        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->warn('No se encontraron contenidos con SVG.');

            return Command::SUCCESS;
        }

        $persist = ! $this->option('dry-run');
        $chunk = max(1, (int) $this->option('chunk'));
        $backupPath = $persist ? $this->backupBodies($query) : null;

        $changed = 0;
        $unchanged = 0;
        $errors = 0;
        $processed = 0;

        $this->info(sprintf(
            '%s — %d contenidos con SVG%s',
            $persist ? 'Persistiendo' : 'Analizando (dry-run)',
            $total,
            $backupPath ? " · backup: {$backupPath}" : '',
        ));

        $query->select(['id', 'body'])->orderBy('id')->chunkById($chunk, function ($rows) use ($svc, $persist, &$changed, &$unchanged, &$errors, &$processed) {
            foreach ($rows as $c) {
                $processed++;

                try {
                    $new = $svc->renderImage((string) $c->body);
                } catch (\Throwable $e) {
                    $errors++;
                    $this->error(sprintf('  %-6s  ERROR: %s', $c->id, $e->getMessage()));

                    continue;
                }

                if ($new === $c->body) {
                    $unchanged++;

                    continue;
                }

                if ($persist) {
                    $c->update(['body' => $new]);
                }
                $changed++;
                $this->line(sprintf('  %-6s  contraste → oscuro + accesibilidad', $c->id));
            }
        }, column: 'id');

        $this->newLine();
        $this->info(sprintf(
            'Resultado: %d cambiados · %d sin cambios · %d errores (de %d procesados)',
            $changed,
            $unchanged,
            $errors,
            $processed,
        ));

        if ($persist && $backupPath) {
            $this->info("Backup de originales: {$backupPath}");
        } elseif (! $persist) {
            $this->warn('Modo dry-run: no se persistió nada. Repite sin --dry-run para guardar.');
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function parseIds(mixed $raw): array
    {
        if (! $raw) {
            return [];
        }

        return collect((array) $raw)
            ->flatMap(fn ($v) => explode(',', (string) $v))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    private function backupBodies($query): ?string
    {
        $originals = (clone $query)->pluck('body', 'id')->toArray();
        if (empty($originals)) {
            return null;
        }

        $path = 'private/svg-normalize-backup-'.now()->format('Ymd_His').'.json';
        Storage::put($path, json_encode($originals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Storage::path($path);
    }
}
