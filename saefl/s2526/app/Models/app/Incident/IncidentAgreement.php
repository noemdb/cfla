<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'description',
        'observations',
        'status_notify',
        'date_notify_email',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_notify_email',
    ];

    protected $casts = [
        'status_notify' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
    const COLUMN_COMMENTS = [
        'incident_id' => 'Incidencia asociada.',
        'description' => 'Descripción.',
        'observations' => 'Observaciones.',
        'status_notify' => 'Notificación.',
        'date_notify_email' => 'Fecha de notificación por correo electrónico.',
        'close_observations' => 'Observaciones sobre el cierre del incidente',
        'status_notify_close' => 'Indica si se notifica el cierre del incidente',
    ];
}
