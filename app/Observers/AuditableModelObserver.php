<?php

namespace App\Observers;

use App\Services\Binnacle;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer genérico de auditoría (Spec BINNACLE-001, Fase 2).
 *
 * Cubre cualquier modelo que implemente App\Contracts\Auditable sin necesidad
 * de un observer dedicado. Cada modelo define su allowlist de atributos
 * (ADR-005) y sus campos enmascarados; aquí solo se delega en el servicio.
 */
class AuditableModelObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'model_created', 'creado', 'info');
    }

    public function updated(Model $model): void
    {
        if (! $model->isDirty()) {
            return;
        }

        $this->log($model, 'model_updated', 'actualizado', 'info');
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'model_deleted', 'eliminado', 'warning');
    }

    private function log(Model $model, string $event, string $verb, string $severity): void
    {
        Binnacle::logModelEvent($model, $event, [
            'title' => $this->label($model).' '.$verb,
            'category' => 'user_action',
            'severity' => $severity,
        ]);
    }

    private function label(Model $model): string
    {
        $short = (new \ReflectionClass($model))->getShortName();

        return ucfirst(str($short)->snake()->replace('_', ' ')->toString());
    }
}
