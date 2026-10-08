<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Estrategia de una celda (día × momento) del proyecto de aula.
 *
 * ─────────────────────────────────────────────────────────────────
 * QUIRK DE PERSISTENCIA `lunes`  —  NO eliminar sin refactor con datos
 * ─────────────────────────────────────────────────────────────────
 * El texto se persiste SIEMPRE en la columna `lunes`; el día real va en
 * `day_of_week`. `martes`…`viernes` son columnas residuo vacías.
 *
 * REGLA: leer y escribir el texto SIEMPRE vía el atributo virtual `estrategia`.
 *
 * @property int $eiprojectk_id
 * @property string $day_of_week
 * @property string $momento_rutina_diaria
 * @property string|null $lunes Columna real donde se persiste el texto.
 * @property int|null $order
 * @property string|null $description
 */
class Eiprojectkstrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiprojectk_id',
        'day_of_week',
        'momento_rutina_diaria',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'order',
        'description',
    ];

    const COLUMN_COMMENTS = [
        'eiprojectk_id' => 'Relación con el proyecto',
        'day_of_week' => 'Día de la semana',
        'momento_rutina_diaria' => 'Momento de la Rutina Diaria',
        'lunes' => 'Estrategia del lunes',
        'martes' => 'Estrategia del martes',
        'miercoles' => 'Estrategia del miércoles',
        'jueves' => 'Estrategia del jueves',
        'viernes' => 'Estrategia del viernes',
        'order' => 'Orden',
        'description' => 'Descripción',
    ];

    /**
     * Los 10 momentos de la rutina diaria, en su orden real (no alfabético).
     */
    const LIST_MOMENT = [
        'Recibimiento' => 'Recibimiento',
        'Momento Cívico' => 'Momento Cívico',
        'Aseo-Desayuno-Aseo' => 'Aseo-Desayuno-Aseo',
        'Periodo: Planificación' => 'Periodo: Planificación',
        'Periodo: Trabajo Libre' => 'Periodo: Trabajo Libre',
        'Periodo: Orden y limpieza' => 'Periodo: Orden y limpieza',
        'Periodo: Intercambio y Recuento' => 'Periodo: Intercambio y Recuento',
        'Periodo: Trabajos en Pequeños Grupos' => 'Periodo: Trabajos en Pequeños Grupos',
        'Periodo: Actividades Colectivas' => 'Periodo: Actividades Colectivas',
        'Periodo: Despedida' => 'Periodo: Despedida',
    ];

    const WEEK_DAYS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
    ];

    public function eiprojectk()
    {
        return $this->belongsTo(Eiprojectk::class, 'eiprojectk_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    // ─── QUIRK `lunes` (ver docblock de la clase) ────────────────

    public function getEstrategiaAttribute()
    {
        return $this->lunes;
    }

    public function setEstrategiaAttribute($value)
    {
        $this->attributes['lunes'] = $value;
    }
}
