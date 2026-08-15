<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagIndicator extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_indicators';

    protected $fillable = [
        'competency_id',
        'code',
        'description',
        'expected_level',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'competency_id', 'code', 'description', 'expected_level'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

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
