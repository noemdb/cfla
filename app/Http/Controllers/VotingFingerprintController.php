<?php

namespace App\Http\Controllers;

use App\Models\VotingPoll;
use App\Models\VotingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class VotingFingerprintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string',
            'poll_token' => 'required|string',
        ]);

        $poll = VotingPoll::where('access_token', $request->poll_token)->firstOrFail();

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $fingerprint = $request->fingerprint;

        // Verificar si ya existe una sesión para esta IP/fingerprint
        $existingSession = VotingSession::where('poll_id', $poll->id)
            ->where(function ($query) use ($ip, $fingerprint) {
                $query->where('ip', $ip)->orWhere('fingerprint', $fingerprint);
            })
            ->first();

        if ($existingSession) {
            $encryptedSessionId = Crypt::encryptString($existingSession->uuid);
            session(['vote_session_id' => $encryptedSessionId]);

            return response()->json([
                'success' => true,
                'has_voted' => $existingSession->voted,
            ]);
        }

        // Crear nueva sesión
        $session = VotingSession::create([
            'ip' => $ip,
            'fingerprint' => $fingerprint,
            'user_agent' => $userAgent,
            'poll_id' => $poll->id,
        ]);

        $encryptedSessionId = Crypt::encryptString($session->uuid);
        session(['vote_session_id' => $encryptedSessionId]);

        return response()->json([
            'success' => true,
            'has_voted' => false,
        ]);
    }
}
