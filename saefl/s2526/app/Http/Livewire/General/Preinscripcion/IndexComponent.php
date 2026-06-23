<?php

namespace App\Http\Livewire\General\Preinscripcion;

use Livewire\Component;

class IndexComponent extends Component
{

    // public function mount($token)
    // {
    //     $poll_token =PollToken::where('token',$token)->first();

    //     if (empty($poll_token)) abort(403, 'Acción no autorizada');

    //     $this->token = ($poll_token) ? $token : null;

    //     $poll_main = ($poll_token) ? PollMain::find($poll_token->poll_main_id) : null ;
    //     $status_active = $poll_main->status_active;

    //     $representant = ($poll_token) ? $poll_token->representant : null ;
    //     $estudiants = ($representant) ? $representant->estudiants : null ;

    //     $this->poll_token = $poll_token;
    //     $this->poll_main = $poll_main;
    //     $this->status_active = $status_active;

    //     $this->list_comment_poll_answer = PollAnswer::COLUMN_COMMENTS;
    //     $this->status_solvent = false;

    //     $this->emit('QuestionsFocus');
    // }

    public function render()
    {
        return view('livewire.general.preinscripcion.index-component');
    }
}
