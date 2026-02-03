<?php

namespace App\Models\app\Voting;

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
        'private_ip',
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
                $session->expires_at = now()->addHours(24);
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

    public function canVote(): bool
    {
        return !$this->voted && !$this->isExpired();
    }

    /**
     * Verificar si un dispositivo específico ya votó en una encuesta
     */
    public static function hasVotedInPoll($pollId, $fingerprint, $privateIp, $publicIp = null)
    {
        $query = static::where('poll_id', $pollId)
            ->where('voted', true)
            ->where('fingerprint', $fingerprint);

        // Si tenemos IP privada, la usamos para mayor precisión
        if ($privateIp) {
            $query->where('private_ip', $privateIp);
        } else if ($publicIp) {
            // Fallback a IP pública si no tenemos privada
            $query->where('ip', $publicIp);
        }

        return $query->exists();
    }

    /**
     * Crear o recuperar sesión para un dispositivo específico
     */
    public static function createOrRetrieveForDevice($pollId, $fingerprint, $publicIp, $privateIp, $userAgent)
    {
        // Buscar sesión existente por fingerprint y IP privada (más específico)
        $session = static::where('poll_id', $pollId)
            ->where('fingerprint', $fingerprint)
            ->where('private_ip', $privateIp)
            ->first();

        // Si no existe, buscar solo por fingerprint (para compatibilidad)
        if (!$session) {
            $session = static::where('poll_id', $pollId)
                ->where('fingerprint', $fingerprint)
                ->whereNull('private_ip') // Solo sesiones sin IP privada
                ->first();
        }

        // Si no existe ninguna sesión, crear una nueva
        if (!$session) {
            $session = static::create([
                'uuid' => Str::uuid()->toString(),
                'poll_id' => $pollId,
                'fingerprint' => $fingerprint,
                'ip' => $publicIp,
                'private_ip' => $privateIp,
                'user_agent' => $userAgent,
                'voted' => false,
                'expires_at' => now()->addHours(24),
            ]);
        } else {
            // Actualizar información de la sesión existente
            $session->update([
                'ip' => $publicIp ?: $session->ip,
                'private_ip' => $privateIp ?: $session->private_ip,
                'user_agent' => $userAgent ?: $session->user_agent,
            ]);
        }

        return $session;
    }

    /**
     * Generar un identificador único del dispositivo
     */
    public static function generateDeviceId($fingerprint, $privateIp, $publicIp)
    {
        return hash('sha256', $fingerprint . '|' . $privateIp . '|' . $publicIp);
    }
}
