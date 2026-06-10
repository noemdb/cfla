<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use Illuminate\Database\Eloquent\Model;

class DiagCompetency extends Model
{
    protected $table = 'diag_competencies';

    protected $fillable = [
        'referent_id',
        'pensum_id',
        'name',
        'description',
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

    const COLUMN_COMMENTS = [
        'name' => 'Nombre de la competencia',
        'description' => 'Descripción de la competencia',
        'pensum_id' => 'Área de formación',
        'referent_id' => 'Referente normativo',
    ];
}
