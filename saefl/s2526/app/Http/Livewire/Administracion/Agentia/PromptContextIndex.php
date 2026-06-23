<?php

namespace App\Http\Livewire\Administracion\Agentia;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Agentia\AiPrompt;
use Illuminate\Support\Facades\Auth;

class PromptContextIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterType = 'all'; // all, system, user

    // Fields for Create/Edit
    public $prompt_id;
    public $prompt_type = 'system';
    public $name;
    public $version;
    public $content;
    public $description;
    public $active = true;
    public $created_at; 
    public $created_by;

    public $isOpen = false;
    public $isViewOpen = false;

    public function render()
    {
        $query = AiPrompt::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('version', 'like', '%' . $this->search . '%');
        }

        if ($this->filterType !== 'all') {
            $query->where('prompt_type', $this->filterType);
        }

        $prompts = $query->orderBy('prompt_type')
            ->orderBy('name')
            ->orderBy('version', 'desc')
            ->paginate(10);

        return view('livewire.administracion.agentia.prompt-context-index', compact('prompts'))
            ->extends('administracion.layouts.dashboard.app')
            ->section('main');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function openViewModal()
    {
        $this->isViewOpen = true;
    }

    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->prompt_id = null;
        $this->prompt_type = 'system';
        $this->name = '';
        $this->version = '';
        $this->content = '';
        $this->description = '';
        $this->active = true;
        $this->created_by = 1;
        $this->created_at = now();
    }    

    public function store()
    {
        $this->validate([
            'prompt_type' => 'required|in:system,user',
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:20',
            'content' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Logic: specific to Roadmap Phase 4
        // "Un prompt nunca se edita: se versiona."
        // If we are "editing" (prompt_id set), we might be correcting a typo? 
        // Or strictly strictly creating NEW version.
        // The UI flow suggests "Create" makes a new one.
        // "Edit" should probably pre-fill a NEW form with data from ID, but save as NEW ID.
        // However, for simplicity here, I will allow Update if ID exists for minor fixes, 
        // but adding a warning or implementing "Clone as New Version" button is better.
        // Given user request "gestionar fase 4", strict immutability is key.
        $exists = AiPrompt::where('prompt_type', $this->prompt_type)
            ->where('name', $this->name)
            ->where('version', $this->version)
            ->where('id', '!=', $this->prompt_id) // Excluir el actual si estamos editando
            ->exists();

        if ($exists) {
            session()->flash('error', 'Ya existe una versión con este número para el mismo tipo y nombre.');
            return;
        }

        // ✅ Si estamos activando este prompt, desactivar TODOS los demás del mismo tipo/contexto
        if ($this->active) {
            AiPrompt::where('prompt_type', $this->prompt_type)
                ->where('name', $this->name)
                ->where('id', '!=', $this->prompt_id) // Excluir el actual si estamos editando
                ->update(['active' => false]);
        }

        AiPrompt::updateOrCreate(
            ['id' => $this->prompt_id],
            [
                'prompt_type' => $this->prompt_type,
                'name' => $this->name,
                'version' => $this->version,
                'content' => $this->content,
                'description' => $this->description,
                'active' => $this->active,
                'created_by' => Auth::id(),
            ]
        );

        session()->flash('message', '✅ Prompt guardado exitosamente.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $prompt = AiPrompt::findOrFail($id);
        $this->prompt_id = $id;
        $this->prompt_type = $prompt->prompt_type;
        $this->name = $prompt->name;
        $this->version = $prompt->version;
        $this->content = $prompt->content;
        $this->description = $prompt->description;
        $this->active = $prompt->active;

        $this->openModal();
    }

    public function clone($id)
    {
        $original = AiPrompt::findOrFail($id);

        // Generar nueva versión: si la versión es "1.0" -> "1.1", si es "2" -> "2.1"
        $newVersion = $this->generateNextVersion($original->version);

        $this->prompt_id = null;
        $this->prompt_type = $original->prompt_type;
        $this->name = $original->name;
        $this->version = $newVersion;
        $this->content = $original->content;
        $this->description = "Clonado desde v{$original->version} - " . ($original->description ?? '');
        $this->active = false; // Nueva versión inactiva por defecto

        $this->openModal();
    }

    private function generateNextVersion($currentVersion)
    {
        // Separar por puntos
        $parts = explode('.', $currentVersion);
        $lastIndex = count($parts) - 1;

        if (is_numeric($parts[$lastIndex])) {
            $parts[$lastIndex]++;
        } else {
            $parts[] = '1';
        }

        return implode('.', $parts);
    }

    private function incrementVersion($v)
    {
        $parts = explode('.', $v);
        if (count($parts) > 1 && is_numeric(end($parts))) {
            $parts[count($parts) - 1]++;
            return implode('.', $parts);
        }
        return $v . '.1';
    }

    public function show($id)
    {
        $prompt = AiPrompt::findOrFail($id);
        $this->prompt_id = $id;
        $this->prompt_type = $prompt->prompt_type;
        $this->name = $prompt->name;
        $this->version = $prompt->version;
        $this->content = $prompt->content;
        $this->description = $prompt->description;
        $this->active = $prompt->active;
        $this->created_at = $prompt->created_at;
        $this->created_by = $prompt->creator;

        $this->openViewModal();
    }

    public function toggleActive($id)
    {
        $prompt = AiPrompt::findOrFail($id);
        
        // Si está intentando DESACTIVAR un prompt activo
        if ($prompt->active) {
            // Verificar si hay otra versión disponible para activar
            $otherVersion = AiPrompt::where('prompt_type', $prompt->prompt_type)
                ->where('name', $prompt->name)
                ->where('id', '!=', $id)
                ->where('active', false)
                ->orderBy('version', 'desc')
                ->first();
                
            if (!$otherVersion) {
                session()->flash('error', 
                    'No se puede desactivar el único prompt activo. ' .
                    'Primero debe crear o activar otra versión.'
                );
                return;
            }
            
            // Desactivar el actual
            $prompt->active = false;
            $prompt->save();
            
            // Activar automáticamente la otra versión
            $otherVersion->active = true;
            $otherVersion->save();
            
            session()->flash('message', 
                "⚠️ Prompt desactivado. Se ha activado automáticamente la versión v{$otherVersion->version}."
            );
        } else {
            // Si está ACTIVANDO un prompt inactivo
            // Desactivar el actualmente activo
            AiPrompt::where('prompt_type', $prompt->prompt_type)
                ->where('name', $prompt->name)
                ->where('active', true)
                ->update(['active' => false]);
            
            // Activar este
            $prompt->active = true;
            $prompt->save();
            
            session()->flash('message', 
                "✅ Prompt activado. La versión anterior ha sido desactivada automáticamente."
            );
        }
    }

    public function delete($id)
    {
        AiPrompt::find($id)->delete();
        session()->flash('message', 'Prompt eliminado.');
    }
}
