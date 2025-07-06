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
                $session->expires_at = now()->addHours(24); // Extender tiempo de expiración
            }
        });
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(VotingPoll::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(VotingVote::class, 'session_uuid', 'uuid');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    /**
     * Verificar si existe una sesión para un dispositivo específico en una encuesta
     */
    public static function existsForDevice($pollId, $fingerprint, $ip = null)
    {
        $query = static::where('poll_id', $pollId);

        if ($fingerprint) {
            $query->where('fingerprint', $fingerprint);
        }

        if ($ip) {
            $query->orWhere(function ($q) use ($pollId, $ip) {
                $q->where('poll_id', $pollId)->where('ip', $ip);
            });
        }

        return $query->exists();
    }

    /**
     * Verificar si un dispositivo ya votó en una encuesta específica
     */
    public static function hasVotedInPoll($pollId, $fingerprint, $ip = null)
    {
        $query = static::where('poll_id', $pollId)->where('voted', true);

        if ($fingerprint) {
            $query->where('fingerprint', $fingerprint);
        }

        if ($ip) {
            $query->orWhere(function ($q) use ($pollId, $ip) {
                $q->where('poll_id', $pollId)->where('ip', $ip)->where('voted', true);
            });
        }

        return $query->exists();
    }

    /**
     * Crear o recuperar sesión para un dispositivo específico
     */
    public static function createOrRetrieveForDevice($pollId, $fingerprint, $ip, $userAgent)
    {
        // Buscar sesión existente por fingerprint primero
        $session = static::where('poll_id', $pollId)
            ->where('fingerprint', $fingerprint)
            ->first();

        // Si no existe por fingerprint, buscar por IP
        if (!$session && $ip) {
            $session = static::where('poll_id', $pollId)
                ->where('ip', $ip)
                ->first();
        }

        // Si no existe ninguna sesión, crear una nueva
        if (!$session) {
            $session = static::create([
                'uuid' => Str::uuid(),
                'poll_id' => $pollId,
                'fingerprint' => $fingerprint,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'voted' => false,
                'expires_at' => now()->addHours(24),
            ]);
        } else {
            // Actualizar información de la sesión existente si es necesario
            $session->update([
                'fingerprint' => $fingerprint ?: $session->fingerprint,
                'ip' => $ip ?: $session->ip,
                'user_agent' => $userAgent ?: $session->user_agent,
            ]);
        }

        return $session;
    }
}
