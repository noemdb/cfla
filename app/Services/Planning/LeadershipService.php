<?php

namespace App\Services\Planning;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeadershipService
{
    /**
     * Segundos de vida de la caché de scope. Corto a propósito: preferimos
     * refrescar seguido antes que arriesgar que un líder vea datos de un
     * área que ya no le pertenece. La invalidación por evento (ver
     * AreaConocimientoObserver, ADR-007) cubre el caso de reasignación
     * inmediata; este TTL es solo una red de seguridad.
     */
    private const CACHE_TTL_SECONDS = 300;

    /** Memoización en memoria: evita recalcular 2 veces en el mismo request. */
    private ?Collection $memoizedAreaIds = null;
    private ?Collection $memoizedAsignaturaIds = null;

    public function __construct(
        protected User $user
    ) {}

    // ─── SCOPE HELPERS ──────────────────────────────────────────

    /**
     * Áreas de conocimiento donde este user es líder.
     *
     * Si es admin, retorna una colección vacía como señal interna de
     * "sin restricción" — NUNCA se usa para poblar un `whereIn` directamente
     * en ese caso, ver `isUnrestricted()`. El admin de verdad (todas las áreas
     * para mostrar en el dashboard) se resuelve aparte en `dashboardMetrics()`.
     */
    public function getAssignedAreaIds(): Collection
    {
        if ($this->memoizedAreaIds !== null) {
            return $this->memoizedAreaIds;
        }

        if ($this->isUnrestricted()) {
            return $this->memoizedAreaIds = collect();
        }

        return $this->memoizedAreaIds = Cache::remember(
            $this->cacheKey('areas'),
            self::CACHE_TTL_SECONDS,
            fn () => AreaConocimiento::where('leader_id', $this->user->id)->pluck('id')
        );
    }

    /**
     * IDs de asignaturas bajo su liderazgo.
     * Cadena: AreaConocimiento → CampoConocimiento → Asignatura
     */
    public function getAssignedAsignaturaIds(): Collection
    {
        if ($this->memoizedAsignaturaIds !== null) {
            return $this->memoizedAsignaturaIds;
        }

        if ($this->isUnrestricted()) {
            return $this->memoizedAsignaturaIds = collect();
        }

        $areaIds = $this->getAssignedAreaIds();
        if ($areaIds->isEmpty()) {
            return $this->memoizedAsignaturaIds = collect();
        }

        return $this->memoizedAsignaturaIds = Cache::remember(
            $this->cacheKey('asignaturas'),
            self::CACHE_TTL_SECONDS,
            fn () => Asignatura::whereHas('areasConocimiento', function ($q) use ($areaIds) {
                $q->whereIn('area_conocimientos.id', $areaIds);
            })->pluck('id')
        );
    }

    /**
     * True si el usuario no debe tener ninguna restricción de scope
     * (actualmente: solo admins). Centralizado aquí para que agregar un
     * futuro rol con el mismo privilegio (p.ej. "coordinador general")
     * solo requiera tocar este método.
     */
    private function isUnrestricted(): bool
    {
        return (bool) $this->user->is_admin;
    }

