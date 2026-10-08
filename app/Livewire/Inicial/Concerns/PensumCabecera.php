<?php

namespace App\Livewire\Inicial\Concerns;

use App\Models\app\Academy\Pensum;
use Illuminate\Support\Collection;

/**
 * Campo opcional `pensum_id` (área de aprendizaje) en la cabecera de un
 * documento, con su select escopado al docente.
 *
 * El componente que lo usa declara `profesor_id` (docente autenticado) y
 * `lapsoActivoId()` (lapso en curso). El select solo muestra los pensums que
 * aparecen en las `pevaluaciones` del docente; `save()` debe validar pertenencia
 * (ver componente), porque el `exists` del FormRequest no basta.
 */
trait PensumCabecera
{
    /** Áreas de aprendizaje (pensums) del docente para el select de cabecera. */
    public Collection $listPensum;

    /**
     * Áreas de aprendizaje disponibles para el docente: solo las que aparecen
     * en SUS `pevaluaciones` del lapso en curso (pestudio 6).
     *
     * @param  int|null  $gradoId  acota a un grado (al elegirlo en el form).
     */
    protected function loadPensums($gradoId = null): Collection
    {
        if (! $this->profesor_id) {
            return collect();
        }

        return Pensum::select('pensums.id')
            ->selectRaw('CONCAT(asignaturas.name, " [", asignaturas.code, "] ", grados.code) as fullname_lg')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pevaluacions', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.profesor_id', $this->profesor_id)
            ->where('pevaluacions.lapso_id', $this->lapsoActivoId())
            ->where('grados.pestudio_id', config('inicial.pestudio_id'))
            ->when($gradoId, fn ($q) => $q->where('pensums.grado_id', $gradoId))
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->orderBy('grados.name')
            ->orderBy('asignaturas.name')
            ->distinct()
            ->pluck('fullname_lg', 'pensums.id');
    }
}
