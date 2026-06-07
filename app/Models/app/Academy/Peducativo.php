<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peducativo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pescolar_id', 'manager_id', 'deputy_id', 'assistant_id',
        'name', 'description', 'order',
        'status_active', 'show_quantitative_indicators',
    ];

    public function pescolar()
    {
        return $this->belongsTo(Pescolar::class, 'pescolar_id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function deputy()
    {
        return $this->belongsTo(\App\Models\User::class, 'deputy_id');
    }

    public function assistant()
    {
        return $this->belongsTo(\App\Models\User::class, 'assistant_id');
    }

    public function pestudios()
    {
        return $this->hasMany(Pestudio::class, 'peducativo_id');
    }

    public function getGradosAttribute()
    {
        return Grado::select('grados.*')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.id', $this->id)
            ->where('peducativos.status_active', "true")
            ->where('pestudios.status_active', "true")
            ->where('grados.status_active', "true")
            ->whereNull('peducativos.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->groupBy('grados.id')
            ->orderBy('grados.code_sm')
            ->get();
    }

    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('peducativos.status_active', $flag);
    }

    public static function getPeducativosAttribute()
    {
        return Peducativo::where('peducativos.status_active', "true")->get();
    }

    public function getFullNameAttribute()
    {
        return "{$this->name}";
    }
}
