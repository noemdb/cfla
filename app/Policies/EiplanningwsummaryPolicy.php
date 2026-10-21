<?php

namespace App\Policies;

use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización de los resúmenes semanales.
 *
 * El resumen no guarda `profesor_id`: su dueño es el docente del plan al que
 * pertenece. Delegar en {@see EiplanningwkPolicy} evita duplicar la regla de
 * pertenencia y mantiene una sola fuente de verdad.
 */
class EiplanningwsummaryPolicy
{
    public function view(User $user, Eiplanningwsummary $summary): bool
    {
        return $this->puedeSobreElPlan($user, $summary, 'view');
    }

    public function update(User $user, Eiplanningwsummary $summary): bool
    {
        return $this->puedeSobreElPlan($user, $summary, 'update');
    }

    public function delete(User $user, Eiplanningwsummary $summary): bool
    {
        return $this->puedeSobreElPlan($user, $summary, 'delete');
    }

    private function puedeSobreElPlan(User $user, Eiplanningwsummary $summary, string $ability): bool
    {
        $plan = $summary->eiplanningwk;

        return $plan !== null
            && Gate::forUser($user)->allows($ability, $plan);
    }
}
