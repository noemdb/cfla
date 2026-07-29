<?php

namespace App\Policies;

use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\User;

class ActivityCommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->isStudent() || $user->is_admin;
    }

    public function update(User $user, ActivityComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->is_admin;
    }

    public function delete(User $user, ActivityComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->is_admin;
    }

    public function approve(User $user): bool
    {
        return $user->is_admin || $user->is_profesor || $user->is_leadership;
    }

    public function viewPending(User $user): bool
    {
        return $user->is_admin || $user->is_profesor || $user->is_leadership;
    }

    public function reject(User $user, ActivityComment $comment): bool
    {
        return $user->is_admin || $user->is_profesor || $user->is_leadership;
    }
}
