<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asignatura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pestudio_id', 'code', 'code_sm', 'name', 'tescala', 'order',
        'hour_t_week', 'hour_p_week', 'unid_credit', 'approved_credit_unir',
        'enable_academic_index', 'enable_lost_regulation', 'enable_official_doc',
        'enable_repairable', 'enable_grupo_estable',
        'observations', 'prelacions',
    ];

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function pensums()
    {
        return $this->hasMany(Pensum::class, 'asignatura_id');
    }

    public function scopeActive($query, $flag = 'true')
    {
        return $query->where('asignaturas.status_active', $flag);
    }

    public function getFullNameAttribute()
    {
        return '[' . $this->code . '] ' . $this->name;
    }
}
