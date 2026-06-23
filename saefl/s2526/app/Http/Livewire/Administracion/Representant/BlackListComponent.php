<?php

namespace App\Http\Livewire\Administracion\Representant;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\app\Estudiante\Representant;

class BlackListComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';
    
    // Filtros
    public $search = '';
    public $status_active = '';
    public $status_blacklist = '';
    public $paginate = 10;
    public $representant_id;
    
    // Variables públicas para estadísticas
    public $total_representants = 0;
    public $active_representants = 0;
    public $blacklist_representants = 0;
    public $current_count = 0;
    
    // Estados de modo
    public $modeIndex = true, $modeCreate = false, $modeEdit = false, $modeShow = false;
    
    // Datos del representante
    public $representant;
    
    // Listeners para eventos
    protected $listeners = [
        'showSwal', 
        'confirmAddToBlacklist',
        'confirmRemoveFromBlacklist',
        'resetFilters'
    ];

    public function mount()
    {
        $this->representant = new Representant;
        $this->resetModes();
        $this->modeIndex = true;
        $this->updateStatistics();
    }

    public function render()
    {
        $query = Representant::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('ci_representant', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status_active !== '', function($query) {
                $query->where('status_active', $this->status_active);
            })
            ->when($this->status_blacklist !== '', function($query) {
                $query->where('status_blacklist', $this->status_blacklist);
            })
            ->withCount('estudiants')
            ->with('estudiants')
            ->orderBy('name');

        $representants = $query->paginate($this->paginate);
        
        // Actualizar estadísticas
        $this->updateStatistics();
        $this->current_count = $representants->count();

        return view('livewire.administracion.representant.black-list-component', [
            'representants' => $representants
        ]);
    }

    // Método para actualizar estadísticas
    private function updateStatistics()
    {
        $this->total_representants = Representant::count();
        $this->active_representants = Representant::where('status_active', 'true')->count();
        $this->blacklist_representants = Representant::where('status_blacklist', 'true')->count();
    }

    // Métodos para gestión de modos
    public function resetModes()
    {
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->modeShow = false;
    }

    public function modeIndex()
    {
        $this->resetModes();
        $this->modeIndex = true;
        $this->representant = new Representant;
    }

    // Métodos para filtros
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusActive()
    {
        $this->resetPage();
    }

    public function updatedStatusBlacklist()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status_active = '';
        $this->status_blacklist = '';
        $this->resetPage();
    }

    // Métodos principales de funcionalidad
    public function addToBlacklist($id)
    {
        $representant = Representant::findOrFail($id);
        
        // Verificar si ya está en lista negra
        if ($representant->status_blacklist == 'true') {
            $this->showSwal(
                'Información', 
                'El representante ya se encuentra en la lista negra', 
                'info'
            );
            return;
        }

        // Mostrar confirmación antes de agregar usando JavaScript directo
        $this->dispatchBrowserEvent('show-confirm-add', [
            'id' => $id,
            'name' => $representant->name
        ]);
    }

    public function confirmAddToBlacklist($id)
    {
        $representant = Representant::findOrFail($id);
        
        // Actualizar estado
        $representant->update([
            'status_blacklist' => 'true',
            'user_id' => Auth::id()
        ]);

        // Actualizar estadísticas
        $this->updateStatistics();

        // Mostrar mensaje de éxito con advertencia
        $this->showSwal(
            '¡Representante agregado a lista negra!', 
            'El representante <strong>' . $representant->name . '</strong> ha sido agregado a la lista negra.<br><br>' .
            '<strong>IMPORTANTE:</strong> Verifique la deuda individual del representante:<br>' .
            '• Plan de pago<br>' .
            '• Conceptos de cobro<br>' .
            '• Cuentas de cobro pendientes',
            'warning'
        );

        $this->modeIndex();
    }

    public function removeFromBlacklist($id)
    {
        $representant = Representant::findOrFail($id);
        
        // Verificar si no está en lista negra
        if ($representant->status_blacklist != 'true') {
            $this->showSwal(
                'Información', 
                'El representante no se encuentra en la lista negra', 
                'info'
            );
            return;
        }

        // Mostrar confirmación antes de retirar usando JavaScript directo
        $this->dispatchBrowserEvent('show-confirm-remove', [
            'id' => $id,
            'name' => $representant->name
        ]);
    }

    public function confirmRemoveFromBlacklist($id)
    {
        $representant = Representant::findOrFail($id);
        
        // Actualizar estado
        $representant->update([
            'status_blacklist' => 'false',
            'user_id' => Auth::id()
        ]);

        // Actualizar estadísticas
        $this->updateStatistics();

        // Mostrar mensaje de éxito con advertencia
        $this->showSwal(
            '¡Representante retirado de lista negra!', 
            'El representante <strong>' . $representant->name . '</strong> ha sido retirado de la lista negra.<br><br>' .
            '<strong>RECOMENDACIÓN:</strong> Verifique el estado actual del representante:<br>' .
            '• Situación financiera<br>' .
            '• Estado de pagos<br>' .
            '• Restricciones administrativas',
            'success'
        );

        $this->modeIndex();
    }

    // Método para mostrar detalles del representante
    public function show($id)
    {
        $this->representant = Representant::with(['estudiants', 'user'])->findOrFail($id);
        $this->representant_id = $id;
        
        $this->resetModes();
        $this->modeShow = true;
    }

    // Métodos para SweetAlert
    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 8000,
            'icon' => $icon,
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Entendido'
        ]);
    }

    // Métodos de utilidad
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function close()
    {
        $this->mount();
    }
}