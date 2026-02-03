<?php

namespace App\Models\app\Voting;

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
        'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(VotingPoll::class, 'poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(VotingVote::class, 'option_id');
    }

    public function getPercentageAttribute(): float
    {
        $totalVotes = $this->poll->total_votes;

        if ($totalVotes === 0) {
            return 0.0;
        }

        return round(($this->votes_count / $totalVotes) * 100, 2);
    }
}
