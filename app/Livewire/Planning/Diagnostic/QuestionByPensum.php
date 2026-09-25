<?php

namespace App\Livewire\Planning\Diagnostic;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionByPensum extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public ?int $pensumId = null;
    public ?int $pestudioId = null;
    public ?int $gradoId = null;
    public ?int $pevaluacionId = null;
    public ?int $grupoEstableId = null;
    public string $search = '';
    public bool $showInactive = true;
    public int $summaryPerPage = 10;

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
        $this->pevaluacionId = null;
        $this->grupoEstableId = null;
        $this->pensumId = null;
        $this->expandedGroups = [];
        $this->resetPage('pensumSummaryPage');
    }

    public function updatedGradoId(): void
    {
        $this->pevaluacionId = null;
        $this->grupoEstableId = null;
        $this->pensumId = null;
        $this->expandedGroups = [];
        $this->resetPage('pensumSummaryPage');
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
                $this->grupoEstableId = $pev->grupo_estable_id;
            }
        }
        $this->expandedGroups = [];
    }

    public function updatedGrupoEstableId(): void
    {
        $this->pevaluacionId = null;
        if ($this->grupoEstableId) {
            $pev = Pevaluacion::where('grupo_estable_id', $this->grupoEstableId)
                ->whereIn('pensum_id', \App\Models\app\Instrument\DiagQuestion::query()->select('pensum_id')->distinct()->pluck('pensum_id'))
                ->first();
            if ($pev) {
                $this->pensumId = $pev->pensum_id;
                $this->pestudioId = $pev->pensum?->pestudio_id ?? $this->pestudioId;
                $this->gradoId = $pev->pensum?->grado_id ?? $this->gradoId;
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
        // si el grupo seleccionado ya no tiene pevaluacion en este pensum, limpiarlo
        if ($this->grupoEstableId && ! Pevaluacion::where('pensum_id', $this->pensumId)->where('grupo_estable_id', $this->grupoEstableId)->exists()) {
            $this->grupoEstableId = null;
        }
        // sincroniza pestudio/grado desde el pensum elegido
        if ($this->pensumId) {
            $p = Pensum::find($this->pensumId);
            if ($p) {
                $this->pestudioId = $p->pestudio_id;
                $this->gradoId = $p->grado_id;
            }
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPageIfNeeded();
    }

    public function updatedSummaryPerPage(): void
    {
        $this->resetPage('pensumSummaryPage');
    }

    public function clearFilters(): void
    {
        $this->pensumId = null;
        $this->pestudioId = null;
        $this->gradoId = null;
        $this->pevaluacionId = null;
        $this->grupoEstableId = null;
        $this->expandedGroups = [];
        $this->resetPage('pensumSummaryPage');
    }

    public function deactivateFiltered(): void
    {
        $grupoId = $this->grupoEstableId ?? Pevaluacion::find($this->pevaluacionId)?->grupo_estable_id;
        if (! $grupoId || ! $this->pensumId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Seleccione un grupo estable y un pensum.']);
            return;
        }

        $query = DiagQuestion::where('pensum_id', $this->pensumId)
            ->when(!$this->showInactive, fn ($qq) => $qq->where('activo', true))
            ->when($this->search !== '', fn ($qq) => $qq->where('pregunta', 'like', '%' . $this->search . '%'));

        $count = (clone $query)->count();
        if ($count === 0) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'No hay preguntas filtradas para desactivar.']);
            return;
        }

        $query->update(['activo' => false]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Desactivadas {$count} pregunta(s) filtradas."]);
    }

    public function activateFiltered(): void
    {
        $grupoId = $this->grupoEstableId ?? Pevaluacion::find($this->pevaluacionId)?->grupo_estable_id;
        if (! $grupoId || ! $this->pensumId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Seleccione un grupo estable y un pensum.']);
            return;
        }

        $query = DiagQuestion::where('pensum_id', $this->pensumId)
            ->when($this->search !== '', fn ($qq) => $qq->where('pregunta', 'like', '%' . $this->search . '%'));

        $count = (clone $query)->where('activo', false)->count();
        if ($count === 0) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'No hay preguntas desactivadas filtradas para activar.']);
            return;
        }

        $query->where('activo', false)->update(['activo' => true]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Activadas {$count} pregunta(s) filtradas."]);
    }

    public function deactivateGroup(int $grupoKey): void
    {
        if (! $this->pensumId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Seleccione un pensum.']);
            return;
        }
        $query = DiagQuestion::where('pensum_id', $this->pensumId)
            ->when(!$this->showInactive, fn ($qq) => $qq->where('activo', true))
            ->when($this->search !== '', fn ($qq) => $qq->where('pregunta', 'like', '%' . $this->search . '%'));
        $count = (clone $query)->count();
        if ($count === 0) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'No hay preguntas para desactivar en este grupo.']);
            return;
        }
        $query->update(['activo' => false]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Desactivadas {$count} pregunta(s) del grupo."]);
    }

    public function activateGroup(int $grupoKey): void
    {
        if (! $this->pensumId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Seleccione un pensum.']);
            return;
        }
        $query = DiagQuestion::where('pensum_id', $this->pensumId)
            ->when($this->search !== '', fn ($qq) => $qq->where('pregunta', 'like', '%' . $this->search . '%'));
        $count = (clone $query)->where('activo', false)->count();
        if ($count === 0) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'No hay preguntas desactivadas para activar en este grupo.']);
            return;
        }
        $query->where('activo', false)->update(['activo' => true]);
        $this->dispatch('notify', ['type' => 'success', 'message' => "Activadas {$count} pregunta(s) del grupo."]);
    }

    public function toggleQuestion(int $questionId): void
    {
        $question = DiagQuestion::find($questionId);
        if (! $question) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pregunta no encontrada.']);
            return;
        }
        $question->activo = ! (bool) $question->activo;
        $question->save();
        $this->dispatch('notify', ['type' => 'success', 'message' => $question->activo ? 'Pregunta activada.' : 'Pregunta desactivada.']);
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

        // Pevaluaciones (grupo_estable) anidadas: pestudio→grado→pevaluacion — solo de pensums con preguntas y solo con grupo NOT NULL
        $pevaluacionsOptions = Pevaluacion::with(['grupoEstable', 'pensum.asignatura', 'seccion.grado', 'profesor', 'lapso'])
            ->whereIn('pensum_id', $pensumIdsWithQuestions)
            ->whereNotNull('grupo_estable_id');
        if ($this->pestudioId) {
            $pevaluacionsOptions->whereIn('pensum_id', Pensum::where('pestudio_id', $this->pestudioId)->pluck('id'));
        }
        if ($this->gradoId) {
            $pevaluacionsOptions->whereIn('pensum_id', Pensum::where('grado_id', $this->gradoId)->pluck('id'));
        }
        $pevaluacionsOptions = $pevaluacionsOptions->orderBy('grupo_estable_id')->orderBy('seccion_id')->get();

        // Grupos estables distintos para el dropdown wireUI — con profesor, asignatura y grado/sección del primer pevaluación del grupo
        $grupoEstablesOptions = GrupoEstable::whereIn('id', $pevaluacionsOptions->pluck('grupo_estable_id')->unique()->filter())
            ->orderBy('code')->get(['id', 'code', 'name']);
        $grupoSelectOptions = $grupoEstablesOptions->mapWithKeys(function ($g) use ($pevaluacionsOptions) {
            $pev = $pevaluacionsOptions->firstWhere('grupo_estable_id', $g->id);
            $asig = $pev?->pensum?->asignatura?->name ?? '?';
            $gradoSec = $pev?->seccion ? (($pev->seccion->grado?->name ?? '?').'/'.$pev->seccion->name) : ($pev?->pensum?->grado?->name ?? '?');
            $prof = $pev?->profesor ? ($pev->profesor->lastname.' '.$pev->profesor->name) : '?';
            $label = $g->code.' — '.$g->name.' · '.$asig.' · '.$gradoSec.' · '.$prof;
            return [$g->id => $label];
        })->toArray();

        // Pensums filtrados por pestudio→grado→pevaluacion/grupo (anidados, pensum deshabilitado por defecto hasta elegir grado)
        $pensumQuery = Pensum::with(['asignatura', 'grado', 'pestudio'])
            ->whereIn('id', $pensumIdsWithQuestions);

        if ($this->pestudioId) {
            $pensumQuery->where('pestudio_id', $this->pestudioId);
        }
        if ($this->gradoId) {
            $pensumQuery->where('grado_id', $this->gradoId);
        }
        if ($this->pevaluacionId) {
            $pensumIdFromPev = Pevaluacion::find($this->pevaluacionId)?->pensum_id;
            if ($pensumIdFromPev) {
                $pensumQuery->where('id', $pensumIdFromPev);
            }
        }
        if ($this->grupoEstableId) {
            $pensumIdsByGrupo = Pevaluacion::where('grupo_estable_id', $this->grupoEstableId)->pluck('pensum_id')->unique();
            $pensumQuery->whereIn('id', $pensumIdsByGrupo);
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

            // Pevaluaciones de ese pensum → agrupar por grupo_estable_id (respetando pevaluacion/grupo seleccionados)
            $pevsQuery = Pevaluacion::with(['grupoEstable', 'seccion.grado', 'profesor', 'lapso'])
                ->where('pensum_id', $this->pensumId);
            if ($this->pevaluacionId) {
                $pevsQuery->where('id', $this->pevaluacionId);
            }
            if ($this->grupoEstableId) {
                $pevsQuery->where('grupo_estable_id', $this->grupoEstableId);
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
            // Sin filtro de pensum pero respetando pestudio→grado para el resumen
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

            // Paginación del resumen por pensum
            $perPage = max(1, (int) $this->summaryPerPage);
            $currentPage = LengthAwarePaginator::resolveCurrentPage('pensumSummaryPage');
            $total = $summary->count();
            $items = $summary->forPage($currentPage, $perPage)->values();
            $summary = new LengthAwarePaginator($items, $total, $perPage, $currentPage, ['path' => request()->url(), 'pageName' => 'pensumSummaryPage']);
        }

        // compatibilidad: seccionsOptions ya no se usa (eliminada), se mantiene vacía
        $seccionsOptions = collect();

        return view('livewire.planning.diagnostic.question-by-pensum', [
            'pestudiosOptions' => $pestudiosOptions,
            'gradosOptions' => $gradosOptions,
            'seccionsOptions' => $seccionsOptions,
            'pevaluacionsOptions' => $pevaluacionsOptions,
            'grupoEstablesOptions' => $grupoEstablesOptions,
            'grupoSelectOptions' => $grupoSelectOptions,
            'pensumOptions' => $pensumOptions,
            'selectedPensum' => $selectedPensum,
            'questions' => $questions,
            'grouped' => $grouped,
            'summary' => $summary,
            'totalQuestions' => $totalQuestions,
        ]);
    }
}
