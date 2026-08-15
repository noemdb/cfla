<?php

namespace App\Observers;

use App\Models\sys\Profile;
use App\Services\Binnacle;

class ProfileObserver
{
    public function created(Profile $profile): void
    {
        Binnacle::logModelEvent($profile, 'model_created', [
            'title' => 'Perfil creado',
            'description' => "Se creó el perfil de {$profile->fullname}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function updated(Profile $profile): void
    {
        if (! $profile->isDirty()) {
            return;
        }

        Binnacle::logModelEvent($profile, 'model_updated', [
            'title' => 'Perfil actualizado',
            'description' => "Se actualizó el perfil de {$profile->fullname}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function deleted(Profile $profile): void
    {
        Binnacle::logModelEvent($profile, 'model_deleted', [
            'title' => 'Perfil eliminado',
            'description' => "Se eliminó el perfil de {$profile->fullname}",
            'category' => 'user_action',
            'severity' => 'warning',
        ]);
    }
}
