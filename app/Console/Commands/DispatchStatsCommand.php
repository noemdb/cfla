<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\BroadcastEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Opción 10 — Métricas de eventos broadcast.
 *
 * Muestra eventos/hora, destinos por evento, canal con más destinatarios y
 * ratio delivered/total. Uso: php8.2 artisan broadcast:stats
 */
class DispatchStatsCommand extends Command
{
    protected $signature = 'broadcast:stats
                            {--hours=24 : ventana de tiempo en horas}
                            {--json : salida JSON}';

    protected $description = 'Muestra métricas de eventos broadcast (auditoría Opción 10)';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $since = now()->subHours($hours);

        $stats = [
            'window_hours' => $hours,
            'total_events' => BroadcastEvent::where('created_at', '>=', $since)->count(),
            'events_per_hour' => round(BroadcastEvent::where('created_at', '>=', $since)->count() / max(1, $hours), 2),
            'delivered_ratio' => $this->deliveredRatio($since),
            'by_event' => BroadcastEvent::where('created_at', '>=', $since)
                ->select('event', DB::raw('count(*) as total'))
                ->groupBy('event')
                ->orderByDesc('total')
                ->pluck('total', 'event')
                ->all(),
            'avg_recipients' => round((float) BroadcastEvent::where('created_at', '>=', $since)->avg('channel_count') ?? 0, 2),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Ventana', "{$hours} h"],
                ['Eventos emitidos', $stats['total_events']],
                ['Eventos/hora', $stats['events_per_hour']],
                ['Destinatarios promedio', $stats['avg_recipients']],
                ['Ratio delivered/total', $stats['delivered_ratio']],
            ]
        );

        if ($stats['by_event']) {
            $this->newLine();
            $this->info('Por evento:');
            $this->table(
                ['Evento', 'Total'],
                collect($stats['by_event'])->map(fn ($total, $event) => [$event, $total])->values()->all()
            );
        }

        return self::SUCCESS;
    }

    private function deliveredRatio($since): string
    {
        $total = BroadcastEvent::where('created_at', '>=', $since)->count();
        if ($total === 0) {
            return '0%';
        }

        $delivered = BroadcastEvent::where('created_at', '>=', $since)->where('delivered', true)->count();

        return round(($delivered / $total) * 100, 1).'%';
    }
}
