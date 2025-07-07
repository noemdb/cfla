<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class VotingVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_uuid',
        'option_id'
    ];

    protected $casts = [
        'option_id' => 'integer'
    ];

    // Relaciones
    public function session(): BelongsTo
    {
        return $this->belongsTo(VotingSession::class, 'session_uuid', 'uuid');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(VotingOption::class, 'option_id');
    }

    public function poll()
    {
        return $this->hasOneThrough(VotingPoll::class, VotingSession::class, 'uuid', 'id', 'session_uuid', 'poll_id');
    }

    // Método estático para crear voto - CORREGIDO
    public static function createVote(string $sessionId, int $optionId): self
    {
        //dd($sessionId, $optionId);
        return static::create([
            'session_id' => $sessionId,
            'option_id' => $optionId
        ]);
    }

    // Métodos de instancia
    public function isValid(): bool
    {
        return $this->session &&
            $this->option &&
            $this->session->voted &&
            $this->session->poll->isActive();
    }
}
