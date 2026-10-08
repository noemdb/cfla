<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Estrategia de una celda (día × momento) de la planificación semanal.
 *
 * ─────────────────────────────────────────────────────────────────
 * QUIRK DE PERSISTENCIA `lunes`  —  NO eliminar sin refactor con datos
 * ─────────────────────────────────────────────────────────────────
 * El texto de la estrategia vive SIEMPRE en la columna `lunes`; el día real
 * se guarda aparte en `day_of_week`. Las columnas `martes`…`viernes` existen
 * en el DDL pero están vacías y quedaron como residuo de una versión anterior
 * del sistema. Los 1.696 registros que se migrarán desde `s2526` tienen esa
 * forma, por eso el quirk se preserva (blueprint/inicial · D3) en lugar de
 * normalizarse.
 *
 * REGLA: leer y escribir el texto SIEMPRE vía el atributo virtual `estrategia`
 * (accessor/mutator). Prohibido asignar `lunes`…`viernes` directamente.
 * Normalizar a una columna única requiere un script de transformación de
 * datos aparte; nunca debe hacerse de forma automática.
 *
 * @property int $eiplanningwk_id
 * @property string|null $day_of_week
 * @property string|null $momento_rutina_diaria
 * @property string|null $lunes Columna real donde se persiste el texto.
 * @property int|null $order
 */
class Eiplanningwstrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiplanningwk_id',
        'day_of_week',
        'momento_rutina_diaria',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'order',
    ];

    const COLUMN_COMMENTS = [
        'eiplanningwk_id' => 'Relación con la planificación semanal',
        'day_of_week' => 'Día de la semana',
        'momento_rutina_diaria' => 'Momento de la Rutina Diaria',
        'lunes' => 'Estrategia del lunes',
        'martes' => 'Estrategia del martes',
        'miercoles' => 'Estrategia del miércoles',
        'jueves' => 'Estrategia del jueves',
        'viernes' => 'Estrategia del viernes',
        'order' => 'Orden',
    ];

    /**
     * Los 10 momentos de la rutina diaria, en su orden real (no alfabético).
     * El orden importa: el wizard de estrategias recorre esta lista.
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

    public function eiplanningwk()
    {
        return $this->belongsTo(Eiplanningwk::class, 'eiplanningwk_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    // ─── QUIRK `lunes` (ver docblock de la clase) ────────────────

    /**
     * Texto de la estrategia, con independencia del día real de la celda.
     */
    public function getEstrategiaAttribute()
    {
        return $this->lunes;
    }

    public function setEstrategiaAttribute($value)
    {
        $this->attributes['lunes'] = $value;
    }
}
