<?php
namespace App\Http\Livewire\Administracion;

use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use Livewire\Component;
use Livewire\WithPagination;

class BaremoComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $search = '';

    // Model properties
    public $baremo_id;
    public $pestudio_id;
    public $pensum_id;
    public $lapso_id;
    public $minimo;
    public $maxima;
    public $valoracion;
    public $description;

    public $activePestudio = null;
    public $activeLapso    = [];

    public $isModalOpen = false;

    // Dropdowns
    public $pestudios = [];
    public $pensums   = [];
    public $lapsos    = [];

    public function mount()
    {
        $this->loadDropdowns();
    }

    private function loadDropdowns()
    {
        $this->pestudios = Pestudio::where('status_active', true)->orderBy('name')->get();
        // Cargar pensums con su grado y asignatura si están disponibles, de lo contrario simplificar
        $this->pensums = Pensum::where('status_active', true)->get();
        // Cargar lapsos
        $this->lapsos = Lapso::orderBy('id')->get();
    }

    public function render()
    {
        $query = Baremo::with(['pestudio', 'pensum', 'lapso']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('valoracion', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Obtener la colección completa en lugar de paginar
        $baremos_raw = $query->orderBy('minimo', 'asc')->get();

        // Agrupar por pestudio y luego por lapso.
        // Si lapso_id es null, lo agruparemos bajo la llave 'general'
        $grouped_baremos = $baremos_raw->groupBy('pestudio_id')->map(function ($itemsByPestudio) {
            return $itemsByPestudio->groupBy(function ($item) {
                return $item->lapso_id ?: 'general';
            });
        });

        // Obtener los pestudios reales que tienen baremos asociados para iterar en la vista
        $activePestudios = Pestudio::whereIn('id', $grouped_baremos->keys())->orderBy('name')->get();

        // Inicializar activePestudio si es null y hay pestudios
        if ($this->activePestudio === null && $activePestudios->isNotEmpty()) {
            $this->activePestudio = $activePestudios->first()->id;
        }

        // Inicializar activeLapso para cada pestudio si no está definido
        foreach ($grouped_baremos as $pId => $lapsosData) {
            if (! isset($this->activeLapso[$pId]) && $lapsosData->keys()->isNotEmpty()) {
                $this->activeLapso[$pId] = $lapsosData->keys()->first();
            }
        }

        return view('livewire.administracion.baremo-component', compact('grouped_baremos', 'activePestudios'));
    }

    public function setActivePestudio($id)
    {
        $this->activePestudio = $id;
    }

    public function setActiveLapso($pestudioId, $lapsoId)
    {
        $this->activeLapso[$pestudioId] = $lapsoId;
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $baremo            = Baremo::findOrFail($id);
        $this->baremo_id   = $baremo->id;
        $this->pestudio_id = $baremo->pestudio_id;
        $this->pensum_id   = $baremo->pensum_id;
        $this->lapso_id    = $baremo->lapso_id;
        $this->minimo      = $baremo->minimo;
        $this->maxima      = $baremo->maxima;
        $this->valoracion  = $baremo->valoracion;
        $this->description = $baremo->description;

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'pestudio_id' => 'required|exists:pestudios,id',
            // 'pensum_id' => 'nullable|exists:pensums,id',
            'lapso_id'    => 'nullable|exists:lapsos,id',
            'minimo'      => 'required|numeric',
            'maxima'      => 'required|numeric|gte:minimo',
            'valoracion'  => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Baremo::updateOrCreate(
            ['id' => $this->baremo_id],
            [
                'pestudio_id' => $this->pestudio_id,
                'pensum_id'   => $this->pensum_id ?: null,
                'lapso_id'    => $this->lapso_id ?: null,
                'minimo'      => $this->minimo,
                'maxima'      => $this->maxima,
                'valoracion'  => $this->valoracion,
                'description' => $this->description,
            ]
        );

        $this->dispatchBrowserEvent('swal', [
            'title'             => $this->baremo_id ? '¡Actualizado!' : '¡Creado Exitosamente!',
            'text'              => $this->baremo_id
                ? 'El baremo ha sido actualizado correctamente.'
                : 'El baremo ha sido creado exitosamente.',
            'icon'              => 'success',
            'confirmButtonText' => 'Aceptar',
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title'  => '¿Eliminar Baremo?',
            'text'   => 'Esta acción eliminará el baremo seleccionado.',
            'icon'   => 'warning',
            'id'     => $id,
            'method' => 'confirmDelete',
        ]);
    }

    public function confirmDelete($id)
    {
        $baremo = Baremo::findOrFail($id);
        $baremo->delete();

        $this->dispatchBrowserEvent('swal', [
            'title'             => '🗑️ Eliminado',
            'text'              => "El baremo ha sido eliminado correctamente.",
            'icon'              => 'success',
            'confirmButtonText' => 'Aceptar',
        ]);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->baremo_id   = null;
        $this->pestudio_id = null;
        $this->pensum_id   = null;
        $this->lapso_id    = null;
        $this->minimo      = '';
        $this->maxima      = '';
        $this->valoracion  = '';
        $this->description = '';
    }
}
