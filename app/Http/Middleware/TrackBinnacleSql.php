<?php

namespace App\Http\Middleware;

use App\Services\Binnacle\SqlQueryAuditor;
use Closure;
use Illuminate\Http\Request;

/**
 * Audita consultas SQL de la request (expansión binnacle).
 *
 * Se aplica SOLO en las rutas académicas marcadas (profesor/coordinación/
 * leadership), nunca globalmente. Arranca el auditor al inicio y lo vuelca
 * al terminar la request; las entradas sql_* se emiten síncronas
 * (config/binnacle.php#sync_event_types).
 */
class TrackBinnacleSql
{
    public function __construct(private SqlQueryAuditor $auditor) {}

    public function handle(Request $request, Closure $next)
    {
        $this->auditor->start();

        try {
            return $next($request);
        } finally {
            $this->auditor->flush();
        }
    }
}
