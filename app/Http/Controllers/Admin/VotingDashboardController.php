<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\app\Voting\VotingPoll;
use App\Models\app\Voting\VotingVote;

class VotingDashboardController extends Controller
{
    public function index()
    {
        $polls = VotingPoll::with(['options', 'sessions'])
            // ->withVotesCount()
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_polls' => VotingPoll::count(),
            'active_polls' => VotingPoll::where('enable', true)->count(),
            'total_votes' => VotingVote::count(),
            'finished_polls' => VotingPoll::where('enable', false)->count(),
        ];

        return view('admin.voting.dashboard', compact('polls', 'stats'));
    }
}
