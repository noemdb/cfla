<?php

namespace App\Services\Planning;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lógica compartida para copiar activities + achievements entre Pevaluaciones.
 *
 * Usada por el comando `activity:copy` y por el wizard Livewire del módulo
 * de Planificación. La base ORIGEN nunca se modifica; el DESTINO siempre es
 * la conexión por defecto salvo indicación contraria.
 */
class ActivityCopyService
{
    /**
     * 1 = DB_CONNECTION (actual), 2 = s2526 (período anterior).
     */
    public function resolveSourceConnection(string $source): ?string
    {
        return match ($source) {
            '1' => (string) config('database.default'),
            '2' => 's2526',
            default => null,
        };
    }

    public function targetConnection(?string $override = null): string
    {
        return (string) ($override ?: config('database.default'));
    }

    /**
     * Huella para detectar copias ya existentes en el destino.
     */
    public function fingerprint(Activity $activity): string
    {
        return implode('|', [
            trim((string) $activity->topic),
            trim((string) $activity->thematic),
            $this->toDate($activity->finicial),
            $this->toDate($activity->ffinal),
        ]);
    }

    private function toDate(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return trim((string) $value);
        }
    }

    public function findPevaluacion(string $connection, int $id): ?Pevaluacion
    {
        return Pevaluacion::on($connection)
            ->with('pensum.asignatura', 'pensum.grado', 'seccion', 'lapso', 'profesor')
            ->find($id);
    }

    /**
     * Buscador de Pevaluaciones en una conexión (para los selects del wizard).
     */
    public function searchPevaluacions(string $connection, string $search = '', int $limit = 30, mixed $pestudioId = null, mixed $gradoId = null)
    {
        $query = Pevaluacion::on($connection)
            ->with('pensum.asignatura', 'seccion.grado', 'profesor', 'lapso')
            ->withCount('activities')
            ->orderBy('id', 'desc');

        if ($pestudioId) {
            $query->whereHas('pensum', fn ($q) => $q->where('pestudio_id', $pestudioId));
        }

        if ($gradoId) {
            $query->whereHas('seccion', fn ($q) => $q->where('grado_id', $gradoId));
        }

        $search = trim($search);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pevaluacions.id', $search)
                    ->orWhereHas('pensum.asignatura', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                    ->orWhereHas('profesor', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('lastname', 'like', "%{$search}%"))
                    ->orWhereHas('seccion', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * Planes de estudio de una conexión, activos e inactivos.
     */
    public function listPestudios(string $connection)
    {
        return \App\Models\app\Academy\Pestudio::on($connection)
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'status_active']);
    }

    /**
     * Grados de una conexión (activos e inactivos), opcionalmente de un plan.
     */
    public function listGrados(string $connection, mixed $pestudioId = null)
    {
        $query = \App\Models\app\Academy\Grado::on($connection)->with('pestudio:id,name');

        if ($pestudioId) {
            $query->where('pestudio_id', $pestudioId);
        }

        return $query->orderBy('order')->orderBy('name')->get(['id', 'name', 'pestudio_id', 'status_active']);
    }

    /**
     * Simulación (dry-run): no escribe nada. Retorna origen, destino,
     * actividades a copiar y omitidas, más conteos.
     *
     * @throws \InvalidArgumentException
     */
    public function preview(int $fromId, int $toId, string $source = '2', ?string $targetConnection = null): array
    {
        $sourceConnection = $this->resolveSourceConnection($source);
        $target = $this->targetConnection($targetConnection);

        if ($sourceConnection === null) {
            throw new \InvalidArgumentException('Fuente de datos inválida. Usa 1 (actual) o 2 (S2526).');
        }

        if (! $fromId || ! $toId) {
            throw new \InvalidArgumentException('Debes indicar el origen y el destino.');
        }

        if ($fromId === $toId && $sourceConnection === $target) {
            throw new \InvalidArgumentException('La Pevaluación origen y destino no pueden ser la misma.');
        }

        $from = $this->findPevaluacion($sourceConnection, $fromId);
        $to = $this->findPevaluacion($target, $toId);

        if (! $from || ! $to) {
            throw new \InvalidArgumentException('Pevaluación origen o destino no encontrada.');
        }

        $sourceActivities = Activity::on($sourceConnection)
            ->with('achievements')
            ->where('pevaluacion_id', $from->id)
            ->orderBy('finicial')
            ->orderBy('id')
            ->get();

        $existing = Activity::on($target)
            ->where('pevaluacion_id', $to->id)
            ->get(['topic', 'thematic', 'finicial', 'ffinal'])
            ->mapWithKeys(fn ($a) => [$this->fingerprint($a) => true]);

        $toCopy = collect();
        $skipped = collect();

        foreach ($sourceActivities as $activity) {
            if ($existing->has($this->fingerprint($activity))) {
                $skipped->push($activity);
            } else {
                $toCopy->push($activity);
            }
        }

        return [
            'from' => $from,
            'to' => $to,
            'source' => $source,
            'sourceConnection' => $sourceConnection,
            'targetConnection' => $target,
            'toCopy' => $toCopy,
            'skipped' => $skipped,
            'achievementsToCopy' => $toCopy->sum(fn ($a) => $a->achievements->count()),
        ];
    }

    /**
     * Copia real dentro de una transacción en el destino.
     *
     * @throws \InvalidArgumentException|\Throwable
     */
    public function copy(int $fromId, int $toId, string $source = '2', ?string $targetConnection = null): array
    {
        $preview = $this->preview($fromId, $toId, $source, $targetConnection);
        $target = $preview['targetConnection'];

        $copiedActivities = 0;
        $copiedAchievements = 0;
        $skippedActivities = $preview['skipped']->count();
        $details = [];

        DB::connection($target)->beginTransaction();

        try {
            // Recargar fingerprints dentro de la transacción por seguridad.
            $existing = Activity::on($target)
                ->where('pevaluacion_id', $preview['to']->id)
                ->get(['topic', 'thematic', 'finicial', 'ffinal'])
                ->mapWithKeys(fn ($a) => [$this->fingerprint($a) => true]);

            foreach ($preview['toCopy'] as $sourceActivity) {
                if ($existing->has($this->fingerprint($sourceActivity))) {
                    $skippedActivities++;
                    $details[] = ['id' => $sourceActivity->id, 'topic' => $sourceActivity->topic, 'status' => 'skipped'];

                    continue;
                }

                // Releer con achievements en la conexión fuente.
                $full = Activity::on($preview['sourceConnection'])->with('achievements')->find($sourceActivity->id);
                if (! $full) {
                    continue;
                }

                $copy = $full->replicate();
                $copy->setConnection($target);
                $copy->pevaluacion_id = $preview['to']->id;
                $copy->comments = null;
                $copy->save();

                foreach ($full->achievements as $achievement) {
                    $achievementCopy = $achievement->replicate();
                    $achievementCopy->setConnection($target);
                    $achievementCopy->activity_id = $copy->id;
                    $achievementCopy->save();
                    $copiedAchievements++;
                }

                $existing->put($this->fingerprint($copy), true);
                $copiedActivities++;
                $details[] = ['id' => $sourceActivity->id, 'new_id' => $copy->id, 'topic' => $sourceActivity->topic, 'status' => 'copied'];
            }

            DB::connection($target)->commit();
        } catch (\Throwable $e) {
            DB::connection($target)->rollBack();

            throw $e;
        }

        return [
            'from' => $preview['from'],
            'to' => $preview['to'],
            'sourceConnection' => $preview['sourceConnection'],
            'targetConnection' => $target,
            'copiedActivities' => $copiedActivities,
            'copiedAchievements' => $copiedAchievements,
            'skippedActivities' => $skippedActivities,
            'details' => $details,
        ];
    }
}
