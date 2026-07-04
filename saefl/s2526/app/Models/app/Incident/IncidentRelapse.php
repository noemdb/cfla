<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentRelapse extends Model
{
    use HasFactory;

    protected $fillable = [
        'duty_id',
        'estudiant_id',
        'description',
        'status',
        'status_notify',
        'status_active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status_notify' => 'boolean',
        'status_active' => 'boolean',
    ];

    public function incident_duty()
    {
        return $this->belongsTo(IncidentDuty::class, 'duty_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiant_id');
    }

    const COLUMN_COMMENTS = [
        'id' => 'Identificador de la reincidencia del incidente',
        'duty_id' => 'Identificador del deber incumplido',
        'estudiant_id' => 'Identificador del estudiante involucrado en la reincidencia',
        'description' => 'Descripción de la reincidencia',
        'status' => 'Estado de la reincidencia',
        'status_notify' => 'Indica si se notificó la reincidencia',
        'status_active' => 'Indica si la reincidencia está activa',
    ];
}
