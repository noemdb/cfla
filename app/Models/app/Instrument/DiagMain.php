<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
use Illuminate\Database\Eloquent\Model;

class DiagMain extends Model
{
    protected $table = 'diag_mains';

    protected $fillable = [
        'name',
        'description',
        'token',
        'active',
        'referent_id',
        'lapso_id',
        'pestudio_id',
    ];

    public function questions()
    {
        return $this->hasMany(DiagQuestion::class, 'diag_main_id');
    }

    public function sessions()
    {
        return $this->hasMany(DiagSession::class, 'diag_main_id');
    }

    public function referent()
    {
        return $this->belongsTo(DiagReferent::class, 'referent_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function pestudio()
    {
        return $this->belongsTo(Pestudio::class, 'pestudio_id');
    }

    public function pensums()
    {
        return $this->hasManyThrough(
            Pensum::class,
            DiagQuestion::class,
            'diag_main_id',
            'id',
            'id',
            'pensum_id'
        )->distinct();
    }

    public static function active()
    {
        return self::where('active', true);
    }

    public function scopeActive($query, $flag = true)
    {
        return $query->where('diag_mains.status_active', $flag ? 'true' : 'false');
    }

    const COLUMN_COMMENTS = [
        'name' => 'Nombre del diagnóstico',
        'description' => 'Descripción del diagnóstico',
        'token' => 'Token de acceso',
        'active' => 'Estado activo',
        'referent_id' => 'Referente normativo',
        'lapso_id' => 'Lapso académico',
        'pestudio_id' => 'Plan de estudio',
    ];
}
