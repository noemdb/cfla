<?php

namespace App\Models\app\Meta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = ['event_name', 'payload'];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'event_name' => 'Nombre del evento del webhook',
        'payload' => 'Datos completos del payload',
        'timestamp' => 'Fecha',
    ];
}
