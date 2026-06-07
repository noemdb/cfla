<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'area', 'rol', 'cargo_id', 'group',
        'assit_schedule_id', 'descripcion', 'finicial', 'ffinal',
        'status_schedule',
    ];

    protected $casts = [
        'finicial' => 'date',
        'ffinal' => 'date',
    ];

    protected $table = 'rols';

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
