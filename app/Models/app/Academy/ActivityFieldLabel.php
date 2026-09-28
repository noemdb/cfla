<?php

namespace App\Models\app\Academy;

use App\Services\ActivityLabelResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Etiqueta de un campo de Activity/Achievement según el Peducativo.
 *
 * Una fila por (peducativo_id, model, field). El label por defecto es el
 * de `Activity::COLUMN_COMMENTS` / `Achievement::COLUMN_COMMENTS` (sembrado
 * en la migración); al editar la fila, form/listas/PDFs muestran el nuevo
 * valor vía `ActivityLabelResolver`.
 *
 * Las sub-etiquetas INICIO/DESARROLLO/CIERRE no viven aquí: son marcadores
 * de parseo del campo `teaching` y no se tocan.
 */
class ActivityFieldLabel extends Model
{
    use HasFactory;

    protected $table = 'activity_field_labels';

    protected $fillable = [
        'peducativo_id', 'model', 'field', 'label', 'placeholder',
    ];

    public const MODEL_ACTIVITY = 'activity';

    public const MODEL_ACHIEVEMENT = 'achievement';

    public function peducativo()
    {
        return $this->belongsTo(Peducativo::class, 'peducativo_id');
    }

    protected static function booted(): void
    {
        $flush = function (self $label): void {
            ActivityLabelResolver::flush($label->peducativo_id);
        };

        static::saved($flush);
        static::deleted($flush);
    }
}
