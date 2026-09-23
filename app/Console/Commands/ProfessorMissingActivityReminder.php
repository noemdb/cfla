<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Notifications\ProfessorMissingActivityNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProfessorMissingActivityReminder extends Command
{
    protected $signature = 'professors:notify-missing-activities
        {--dry-run : Simula el envío sin persistir notificaciones}
        {--lapso= : ID del lapso a evaluar (por defecto: lapso vigente)}';

    protected $description = 'Notifica a los profesores que no tienen al menos una actividad en sus pevaluaciones asignadas (lunes)';

    public function handle(): int
    {
        $lapso = $this->option('lapso')
            ? Lapso::find((int) $this->option('lapso'))
            : Lapso::current();

        if (! $lapso) {
            $this->error('No se encontró lapso vigente ni el lapso indicado.');

            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $this->info('Lapso evaluado: '.$lapso->name.' (ID '.$lapso->id.')'.($isDryRun ? ' [dry-run]' : ''));

        // Profesores activos con usuario activo y al menos una pevaluación en el lapso vigente
        // con pestudio activo y planning_module true (mismo criterio que el índice del profesor)
        $profesores = Profesor::query()
            ->where('status_active', 'true')
            ->whereHas('user', fn ($q) => $q->where('is_active', 'enable')->where('is_profesor', true))
            ->whereHas('pevaluacions', function ($q) use ($lapso) {
                $q->where('lapso_id', $lapso->id)
                  ->whereNull('pevaluacions.deleted_at')
                  ->whereHas('pensum.pestudio', fn ($pq) => $pq->where('status_active', 'true')->where('planning_module', true));
            })
            ->with(['user', 'pevaluacions' => fn ($q) => $q->where('lapso_id', $lapso->id)->whereNull('deleted_at')->withCount('activities')])
            ->get();

        $notified = 0;
        $skipped = 0;

        foreach ($profesores as $profesor) {
            $user = $profesor->user;
            if (! $user) {
                continue;
            }

            $pevaluaciones = $profesor->pevaluacions;
            $total = $pevaluaciones->count();
            if ($total === 0) {
                continue;
            }

            // Profesores sin al menos una actividad: aquellos donde ninguna pevaluación tiene activities_count > 0
            // Pero el requerimiento "no tengan al menos una activity asociada a sus pevaluacions" se interpreta como:
            // el profesor no tiene NINGUNA actividad en NINGUNA de sus pevaluaciones del lapso.
            $hasAnyActivity = $pevaluaciones->contains(fn ($pev) => ($pev->activities_count ?? 0) > 0);

            if ($hasAnyActivity) {
                $skipped++;
                continue;
            }

            // Alternativa estricta (si se requiere por pevaluación): contar cuántas pevaluaciones sin actividad
            // $missing = $pevaluaciones->filter(fn($pev) => ($pev->activities_count ?? 0) === 0)->count();
            // if ($missing === 0) continue; // tiene al menos una por pevaluación

            $message = 'No tienes actividades registradas para el lapso '.$lapso->name.'. '
                .'Tienes '.$total.' plan(es) de evaluación asignado(s) sin actividades. Por favor registra al menos una actividad.';

            $url = route('app.profesors.activities.index', ['lapso_id' => $lapso->id]);

            if ($isDryRun) {
                $this->line("  [dry-run] Profesor #{$profesor->id} ({$user->username}) – {$user->email} – {$total} pevaluaciones sin actividad");
            } else {
                try {
                    app(NotificationService::class)->notifyUsers(
                        [$user],
                        new ProfessorMissingActivityNotification(
                            message: $message,
                            url: $url,
                            missingCount: $total,
                            totalPevaluaciones: $total,
                            lapsoName: $lapso->name,
                            lapsoId: $lapso->id,
                        )
                    );
                } catch (\Throwable $e) {
                    Log::warning('ProfessorMissingActivityReminder: fallo al notificar', [
                        'profesor_id' => $profesor->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            $notified++;
        }

        $this->info("Profesores evaluados: ".count($profesores)." | Notificados: {$notified} | Omitidos (ya tienen actividad): {$skipped}");

        return self::SUCCESS;
    }
}
