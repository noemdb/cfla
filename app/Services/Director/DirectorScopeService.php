<?php
// app/Services/Director/DirectorScopeService.php

namespace App\Services\Director;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scope de SOLO LECTURA para el rol Dirección.
 *
 * A diferencia de coordinacion (scoped por manager_id) y leadership (scoped
 * por áreas), la dirección supervisa TODA la institución: estos métodos no
 * filtran por el usuario, devuelven todas las entidades activas.
 *
 * ⚠️ REGLA DE ORO: este servicio NO contiene métodos que muten el estado
 * (save, update, approve, reject, comment, observe...). Si algún día la
 * dirección requiere una acción de escritura, se agregará con su propio
 * ADR y su propia guarda de autorización.
 */
class DirectorScopeService
{
    public function __construct(
        protected User $user
    ) {}

    /**
     * Todos los Peducativos activos (visión global de la dirección).
     */
    public function queryPeducativos()
    {
        return Peducativo::where('status_active', 'true')->orderBy('order');
    }

    /**
     * Todos los Pestudios activos con planificación habilitada.
     */
    public function queryPestudios()
    {
        return Pestudio::where('status_active', 'true')
            ->where('planning_module', 1);
    }

    /**
     * Todos los Pensums.
     */
    public function queryPensums()
    {
        return Pensum::query();
    }

    /**
     * Todas las Pevaluacions.
     */
    public function queryPevaluacions()
    {
        return Pevaluacion::query();
    }

    /**
     * Todas las Activities.
     */
    public function queryActivities()
    {
        return Activity::query();
    }

    /**
     * Recursos compartidos visibles.
     */
    public function queryResources()
    {
        return LmsActivityResource::where('is_visible', true);
    }

    /**
     * Profesores activos con carga académica (para KPIs docentes).
     */
    public function queryProfesores()
    {
        return Profesor::where('status_active', 'true')
            ->whereHas('pevaluacions');
    }

    /**
     * Verifica que un usuario logueado tiene derechos de dirección.
     * Usado por los componentes como guarda de defensa en profundidad.
     */
    public function assertCanSupervise(): void
    {
        if (! $this->user->is_director) {
            abort(403, 'No tienes permisos de dirección para supervisar esta información.');
        }
    }
}
