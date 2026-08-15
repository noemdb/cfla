<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagReportAiDraft extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_report_ai_drafts';

    protected $fillable = [
        'report_id',
        'llm_provider',
        'llm_model',
        'system_prompt_id',
        'user_prompt_id',
        'prompt_version_label',
        'input_hash',
        'output_text',
        'status',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * output_text excluido por volumen; solo metadatos del draft IA.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'report_id', 'llm_provider', 'llm_model',
            'system_prompt_id', 'user_prompt_id', 'prompt_version_label',
            'input_hash', 'status',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function systemPrompt()
    {
        return $this->belongsTo(\App\Models\sys\AiPrompt::class, 'system_prompt_id');
    }

    public function userPrompt()
    {
        return $this->belongsTo(\App\Models\sys\AiPrompt::class, 'user_prompt_id');
    }
}
