<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotingVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_uuid',
        'option_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(VotingSession::class, 'session_uuid', 'uuid');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(VotingOption::class, 'option_id');
    }
}
