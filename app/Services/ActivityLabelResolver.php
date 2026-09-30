<?php

namespace App\Services;

use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\ActivityFieldLabel;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Resuelve los labels de Activity/Achievement según el Peducativo asociado
 * al Pevaluacion (Activity.pevaluacion_id → pensum → pestudio → peducativo).
 *
 * Fuente: `activity_field_labels` (una fila por peducativo×campo), con
 * fallback a `Activity::COLUMN_COMMENTS` + `Achievement::COLUMN_COMMENTS`
 * cuando no hay fila o la tabla aún no existe. Resultado cacheado por
 * peducativo; `ActivityFieldLabel` invalida la caché al guardar/borrar.
 */
class ActivityLabelResolver
{
    /**
     * Labels compuestos (no son columnas del modelo): se guardan igual en
     * `activity_field_labels` y son editables por peducativo en el CRUD.
     * Clave "modelo.campo" => etiqueta por defecto.
     */
    public const VIRTUAL_LABELS = [
        'activity.topic_thematic_referentes' => 'Referentes teórico-prácticos',
        'activity.ODS_sistematizacion' => 'ODS / Sistematización',
        'activity.teaching_assessment' => 'Enseñanza y Evaluación',
    ];

    /**
     * Defaults con clave "modelo.campo" (COLUMN_COMMENTS + virtuales).
     */
    public static function keyedDefaults(): array
    {
        return array_merge(
            collect(Activity::COLUMN_COMMENTS)
                ->mapWithKeys(fn ($label, $field) => ["activity.{$field}" => $label])
                ->all(),
            collect(Achievement::COLUMN_COMMENTS)
                ->mapWithKeys(fn ($label, $field) => ["achievement.{$field}" => $label])
                ->all(),
            static::VIRTUAL_LABELS,
        );
    }

    /**
     * Filas para sembrar/sincronizar (model, field, label).
     */
    public static function defaultRows(): array
    {
        $rows = [];
        foreach (Activity::COLUMN_COMMENTS as $field => $label) {
            $rows[] = ['model' => ActivityFieldLabel::MODEL_ACTIVITY, 'field' => $field, 'label' => $label];
        }
        foreach (Achievement::COLUMN_COMMENTS as $field => $label) {
            $rows[] = ['model' => ActivityFieldLabel::MODEL_ACHIEVEMENT, 'field' => $field, 'label' => $label];
        }
        foreach (static::VIRTUAL_LABELS as $key => $label) {
            [$model, $field] = explode('.', $key, 2);
            $rows[] = ['model' => $model, 'field' => $field, 'label' => $label];
        }

        return $rows;
    }

    /**
     * Mapa plano field => label (fusiona activity + achievement).
     */
    public static function forPeducativo(?int $peducativoId): array
    {
        $defaults = array_merge(Activity::COLUMN_COMMENTS, Achievement::COLUMN_COMMENTS);
        foreach (static::VIRTUAL_LABELS as $key => $label) {
            [, $field] = explode('.', $key, 2);
            $defaults[$field] = $label;
        }

        if (! $peducativoId) {
            return $defaults;
        }

        if (! Schema::hasTable('activity_field_labels')) {
            return $defaults;
        }

        try {
            return Cache::rememberForever(
                "activity-field-labels:peducativo:{$peducativoId}",
                function () use ($peducativoId, $defaults) {
                    $overrides = ActivityFieldLabel::query()
                        ->where('peducativo_id', $peducativoId)
                        ->pluck('label', 'field')
                        ->all();

                    return array_merge($defaults, $overrides);
                }
            );
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    public static function forPevaluacion(Pevaluacion|int|null $pevaluacion): array
    {
        if ($pevaluacion === null) {
            return static::forPeducativo(null);
        }

        if (! $pevaluacion instanceof Pevaluacion) {
            $pevaluacion = Pevaluacion::with('pensum.pestudio')->find($pevaluacion);
        }

        if (! $pevaluacion) {
            return static::forPeducativo(null);
        }

        $peducativoId = $pevaluacion->pensum?->pestudio?->peducativo_id;

        return static::forPeducativo($peducativoId ? (int) $peducativoId : null);
    }

    public static function forActivity(Activity|int|null $activity): array
    {
        if ($activity instanceof Activity) {
            return static::forPevaluacion($activity->pevaluacion_id);
        }

        if (is_int($activity)) {
            $activity = Activity::find($activity);
        }

        return static::forPevaluacion($activity?->pevaluacion_id);
    }

    public static function flush(?int $peducativoId): void
    {
        if (! $peducativoId) {
            return;
        }

        Cache::forget("activity-field-labels:peducativo:{$peducativoId}");
    }
}
