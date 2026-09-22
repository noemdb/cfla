<?php

namespace App\Livewire\Planning\Diagnostic;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Instrument\DiagQuestion;
use Livewire\Component;

class QuestionByPensum extends Component
{
    public ?int $pensumId = null;
    public ?int $pestudioId = null;
    public ?int $gradoId = null;
    public ?int $seccionId = null;
    public ?int $pevaluacionId = null;
    public string $search = '';
    public bool $showInactive = false;

    /** @var array<int,bool> grupos expandidos key = grupo_estable_id (0=sin grupo) */
    public array $expandedGroups = [];

    public function mount(?int $pensumId = null): void
    {
        if ($pensumId) {
            $this->pensumId = $pensumId;
        } elseif (request()->query('pensumId')) {
            $this->pensumId = (int) request()->query('pensumId');
        }
    }

    public function updatedPestudioId(): void
    {
        $this->gradoId = null;
        $this->seccionId = null;
        $this->pevaluacionId = null;
        // si el pensum actual ya no pertenece al pestudio, limpiarlo
        if ($this->pensumId && Pensum::find($this->pensumId)?->pestudio_id !== $this->pestudioId) {
            $this->pensumId = null;
        }
        $this->expandedGroups = [];
    }

    public function updatedGradoId(): void
    {
        $this->seccionId = null;
        $this->pevaluacionId = null;
        if ($this->pensumId && Pensum::find($this->pensumId)?->grado_id !== $this->gradoId) {
            $this->pensumId = null;
        }
        $this->expandedGroups = [];
    }

    public function updatedSeccionId(): void
    {
        $this->pevaluacionId = null;
        // seccion filtra vía pevaluacion; si el pensum no tiene pevaluacion en esa sección, limpiar selección
        if ($this->pensumId && $this->seccionId) {
            $has = Pevaluacion::where('pensum_id', $this->pensumId)->where('seccion_id', $this->seccionId)->exists();
            if (! $has) {
                $this->pensumId = null;
            }
        }
        $this->expandedGroups = [];
    }

    public function updatedPevaluacionId(): void
    {
        // al elegir pevaluación (grupo_estable) anida el pensum automáticamente
        if ($this->pevaluacionId) {
            $pev = Pevaluacion::find($this->pevaluacionId);
            if ($pev) {
                $this->pensumId = $pev->pensum_id;
                $this->pestudioId = $pev->pensum?->pestudio_id ?? $this->pestudioId;
                $this->gradoId = $pev->pensum?->grado_id ?? $this->gradoId;
                $this->seccionId = $pev->seccion_id;
            }
        }
        $this->expandedGroups = [];
    }

