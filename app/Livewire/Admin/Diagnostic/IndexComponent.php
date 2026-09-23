<?php

namespace App\Livewire\Admin\Diagnostic;

use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Services\Binnacle;
use Livewire\Attributes\Layout;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions;

    public string $search = '';

    public function toggleAllPestudio($pestudioId, $activate)
    {
        $this->applyBulkActivation(
            Pensum::where('pestudio_id', $pestudioId)->where('status_active', true),
            (bool) $activate,
            ['scope' => 'pestudio', 'pestudio_id' => (int) $pestudioId]
        );
    }

    public function toggleAllGrado($pestudioId, $gradoId, $activate)
    {
        $this->applyBulkActivation(
            Pensum::where('pestudio_id', $pestudioId)->where('grado_id', $gradoId)->where('status_active', true),
            (bool) $activate,
            ['scope' => 'grado', 'pestudio_id' => (int) $pestudioId, 'grado_id' => (int) $gradoId]
        );
    }

    public function toggleStatus($pensumId)
    {
        $pensum = Pensum::withCount([
            'diagQuestions as active_questions_count' => fn ($q) => $q->where('activo', true),
        ])->findOrFail($pensumId);

        // No activar un área sin preguntas: en /diagnostico no aparecería.
        if (! $pensum->status_active_diagnostic && $pensum->active_questions_count === 0) {
            $this->notification()->error(
                'Área sin preguntas',
                'No puedes activar esta área porque no tiene preguntas activas. Agrega preguntas antes de activarla.'
            );

            return;
        }

        $pensum->status_active_diagnostic = ! $pensum->status_active_diagnostic;
        $pensum->save();

        $this->notification()->success(
            'Estado Actualizado',
            'El área ha sido '.($pensum->status_active_diagnostic ? 'activada' : 'desactivada').' para el diagnóstico.'
        );
    }

    /**
     * Activa/desactiva en bloque, omitiendo áreas sin preguntas activas al
     * activar. Registra la operación en la bitácora (el update masivo por
     * query builder no dispara el observer AuditableModelObserver).
     */
    private function applyBulkActivation($query, bool $activate, array $context): void
    {
        $pensums = $query
            ->withCount(['diagQuestions as active_questions_count' => fn ($q) => $q->where('activo', true)])
            ->get();

        $ids = $activate
            ? $pensums->where('active_questions_count', '>', 0)->pluck('id')
            : $pensums->pluck('id');

        $skipped = $pensums->count() - $ids->count();

        if ($ids->isEmpty()) {
            $this->notification()->error(
                'Sin áreas para activar',
                'Ninguna de las áreas seleccionadas tiene preguntas activas. Agrega preguntas antes de activarlas.'
            );

            return;
        }

        $affected = Pensum::whereIn('id', $ids)->update(['status_active_diagnostic' => $activate]);

        Binnacle::log('diagnostic_bulk_activation', [
            'title' => 'Activación masiva de áreas de diagnóstico',
            'description' => ($activate ? 'Activadas' : 'Desactivadas')." {$affected} área(s) de diagnóstico.",
            'category' => 'user_action',
            'severity' => 'info',
            'subject' => auth()->user() ?? Binnacle::systemSubject(),
            'metadata' => $context + [
                'activate' => $activate,
                'affected' => $affected,
                'skipped_without_questions' => $skipped,
            ],
        ]);

        $this->notification()->success(
            'Actualización Masiva',
            'Se han '.($activate ? 'activado' : 'desactivado')." {$affected} área(s)."
            .($skipped > 0 ? " {$skipped} sin preguntas fueron omitidas." : '')
        );
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $search = trim($this->search);

        $pensums = Pensum::query()
            ->where('pensums.status_active', true)
            ->where(function ($q) {
                $q->whereHas('pevaluacions')->orWhereHas('diagQuestions');
            })
            ->whereHas('grado', fn ($q) => $q->where('status_active', 'true'))
            ->with(['asignatura', 'grado', 'pestudio'])
            ->withCount([
                'diagQuestions',
                'diagQuestions as active_questions_count' => fn ($q) => $q->where('activo', true),
            ])
            ->when($search !== '', function ($q) use ($search) {
                $term = '%'.$search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('asignatura', function ($a) use ($term) {
                        $a->where('name', 'like', $term)->orWhere('code', 'like', $term);
                    })->orWhereHas('grado', fn ($g) => $g->where('name', 'like', $term));
                });
            })
            ->orderBy('grado_id')
            ->orderBy('asignatura_id')
            ->get()
            ->groupBy(['pestudio_id', 'grado_id']);

        $pestudios = Pestudio::with(['grados.seccions'])
            ->where('status_active', 'true')
            ->when($search !== '', fn ($q) => $q->whereIn('id', $pensums->keys()->all()))
            ->orderBy('order', 'asc')
            ->get();

        return view('livewire.admin.diagnostic.index-component', [
            'pestudios' => $pestudios,
            'groupedPensums' => $pensums,
        ]);
    }
}
