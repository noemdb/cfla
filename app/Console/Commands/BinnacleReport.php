<?php

namespace App\Console\Commands;

use App\Models\BinnacleEntry;
use App\Models\User;
use App\Notifications\BinnacleDailyReportNotification;
use App\Services\Binnacle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Resumen diario de la bitácora (mejora propuesta #5).
 *
 * Genera un resumen del día anterior — eventos critical/alert, accesos y top
 * actores — y lo envía por email a los roles admin/is_director (matriz RBAC
 * BINNACLE-001 §6). El envío queda auditado en la propia bitácora con
 * event_type=binnacle_report_sent.
 *
 * Uso:
 *   php8.2 artisan binnacle:report                  # resumen del día anterior
 *   php8.2 artisan binnacle:report --date=2026-08-14
 *   php8.2 artisan binnacle:report --email=ops@colegio.edu.ve --no-notify
 */
class BinnacleReport extends Command
{
    protected $signature = 'binnacle:report
        {--date= : Fecha del resumen (Y-m-d, por defecto ayer)}
        {--email= : Email adicional explícito}
        {--no-notify : No enviar emails, solo generar/imprimir el resumen}';

    protected $description = 'Genera y envía el resumen diario de la bitácora a admin/dirección';

    public function handle(): int
    {
        $date = $this->option('date') ?: now()->subDay()->toDateString();
        $range = [
            "{$date} 00:00:00",
            "{$date} 23:59:59",
        ];

        $data = $this->buildReport($date, $range);

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Fecha', $data['date']],
                ['Entradas totales', $data['total_entries']],
                ['Critical/alert', $data['critical_count']],
                ['Accesos', $data['access_count']],
            ]
        );

        if (! $this->option('no-notify')) {
            $this->send($date, $data);
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReport(string $date, array $range): array
    {
        $base = BinnacleEntry::whereBetween('created_at', $range);

        $critical = (clone $base)->whereIn('event_severity', ['critical', 'alert']);
        $access = (clone $base)->where('event_type', 'access');

        $topActors = DB::table('binnacle_entries')
            ->whereBetween('created_at', $range)
            ->whereNotNull('subject_identifier')
            ->groupBy('subject_identifier')
            ->selectRaw('subject_identifier as identifier, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        $recentCritical = (clone $critical)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['event_severity', 'title', 'created_at'])
            ->map(fn (BinnacleEntry $e) => [
                'severity' => $e->event_severity,
                'title' => $e->title,
                'time' => $e->created_at?->format('H:i'),
            ])
            ->all();

        return [
            'date' => $date,
            'total_entries' => $base->count(),
            'critical_count' => $critical->count(),
            'access_count' => $access->count(),
            'top_actors' => $topActors,
            'recent_critical' => $recentCritical,
        ];
    }

    private function send(string $date, array $data): void
    {
        // Destinatarios: alert_recipients config (si existe) + roles admin/director.
        $recipients = config('binnacle.alert_recipients', []);

        if ($this->option('email')) {
            $recipients[] = $this->option('email');
        }

        if (! empty($recipients)) {
            Notification::route('mail', array_values(array_unique($recipients)))
                ->notify(new BinnacleDailyReportNotification($data));
        }

        $users = User::query()
            ->where(fn ($q) => $q->where('is_admin', true)->orWhere('is_director', true))
            ->where('is_active', 'enable')
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new BinnacleDailyReportNotification($data));
        }

        // Meta-auditoría: el envío queda registrado en la propia bitácora.
        Binnacle::log('binnacle_report_sent', [
            'title' => 'Resumen diario de bitácora enviado',
            'description' => "Resumen del {$date} enviado a admin/dirección.",
            'category' => 'system',
            'severity' => 'info',
            'metadata' => [
                'date' => $date,
                'recipients' => count($recipients) + $users->count(),
                'critical_count' => $data['critical_count'],
            ],
        ]);
    }
}
