<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo', 'name', 'minimo', 'maximo', 'aprobacion',
    ];

    protected $table = 'escalas';
}
