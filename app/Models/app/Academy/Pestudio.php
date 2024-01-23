<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pestudio extends Model
{
    use HasFactory;

    public function grados()
    {
        return $this->hasMany(Grado::class,'pestudio_id');
    }

    public function getGradosActive()
    {
        return $this->grados->where('status_active','true');
    }

    public function scopeActive($query, $flag='true') {
        return $query->where('pestudios.status_active',  $flag='true');
    }

}
