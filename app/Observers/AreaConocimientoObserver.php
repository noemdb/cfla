<?php

namespace App\Observers;

use App\Models\app\Academy\AreaConocimiento;
use Illuminate\Support\Facades\Cache;

class AreaConocimientoObserver
{
    public function saved(AreaConocimiento $area): void
    {
        $this->forgetLeaderCache($area->leader_id);

        // Si leader_id cambió en este guardado, también invalidamos
        // la caché del líder ANTERIOR (que ya no debería ver esta área).
        if ($area->wasChanged('leader_id')) {
            $this->forgetLeaderCache($area->getOriginal('leader_id'));
        }
    }

    public function deleted(AreaConocimiento $area): void
    {
        $this->forgetLeaderCache($area->leader_id);
    }

    private function forgetLeaderCache(?int $userId): void
    {
        if (!$userId) {
            return;
        }
        Cache::forget("leadership:{$userId}:areas");
        Cache::forget("leadership:{$userId}:asignaturas");
    }
}
