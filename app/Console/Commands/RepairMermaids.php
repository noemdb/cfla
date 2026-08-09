<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Services\Lms\LmsMermaidAiRepairService;
use App\Services\Lms\LmsMermaidRepairService;
use Illuminate\Console\Command;

/**
 * Revisa y repara diagramas Mermaid almacenados en la BD usando reparación
 * DETERMINISTA (sin IA, sin coste de API): fuerza graph TD, reinserta
 * espacios en labels concatenados (ej. "AutoconocimientoVocacional") y parte
 * etiquetas largas en multi-línea con <br/>.
 *
 * Orígenes escaneados:
 *   - lms_html_embeds.html_content   (diagramas publicados / cards de sección)
 *   - lms_activity_contents.body     (bloques de contenido guardados en borrador)
 *
 * Uso (producción):
 *   php8.2 artisan lms:repair-mermaid --dry-run          # solo reporta (recomendado primero)
 *   php8.2 artisan lms:repair-mermaid                    # repara y persiste los dañados
 *   php8.2 artisan lms:repair-mermaid --only=embeds      # solo lms_html_embeds
 *   php8.2 artisan lms:repair-mermaid --only=contents    # solo lms_activity_contents
 *   php8.2 artisan lms:repair-mermaid --ids=12,34,56     # registros concretos (embeds o contents)
 *   php8.2 artisan lms:repair-mermaid --limit=50         # acota el lote
 *   php8.2 artisan lms:repair-mermaid --ai               # usa IA para los que requieren reestructuración
 *   php8.2 artisan lms:repair-mermaid --ai --activity=37 # solo de esa actividad
 *
 * Idempotente: un registro ya correcto no se toca ni se reporta como dañado.
 *
 * Con --ai: usa IA para reparar diagramas que la reparación determinista
 * no puede resolver (demasiados nodos, flechas rotas, sintaxis ilegible).
 * Cadena de modelos: diagram_primary → fallback1 → fallback2.
 */
class RepairMermaids extends Command
{
    protected $signature = 'lms:repair-mermaid
                          {--ids=* : Solo estos ids (lms_html_embeds o lms_activity_contents)}
                          {--limit= : Máximo de registros a procesar (0 = sin límite)}
                          {--dry-run : Reportar sin persistir cambios}
                          {--only= : Filtrar fuente: embeds, contents o all (default: all)}
                          {--ai : Usar IA para reparar diagramas que la reparación determinista no puede resolver}
                          {--activity= : Solo contenidos de esta actividad}';

    protected $description = 'Repara diagramas Mermaid en la BD. Con --ai: usa IA para diagramas que requieren reestructuración.';

    public function handle(LmsMermaidRepairService $mermaid, ?LmsMermaidAiRepairService $aiRepair = null): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $only = strtolower($this->option('only') ?? 'all');
        $limit = (int) ($this->option('limit') ?? 0);
        $ids = array_filter(array_map('intval', $this->option('ids') ?? []));
        $useAi = (bool) $this->option('ai');
        $activityFilter = $this->option('activity') ? (int) $this->option('activity') : null;

        if ($useAi && ! $aiRepair) {
            $this->error('--ai requiere el servicio LmsMermaidAiRepairService. Verifica la configuración.');

            return self::FAILURE;
        }

        if (! in_array($only, ['all', 'embeds', 'contents'], true)) {
            $this->error("--only inválido: {$only} (use all, embeds o contents).");

            return self::FAILURE;
        }

        $mode = $useAi ? 'determinista + IA' : 'determinista';
        $this->line($dryRun
            ? '<comment>Modo DRY-RUN</comment>: solo reporte, no se persistirá nada.'
            : "Reparando diagramas Mermaid ({$mode})...");

        $rows = [];
        $stats = ['scanned' => 0, 'with_mermaid' => 0, 'fixed' => 0, 'ai_fixed' => 0, 'unchanged' => 0, 'skipped' => 0, 'errors' => 0];

        // ─── lms_html_embeds.html_content ─────────────────────────
        if (in_array($only, ['all', 'embeds'], true)) {
            $query = LmsHtmlEmbed::query();
            if ($ids) {
                $query->whereIn('id', $ids);
            }
            if ($activityFilter) {
                $query->where('activity_id', $activityFilter);
            }
            if ($limit > 0) {
                $query->limit($limit);
            }
            foreach ($query->orderBy('id')->cursor() as $embed) {
                $stats['scanned']++;
                $rows[] = $this->processRecord($mermaid, $aiRepair, 'embed', $embed->id, $embed->activity_id, $embed->html_content ?? '', $dryRun, $useAi, $stats);
            }
        }

