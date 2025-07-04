<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VotingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'ip',
        'fingerprint',
        'user_agent',
        'voted',
        'expires_at',
        'poll_id',
    ];

    protected $casts = [
        'voted' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->uuid)) {
                $session->uuid = Str::uuid();
            }

            if (empty($session->expires_at)) {
                $session->expires_at = now()->addMinutes(10);
            }
        });
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(VotingPoll::class, 'poll_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
}
