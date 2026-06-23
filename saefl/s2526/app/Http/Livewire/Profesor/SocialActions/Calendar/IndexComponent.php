<?php

namespace App\Http\Livewire\Profesor\SocialActions\Calendar;

use App\Models\app\SocialAction\CommunityAction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Livewire\Component;

class IndexComponent extends Component
{
    public $community_action_dates,$current,$start,$end;

    public $modeIndex,$modeView,$modeCalendar,$modeDetails;

    protected $listeners = [ 'updateCalendar'];

    public function mount()
    {
        $this->current = Date::now()->startOfMonth();
        $this->start = Date::now()->startOfMonth();
        $this->end = Date::now()->endOfMonth();

        $this->cleanView();
    }


    public function render()
    {
        $community_actions = CommunityAction::where('user_id',Auth::id())->get();
        return view('livewire.profesor.social-actions.calendar.index-component',compact(
                'community_actions',
            )
        );
    }

    public function updateCalendar()
    {
        // $this->close();
    }

    public function viewDetails($date)
    {
        $this->community_action_dates = CommunityAction::where('date',$date)->get();
        $this->modeDetails = true;
    }

    public function lastMonth()
    {
        $month =$this->start->sub('1 month'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function nextMonth()
    {
        $month =$this->start->add('1 month'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function lastYear()
    {
        $month =$this->start->sub('1 year'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function nextYear()
    {
        $month =$this->start->add('1 year'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function cleanView()
    {
        $this->modeDetails = false;
    }

}
