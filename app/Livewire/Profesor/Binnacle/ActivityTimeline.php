<?php

namespace App\Livewire\Profesor\Binnacle;

use App\Livewire\Admin\Binnacle\UserActivityTimeline;
use Livewire\Attributes\Layout;

/**
 * Línea de actividad de binnacle para profesores.
 *
 * Reutiliza UserActivityTimeline en modo "mi actividad": el profesor solo ve
 * sus propios registros (selfMode = true, userId bloqueado al autenticado).
 * Cambia únicamente el layout (profesors.layouts.app) frente al panel admin.
 */
class ActivityTimeline extends UserActivityTimeline
{
    public function mount(): void
    {
        $this->selfMode = true;
        $this->userId = (int) auth()->id();

        if ($this->dateFrom === null) {
            $this->applyDateRange();
        }
    }

    #[Layout('profesors.layouts.app')]
    public function layout() {}
}