    public function updatedPensumId(): void
    {
        $this->expandedGroups = [];
        $this->resetPageIfNeeded();
        // si el pevaluacion seleccionado ya no pertenece al pensum, limpiarlo
        if ($this->pevaluacionId && Pevaluacion::find($this->pevaluacionId)?->pensum_id !== $this->pensumId) {
            $this->pevaluacionId = null;
        }
        // sincroniza pestudio/grado desde el pensum elegido
        if ($this->pensumId) {
            $p = Pensum::find($this->pensumId);
            if ($p) {
                $this->pestudioId = $p->pestudio_id;
                $this->gradoId = $p->grado_id;
                // seccion se mantiene si ya estaba y pertenece al grado, si no se limpia
                if ($this->seccionId && Seccion::find($this->seccionId)?->grado_id !== $this->gradoId) {
                    $this->seccionId = null;
                }
            }
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPageIfNeeded();
    }

    public function clearFilters(): void
    {
        $this->pensumId = null;
        $this->pestudioId = null;
        $this->gradoId = null;
        $this->seccionId = null;
        $this->pevaluacionId = null;
        $this->expandedGroups = [];
    }

    public function toggleGroup(int $grupoKey): void
    {
        $this->expandedGroups[$grupoKey] = ! ($this->expandedGroups[$grupoKey] ?? true);
    }

    private function resetPageIfNeeded(): void
    {
        // placeholder si se añade paginación
    }

    public function render()
    {
        // ── Pensums con preguntas (base para filtros) ──
        $pensumIdsWithQuestions = DiagQuestion::query()
            ->select('pensum_id')
            ->distinct()
            ->pluck('pensum_id');

        // Pestudios que tienen al menos un pensum con preguntas
        $pestudiosOptions = Pestudio::whereIn('id',
            Pensum::whereIn('id', $pensumIdsWithQuestions)->select('pestudio_id')
        )->orderBy('code')->get(['id', 'code', 'name']);

        // Grados anidados a pestudio (pestudio->grados) — solo activos status_active='true'
        $gradosOptions = collect();
        if ($this->pestudioId) {
            $gradosOptions = Grado::where('pestudio_id', $this->pestudioId)
                ->where('status_active', 'true')
                ->whereIn('id', Pensum::whereIn('id', $pensumIdsWithQuestions)->pluck('grado_id'))
                ->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
        } else {
            $gradosOptions = Grado::where('status_active', 'true')
                ->whereIn('id', Pensum::whereIn('id', $pensumIdsWithQuestions)->pluck('grado_id'))
                ->orderBy('pestudio_id')->orderBy('order')->get(['id', 'name', 'code', 'pestudio_id']);
        }

        // Secciones anidadas a grado (grado->seccions) — pestudio->grados->seccions — solo activas status_active='true'
        $seccionsOptions = collect();
        if ($this->gradoId) {
            $seccionsOptions = Seccion::where('grado_id', $this->gradoId)
                ->where('status_active', 'true')
                ->orderBy('name')->get(['id', 'name', 'grado_id']);
        } elseif ($this->pestudioId) {
            $gradoIds = Grado::where('pestudio_id', $this->pestudioId)->where('status_active', 'true')->pluck('id');
            $seccionsOptions = Seccion::whereIn('grado_id', $gradoIds)->where('status_active', 'true')->orderBy('name')->get(['id', 'name', 'grado_id']);
        } else {
            $gradoIds = Pensum::whereIn('id', $pensumIdsWithQuestions)->pluck('grado_id')->unique();
            $gradoIds = Grado::whereIn('id', $gradoIds)->where('status_active', 'true')->pluck('id');
            $seccionsOptions = Seccion::whereIn('grado_id', $gradoIds)->where('status_active', 'true')->orderBy('name')->get(['id', 'name', 'grado_id']);
        }

        // Pevaluaciones (grupo_estable) anidadas: pestudio→grado→seccion→pevaluacion — solo de pensums con preguntas
        $pevaluacionsOptions = Pevaluacion::with(['grupoEstable', 'pensum.asignatura', 'seccion.grado', 'profesor', 'lapso'])
            ->whereIn('pensum_id', $pensumIdsWithQuestions);
        if ($this->pestudioId) {
            $pevaluacionsOptions->whereIn('pensum_id', Pensum::where('pestudio_id', $this->pestudioId)->pluck('id'));
        }
        if ($this->gradoId) {
            $pensumIdsGrado = Pensum::where('grado_id', $this->gradoId)->pluck('id');
            $seccionIdsGrado = Seccion::where('grado_id', $this->gradoId)->where('status_active', 'true')->pluck('id');
            $pevaluacionsOptions->where(function ($q) use ($pensumIdsGrado, $seccionIdsGrado) {
                $q->whereIn('pensum_id', $pensumIdsGrado)->orWhereIn('seccion_id', $seccionIdsGrado);
            });
        }
        if ($this->seccionId) {
            $pevaluacionsOptions->where('seccion_id', $this->seccionId);
        }
        $pevaluacionsOptions = $pevaluacionsOptions->orderBy('grupo_estable_id')->orderBy('seccion_id')->get();

        // Pensums filtrados por pestudio→grado→seccion→pevaluacion (anidados, va después de seccionId y pevaluacionId)
        $pensumQuery = Pensum::with(['asignatura', 'grado', 'pestudio'])
            ->whereIn('id', $pensumIdsWithQuestions);

        if ($this->pestudioId) {
            $pensumQuery->where('pestudio_id', $this->pestudioId);
        }
        if ($this->gradoId) {
            $pensumQuery->where('grado_id', $this->gradoId);
        }
        if ($this->seccionId) {
            $pensumIdsBySeccion = Pevaluacion::where('seccion_id', $this->seccionId)->pluck('pensum_id')->unique();
            $pensumQuery->whereIn('id', $pensumIdsBySeccion);
        }
        if ($this->pevaluacionId) {
            $pensumIdFromPev = Pevaluacion::find($this->pevaluacionId)?->pensum_id;
            if ($pensumIdFromPev) {
                $pensumQuery->where('id', $pensumIdFromPev);
            }
        }

        $pensumOptions = $pensumQuery->orderBy('pestudio_id')->orderBy('grado_id')->get()
            ->sortBy(fn (Pensum $p) => ($p->pestudio?->code ?? '') . ($p->grado?->order ?? 0) . ($p->asignatura?->name ?? ''))
            ->values();

        $selectedPensum = $this->pensumId
            ? Pensum::with(['asignatura', 'grado', 'pestudio'])->find($this->pensumId)
            : null;

        $questions = collect();
        $grouped = collect();
        $summary = collect();
        $totalQuestions = 0;

        if ($selectedPensum) {
            $q = DiagQuestion::with(['competency', 'indicator', 'options', 'diagMain'])
                ->where('pensum_id', $this->pensumId)
                ->when(!$this->showInactive, fn ($qq) => $qq->where('activo', true))
                ->when($this->search !== '', fn ($qq) => $qq->where('pregunta', 'like', '%' . $this->search . '%'))
                ->orderBy('orden')
                ->orderBy('id');

            $questions = $q->get();
            $totalQuestions = $questions->count();

            // Pevaluaciones de ese pensum → agrupar por grupo_estable_id (respetando filtros anidados)
            $pevsQuery = Pevaluacion::with(['grupoEstable', 'seccion.grado', 'profesor', 'lapso'])
                ->where('pensum_id', $this->pensumId);
            if ($this->seccionId) {
                $pevsQuery->where('seccion_id', $this->seccionId);
            }
            if ($this->pevaluacionId) {
                $pevsQuery->where('id', $this->pevaluacionId);
            }
            $pevs = $pevsQuery->orderBy('lapso_id')->orderBy('seccion_id')->get();

            $groupedPevs = $pevs->groupBy(fn (Pevaluacion $p) => $p->grupo_estable_id ?? 0);

            // mapa grupo_estable_id => GrupoEstable model
            $grupoIds = $groupedPevs->keys()->filter(fn ($k) => $k !== 0)->values();
            $gruposMap = GrupoEstable::whereIn('id', $grupoIds)->get()->keyBy('id');

            foreach ($groupedPevs as $grupoKey => $pevGroup) {
                $grupo = $grupoKey !== 0 ? ($gruposMap[$grupoKey] ?? null) : null;
                $key = (int) $grupoKey;
                // expandir por defecto
                if (! array_key_exists($key, $this->expandedGroups)) {
                    $this->expandedGroups[$key] = true;
                }
                $grouped->push((object) [
                    'key' => $key,
                    'grupo' => $grupo, // null = sin grupo estable
                    'pevs' => $pevGroup,
                    'pevsCount' => $pevGroup->count(),
                    'questions' => $questions,
                    'isExpanded' => (bool) ($this->expandedGroups[$key] ?? true),
                ]);
            }

            // Si el pensum no tiene pevaluaciones, igual mostrar un único bloque "Sin grupo estable" con las preguntas
            if ($grouped->isEmpty()) {
                $key = 0;
                if (! array_key_exists($key, $this->expandedGroups)) {
                    $this->expandedGroups[$key] = true;
                }
                $grouped->push((object) [
                    'key' => 0,
                    'grupo' => null,
                    'pevs' => collect(),
                    'pevsCount' => 0,
                    'questions' => $questions,
                    'isExpanded' => (bool) ($this->expandedGroups[$key] ?? true),
                ]);
            }
        } else {
            // Sin filtro de pensum pero respetando pestudio→grado→seccion para el resumen
            $pensumIdsFiltered = $pensumOptions->pluck('id');
            $summary = DiagQuestion::selectRaw('pensum_id, COUNT(*) as total_q, SUM(CASE WHEN activo=1 THEN 1 ELSE 0 END) as activas')
                ->whereIn('pensum_id', $pensumIdsFiltered)
                ->groupBy('pensum_id')
                ->orderByDesc('total_q')
                ->get()
                ->map(function ($row) {
                    $pensum = Pensum::with(['asignatura', 'grado', 'pestudio'])->find($row->pensum_id);
                    $pevsCount = Pevaluacion::where('pensum_id', $row->pensum_id)->count();
                    $gruposCount = Pevaluacion::where('pensum_id', $row->pensum_id)
                        ->whereNotNull('grupo_estable_id')
                        ->distinct('grupo_estable_id')
                        ->count('grupo_estable_id');
                    return (object) [
                        'pensum' => $pensum,
                        'pensum_id' => $row->pensum_id,
                        'total_q' => (int) $row->total_q,
                        'activas' => (int) $row->activas,
                        'pevsCount' => $pevsCount,
                        'gruposCount' => $gruposCount,
                    ];
                })
                ->filter(fn ($r) => $r->pensum !== null)
                ->values();
        }

        return view('livewire.planning.diagnostic.question-by-pensum', [
            'pestudiosOptions' => $pestudiosOptions,
            'gradosOptions' => $gradosOptions,
            'seccionsOptions' => $seccionsOptions,
            'pevaluacionsOptions' => $pevaluacionsOptions,
            'pensumOptions' => $pensumOptions,
            'selectedPensum' => $selectedPensum,
            'questions' => $questions,
            'grouped' => $grouped,
            'summary' => $summary,
            'totalQuestions' => $totalQuestions,
        ]);
    }
}
