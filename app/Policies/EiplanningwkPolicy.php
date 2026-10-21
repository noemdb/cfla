<?php

namespace App\Policies;

use App\Models\app\Inicial\Eiplanningwk;
use App\Models\User;

/**
 * Autorización de los planes semanales de Educación Inicial.
 *
 * La pertenencia se calcula SIEMPRE por la ficha `profesors` del usuario
 * autenticado, no por un `profesor_id` que llegue desde el formulario. Así la
 * misma regla vale para listar, abrir, editar, clonar y eliminar.
 */
class EiplanningwkPolicy
{
    public function view(User $user, Eiplanningwk $plan): bool
    {
        return $this->esPropietario($user, $plan);
    }

    public function update(User $user, Eiplanningwk $plan): bool
    {
        return $this->esPropietario($user, $plan);
    }

    public function delete(User $user, Eiplanningwk $plan): bool
    {
        return $this->esPropietario($user, $plan);
    }

    /**
     * Clonar exige la misma pertenencia que editar: la copia se asigna al
     * docente autenticado, nunca al `profesor_id` crudo del original.
     */
    public function duplicate(User $user, Eiplanningwk $plan): bool
    {
        return $this->esPropietario($user, $plan);
    }

    private function esPropietario(User $user, Eiplanningwk $plan): bool
    {
        $profesorId = $user->profesor?->id;

        return $profesorId !== null
            && (int) $plan->profesor_id === (int) $profesorId;
    }
}
