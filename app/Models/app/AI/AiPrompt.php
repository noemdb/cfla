<?php

namespace App\Models\app\AI;

use Illuminate\Database\Eloquent\Model;
use App\User as User;

class AiPrompt extends Model
{
    protected $table = 'ai_prompts';

    protected $fillable = [
        'prompt_type',
        'name',
        'version',
        'content',
        'description',
        'active',
        'created_by',
    ];

    // Relación con el usuario creador
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('prompt_type', 'system');
    }

    public function scopeUser($query)
    {
        return $query->where('prompt_type', 'user');
    }
}
