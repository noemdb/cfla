<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'asignatura_id');
    }

    public function getFullNameAttribute()
    {
        return '['.$this->code .'] ' . $this->name;
    }
}
