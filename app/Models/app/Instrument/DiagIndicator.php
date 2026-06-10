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

    public function competency()
    {
        return $this->belongsTo(DiagCompetency::class, 'competency_id');
    }

    const COLUMN_COMMENTS = [
        'code' => 'Código del indicador',
        'description' => 'Descripción del indicador',
        'expected_level' => 'Nivel esperado',
        'competency_id' => 'Competencia asociada',
    ];
}
