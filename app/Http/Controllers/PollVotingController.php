<?php

namespace App\Http\Controllers;

use App\Models\VotingPoll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PollVotingController extends Controller
{
    public function show($accessToken)
    {
        $poll = VotingPoll::with('options')
            ->where('access_token', $accessToken)
            ->firstOrFail();

        // Verificar si la encuesta ha expirado
        if ($poll->isExpired()) {
            $poll->update(['enable' => false]);
        }

        // Verificar si el usuario ya tiene una sesión
        $sessionId = session('vote_session_id');
        $hasVoted = false;

        if ($sessionId) {
            try {
                $decryptedSessionId = Crypt::decryptString($sessionId);
                $session = VotingSession::where('uuid', $decryptedSessionId)
                    ->where('poll_id', $poll->id)
                    ->first();

                if ($session) {
                    $hasVoted = $session->voted;
                }
            } catch (\Exception $e) {
                // Sesión inválida, crear nueva
                session()->forget('vote_session_id');
            }
        }

        return view('voting.poll', compact('poll', 'hasVoted'));
    }

    public function vote(Request $request, $accessToken)
    {
        $request->validate([
            'option_id' => 'required|exists:voting_options,id',
        ]);

        $poll = VotingPoll::with('options')
            ->where('access_token', $accessToken)
            ->where('enable', true)
            ->firstOrFail();

        // Verificar si la encuesta ha expirado
        if ($poll->isExpired()) {
            return redirect()->back()->with('error', 'La encuesta ha expirado.');
        }

        // Verificar que la opción pertenece a esta encuesta
        $option = $poll->options()->findOrFail($request->option_id);

        // Obtener o crear sesión
        $sessionId = session('vote_session_id');

        if (!$sessionId) {
            return redirect()->back()->with('error', 'Sesión inválida. Recarga la página.');
        }

        try {
            $decryptedSessionId = Crypt::decryptString($sessionId);
            $session = VotingSession::where('uuid', $decryptedSessionId)
                ->where('poll_id', $poll->id)
                ->firstOrFail();

            if ($session->voted) {
                return redirect()->back()->with('error', 'Ya has votado en esta encuesta.');
            }

            if ($session->isExpired()) {
                return redirect()->back()->with('error', 'Tu sesión ha expirado.');
            }

            // Registrar el voto
            VotingVote::create([
                'session_uuid' => $session->uuid,
                'option_id' => $option->id,
            ]);

            // Marcar sesión como votada
            $session->update(['voted' => true]);

            return redirect()->back()->with('success', '¡Tu voto ha sido registrado exitosamente!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el voto.');
        }
    }
}
