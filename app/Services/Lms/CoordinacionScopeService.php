<?php

namespace App\Services\Lms;

use App\Models\User;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CoordinacionScopeService
{
    /**
     * TTL de caché para scopes. Suficientemente largo para que el dashboard
     * no golpee la DB en cada request, pero lo bastante corto para no quedar
     * desactualizado si se reasigna un coordinador.
     */
    private const CACHE_TTL_SECONDS = 300;

    /** Memoización en memoria: evita recalcular 2 veces en el mismo request. */
    protected ?Collection $peducativoIds = null;
    protected ?Collection $pestudioIds = null;

    public function __construct(
        protected User $user
    ) {}

    /**
     * True si el usuario no debe tener restricción de scope
     * (actualmente: solo admins).
     */
    private function isUnrestricted(): bool
    {
        return (bool) $this->user->is_admin;
    }

    /**
     * IDs de Peducativos donde el user es manager.
     */
    public function getPeducativoIds(): Collection
    {
        if ($this->peducativoIds !== null) {
            return $this->peducativoIds;
        }

        if ($this->isUnrestricted()) {
            return $this->peducativoIds = Cache::remember(
                $this->cacheKey('peducativos-admin'),
                self::CACHE_TTL_SECONDS,
                fn () => Peducativo::where('status_active', 'true')->pluck('id')
            );
        }

        return $this->peducativoIds = Cache::remember(
            $this->cacheKey('peducativos'),
            self::CACHE_TTL_SECONDS,
            fn () => Peducativo::where('manager_id', $this->user->id)
                ->where('status_active', 'true')
                ->pluck('id')
        );
    }

    /**
     * Peducativos completos del coordinador.
     */
    public function getPeducativos(): Collection
    {
        $ids = $this->getPeducativoIds();
        if ($ids->isEmpty()) return collect();

        return Peducativo::whereIn('id', $ids)
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get();
    }

    /**
     * IDs de Pestudios de los peducativos del coordinador.
     */
    public function getPestudioIds(): Collection
    {
        if ($this->pestudioIds !== null) {
            return $this->pestudioIds;
        }

        $peducativoIds = $this->getPeducativoIds();
        if ($peducativoIds->isEmpty()) return $this->pestudioIds = collect();

        return $this->pestudioIds = Cache::remember(
            $this->cacheKey('pestudios'),
            self::CACHE_TTL_SECONDS,
            fn () => Pestudio::whereIn('peducativo_id', $peducativoIds)
                ->where('status_active', 'true')
                ->where('planning_module', 1)
                ->pluck('id')
        );
    }

    /**
     * Pestudios completos del coordinador.
     */
    public function getPestudios(): Collection
    {
        $ids = $this->getPestudioIds();
        if ($ids->isEmpty()) return collect();

        return Pestudio::whereIn('id', $ids)
            ->where('status_active', 'true')
            ->get();
    }

    /**
     * Aplica scope de peducativo a query de Pestudio.
     */
    public function scopePestudios($query)
    {
        $peducativoIds = $this->getPeducativoIds();
        if ($peducativoIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query->whereIn('peducativo_id', $peducativoIds);
    }

    /**
     * Aplica scope de peducativo a query de Pensum.
     */
    public function scopePensums($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query->whereIn('pestudio_id', $pestudioIds);
    }

    /**
     * Aplica scope de peducativo a query de Pevaluacion.
     */
    public function scopePevaluacions($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('pevaluacions.*');
    }

    /**
     * Aplica scope de peducativo a query de Activity.
     */
    public function scopeActivities($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('activities.*');
    }

    /**
     * Aplica scope de peducativo a query de LmsActivityResource.
     */
    public function scopeResources($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('activities', 'lms_activity_resources.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('lms_activity_resources.*');
    }

    /**
     * Aplica scope de peducativo a query de Profesor.
     * Cadena: Profesor → Pevaluacion → Pensum → Pestudio
     */
    public function scopeProfesores(Builder $query): Builder
    {
        if ($this->isUnrestricted()) {
            return $query; // admin: sin restricción
        }

        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query->whereHas('pevaluacions.pensum', function ($q) use ($pestudioIds) {
            $q->whereIn('pestudio_id', $pestudioIds);
        });
    }

    /**
     * Cantidad de profesores activos dentro del scope del coordinador.
     */
    public function getProfesoresCount(): int
    {
        if ($this->isUnrestricted()) {
            return Profesor::where('status_active', 'true')
                ->whereHas('pevaluacions')
                ->count();
        }

        return Cache::remember(
            $this->cacheKey('profesores-count'),
            self::CACHE_TTL_SECONDS,
            fn () => Profesor::where('status_active', 'true')
                ->whereHas('pevaluacions.pensum', function ($q) {
                    $q->whereIn('pestudio_id', $this->getPestudioIds());
                })
                ->count()
        );
    }

    /** Prefijo de caché namespaced por usuario. */
    private function cacheKey(string $suffix): string
    {
        return "coordinacion:{$this->user->id}:{$suffix}";
    }

    /**
     * Verifica si una Pevaluacion está dentro del scope del coordinador.
     */
    public function pevaluacionIsInScope(int $pevaluacionId): bool
    {
        return Pevaluacion::where('pevaluacions.id', $pevaluacionId)
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $this->getPestudioIds())
            ->exists();
    }
}
