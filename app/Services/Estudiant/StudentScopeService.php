<?php

namespace App\Services\Estudiant;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StudentScopeService
{
    protected ?Estudiant $estudiant = null;
    protected ?Collection $seccionIds = null;
    protected ?Collection $gradoIds = null;
    protected ?Inscripcion $inscripcion = null;
    protected ?Seccion $seccion = null;

    public function __construct(
        protected User $user
    ) {
        $this->estudiant = Estudiant::where('user_id', $user->id)->first();

        if ($this->estudiant) {
            $this->inscripcion = $this->estudiant->inscripcion;
            $this->seccion = $this->inscripcion?->seccion;
        }
    }

    /**
     * Obtener el estudiante asociado al user.
     */
    public function getEstudiant(): ?Estudiant
    {
        return $this->estudiant;
    }

    /**
     * Obtener la inscripción activa del estudiante.
     */
    public function getInscripcion(): ?Inscripcion
    {
        return $this->inscripcion;
    }

    /**
     * IDs de secciones del estudiante (vía inscripción activa).
     * Retorna colección vacía si no hay inscripción o estudiante.
     */
    public function getSeccionIds(): Collection
    {
        if ($this->seccionIds !== null) {
            return $this->seccionIds;
        }

        if (!$this->seccion) {
            return $this->seccionIds = collect();
        }

        return $this->seccionIds = collect([$this->seccion->id]);
    }

    /**
     * IDs de grados del estudiante (vía sección de la inscripción).
     * Retorna colección vacía si no hay inscripción activa.
     */
    public function getGradoIds(): Collection
    {
        if ($this->gradoIds !== null) {
            return $this->gradoIds;
        }

        if (!$this->seccion || !$this->seccion->grado_id) {
            return $this->gradoIds = collect();
        }

        return $this->gradoIds = collect([$this->seccion->grado_id]);
    }

    /**
     * ID del grado del estudiante como entero, o null.
     */
    public function getGradoId(): ?int
    {
        return $this->seccion?->grado_id;
    }

    /**
     * Query scope para Pevaluacions visibles al estudiante.
     * Cuando no hay sección asignada, retorna query que no trae resultados.
     */
    public function scopePevaluacions(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('seccion_id', $seccionIds);
    }

    /**
     * Query scope para Activities con publicación visible.
     * Cuando no hay sección asignada, retorna query que no trae resultados.
     */
    public function scopeActivities(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
            ->whereHas('lmsPublication', fn($q) => $q->visibleNow());
    }

    /**
     * Verifica si una actividad es visible para este estudiante.
     */
    public function isActivityVisible(Activity $activity): bool
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return false;
        }

        return $activity->status
            && $activity->lmsPublication?->isVisibleToStudents()
            && $activity->pevaluacion
            && $seccionIds->contains($activity->pevaluacion->seccion_id);
    }

    /**
     * Query scope para LmsActivityResource visibles.
     * Cuando no hay sección asignada, retorna query que no trae resultados.
     */
    public function scopeResources(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('is_visible', true)
            ->whereHas('activity.pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds));
    }

    /**
     * IDs de pensums asociados al estudiante.
     * Retorna colección vacía si no hay grado asignado.
     */
    public function getPensumIds(): Collection
    {
        $gradoIds = $this->getGradoIds();
        if ($gradoIds->isEmpty()) {
            return collect();
        }

        return Pensum::whereIn('grado_id', $gradoIds)->pluck('id');
    }

    /**
     * Pensums completos con asignatura del grado del estudiante.
     */
    public function getPensumsWithAsignatura(): Collection
    {
        $gradoIds = $this->getGradoIds();
        if ($gradoIds->isEmpty()) {
            return collect();
        }

        return Pensum::whereIn('grado_id', $gradoIds)
            ->with('asignatura')
            ->orderBy('asignatura_id')
            ->get();
    }

    /**
     * Datos completos de inscripción del estudiante.
     * Retorna null si no hay estudiante o inscripción.
     */
    public function getInscripcionData(): ?array
    {
        if (!$this->estudiant || !$this->seccion) {
            return null;
        }

        $grado = $this->seccion->grado;
        $pestudio = $grado?->pestudio;

        return [
            'estudiant'   => $this->estudiant,
            'inscripcion' => $this->inscripcion,
            'seccion'     => $this->seccion,
            'grado'       => $grado,
            'pestudio'    => $pestudio,
            'peducativo'  => $pestudio?->peducativo,
        ];
    }
}
