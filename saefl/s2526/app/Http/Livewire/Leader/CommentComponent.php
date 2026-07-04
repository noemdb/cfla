<?php

namespace App\Http\Livewire\Leader;

use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use Livewire\Component;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentComponent extends Component
{
    public $activities;
    public $pevaluacion,$pevaluacion_id,$activity_id;
    public $modeComments;
    public $activity,$comments,$status;

    public function mount($activity_id)
    {
        $this->activity = Activity::findOrFail($activity_id);
        $this->activity_id = $activity_id;
        $this->modeComments = false;
    }

    public function render()
    {
        // Evitar recargar innecesariamente si ya está en memoria
        if (!$this->activity || $this->activity->id !== $this->activity_id) {
            $this->activity = Activity::findOrFail($this->activity_id);
        }
        return view('livewire.leader.comment-component');
    }

    public function saveComent()
    {
        // 🔹 1. Recargar el modelo (Livewire no persiste objetos Eloquent entre requests)
        $this->activity = Activity::findOrFail($this->activity_id);

        // 🔹 2. Validar datos (status como booleano/integer 0-1)
        $this->validate([
            'comments' => 'nullable|string|max:65535',
            'status'   => 'required|boolean', // ✅ tinyint(1) → boolean en Laravel
        ]);

        $this->activity->comments = $this->comments;
        $this->activity->status   = (bool) $this->status; // Conversión explícita por seguridad
        $this->activity->save();

        // 🔹 4. Resetear solo los campos del formulario
        $this->reset(['comments', 'status']);
        $this->modeComments = false;

        // 🔹 5. Notificar éxito
        $this->showSwal('¡Excelente, buen trabajo!', 'Actualización realizada exitosamente');
        
        // 🔹 6. Emitir evento para refrescar componentes padre si es necesario
        $this->emit('commentUpdated', $this->activity_id);
    }

    public function setModeComment($activitie_id)
    {
        // 🔹 Corrección ortográfica: $activitie_id → $activity_id
        $this->activity = Activity::findOrFail($activitie_id);
        $this->activity_id = $this->activity->id; // Asegurar consistencia
        $this->comments = $this->activity->comments;
        $this->status   = $this->activity->status;
        $this->modeComments = true;
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            // 'toast'=>true,
            // 'position'=>'top-end',
        ]);
    }

    public function close()
    {
        $this->modeComments = false;
    }
}
