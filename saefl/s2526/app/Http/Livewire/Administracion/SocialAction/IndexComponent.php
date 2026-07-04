<?php

namespace App\Http\Livewire\Administracion\SocialAction;

use App\Models\app\Pescolar\Grado;
use App\Models\app\SocialAction\CommunityAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    
    // Variables para listado y búsqueda
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Variables para crear/editar
    public $communityActionId;
    public $title;
    public $description;
    public $observations;
    public $date;
    public $duration;
    public $status = true;
    public $type = 'individual';
    public $entity_benefic;
    public $location;
    public $required;
    public $image;
    public $grado_id;
    public $currentImage;
    
    // Variable para almacenar la acción comunitaria actual para ver detalles
    public $viewCommunityAction;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'observations' => 'nullable|string',
            'date' => 'required|date',
            'duration' => 'required|numeric|min:1',
            'status' => 'required|boolean',
            'type' => 'required|in:individual,group',
            'entity_benefic' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'required' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'grado_id' => 'required|exists:grados,id',
        ];
    }

    public function mount($grado_id = null)
    {
        $this->date = now()->format('Y-m-d');
        $this->grado_id = $grado_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    // Métodos para crear
    public function openCreateModal()
    {
        $this->resetInputFields();
        $this->dispatchBrowserEvent('open-modal', ['modal' => 'createCommunityActionModal']);
    }

    public function closeCreateModal()
    {
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'createCommunityActionModal']);
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->reset([
            'communityActionId', 'title', 'description', 'observations', 'duration', 
            'status', 'type', 'entity_benefic', 'location', 'required', 'image', 'grado_id',
            'currentImage'
        ]);
        $this->date = now()->format('Y-m-d');
        $this->status = true;
        $this->type = 'individual';
    }

    public function store()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'grado_id' => $this->grado_id,
            'title' => $this->title,
            'description' => $this->description,
            'observations' => $this->observations,
            'date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'type' => $this->type,
            'entity_benefic' => $this->entity_benefic,
            'location' => $this->location,
            'required' => $this->required,
        ];

        if ($this->image) {
            $imageName = time() . '_' . $this->image->getClientOriginalName();
            $this->image->storeAs('social_accions', $imageName, 'public');
            $data['image'] = $imageName;
        }

        CommunityAction::create($data);
        
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'createCommunityActionModal']);
        $this->resetInputFields();
        session()->flash('message', 'Acción comunitaria creada correctamente.');
    }

    // Métodos para editar
    public function openEditModal($id)
    {
        $this->resetInputFields();
        $this->communityActionId = $id;
        $communityAction = CommunityAction::find($id);
        
        if ($communityAction) {
            $this->title = $communityAction->title;
            $this->description = $communityAction->description;
            $this->observations = $communityAction->observations;
            $this->date = $communityAction->date;
            $this->duration = $communityAction->duration;
            $this->status = $communityAction->status;
            $this->type = $communityAction->type;
            $this->entity_benefic = $communityAction->entity_benefic;
            $this->location = $communityAction->location;
            $this->required = $communityAction->required;
            $this->grado_id = $communityAction->grado_id;
            $this->currentImage = $communityAction->image;
            
            $this->dispatchBrowserEvent('open-modal', ['modal' => 'editCommunityActionModal']);
        }
    }

    public function closeEditModal()
    {
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'editCommunityActionModal']);
        $this->resetInputFields();
    }

    public function update()
    {
        $this->validate();

        $communityAction = CommunityAction::find($this->communityActionId);
        
        if (!$communityAction) {
            session()->flash('error', 'Acción comunitaria no encontrada.');
            return;
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'observations' => $this->observations,
            'date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'type' => $this->type,
            'entity_benefic' => $this->entity_benefic,
            'location' => $this->location,
            'required' => $this->required,
            'grado_id' => $this->grado_id,
        ];

        if ($this->image) {
            // Eliminar imagen anterior si existe
            if ($communityAction->image) {
                Storage::disk('public')->delete('social_accions/' . $communityAction->image);
            }
            
            $imageName = time() . '_' . $this->image->getClientOriginalName();
            $this->image->storeAs('social_accions', $imageName, 'public');
            $data['image'] = $imageName;
        }

        $communityAction->update($data);
        
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'editCommunityActionModal']);
        $this->resetInputFields();
        session()->flash('message', 'Acción comunitaria actualizada correctamente.');
    }

    // Métodos para ver detalles
    public function openViewModal($id)
    {
        $this->viewCommunityAction = CommunityAction::with(['user', 'grado'])->find($id);
        $this->dispatchBrowserEvent('open-modal', ['modal' => 'viewCommunityActionModal']);
    }

    public function closeViewModal()
    {
        $this->dispatchBrowserEvent('close-modal', ['modal' => 'viewCommunityActionModal']);
        $this->viewCommunityAction = null;
    }

    // Método para eliminar
    public function delete($id)
    {
        $communityAction = CommunityAction::find($id);
        
        if ($communityAction && $communityAction->statusDelete) {
            // Eliminar la imagen si existe
            if ($communityAction->image) {
                Storage::disk('public')->delete('social_accions/' . $communityAction->image);
            }
            
            $communityAction->delete();
            session()->flash('message', 'Acción comunitaria eliminada correctamente.');
        } else {
            session()->flash('error', 'No se puede eliminar esta acción comunitaria porque tiene horas registradas.');
        }
    }

    public function render()
    {
        $query = CommunityAction::query();
        
        // Filtrar por grado_id si está presente
        if ($this->grado_id) {
            $query->where('grado_id', $this->grado_id);
        }
        
        // Aplicar búsqueda
        $query->where(function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
              ->orWhere('description', 'like', '%' . $this->search . '%')
              ->orWhere('type', 'like', '%' . $this->search . '%');
        });
        
        // Ordenar
        $query->orderBy($this->sortField, $this->sortDirection);
        
        // Paginar
        $communityActions = $query->paginate($this->perPage);

        // Obtener grados para los selectores
        $grados = Grado::where('status_active', true)->get();
        $types = CommunityAction::getTypes();
        $statuses = CommunityAction::getStatus();
        
        // Obtener el nombre del grado actual si existe
        $currentGrado = null;
        if ($this->grado_id) {
            $currentGrado = Grado::find($this->grado_id);
        }

        return view('livewire.administracion.social-action.index-component', [
            'communityActions' => $communityActions,
            'grados' => $grados,
            'types' => $types,
            'statuses' => $statuses,
            'currentGrado' => $currentGrado,
        ]);
    }
}
// livewire.administracion.social-action.index-component