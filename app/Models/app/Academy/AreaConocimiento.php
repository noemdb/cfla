<?php

namespace App\Models\app\Academy;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaConocimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'peducativo_id', 'pestudio_id', 'leader_id',
        'name', 'code', 'code_sm', 'description', 'observations',
        'order', 'enable_academic_index',
    ];

    protected $table = 'area_conocimientos';

    public function peducativo()
    {
        return $this->belongsTo(Peducativo::class, 'peducativo_id');
    }

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function campo_conocimientos()
    {
        return $this->hasMany(CampoConocimiento::class, 'area_conocimiento_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' [' . ($this->pestudio->code ?? '?') . ']';
    }
}
