<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Services\Lms\LmsPrintRepairService;
use Illuminate\Console\Command;

/**
 * Reparaciones deterministas a contenido LMS para mejorar la impresión en
 * modo libro (orientación horizontal, layout de dos columnas).
 *
 * Escanea lms_activity_contents.body y aplica:
 *   - Clampeo de clases Tailwind de texto excesivo (text-3xl+ → text-base)
 *   - Normalización de font-size inline (>18px → 11pt)
 *   - Aseguramiento de max-width:100% en <img>, <svg>
 *   - Envoltura de <table> en div scrollable
 *   - Limpieza de !important en estilos inline
 *   - Normalización de tamaños de heading (h1–h6 → 14pt/12pt)
 *
 * Uso (producción):
 *   php8.2 artisan lms:repair-print --dry-run          # solo reporta
 *   php8.2 artisan lms:repair-print                     # repara y persiste
 *   php8.2 artisan lms:repair-print --ids=12,34,56     # registros concretos
 *   php8.2 artisan lms:repair-print --limit=100         # limita el lote
 *   php8.2 artisan lms:repair-print --activity=85       # solo de esa actividad
 *
 * Idempotente: un registro ya correcto no se toca ni se reporta.
 */
class LmsPrintRepair extends Command
{
    protected $signature = 'lms:repair-print
                          {--ids=* : Solo estos ids de lms_activity_contents}
                          {--limit= : Máximo de registros a procesar (0 = sin límite)}
                          {--dry-run : Reportar sin persistir cambios}
                          {--activity= : Solo contenidos de esta actividad}';

    protected $description = 'Repara contenido LMS para mejorar impresión en modo libro (determinista, sin IA)';

    public function handle(LmsPrintRepairService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) ($this->option('limit') ?? 0);
        $ids = array_filter(array_map('intval', $this->option('ids') ?? []));
        $activityId = $this->option('activity') ? (int) $this->option('activity') : null;

        $this->line($dryRun
            ? '<comment>Modo DRY-RUN</comment>: solo reporte, no se persistirá nada.'
            : 'Reparando contenido para impresión (determinista)...');

        $query = LmsActivityContent::query()
            ->whereNotNull('body')
            ->where('body', '!=', '');

        if ($ids) {
            $query->whereIn('id', $ids);
        }
        if ($activityId) {
            $query->whereHas('section', fn ($q) => $q->where('activity_id', $activityId));
        }
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = [];
        $stats = [
            'scanned' => 0,
            'changed' => 0,
            'unchanged' => 0,
            'titles_fixed' => 0,
        ];

        foreach ($query->orderBy('id')->cursor() as $content) {
            $stats['scanned']++;
            $activityId = $content->section?->activity_id ?? '?';

            // Reparar body
            $result = $service->repairBody($content->body);
            $bodyIssues = $result['issues'] ? implode(', ', $result['issues']) : '—';

            // Reparar título
            $titleResult = $service->repairTitle($content->title ?? '');
            if ($titleResult['changed']) {
                $stats['titles_fixed']++;
            }

            if ($result['changed'] || $titleResult['changed']) {
                $stats['changed']++;
                $action = $dryRun ? 'REPARARÍA' : 'REPARADO';

                $rows[] = [
                    $content->id,
                    $activityId,
                    $action,
                    $bodyIssues . ($titleResult['changed'] ? ', title_truncated' : ''),
                ];

                if (! $dryRun) {
                    $content->update([
                        'body' => $result['body'],
                        'title' => $titleResult['title'],
                    ]);
                }
            } else {
                $stats['unchanged']++;
            }
        }

        // ─── Salida ───────────────────────────────────────────────
        if ($rows) {
            $this->table(['ID', 'Actividad', 'Acción', 'Reparaciones'], $rows);
        } else {
            $this->info('No se encontraron registros para procesar.');
        }

        $this->newLine();
        $this->line(sprintf(
            '<info>Escaneados:</info> %d | <info>reparados:</info> %d | <info>títulos truncados:</info> %d | <info>sin cambios:</info> %d',
            $stats['scanned'],
            $stats['changed'],
            $stats['titles_fixed'],
            $stats['unchanged'],
        ));

        return self::SUCCESS;
    }
}
