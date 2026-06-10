<?php

namespace App\Policies;

use App\Models\User;
use App\Models\app\Academy\Activity;

class LmsActivityPolicy
{
    public function editContent(User $user, Activity $activity): bool
    {
        if ($user->is_admin) {
            return true;
        }
        return $activity->pevaluacion?->profesor_id === $user->id;
    }

    public function publish(User $user, Activity $activity): bool
    {
        return $this->editContent($user, $activity);
    }

    public function view(User $user, Activity $activity): bool
    {
        if (!$user->is_student) {
            return false;
        }
        $publication = $activity->lmsPublication;
        if (!$publication || !$publication->isVisibleToStudents()) {
            return false;
        }
        return true;
    }

    public function audit(User $user, Activity $activity): bool
    {
        return $user->is_admin || $user->is_planner || $user->is_diagnostic;
    }
}
