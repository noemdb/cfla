<?php

namespace App\Models\app\Inicial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eiplanningbwstrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'eiplanningbwk_id',
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
        'eiplanningbwk_id' => 'Relación con la planificación semanal',
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

    public function eiplanningbwk()
    {
        return $this->belongsTo(Eiplanningbwk::class, 'eiplanningbwk_id');
    }

    // Scope para filtrar por día de la semana
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    // Accessor para obtener la estrategia según el día
    public function getEstrategiaAttribute()
    {
        // Si tenemos day_of_week, usar el campo lunes como estrategia principal
        if ($this->day_of_week) {
            return $this->lunes;
        }

        // Mantener compatibilidad con el sistema anterior
        return $this->lunes;
    }

    // Mutator para establecer la estrategia
    public function setEstrategiaAttribute($value)
    {
        $this->attributes['lunes'] = $value;
    }
}