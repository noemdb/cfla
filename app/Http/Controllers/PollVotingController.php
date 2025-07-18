<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\VotingPoll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PollVotingController extends Controller
{
    public function asistent()
    {
        Visit::logFromRequest(request());

        $polls = VotingPoll::where('enable', true)
            ->with(['options'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('voting.asistent', compact('polls'));
    }

    public function show($token)
    {
        $poll = VotingPoll::where('access_token', $token)->first();

        if (!$poll) {
            abort(404, 'Encuesta no encontrada');
        }

        return view('voting.poll', [
            'accessToken' => $token,
            'poll' => $poll
        ]);
    }

    public function showQR($uuid)
    {
        $session = VotingSession::where('uuid', $uuid)->first();

        if (!$session) {
            abort(404, 'Sesión no encontrada');
        }

        $vote = VotingVote::where('session_uuid', $session->uuid)->first();

        if (!$vote) {
            abort(404, 'Voto no encontrado');
        }

        $participationUrl = route('poll.participation.show', ['uuid' => $session->uuid]);

        $qrCode = QrCode::size(200)
            ->margin(2)
            ->generate($participationUrl);

        return view('voting.showQR', [
            'session' => $session,
            'poll' => $session->poll,
            'vote' => $vote,
            'qrCode' => $qrCode,
            'participationUrl' => $participationUrl
        ]);
    }

    public function showParticipation($uuid)
    {
        $session = VotingSession::where('uuid', $uuid)->first();

        if (!$session) {
            abort(404, 'Sesión no encontrada');
        }

        $vote = VotingVote::where('session_uuid', $session->uuid)->first(); //dd($uuid,$session,$vote );

        if (!$vote) {
            abort(404, 'Voto no encontrado');
        }

        // Obtener estadísticas actuales de la encuesta
        $poll = $session->poll;
        $totalVotes = VotingVote::whereHas('session', function ($query) use ($poll) {
            $query->where('poll_id', $poll->id);
        })->count();

        $optionStats = [];
        foreach ($poll->options as $option) {
            $optionVotes = VotingVote::where('option_id', $option->id)->count();
            $percentage = $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100, 1) : 0;

            $optionStats[] = [
                'label' => $option->label,
                'votes' => $optionVotes,
                'percentage' => $percentage,
                'is_user_choice' => $option->id === $vote->option_id
            ];
        }

        return view('voting.participation', [
            'session' => $session,
            'poll' => $poll,
            'vote' => $vote,
            'totalVotes' => $totalVotes,
            'optionStats' => $optionStats
        ]);
    }

    // public function showParticipation($uuid)
    // {
    //     dd("Método ejecutado con UUID: " . $uuid);
    // }

    public function index()
    {
        // Obtener encuestas activas con sus opciones y conteo de votos
        $polls = VotingPoll::where('enable', true)
            ->with(['options'])
            ->withCount(['votes' => function($query) {
                $query->whereHas('option', function($subQuery) {
                    $subQuery->whereColumn('poll_id', 'voting_polls.id');
                });
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Calcular estadísticas generales
        $activePolls = VotingPoll::where('enable', true)->count();

        $totalVotes = VotingVote::whereHas('option', function($query) {
            $query->whereHas('poll', function($subQuery) {
                $subQuery->where('enable', true);
            });
        })->count();

        $pollsWithTimeLimit = VotingPoll::where('enable', true)
            ->whereNotNull('time_active')
            ->where('time_active', '>', 0)
            ->count();

        return view('voting.index', compact(
            'polls',
            'activePolls',
            'totalVotes',
            'pollsWithTimeLimit'
        ));
    }

    public function result($access_token)
    {
        $poll = VotingPoll::where('access_token', $access_token)
            ->where('enable', true)
            ->first();

        if (!$poll) {
            abort(404, 'Encuesta no encontrada o no está activa');
        }

        return view('voting.result', [
            'poll' => $poll,
            'access_token' => $access_token
        ]);
    }

    public function results()
    {
        $polls = VotingPoll::where('enable', true)
            ->with(['options'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('voting.results', [
            'polls' => $polls
        ]);
    }

    public function guia()
    {
        return view('voting.guia');
    }

    public function proposal()
    {
        return view('voting.proposal');
    }
}
