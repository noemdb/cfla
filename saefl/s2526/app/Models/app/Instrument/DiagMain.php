<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Pensum;

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

    const COLUMN_COMMENTS = [
        'name' => 'Nombre',
        'token' => 'Ident. de acceso',
        'description' => 'Descripción',
        'status' => 'Estado (Activo/Desactivo)',
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
        return $this->belongsTo(\App\Models\app\Pescolar\Lapso::class, 'lapso_id'); // Assuming Lapso model namespace
    }

    public function pestudio()
    {
        return $this->belongsTo(\App\Models\app\Pescolar\Pestudio::class, 'pestudio_id'); // Assuming Pestudio model namespace
    }

    public static function active()
    {
        return DiagMain::where('active',true)->get();
    }

    public function scopeActive($query, $flag = true)
    {
        return $query->where('diag_mains.status_active', $flag);
    }

    public function pensums()
    {
        return $this->hasManyThrough(
            Pensum::class,
            DiagQuestion::class,
            'diag_main_id', // Foreign key on DiagQuestion table
            'id', // Foreign key on Pensum table
            'id', // Local key on DiagMain table
            'pensum_id' // Local key on DiagQuestion table
        )->distinct();
    }
}
