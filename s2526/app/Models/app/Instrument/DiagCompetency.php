<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Pescolar\Pensum;

class DiagCompetency extends Model
{
    protected $table = 'diag_competencies';

    protected $fillable = [
        'referent_id',
        'pensum_id',
        'name',
        'description',
    ];

    const COLUMN_COMMENTS = [
        'referent_id' => 'Referente Normativo',
        'pensum_id' => 'Area de Formacion',
        'name' => 'Nombre',
        'description' => 'Descripción',
    ];

    public function referent()
    {
        return $this->belongsTo(DiagReferent::class, 'referent_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function indicators()
    {
        return $this->hasMany(DiagIndicator::class, 'competency_id');
    }
}
