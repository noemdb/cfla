<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Retención de la tabla `notifications` (ítem 14).
 *
 * Solo crece: cada actividad, cambio de horario o recordatorio deja una fila
 * para siempre, y tanto la campana como el listado administrativo paginan
 * sobre ella. Este comando borra las ya leídas más antiguas que el umbral.
 *
 * Qué NO se pierde al borrar: la bitácora sigue teniendo la traza de cada
 * emisión (`notification.dispatched`) y de cada lectura (`notification.read`),
 * así que el histórico de auditoría no depende de esta tabla.
 *
 * Las NO leídas son la bandeja de trabajo del usuario: no se tocan salvo que
 * se pidan explícitamente con `--purge-unread-days`.
 */
class NotificationPrune extends Command
{
    protected $signature = 'notifications:prune
        {--days= : Días de antigüedad para borrar las LEÍDAS (default: config notifications.retention_days; 0 = no borrar)}
        {--purge-unread-days=0 : Días para borrar también las NO leídas (0 = conservarlas)}
        {--chunk=1000 : Filas por lote}
        {--dry-run : Contar y mostrar lo que se borraría, sin borrar}';

    protected $description = 'Borra notificaciones leídas más antiguas que el umbral de retención';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = max(1, (int) $this->option('chunk'));
        $readDays = $this->option('days') !== null
            ? (int) $this->option('days')
            : (int) config('notifications.retention_days', 180);
        $unreadDays = (int) $this->option('purge-unread-days');

        $this->newLine();
        $this->info('Retención de notificaciones'.($dryRun ? ' (dry-run, no se borra nada)' : ''));

        // OJO: cada contador usa su propio builder. Los métodos de Builder
        // MUTAN la instancia (`whereNotNull()->count()` deja el where pegado),
        // así que reutilizar uno para varios recuentos acumularía condiciones
        // y el segundo Saldría siempre a cero.
        $this->line(sprintf(
            '  Filas actuales: %d   leídas: %d   sin leer: %d',
            DB::table('notifications')->count(),
            DB::table('notifications')->whereNotNull('read_at')->count(),
            DB::table('notifications')->whereNull('read_at')->count()
        ));

        $readCutoff = $readDays > 0 ? now()->subDays($readDays) : null;
        $unreadCutoff = $unreadDays > 0 ? now()->subDays($unreadDays) : null;

        $deletedRead = $readCutoff
            ? $this->prune(
                DB::table('notifications')->whereNotNull('read_at')->where('created_at', '<', $readCutoff),
                $chunk,
                $dryRun,
                'leídas'
            )
            : 0;

        $deletedUnread = $unreadCutoff
            ? $this->prune(
                DB::table('notifications')->whereNull('read_at')->where('created_at', '<', $unreadCutoff),
                $chunk,
                $dryRun,
                'NO leídas'
            )
            : 0;

        $this->newLine();
        if ($readCutoff === null && $unreadCutoff === null) {
            $this->warn('  No hay nada que podar (--days=0 y sin --purge-unread-days).');

            return self::SUCCESS;
        }

        $this->line(sprintf(
            '  Leídas anteriores a %s: %d',
            $readCutoff?->toDateString() ?? '—',
            $deletedRead
        ));
        if ($unreadCutoff) {
            $this->line(sprintf('  NO leídas anteriores a %s: %d', $unreadCutoff->toDateString(), $deletedUnread));
        }

        $this->line('  Filas restantes: '.DB::table('notifications')->count());

        if ($dryRun) {
            $this->newLine();
            $this->comment('  Dry-run: nada se ha borrado. Reejecuta sin --dry-run para aplicar.');
        }

        return self::SUCCESS;
    }

    /**
     * Borra por lotes para no bloquear la tabla con un DELETE masivo.
     */
    private function prune($query, int $chunk, bool $dryRun, string $label): int
    {
        $total = (int) (clone $query)->count();

        if ($total === 0) {
            return 0;
        }

        if ($dryRun) {
            return $total;
        }

        $deleted = 0;

        do {
            $ids = (clone $query)->limit($chunk)->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deleted += DB::table('notifications')->whereIn('id', $ids)->delete();
        } while ($deleted < $total);

        $this->line("  (podadas {$label} en lotes de {$chunk})");

        return $deleted;
    }
}
