<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Poda de las reclamaciones de idempotencia (`notification_dedupe`).
 *
 * La clave de una reclamación incluye el `bucket` temporal, así que en cuanto
 * avanza la ventana anti-spam la clave es otra y la fila vieja ya no bloquea
 * nada: solo ocupa espacio. Por eso se poda por antigüedad con un margen
 * holgado (30 días por defecto), que cubre de sobra la ventana más larga
 * usada por los emisores, sin tener que acoplar este comando a cada ventana
 * configurada.
 */
class NotificationDedupePrune extends Command
{
    protected $signature = 'notifications:prune-dedupe
        {--days=30 : Antigüedad mínima en días para conservar una reclamación}
        {--dry-run : Solo mostrar cuántas se borrarían}';

    protected $description = 'Elimina las reclamaciones de idempotencia de notificaciones caducadas';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);
        $table = NotificationService::DEDUPE_TABLE;

        $count = (int) DB::table($table)->where('created_at', '<', $cutoff)->count();

        if ($this->option('dry-run')) {
            $this->info("[dry-run] Se eliminarían {$count} reclamaciones anteriores a {$cutoff->toDateTimeString()}");

            return self::SUCCESS;
        }

        $deleted = DB::table($table)->where('created_at', '<', $cutoff)->delete();

        $this->info("Reclamaciones eliminadas: {$deleted} (anteriores a {$cutoff->toDateTimeString()})");

        return self::SUCCESS;
    }
}
