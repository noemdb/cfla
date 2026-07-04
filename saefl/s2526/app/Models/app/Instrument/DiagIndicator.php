<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagIndicator extends Model
{
    protected $table = 'diag_indicators';

    protected $fillable = [
        'competency_id',
        'code',
        'description',
        'expected_level',
    ];

    const COLUMN_COMMENTS = [
        'competency_id' => 'Competencia',
        'code' => 'Código',
        'description' => 'Descripción',
        'expected_level' => 'Nivel Esperado',
    ];

    public function competency()
    {
        return $this->belongsTo(DiagCompetency::class, 'competency_id');
    }
}
