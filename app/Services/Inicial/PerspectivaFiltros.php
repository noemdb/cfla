<?php

namespace App\Services\Inicial;

use Illuminate\Support\Collection;

/**
 * Datos de los filtros (GET) de las tres perspectivas de revisión.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ UNA CLASE
 * ─────────────────────────────────────────────────────────────────────────────
 * Los tres índices (Evaluación, Planificación, Académico) ofrecen los mismos
 * filtros: docente, grado, sección y —en Evaluación— lapso. El legacy cada uno
 * repetía sus consultas y además el legacy de Evaluación leía `seccion_id` sin
 * ofrecerlo en el formulario, así que el coordinator podía filtrar por sección
 * sin poder elegirla (bug "filtros de sección uniformes", F5).
 *
 * Aquí la lista de docentes, grados, secciones y periodos sale de un sitio, y
 * las secciones dependen SIEMPRE del grado elegido: una sección de bachillerato
 * nunca se ofrece en el desplegable de Educación Inicial.
 */
final class PerspectivaFiltros
{
    /**
     * Docentes con carga académica en Educación Inicial, como `id => etiqueta`.
     *
     * Se Cruzan `pevaluacions → pensums → grados` en vez de leer un flag del
     * profesor: el mismo criterio que usa `EifinalkContext`, para que el
     * desplegable y los listados no puedan discrepar sobre quién es docente de
     * Inicial.
     *
     * @return Collection<int, string>
     */
    public function profesores(): Collection
    {
        return \App\Models\app\Academy\Profesor::query()
            ->select('profesors.id', 'profesors.name', 'profesors.lastname')
            ->distinct()
            ->join('pevaluacions', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->where('grados.pestudio_id', config('inicial.pestudio_id'))
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->orderBy('profesors.lastname')
            ->orderBy('profesors.name')
            ->get()
            ->mapWithKeys(fn ($profesor) => [
                $profesor->id => trim("{$profesor->lastname}, {$profesor->name}"),
            ]);
    }

    /**
     * Grados de Educación Inicial (3 grupos de edad), `id => nombre`.
     *
     * @return Collection<int, string>
     */
    public function grados(): Collection
    {
        return \App\Models\app\Academy\Grado::query()
            ->where('pestudio_id', config('inicial.pestudio_id'))
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Secciones de un grado, `id => nombre`. Vacío si no se eligió grado: sin
     * grado no hay secciones válidas que ofrecer.
     *
     * @return Collection<int, string>
     */
    public function secciones(?int $gradoId): Collection
    {
        if (! $gradoId) {
            return collect();
        }

        return \App\Models\app\Academy\Seccion::query()
            ->where('grado_id', $gradoId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Periodos, `id => nombre`. Lo usa el plan de evaluación, cuyo filtro real
     * es el lapso (y no la sección).
     *
     * @return Collection<int, string>
     */
    public function lapsos(): Collection
    {
        return \App\Models\app\Academy\Lapso::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
