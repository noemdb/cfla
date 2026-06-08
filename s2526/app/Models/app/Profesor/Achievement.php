<?php

namespace App\Models\app\Profesor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = ['activity_id','name','weighting','status_quantitative_weighting'];

    const COLUMN_COMMENTS = [
        'activity_id' => 'Actividad',
        'name' => 'Nombre',
        'weighting' => 'Ponderación',
        'status_quantitative_weighting' => 'El indicador es ponderado (cuantitativo)',
    ];

    public function activity()
    {
        return $this->belongsTo('App\Models\app\Profesor\Activity');
    }
}
