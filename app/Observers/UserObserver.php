<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Binnacle;

class UserObserver
{
    public function created(User $user): void
    {
        Binnacle::logModelEvent($user, 'model_created', [
            'title' => 'Usuario creado',
            'description' => "Se creó un nuevo usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function updated(User $user): void
    {
        if (! $user->isDirty()) {
            return;
        }

        Binnacle::logModelEvent($user, 'model_updated', [
            'title' => 'Usuario actualizado',
            'description' => "Se actualizó el usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function deleted(User $user): void
    {
        Binnacle::logModelEvent($user, 'model_deleted', [
            'title' => 'Usuario eliminado',
            'description' => "Se eliminó el usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'warning',
        ]);
    }
}
