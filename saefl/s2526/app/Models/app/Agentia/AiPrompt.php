<?php

namespace App\Models\app\Agentia;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt_type',
        'name',
        'version',
        'content',
        'description',
        'active',
        'created_by'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the user who created the prompt.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
