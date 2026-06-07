<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pescolar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institucion_id', 'name', 'description',
        'finicial', 'ffinal', 'date_work', 'date_begin', 'color',
    ];

    public function peducativos()
    {
        return $this->hasMany(Peducativo::class, 'pescolar_id');
    }
}
