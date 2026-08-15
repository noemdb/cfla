<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagOption extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_options';

    protected $fillable = [
        'question_id',
        'opcion',
        'valor',
        'orden',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * opcion (texto) excluido por volumen; solo metadatos.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'question_id', 'valor', 'orden'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function question()
    {
        return $this->belongsTo(DiagQuestion::class, 'question_id');
    }
}
