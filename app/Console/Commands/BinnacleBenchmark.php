<?php

namespace App\Console\Commands;

use App\Models\BinnacleEntry;
use App\Services\Binnacle;
use Illuminate\Console\Command;

/**
 * Mide el tiempo del filtro combinado del panel (Spec §11, criterio Fase 2):
 * rango de fecha + severidad + categoría + búsqueda por usuario, sobre la
 * tabla real. Ejecutar tras binnacle:seed-test.
 */
class BinnacleBenchmark extends Command
{
    protected $signature = 'binnacle:benchmark
        {--iterations=5 : Número de ejecuciones por consulta}';

    protected $description = 'Benchmark del filtro combinado de la bitácora (Spec §11)';

    public function handle(): int
    {
        $iterations = max(1, (int) $this->option('iterations'));

        $total = BinnacleEntry::count();
        $this->info("Filas en binnacle_entries: {$total}");

        $queries = [
            'filtro: fecha + severidad + categoría' => function () {
                BinnacleEntry::query()
                    ->where('event_category', 'security')
                    ->where('event_severity', 'critical')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
            },
            'filtro: + búsqueda por usuario' => function () {
                BinnacleEntry::query()
                    ->where('event_category', 'security')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->where('subject_identifier', 'like', 'seed%')
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
            },
            'búsqueda de texto libre' => function () {
                BinnacleEntry::query()
                    ->where('title', 'like', '%prueba%')
                    ->orderByDesc('created_at')
                    ->limit(50)
                    ->get();
            },
        ];

        $exit = self::SUCCESS;

        foreach ($queries as $label => $query) {
            $times = [];

            for ($i = 0; $i < $iterations; $i++) {
                $start = hrtime(true);
                $query();
                $times[] = (hrtime(true) - $start) / 1e6;
            }

            $avg = array_sum($times) / count($times);
            $ok = $avg < 1000 ? 'OK <1s' : 'LENTO ≥1s';
            $this->line(sprintf('  %-45s avg %8.2f ms  [%s]', $label, $avg, $ok));

            if ($avg >= 1000) {
                $exit = self::FAILURE;
            }
        }

        $integrity = Binnacle::verifyChainIntegrity();
        $this->line("Integridad de cadena: {$integrity['total']} eslabones, {$integrity['broken_links']} rotos, válida=".($integrity['valid'] ? 'sí' : 'NO'));

        return $exit;
    }
}
