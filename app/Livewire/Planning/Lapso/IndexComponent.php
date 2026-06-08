<?php

namespace App\Livewire\Planning\Lapso;

use App\Models\app\Academy\Lapso;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // Modal modes
    public $modeIndex = true;
    public $modeForm = false;

    // Editing flag
    public $isEditing = false;
    public $lapso_id;

    // Form fields
    public $code, $code_sm, $name;
    public $finicial, $ffinal, $academic_start_date, $date_cutnote;
    public $date_start_census, $time_start_census, $date_end_census, $time_end_census;
    public $date_preclosing, $time_preclosing;
    public $status_last = 'false';

    // Preview
    public $previewMode = false;
    public $previewLapso = null;

    // Search & filters
    public $search = '';

    // Confirm delete
    public $confirmDeleteId = null;

    protected $rules = [
        'code' => 'required|string|max:10',
        'code_sm' => 'required|string|max:4',
        'name' => 'required|string|max:255',
        'finicial' => 'required|date',
        'ffinal' => 'required|date|after_or_equal:finicial',
        'academic_start_date' => 'nullable|date',
        'date_cutnote' => 'nullable|date',
        'date_start_census' => 'nullable|date',
        'time_start_census' => 'nullable|string|max:8',
        'date_end_census' => 'nullable|date|after_or_equal:date_start_census',
        'time_end_census' => 'nullable|string|max:8',
        'date_preclosing' => 'nullable|date',
        'time_preclosing' => 'nullable|string|max:8',
        'status_last' => 'required|in:true,false',
    ];

    public function mount()
    {
        $this->close();
    }

    public function render()
    {
        $query = Lapso::withCount(['profesor_guias', 'pevaluacions']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('code_sm', 'like', "%{$this->search}%");
            });
        }

        $lapsos = $query->orderBy('id')
            ->paginate(15);

        return view('livewire.planning.lapso.index-component', [
            'lapsos' => $lapsos,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->lapso_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $lapso = Lapso::findOrFail($id);
        $this->lapso_id = $lapso->id;
        $this->code = $lapso->code;
        $this->code_sm = $lapso->code_sm;
        $this->name = $lapso->name;
        $this->finicial = $lapso->finicial ? $lapso->finicial->format('Y-m-d') : null;
        $this->ffinal = $lapso->ffinal ? $lapso->ffinal->format('Y-m-d') : null;
        $this->academic_start_date = $lapso->academic_start_date ? $lapso->academic_start_date->format('Y-m-d') : null;
        $this->date_cutnote = $lapso->date_cutnote ? $lapso->date_cutnote->format('Y-m-d') : null;
        $this->date_start_census = $lapso->date_start_census ? $lapso->date_start_census->format('Y-m-d') : null;
        $this->time_start_census = $lapso->time_start_census;
        $this->date_end_census = $lapso->date_end_census ? $lapso->date_end_census->format('Y-m-d') : null;
        $this->time_end_census = $lapso->time_end_census;
        $this->date_preclosing = $lapso->date_preclosing ? $lapso->date_preclosing->format('Y-m-d') : null;
        $this->time_preclosing = $lapso->time_preclosing;
        $this->status_last = $lapso->status_last;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campo ENUM('true','false')
        $val = $this->status_last;
        $this->status_last = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';

        $this->validate();

        $data = [
            'code' => $this->code,
            'code_sm' => $this->code_sm,
            'name' => $this->name,
            'finicial' => $this->finicial,
            'ffinal' => $this->ffinal,
            'academic_start_date' => $this->academic_start_date ?: null,
            'date_cutnote' => $this->date_cutnote ?: null,
            'date_start_census' => $this->date_start_census ?: null,
            'time_start_census' => $this->time_start_census ?: null,
            'date_end_census' => $this->date_end_census ?: null,
            'time_end_census' => $this->time_end_census ?: null,
            'date_preclosing' => $this->date_preclosing ?: null,
            'time_preclosing' => $this->time_preclosing ?: null,
            'status_last' => $this->status_last,
        ];

        if ($this->isEditing) {
            $lapso = Lapso::findOrFail($this->lapso_id);
            $lapso->update($data);
            $this->notification()->success(
                title: 'Lapso Actualizado',
                description: 'El lapso se actualizó correctamente.'
            );
        } else {
            Lapso::create($data);
            $this->notification()->success(
                title: 'Lapso Creado',
                description: 'El lapso se creó correctamente.'
            );
        }

        $this->close();
        $this->modeIndex = true;
    }

    // ─── DELETE ──────────────────────────────────────────────────

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
    }

    public function destroy()
    {
        $lapso = Lapso::withCount(['profesor_guias', 'pevaluacions'])
            ->findOrFail($this->confirmDeleteId);

        if ($lapso->profesor_guias_count > 0 || $lapso->pevaluacions_count > 0) {
            $errors = [];
            if ($lapso->pevaluacions_count > 0) {
                $errors[] = "{$lapso->pevaluacions_count} carga(s) académica(s)";
            }
            if ($lapso->profesor_guias_count > 0) {
                $errors[] = "{$lapso->profesor_guias_count} profesor(es) guía";
            }
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: 'El lapso tiene ' . implode(' y ', $errors) . ' asociados. Elimínelos primero.'
            );
            $this->cancelDelete();
            return;
        }

        $lapso->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Lapso Eliminado',
            description: 'El lapso se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewLapso = Lapso::withCount(['profesor_guias', 'pevaluacions'])
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewLapso = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'code', 'code_sm', 'name',
            'finicial', 'ffinal', 'academic_start_date', 'date_cutnote',
            'date_start_census', 'time_start_census',
            'date_end_census', 'time_end_census',
            'date_preclosing', 'time_preclosing',
        ]);
        $this->status_last = 'false';
    }

    public function close()
    {
        $this->modeIndex = false;
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
