<?php

namespace App\Http\Controllers;

use App\Models\VotingPoll;
use App\Models\VotingSession;
use App\Models\VotingVote;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PollVotingController extends Controller
{
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

        $vote = VotingVote::where('session_uuid', $session->uuid)->first();

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

    /**
     * Display a listing of active polls for voting
     */
    public function index()
    {
        // Obtener encuestas activas que no han expirado
        $polls = VotingPoll::with(['options', 'options.votes'])
            ->where('enable', true)
            ->where(function ($query) {
                $query->whereNull('date')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('date', '<=', now())
                            ->where(function ($timeQuery) {
                                $timeQuery->whereNull('time_active')
                                    ->orWhereRaw('DATE_ADD(date, INTERVAL time_active MINUTE) > NOW()');
                            });
                    });
            })
            ->withCount(['options as votes_count' => function ($query) {
                $query->join('voting_votes', 'voting_options.id', '=', 'voting_votes.option_id');
            }])
            ->orderBy('date', 'desc')
            ->paginate(12);

        // Calcular estadísticas
        $totalVotes = VotingVote::whereHas('option.poll', function ($query) {
            $query->where('enable', true);
        })->count();

        $pollsWithTimeLimit = VotingPoll::where('enable', true)
            ->whereNotNull('time_active')
            ->count();

        return view('voting.index', compact('polls', 'totalVotes', 'pollsWithTimeLimit'));
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
}
