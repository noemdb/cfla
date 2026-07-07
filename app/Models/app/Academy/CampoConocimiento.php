<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoConocimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_conocimiento_id', 'asignatura_id', 'observations',
    ];

    protected $table = 'campo_conocimientos';

    public function area_conocimiento()
    {
        return $this->belongsTo(AreaConocimiento::class, 'area_conocimiento_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }
}
