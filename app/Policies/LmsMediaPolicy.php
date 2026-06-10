<?php

namespace App\Policies;

use App\Models\User;
use App\Models\app\Academy\Lms\LmsMediaLibrary;

class LmsMediaPolicy
{
    public function view(User $user, LmsMediaLibrary $media): bool
    {
        return $user->is_admin || $media->uploaded_by === $user->id;
    }

    public function delete(User $user, LmsMediaLibrary $media): bool
    {
        return $user->is_admin || $media->uploaded_by === $user->id;
    }
}
