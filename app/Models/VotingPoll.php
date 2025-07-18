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
        'description',
        'access_token',
        'enable',
        'date',
        'time_active',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'date' => 'datetime',
        'time_active' => 'integer',
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
        return $this->hasManyThrough(VotingVote::class, VotingSession::class, 'poll_id', 'session_uuid', 'id', 'uuid');
    }

    public function scopeWithVotesCount($query)
    {
        return $query->withCount([
            'sessions as votes_count' => function ($q) {
                //$q->where('voted', true);
            }
        ]);
    }

    public function isActive(): bool
    {
        return $this->enable && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        if (!$this->date) {
            return false;
        }

        $endTime = $this->date->copy()->addMinutes($this->time_active);
        return now()->greaterThan($endTime);
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->date || !$this->enable) {
            return null;
        }

        $endTime = $this->date->copy()->addMinutes($this->time_active);
        $now = now();

        if ($now->greaterThan($endTime)) {
            return 'Expirada';
        }

        $remaining = $now->diffInMinutes($endTime, false);

        if ($remaining <= 0) {
            return 'Expirada';
        }

        $hours = intval($remaining / 60);
        $minutes = $remaining % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m restantes";
        } else {
            return "{$minutes}m restantes";
        }
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->sessions()->where('voted', true)->count();
    }

    public function getVotingUrlAttribute(): string
    {
        return route('poll.vote', ['token' => $this->access_token]);
    }

    public function getResultsUrlAttribute(): string
    {
        return route('poll.results', ['token' => $this->access_token]);
    }

    public function scopeActive($query)
    {
        return $query->where('enable', true);
    }

    /**
     * Obtener estadísticas de participación única
     */
    public function getUniqueParticipantsCount(): int
    {
        return $this->sessions()->where('voted', true)->count();
    }

    /**
     * Verificar si un dispositivo puede votar en esta encuesta
     */
    public function canDeviceVote($fingerprint, $privateIp = null, $publicIp = null): bool
    {
        // Verificar si la encuesta está activa
        if (!$this->isActive()) {
            return false;
        }

        // Verificar si el dispositivo ya votó
        return !VotingSession::hasVotedInPoll($this->id, $fingerprint, $privateIp, $publicIp);
    }

    /**
     * Obtener estadísticas detalladas de la encuesta
     */
    public function getDetailedStats(): array
    {
        $totalVotes = $this->getUniqueParticipantsCount();
        $optionsWithVotes = $this->options()->with(['votes' => function ($query) {
            $query->join('voting_sessions', 'voting_votes.session_uuid', '=', 'voting_sessions.uuid')
                ->where('voting_sessions.voted', true);
        }])->get();

        $results = [];
        foreach ($optionsWithVotes as $option) {
            $voteCount = $option->votes->count();
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;

            $results[] = [
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $percentage
            ];
        }

        return [
            'total_votes' => $totalVotes,
            'results' => $results,
            'is_active' => $this->isActive(),
            'time_remaining' => $this->time_remaining
        ];
    }
}
