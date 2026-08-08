<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Services\Lms\LmsSvgAiRepairService;
use App\Services\Lms\LmsSvgRepairService;
use App\Services\Lms\SvgRepairResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Repara diagramas SVG dañados en lms_activity_contents usando IA con cadena
 * de modelos (fallback) y reparación determinista como red de seguridad.
 *
 * Uso:
 *   php8.2 artisan lms:repair-svgs                # repara los dañados (persiste)
 *   php8.2 artisan lms:repair-svgs --all          # procesa TODOS los contents con <svg>
 *   php8.2 artisan lms:repair-svgs --dry-run      # solo reporta
 *   php8.2 artisan lms:repair-svgs --ids=2232,2238
 *   php8.2 artisan lms:repair-svgs --limit=5
 *   php8.2 artisan lms:repair-svgs --models=anthropic/claude-sonnet-4,mistralai/mistral-large
 */
class RepairSvgs extends Command
{
    protected $signature = 'lms:repair-svgs
                          {--all : Procesar TODOS los contenidos con <svg> (no solo los dañados)}
                          {--ids=* : Solo reparar estos ids de contenido (lms_activity_contents)}
                          {--limit= : Máximo de contenidos a procesar (0 = sin límite)}
                          {--dry-run : Reportar sin persistir cambios}
                          {--normalize : Persistir la normalización determinista de contraste/accesibilidad en todos los SVG (sin IA)}
                          {--models= : Cadena custom de modelos separada por comas (anula la config)}';

    protected $description = 'Repara diagramas SVG dañados (IA con fallback + reparación determinista) o normaliza su contraste';

    public function handle(): int
    {
        // Modo determinista (sin IA): persistir normalización de contraste.
        if ($this->option('normalize')) {
            return $this->normalizeInPlace();
        }

        /** @var LmsSvgAiRepairService $service */
        $service = app(LmsSvgAiRepairService::class);

        $ids = $this->option('ids') ?: null;
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $persist = ! $this->option('dry-run');
        $models = $this->parseModels($this->option('models'));

        if ($ids !== null) {
            $ids = $this->parseIds($ids);
        } elseif ($this->option('all')) {
            // --all: procesar TODOS los contenidos con <svg> embebido
            // (incluye los sanos; el pipeline los marca 'unchanged').
            $ids = LmsActivityContent::where(function ($q) {
                $q->where('type', 'IMAGE')->orWhere('type', 'HTML');
            })->where('body', 'like', '%<svg%')->pluck('id')->all();
        }

        // Backup de los bodies originales antes de persistir.
        $backupPath = null;
        if ($persist) {
            $backupPath = $this->backupOriginals($ids);
        }

        $this->info('Escaneando contenidos LMS con SVG…');
        $results = $service->repairAll($ids, $limit, [
            'persist' => $persist,
            'models' => $models,
            'notify' => fn (string $type, string $title, string $desc) => $this->notify($type, $title, $desc),
        ]);

        if ($results->isEmpty()) {
            $this->warn('No se encontraron contenidos con SVG dañado.');

            return Command::SUCCESS;
        }

        $this->newLine();
        $this->line(sprintf(
            '%-6s  %-14s  %-24s  %s',
            'id', 'estrategia', 'modelo', 'cambios'
        ));
        $this->line(str_repeat('─', 100));

        foreach ($results as $result) {
            $this->renderResult($result);
        }

        $this->newLine();
        $summary = [
            'ai' => $results->where('strategy', 'ai')->count(),
            'determinista' => $results->where('strategy', 'deterministic')->count(),
            'sin cambios' => $results->where('strategy', 'unchanged')->count(),
            'sin svg' => $results->where('strategy', 'no-svg')->count(),
            'errores' => $results->where('strategy', 'error')->count(),
        ];
        $this->info(sprintf(
            'Resumen: %d IA · %d determinista · %d sin cambios · %d sin svg · %d errores',
            $summary['ai'],
            $summary['determinista'],
            $summary['sin cambios'],
            $summary['sin svg'],
            $summary['errores'],
        ));

        if ($persist && $backupPath) {
            $this->info("Backup de originales: {$backupPath}");
        }

        if ($this->option('dry-run')) {
            $this->warn('Modo dry-run: no se persistió ningún cambio. Repite sin --dry-run para guardar.');
        }

        return Command::SUCCESS;
    }

