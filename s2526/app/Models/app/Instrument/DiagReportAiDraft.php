<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\AI\AiPrompt;

class DiagReportAiDraft extends Model
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

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function systemPrompt()
    {
        return $this->belongsTo(AiPrompt::class, 'system_prompt_id');
    }

    public function userPrompt()
    {
        return $this->belongsTo(AiPrompt::class, 'user_prompt_id');
    }
}
