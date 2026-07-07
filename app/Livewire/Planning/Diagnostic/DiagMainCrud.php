<?php

namespace App\Livewire\Planning\Diagnostic;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagReferent;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class DiagMainCrud extends Component
{
    use WireUiActions;

    // ─── Dialog states ───────────────────────────────────────────
    public $showDialog = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailItem = null;

    // ─── Form fields ────────────────────────────────────────────────
    public $name = '';
    public $description = '';
    public $token = '';
    public $active = true;
    public $lapso_id = '';
    public $pestudio_id = '';
    public $referent_id = '';

    // ─── Select lists ───────────────────────────────────────────────
    public $lapsos = [];
    public $pestudios = [];
    public $referents = [];

    // ─── Mount ──────────────────────────────────────────────────────
    public function mount()
    {
        $this->lapsos = Lapso::orderBy('id')->get(['id', 'name']);
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
        $this->referents = DiagReferent::where('active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
    }

    // ─── CRUD actions ──────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $currentLapso = Lapso::current();
        $this->lapso_id = $currentLapso?->id ?? $this->lapsos->first()?->id ?? '';
        $this->active = true;
        $this->editingId = null;
        $this->showDialog = true;
    }

    public function edit(int $id)
    {
        $diag = DiagMain::findOrFail($id);
        $this->editingId = $id;
        $this->name = $diag->name;
        $this->description = $diag->description;
        $this->token = $diag->token ?? '';
        $this->active = (bool) $diag->active;
        $this->lapso_id = $diag->lapso_id;
        $this->pestudio_id = $diag->pestudio_id;
        $this->referent_id = $diag->referent_id;
        $this->showDialog = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'token' => 'nullable|string|max:100',
            'active' => 'boolean',
            'lapso_id' => 'nullable|integer|exists:lapsos,id',
            'pestudio_id' => 'nullable|integer|exists:pestudios,id',
            'referent_id' => 'nullable|integer|exists:diag_referents,id',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'token' => $this->token ?: null,
            'active' => $this->active,
            'lapso_id' => $this->lapso_id ?: null,
            'pestudio_id' => $this->pestudio_id ?: null,
            'referent_id' => $this->referent_id ?: null,
        ];

        if ($this->editingId) {
            $diag = DiagMain::findOrFail($this->editingId);
            $diag->update($data);
            $this->notification()->success(
                'Diagnóstico actualizado',
                'El diagnóstico se actualizó correctamente.'
            );
        } else {
            DiagMain::create($data);
            $this->notification()->success(
                'Diagnóstico creado',
                'El diagnóstico se creó correctamente.'
            );
        }

        $this->closeDialog();
    }

    public function confirmDelete(int $id)
    {
        $diag = DiagMain::findOrFail($id);
        $questionsCount = DiagQuestion::where('diag_main_id', $id)->count();

        $message = "Esta acción eliminará permanentemente el diagnóstico \"{$diag->name}\".";
        if ($questionsCount > 0) {
            $message .= " Tiene {$questionsCount} pregunta(s) asociadas que también se eliminarán.";
        }

        $this->dialog()->confirm([
            'title' => 'Eliminar Diagnóstico',
            'description' => $message . ' No se puede deshacer.',
            'icon' => 'error',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'delete',
                'params' => $id,
                'color' => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function delete(int $id)
    {
        $diag = DiagMain::findOrFail($id);
        $diag->delete();

        $this->notification()->success(
            'Diagnóstico eliminado',
            'El diagnóstico se eliminó correctamente.'
        );
    }

    public function toggleActive(int $id)
    {
        $diag = DiagMain::findOrFail($id);
        $diag->active = !$diag->active;
        $diag->save();

        $this->notification()->success(
            $diag->active ? 'Diagnóstico activado' : 'Diagnóstico desactivado',
            $diag->active
                ? 'El diagnóstico ahora está activo.'
                : 'El diagnóstico ha sido desactivado.'
        );
    }

    // ─── Dialog helpers ────────────────────────────────────────────

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->resetForm();
    }

    public function detail(int $id)
    {
        $this->detailItem = DiagMain::with(['lapso', 'pestudio', 'referent'])
            ->withCount(['questions', 'sessions'])
            ->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->detailItem = null;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->token = '';
        $this->active = true;
        $this->lapso_id = '';
        $this->pestudio_id = '';
        $this->referent_id = '';
        $this->editingId = null;
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        $diagMains = DiagMain::with(['lapso', 'pestudio', 'referent'])
            ->withCount(['questions', 'sessions'])
            ->orderBy('id')
            ->get();

        return view('livewire.planning.diagnostic.diag-main-crud', [
            'diagMains' => $diagMains,
        ]);
    }
}
