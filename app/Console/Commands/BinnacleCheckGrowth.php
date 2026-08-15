<?php

namespace App\Console\Commands;

use App\Services\Binnacle;
use Illuminate\Console\Command;

/**
 * Evalúa el crecimiento de binnacle_entries y decide si el particionado por
 * rango de fecha está justificado (Spec §9 / mejora propuesta #8).
 *
 * Usa la proyección compartida Binnacle::projectedGrowth(): con el ritmo real
 * de los últimos 30 días estima las filas a `partition_lookahead_months` meses
 * y las compara con `partition_threshold` (config/binnacle.php). Referencia:
 * el benchmark a 50k filas dio filtros <15ms; el umbral por defecto (1M)
 * deja margen. Cuando se supera, remite al procedimiento documentado en
 * blueprint/binnacle/particionado-procedimiento.md.
 *
 * Uso:
 *   php8.2 artisan binnacle:check-growth              # reporte (default)
 *   php8.2 artisan binnacle:check-growth --check      # solo exit code (health check)
 */
class BinnacleCheckGrowth extends Command
{
    protected $signature = 'binnacle:check-growth
        {--check : Modo health check: exit 0 = sin necesidad, 1 = particionado recomendado}';

    protected $description = 'Proyecta el crecimiento de la bitácora y recomienda particionado (Spec §9)';

    public function handle(): int
    {
        $g = Binnacle::projectedGrowth();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Filas actuales', number_format($g['total'])],
                ['Últimos 30 días', number_format($g['last_30_days'])],
                ['Promedio diario', number_format($g['daily_rate'], 1)],
                ['Proyección a '.$g['lookahead_months'].' meses', number_format($g['projected'])],
                ['Umbral de particionado', number_format($g['threshold'])],
            ]
        );

        if (! $g['partition_needed']) {
            $this->info('Sin necesidad de particionado (Spec §9): la proyección queda por debajo del umbral.');

            return self::SUCCESS;
        }

        $this->error('Particionado recomendado (Spec §9): la proyección supera el umbral. Ver blueprint/binnacle/particionado-procedimiento.md.');

        if (! $this->option('check')) {
            // Meta-auditoría: la recomendación queda en la propia bitácora.
            Binnacle::log('binnacle_partition_recommended', [
                'title' => 'Particionado de bitácora recomendado',
                'description' => "Proyección de {$g['projected']} filas supera el umbral de {$g['threshold']} (Spec §9).",
                'category' => 'system',
                'severity' => 'warning',
                'metadata' => $g,
            ]);
        }

        return self::FAILURE;
    }
}
