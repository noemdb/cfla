<?php

namespace App\Http\Livewire\Poll;

use App\Models\app\Poll\PollMain;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $poll_mains,$poll_main,$poll_main_id,$token;
    public $user_id;
    private $user;
    public $modeMain,$modeVote;

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $this->user = ($user) ? $user : null;
        $this->user_id = ($user) ? $user->id : null;
        $this->modeMain = true;
    }
    public function render()
    {
        $this->poll_mains = PollMain::getPollMainsActiveByUserId($this->user_id);
        return view('livewire.poll.index-component');
    }

    public function vote($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $this->poll_main = $poll_main;
        $poll_token = $poll_main->getTokenAttendeeUserId($this->user_id);
        $this->token = $poll_token->token;
        $this->modeMain = false;
        $this->modeVote = true;

    }

    public function close()
    {
        $this->modeMain = true;
        $this->modeVote = false;
    }
}