    /**
     * Aplica scope de liderazgo a una query de Pensums.
     */
    public function scopePensums(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: null);
    }

    /**
     * Aplica scope de liderazgo a una query de Pevaluacions.
     * Cadena: Pevaluacion → Pensum → Asignatura
     */
    public function scopePevaluacions(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: 'pensum');
    }

    /**
     * Aplica scope de liderazgo a una query de Activities.
     * Cadena: Activity → Pevaluacion → Pensum → Asignatura
     */
    public function scopeActivities(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: 'pevaluacion.pensum');
    }

    /**
     * Helper DRY compartido por los 3 métodos `scope*()` de arriba. El único
     * eje de variación entre ellos es cuántos saltos de relación hay que dar
     * hasta llegar a la columna `asignatura_id` — todo lo demás (bypass admin,
     * manejo de colección vacía, nombre de columna) es idéntico.
     *
     * @param  Builder  $query
     * @param  string|null  $relationPath  Ruta dot-notation hasta el modelo
     *         que tiene `asignatura_id` (null = la propia query ya es ese modelo).
     */
    private function applyAsignaturaScope(Builder $query, ?string $relationPath): Builder
    {
        if ($this->isUnrestricted()) {
            return $query; // admin: sin restricción, no tocar la query
        }

        $asignaturaIds = $this->getAssignedAsignaturaIds();

        if ($relationPath === null) {
            return $query->whereIn('asignatura_id', $asignaturaIds);
        }

        return $query->whereHas($relationPath, function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        });
    }

    /**
     * Profesores asociados a las áreas del líder.
     * Cadena: Profesor → Pevaluacion → Pensum → Asignatura
     */
    public function getAssignedProfesores(): Collection
    {
        if ($this->isUnrestricted()) {
            return Profesor::query()->distinct()->get();
        }

        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) return collect();

        return Profesor::whereHas('pevaluacions.pensum', function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        })->distinct()->get();
    }

    /**
     * Guarda de autorización explícita para acciones críticas (comentar,
     * aprobar/rechazar una actividad). Reemplaza el patrón de "silencio +
     * 0 resultados" del draft original (ver ADR-008): lanza una excepción
     * estándar de Laravel que el framework ya sabe convertir en 403,
     * en vez de dejar cada componente reimplementar su propio chequeo.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function assertCanAccessAsignatura(int $asignaturaId): void
    {
        if ($this->isUnrestricted()) {
            return;
        }

        if (!$this->getAssignedAsignaturaIds()->contains($asignaturaId)) {
            abort(403, 'No tienes permiso para operar sobre actividades fuera de tus áreas asignadas.');
        }
    }

    // ─── MÉTRICAS DEL DASHBOARD ─────────────────────────────────

    public function dashboardMetrics(): array
    {
        // Para el dashboard SÍ necesitamos los IDs reales de área incluso
        // si el usuario es admin (para listar las tarjetas de área), así
        // que aquí resolvemos explícito en vez de usar isUnrestricted().
        $areaIds = $this->isUnrestricted()
            ? AreaConocimiento::pluck('id')
            : $this->getAssignedAreaIds();

        if ($areaIds->isEmpty()) {
            return $this->emptyMetrics();
        }

        $pevaQuery = Pevaluacion::query();
        $activityQuery = Activity::query();
        $profesorQuery = Profesor::query();

        if (!$this->isUnrestricted()) {
            $pevaQuery = $this->scopePevaluacions($pevaQuery);
            $activityQuery = $this->scopeActivities($activityQuery);
            $profesorQuery = $profesorQuery->whereHas('pevaluacions.pensum', function ($q) {
                $q->whereIn('asignatura_id', $this->getAssignedAsignaturaIds());
            });
        }

        return [
            'total_areas' => $areaIds->count(),
            'total_asignaturas' => $this->isUnrestricted()
                ? Asignatura::count()
                : $this->getAssignedAsignaturaIds()->count(),
            'total_pevas' => $pevaQuery->count(),
            'activities_in_review' => $activityQuery->where('status', 0)->count(),
            'total_profesores' => $profesorQuery->distinct()->count(),
            'areas' => AreaConocimiento::whereIn('id', $areaIds)
                ->withCount('campo_conocimientos')
                ->get()
                ->map(fn($area) => [
                    'id' => $area['id'] ?? $area->id,
                    'name' => $area->name,
                    'code' => $area->code,
                    'description' => $area->description,
                    'total_asignaturas' => $area->campo_conocimientos_count,
                ]),
        ];
    }

    protected function emptyMetrics(): array
    {
        return [
            'total_areas' => 0,
            'total_asignaturas' => 0,
            'total_pevas' => 0,
            'activities_in_review' => 0,
            'total_profesores' => 0,
            'areas' => [],
        ];
    }

    /** Prefijo de caché namespaced por usuario, para invalidación selectiva. */
    private function cacheKey(string $suffix): string
    {
        return "leadership:{$this->user->id}:{$suffix}";
    }
}
