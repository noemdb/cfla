<?php

namespace App\Policies;

use App\Models\app\Academy\DidacticPlan;
use App\Models\User;

class DidacticPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin || $user->is_planner || $user->is_diagnostic;
    }

    public function view(User $user, DidacticPlan $plan): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->is_admin || $user->is_planner;
    }

    public function update(User $user, DidacticPlan $plan): bool
    {
        return $this->create($user) && $plan->isDraft();
    }

    public function approve(User $user, DidacticPlan $plan): bool
    {
        return $this->create($user) && $plan->isDraft();
    }

    public function delete(User $user, DidacticPlan $plan): bool
    {
        return $this->create($user) && $plan->isDraft();
    }

    public function archive(User $user, DidacticPlan $plan): bool
    {
        return $this->create($user) && $plan->isApproved();
    }

    public function downloadPdf(User $user, DidacticPlan $plan): bool
    {
        return $this->view($user, $plan) && $plan->isDownloadable();
    }
}
