<?php

namespace App\Livewire\Planning\ActivityLabel;

use App\Models\app\Academy\ActivityFieldLabel;
use App\Models\app\Academy\Peducativo;
use App\Services\ActivityLabelResolver;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions, WithPagination;

    // Modal modes
    public $modeIndex = true;

    public $modeForm = false;

    // Editing flag
    public $isEditing = false;

    public $label_id;

    // Form fields
    public $peducativo_id;

    public $model = ActivityFieldLabel::MODEL_ACTIVITY;

    public $field;

    public $label;

    public $placeholder;

    // Select lists
    public $peducativos = [];

    public $defaults = [];

    // Search & filters
    public $search = '';

    public $filter_peducativo = '';

    public $filter_model = '';

    public $paginate = 15;

    // Confirm reset (volver al valor por defecto de COLUMN_COMMENTS)
    public $confirmResetId = null;

    // Confirm delete
    public $confirmDeleteId = null;

    // Clone: source row + target peducativo
    public $cloneSourceId = null;

    public $cloneTargetPeducativoId = '';

    public $cloneSource = null;

    protected function rules()
    {
        $unique = Rule::unique('activity_field_labels')
            ->where(fn ($q) => $q
                ->where('peducativo_id', $this->peducativo_id)
                ->where('model', $this->model)
                ->where('field', $this->field));

        if ($this->isEditing && $this->label_id) {
            $unique->ignore($this->label_id);
        }

        return [
            'peducativo_id' => 'required|integer|exists:peducativos,id',
            'model' => 'required|in:activity,achievement',
            'field' => ['required', 'string', 'max:60', $unique],
            'label' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
        ];
    }

    public function mount()
    {
        $this->peducativos = Peducativo::where('status_active', 'true')
            ->orderBy('order')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->defaults = ActivityLabelResolver::keyedDefaults();

        // Peducativo por defecto: el primero activo (cubre el caso de uso
        // principal: editar etiquetas de un programa educativo concreto).
        $this->filter_peducativo = (string) (array_key_first($this->peducativos) ?? '');

        $this->close();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPeducativo()
    {
        $this->resetPage();
    }

    public function updatingFilterModel()
    {
        $this->resetPage();
    }

    public function updatingPaginate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityFieldLabel::with('peducativo')
            ->when($this->filter_peducativo !== '', fn ($q) => $q->where('peducativo_id', $this->filter_peducativo))
            ->when($this->filter_model !== '', fn ($q) => $q->where('model', $this->filter_model))
            ->when($this->search !== '', fn ($q) => $q->where(function ($sq) {
                $sq->where('field', 'like', "%{$this->search}%")
                    ->orWhere('label', 'like', "%{$this->search}%");
            }));

        $labels = $query
            ->join('peducativos', 'peducativos.id', '=', 'activity_field_labels.peducativo_id')
            ->orderBy('peducativos.order')
            ->orderBy('peducativos.name')
            ->orderBy('activity_field_labels.model')
            ->orderBy('activity_field_labels.field')
            ->select('activity_field_labels.*')
            ->paginate($this->paginate);

        return view('livewire.planning.activity-label.index-component', [
            'labels' => $labels,
        ]);
    }

    public function defaultFor(string $model, string $field): ?string
    {
        return $this->defaults["{$model}.{$field}"] ?? null;
    }

    public function isCustomized(ActivityFieldLabel $row): bool
    {
        $default = $this->defaultFor($row->model, $row->field);

        return $default !== null && $row->label !== $default;
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->close();
        $this->isEditing = false;
        $this->label_id = null;
        // Preseleccionar el peducativo filtrado para crear más rápido.
        $this->peducativo_id = $this->filter_peducativo !== ''
            ? (int) $this->filter_peducativo
            : (array_key_first($this->peducativos) ?? null);
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $this->close();
        $row = ActivityFieldLabel::findOrFail($id);
        $this->label_id = $row->id;
        $this->peducativo_id = $row->peducativo_id;
        $this->model = $row->model;
        $this->field = $row->field;
        $this->label = $row->label;
        $this->placeholder = $row->placeholder;
        $this->isEditing = true;
        $this->modeForm = true;
    }

    public function save()
    {
        $this->field = $this->field !== null ? trim((string) $this->field) : null;
        $this->validate();

        $data = [
            'peducativo_id' => $this->peducativo_id,
            'model' => $this->model,
            'field' => $this->field,
            'label' => trim($this->label),
            'placeholder' => $this->placeholder !== null && trim($this->placeholder) !== ''
                ? trim($this->placeholder)
                : null,
        ];

        if ($this->isEditing) {
            $row = ActivityFieldLabel::findOrFail($this->label_id);
            // Scope estricto: solo esta fila (este peducativo). Ningún otro
            // peducativo se toca en edición.
            $row->update([
                'label' => $data['label'],
                'placeholder' => $data['placeholder'],
            ]);

            $scopeName = $this->peducativos[$row->peducativo_id] ?? "#{$row->peducativo_id}";
            $this->notification()->success(
                title: 'Etiqueta Actualizada',
                description: "La etiqueta '{$row->field}' se actualizó solo en {$scopeName}."
            );
        } else {
            $row = ActivityFieldLabel::create($data);

            $this->notification()->success(
                title: 'Etiqueta Creada',
                description: "La etiqueta '{$row->field}' se creó correctamente."
            );
        }

        $this->close();
        $this->modeIndex = true;
    }

    // ─── RESET (volver al valor por defecto) ─────────────────────

    public function confirmReset($id)
    {
        $this->confirmResetId = $id;
    }

    public function cancelReset()
    {
        $this->confirmResetId = null;
    }

    public function resetToDefault()
    {
        $row = ActivityFieldLabel::findOrFail($this->confirmResetId);
        $default = $this->defaultFor($row->model, $row->field);

        // Si el campo ya no existe en el default, se conserva la fila.
        if ($default === null) {
            $this->notification()->warning(
                title: 'Sin valor por defecto',
                description: "El campo '{$row->field}' no tiene default conocido; se conserva."
            );
            $this->cancelReset();

            return;
        }

        $row->update(['label' => $default, 'placeholder' => null]);
        $this->cancelReset();

        $this->notification()->success(
            title: 'Etiqueta Restablecida',
            description: "La etiqueta '{$row->field}' volvió a su valor por defecto."
        );
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
        $row = ActivityFieldLabel::findOrFail($this->confirmDeleteId);
        $field = $row->field;
        $row->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Etiqueta Eliminada',
            description: "La etiqueta '{$field}' se eliminó. El campo vuelve a su valor por defecto; usa Sincronizar para recrearla."
        );
    }

    // ─── CLONE (copiar etiqueta a otro peducativo) ───────────────

    /**
     * Abre el diálogo de clonación (x-dialog WireUI). El cierre visual lo
     * hace el navegador con `close()`; aquí solo se fija el estado.
     */
    public function openClone($id)
    {
        $source = ActivityFieldLabel::with('peducativo')->findOrFail($id);
        $this->cloneSourceId = $source->id;
        $this->cloneSource = $source;
        // Destino por defecto: el peducativo filtrado si es distinto,
        // si no el primer otro programa activo.
        $this->cloneTargetPeducativoId = ($this->filter_peducativo !== '' && (int) $this->filter_peducativo !== $source->peducativo_id)
            ? $this->filter_peducativo
            : (string) (collect($this->peducativos)->keys()->first(fn ($pid) => (int) $pid !== $source->peducativo_id) ?? '');

        $this->dialog()->id('label-clone')->show([
            'icon' => 'question',
            'close' => false,
        ]);
    }

    public function closeClone()
    {
        $this->cloneSourceId = null;
        $this->cloneTargetPeducativoId = '';
        $this->cloneSource = null;
    }

    public function cloneToTarget()
    {
        $source = ActivityFieldLabel::findOrFail($this->cloneSourceId);

        $this->validate([
            'cloneTargetPeducativoId' => [
                'required', 'integer', 'exists:peducativos,id',
                Rule::notIn([$source->peducativo_id]),
            ],
        ], [], ['cloneTargetPeducativoId' => 'Programa destino']);

        $targetId = (int) $this->cloneTargetPeducativoId;

        ActivityFieldLabel::updateOrCreate(
            [
                'peducativo_id' => $targetId,
                'model' => $source->model,
                'field' => $source->field,
            ],
            [
                'label' => $source->label,
                'placeholder' => $source->placeholder,
            ]
        );

        $targetName = $this->peducativos[$targetId] ?? "#{$targetId}";
        $this->closeClone();

        $this->notification()->success(
            title: 'Etiqueta Clonada',
            description: "La etiqueta '{$source->field}' se copió a {$targetName}."
        );
    }

    // ─── SYNC (crear filas faltantes desde los defaults) ─────────

    public function sync()
    {
        $targets = $this->filter_peducativo !== ''
            ? [(int) $this->filter_peducativo]
            : array_keys($this->peducativos);

        $created = 0;
        foreach ($targets as $peducativoId) {
            $created += $this->syncPeducativo($peducativoId, silent: true);
        }

        $this->notification()->success(
            title: 'Sincronizado',
            description: $created > 0
                ? "Se crearon {$created} etiqueta(s) faltante(s)."
                : 'Todas las etiquetas ya existían.'
        );
    }

    /**
     * Crea las filas (model, field) que falten para un peducativo.
     */
    private function syncPeducativo(int $peducativoId, bool $silent = false): int
    {
        $existing = ActivityFieldLabel::where('peducativo_id', $peducativoId)
            ->get(['model', 'field'])
            ->map(fn ($r) => "{$r->model}.{$r->field}")
            ->all();

        $now = now()->toDateTimeString();
        $rows = [];

        foreach (ActivityLabelResolver::defaultRows() as $def) {
            if (! in_array("{$def['model']}.{$def['field']}", $existing, true)) {
                $rows[] = [
                    'peducativo_id' => $peducativoId,
                    'model' => $def['model'],
                    'field' => $def['field'],
                    'label' => $def['label'],
                    'placeholder' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            ActivityFieldLabel::insertOrIgnore($chunk);
        }

        // insertOrIgnore no dispara eventos del modelo: invalidar siempre.
        ActivityLabelResolver::flush($peducativoId);

        return count($rows);
    }

    // ─── HELPERS ─────────────────────────────────────────────────

    public function close()
    {
        $this->modeIndex = false;
        $this->modeForm = false;
        $this->reset(['label_id', 'peducativo_id', 'field', 'label', 'placeholder']);
        $this->model = ActivityFieldLabel::MODEL_ACTIVITY;
        $this->isEditing = false;
        $this->confirmResetId = null;
        $this->confirmDeleteId = null;
        $this->closeClone();
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
