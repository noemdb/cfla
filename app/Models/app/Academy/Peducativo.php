<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peducativo extends Model
{
    use HasFactory;

    public function getGradosAttribute()
    {
        return Grado::select('grados.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id', $this->id)
            ->where('peducativos.status_active', "true")
            ->where('pestudios.status_active', "true")
            ->where('grados.status_active', "true")
            ->groupBy('grados.id')
            ->orderBy('grados.code_sm')
            ->get();
    }

    public function scopeActive($query, $flag='true') {
        return $query->where('peducativos.status_active',  $flag='true');
    }

    public static function getPeducativosAttribute()
    {
        return Peducativo::where('peducativos.status_active', "true")->get();
    }
    
}
