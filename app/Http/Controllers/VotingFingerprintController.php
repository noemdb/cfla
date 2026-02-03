<?php

namespace App\Http\Controllers;

use App\Models\app\Voting\VotingPoll;
use App\Models\app\Voting\VotingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VotingFingerprintController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string|max:255',
            'poll_token' => 'required|string',
            'private_ip' => 'nullable|string|max:45',
        ]);

        try {
            $poll = VotingPoll::where('access_token', $request->poll_token)->firstOrFail();

            $publicIp = $request->ip();
            $privateIp = $request->private_ip;
            $userAgent = $request->userAgent();
            $fingerprint = $this->sanitizeFingerprint($request->fingerprint);

            Log::info('Fingerprint store request with private IP', [
                'poll_id' => $poll->id,
                'public_ip' => $publicIp,
                'private_ip' => $privateIp,
                'fingerprint' => substr($fingerprint, 0, 8) . '...',
            ]);

            // Verificar si el dispositivo específico ya votó en esta encuesta
            $hasVoted = VotingSession::hasVotedInPoll($poll->id, $fingerprint, $privateIp, $publicIp);

            if ($hasVoted) {
                $existingSession = VotingSession::where('poll_id', $poll->id)
                    ->where('fingerprint', $fingerprint)
                    ->where(function ($query) use ($privateIp, $publicIp) {
                        if ($privateIp) {
                            $query->where('private_ip', $privateIp);
                        } else {
                            $query->where('ip', $publicIp);
                        }
                    })
                    ->where('voted', true)
                    ->first();

                return response()->json([
                    'success' => true,
                    'has_voted' => true,
                    'message' => 'Este dispositivo ya ha participado en esta encuesta.',
                    'session_uuid' => $existingSession ? $existingSession->uuid : null,
                ]);
            }

            // Verificar si la encuesta está activa
            if (!$poll->isActive()) {
                return response()->json([
                    'success' => false,
                    'has_voted' => false,
                    'message' => 'La encuesta no está activa actualmente.',
                    'poll_status' => [
                        'is_active' => false,
                        'is_expired' => $poll->isExpired(),
                        'time_remaining' => $poll->time_remaining,
                    ]
                ]);
            }

            // Crear o recuperar sesión para este dispositivo específico
            $session = DB::transaction(function () use ($poll, $fingerprint, $publicIp, $privateIp, $userAgent) {
                return VotingSession::createOrRetrieveForDevice(
                    $poll->id,
                    $fingerprint,
                    $publicIp,
                    $privateIp,
                    $userAgent
                );
            });

            Log::info('Session created/retrieved with private IP', [
                'session_uuid' => $session->uuid,
                'poll_id' => $poll->id,
                'public_ip' => $publicIp,
                'private_ip' => $privateIp,
                'voted' => $session->voted,
            ]);

            return response()->json([
                'success' => true,
                'has_voted' => $session->voted,
                'session_uuid' => $session->uuid,
                'message' => $session->voted ?
                    'Este dispositivo ya ha participado en esta encuesta.' :
                    'Dispositivo registrado correctamente.',
                'poll_status' => [
                    'is_active' => $poll->isActive(),
                    'time_remaining' => $poll->time_remaining,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Poll not found', ['poll_token' => $request->poll_token]);

            return response()->json([
                'success' => false,
                'message' => 'Encuesta no encontrada.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in fingerprint store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poll_token' => $request->poll_token,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.',
            ], 500);
        }
    }

    /**
     * Sanitizar y validar el fingerprint recibido
     */
    private function sanitizeFingerprint($fingerprint)
    {
        // Remover caracteres no deseados y limitar longitud
        $sanitized = preg_replace('/[^a-zA-Z0-9]/', '', $fingerprint);
        return substr($sanitized, 0, 64);
    }

    /**
     * Verificar el estado de una sesión específica
     */
    public function checkSession(Request $request)
    {
        $request->validate([
            'session_uuid' => 'required|string',
        ]);

        try {
            $session = VotingSession::where('uuid', $request->session_uuid)->firstOrFail();

            return response()->json([
                'success' => true,
                'session' => [
                    'uuid' => $session->uuid,
                    'voted' => $session->voted,
                    'expires_at' => $session->expires_at,
                    'is_expired' => $session->isExpired(),
                    'public_ip' => $session->ip,
                    'private_ip' => $session->private_ip,
                ],
                'poll' => [
                    'id' => $session->poll->id,
                    'title' => $session->poll->title,
                    'is_active' => $session->poll->isActive(),
                    'time_remaining' => $session->poll->time_remaining,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión no encontrada.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error checking session', [
                'session_uuid' => $request->session_uuid,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.',
            ], 500);
        }
    }
}