        // ─── lms_activity_contents.body ───────────────────────────
        if (in_array($only, ['all', 'contents'], true)) {
            $query = LmsActivityContent::query();
            if ($ids) {
                $query->whereIn('id', $ids);
            }
            if ($activityFilter) {
                $query->whereHas('section', fn ($q) => $q->where('activity_id', $activityFilter));
            }
            if ($limit > 0) {
                $query->limit($limit);
            }
            foreach ($query->orderBy('id')->cursor() as $content) {
                $stats['scanned']++;
                $rows[] = $this->processRecord($mermaid, $aiRepair, 'content', $content->id, $content->section?->activity_id, $content->body ?? '', $dryRun, $useAi, $stats);
            }
        }

        // ─── Salida ───────────────────────────────────────────────
        if ($rows) {
            $this->table(['Fuente', 'ID', 'Actividad', 'Acción', 'Problemas detectados'], $rows);
        } else {
            $this->info('No se encontraron registros para procesar.');
        }

        $this->newLine();
        $this->line(sprintf(
            '<info>Escaneados:</info> %d | <info>con Mermaid:</info> %d | <info>reparados:</info> %d | <info>IA reparados:</info> %d | <info>sin cambios:</info> %d | <info>omitidos:</info> %d | <info>errores:</info> %d',
            $stats['scanned'],
            $stats['with_mermaid'],
            $stats['fixed'],
            $stats['ai_fixed'],
            $stats['unchanged'],
            $stats['skipped'],
            $stats['errors']
        ));

        if ($dryRun && $stats['fixed'] > 0) {
            $this->newLine();
            $this->warn("DRY-RUN: {$stats['fixed']} registro(s) se repararían. Ejecuta sin --dry-run para persistir.");
        }

        return $stats['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Procesa un registro individual: detecta Mermaid, valida y repara.
     *
     * @return array{0: string, 1: int, 2: string, 3: string, 4: string}
     */
    private function processRecord(LmsMermaidRepairService $mermaid, ?LmsMermaidAiRepairService $aiRepair, string $source, int $id, ?int $activityId, string $body, bool $dryRun, bool $useAi, array &$stats): array
    {
        $activityLabel = (string) ($activityId ?? '—');
        $body = trim($body ?? '');

        if ($body === '') {
            $stats['skipped']++;

            return [$source, $id, $activityLabel, 'omitido', 'sin contenido'];
        }

        if (! $mermaid->hasMermaid($body)) {
            $stats['skipped']++;

            return [$source, $id, $activityLabel, 'omitido', 'sin diagrama Mermaid'];
        }

        $stats['with_mermaid']++;

        $src = $mermaid->extractMermaidCode($body);
        $validation = $mermaid->validate($src);
        $fixed = $mermaid->postProcess($src);
        $changed = $fixed !== $src;
        $issues = $validation['issues'];

        // Un diagrama con issues pero que el post-proceso ya deja intacto
        // (p.ej. solo "demasiados nodos") no puede repararse de forma
        // determinista: se reporta pero no se toca.
        if ($changed) {
            if (! $dryRun) {
                $newBody = $mermaid->repairBody($body);
                if ($source === 'embed') {
                    LmsHtmlEmbed::where('id', $id)->update(['html_content' => $newBody]);
                } else {
                    LmsActivityContent::where('id', $id)->update(['body' => $newBody]);
                }
            }
            $stats['fixed']++;
            $detail = $issues ? implode('; ', $issues) : 'labels concatenados / formato';

            return [$source, $id, $activityLabel, $dryRun ? 'REPARARÍA' : 'reparado', $detail];
        }

        if ($issues) {
            // ── Reparación IA (si --ai está activo) ──────────────
            if ($useAi && $aiRepair && ! $dryRun) {
                $aiResult = $aiRepair->repairBody($body);
                if ($aiResult['ok'] && $aiResult['newBody']) {
                    $newBody = $aiResult['newBody'];
                    if ($source === 'embed') {
                        LmsHtmlEmbed::where('id', $id)->update(['html_content' => $newBody]);
                    } else {
                        LmsActivityContent::where('id', $id)->update(['body' => $newBody]);
                    }
                    $stats['ai_fixed']++;

                    return [$source, $id, $activityLabel, 'IA reparado', $aiResult['message']." ({$aiResult['attempts']} intento(s))"];
                }
                // Si la IA falló, reportar como sin cambio con el error
                $stats['unchanged']++;

                return [$source, $id, $activityLabel, 'sin cambio', implode('; ', $issues)." (IA: {$aiResult['message']})"];
            }

            $stats['unchanged']++;

            return [$source, $id, $activityLabel, 'sin cambio', implode('; ', $issues).' (requiere IA/ajuste manual)'];
        }

        $stats['unchanged']++;

        return [$source, $id, $activityLabel, 'ok', '—'];
    }
}