    /**
     * Aplica la normalización determinista (repair + contraste + accesibilidad)
     * sobre todos los bodies con <svg> y la persiste. Sin IA. Mejora 1: el
     * contenido queda legible en BD para que el render no re-procese cada vez.
     */
    private function normalizeInPlace(): int
    {
        $persist = ! $this->option('dry-run');
        $svc = app(LmsSvgRepairService::class);

        $query = LmsActivityContent::where('body', 'like', '%<svg%');

        // Solo los ids objetivo: por defecto "todos los que tengan svg fondo",
        // pero si --ids=... limita a esos exactos.
        $ids = $this->option('ids') ?: null;
        if ($ids !== null) {
            $ids = $this->parseIds($ids);
            $query->whereIn('id', $ids);
        }
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        if ($limit) {
            $query->limit($limit);
        }

        $changed = 0;
        $unchanged = 0;
        $backupPath = null;

        if ($persist) {
            $backupPath = $this->backupOriginals($ids);
        }

        $query->orderBy('id')->get()->each(function (LmsActivityContent $c) use ($svc, $persist, &$changed, &$unchanged) {
            $new = $svc->renderImage($c->body ?? '');
            if ($new === ($c->body ?? '')) {
                $unchanged++;

                return;
            }

            if ($persist) {
                $c->update(['body' => $new]);
            }
            $changed++;
            $this->line(sprintf('  %-6s  contraste → oscuro + accesibilidad', $c->id));
        });

        $this->info(sprintf('Normalización: %d cambiados · %d sin cambios', $changed, $unchanged));
        if ($persist && $backupPath) {
            $this->info("Backup de originales: {$backupPath}");
        }
        if (! $persist) {
            $this->warn('Modo dry-run: no se persistió ningún cambio. Repite sin --dry-run para guardar.');
        }

        return Command::SUCCESS;
    }

    private function renderResult(SvgRepairResult $result): void
    {
        $id = (string) ($result->contentId ?? '-');
        $strategy = match ($result->strategy) {
            'ai' => 'IA ✅',
            'deterministic' => 'determinista ⚙',
            'unchanged' => 'sin cambios',
            'no-svg' => 'sin svg',
            'error' => 'ERROR ❌',
        };
        $model = $result->model ?? '-';
        $changes = implode(', ', $result->changes);

        if ($result->strategy === 'error') {
            $this->error(sprintf('%-6s  %-14s  %-24s  %s', $id, $strategy, $model, $result->error));
        } else {
            $this->line(sprintf('%-6s  %-14s  %-24s  %s', $id, $strategy, $model, $changes));
        }
    }

    private function notify(string $type, string $title, string $desc): void
    {
        match ($type) {
            'info' => $this->line("  ℹ {$title}: {$desc}"),
            'warning' => $this->warn("  ⚠ {$title}: {$desc}"),
            'error' => $this->error("  ✖ {$title}: {$desc}"),
            default => $this->line("  {$title}: {$desc}"),
        };
    }

    /**
     * @return list<int>
     */
    private function parseIds(mixed $raw): array
    {
        // Acepta tanto --ids=1,2,3 como --ids=1 --ids=2.
        return collect((array) $raw)
            ->flatMap(fn ($v) => explode(',', (string) $v))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    /**
     * @return list<array{model: string, label: string}>|null
     */
    private function parseModels(mixed $raw): ?array
    {
        if (! $raw || ! is_string($raw) || trim($raw) === '') {
            return null;
        }

        return collect(explode(',', $raw))
            ->map(fn ($m) => trim($m))
            ->filter()
            ->map(fn ($m, $i) => [
                'model' => $m,
                'label' => 'Reparación SVG custom '.($i + 1),
            ])
            ->values()
            ->all();
    }

    /**
     * Guarda un JSON con los bodies originales de los contenidos objetivo.
     */
    private function backupOriginals(?array $ids): ?string
    {
        $query = LmsActivityContent::where(function ($q) {
            $q->where('type', 'IMAGE')->orWhere('type', 'HTML');
        })->where('body', 'like', '%<svg%');

        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        $originals = $query->pluck('body', 'id')->toArray();
        if (empty($originals)) {
            return null;
        }

        $path = 'private/svg-repair-backup-'.now()->format('Ymd_His').'.json';
        Storage::put($path, json_encode($originals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return Storage::path($path);
    }
}
