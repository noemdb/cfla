<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VotingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'label',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(VotingPoll::class, 'poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(VotingVote::class, 'option_id');
    }

    public function getVotesCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
