<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VotingPoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'access_token',
        'enable',
        'date',
        'time_active',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($poll) {
            if (empty($poll->access_token)) {
                $poll->access_token = Str::random(32);
            }
        });
    }

    public function options(): HasMany
    {
        return $this->hasMany(VotingOption::class, 'poll_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(VotingSession::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasManyThrough(
            VotingVote::class,
            VotingOption::class,
            'poll_id', // Foreign key on voting_options table
            'option_id', // Foreign key on voting_votes table
            'id', // Local key on voting_polls table
            'id' // Local key on voting_options table
        );
    }

    public function getVotesCountAttribute(): int
    {
        return $this->votes()->count();
    }

    public function isExpired(): bool
    {
        if (!$this->enable || !$this->date) {
            return false;
        }

        $endTime = $this->date->addMinutes($this->time_active);
        return now()->greaterThan($endTime);
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->enable || !$this->date) {
            return null;
        }

        $endTime = $this->date->addMinutes($this->time_active);
        $remaining = now()->diffInMinutes($endTime, false);

        if ($remaining <= 0) {
            return 'Expirada';
        }

        $hours = intval($remaining / 60);
        $minutes = $remaining % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m restantes";
        }

        return "{$minutes}m restantes";
    }

    public function scopeWithVotesCount($query)
    {
        return $query->withCount([
            'votes' => function ($query) {
                // No necesita condiciones adicionales
            }
        ]);
    }
}
