<?php

namespace App\Http\Livewire\Profesor\Incident\Interview;

use App\Models\app\Incident\Incident;
use App\Models\app\Pescolar\Lapso;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor,$incidents,$incidents_announcements,$lapsos,$lapso_active,$tabActive,$fecha;

    public $current,$start,$end;

    public $modeIndex,$modeView,$modeCalendar,$modeDetails;

    protected $listeners = ['loadInterview'];
    public function loadInterview()
    {
        $this->mount();
        $this->render();
    }

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $this->fecha = Carbon::Now();
        $this->profesor = $profesor;
        $this->incidents = ($profesor) ? $profesor->incidents : collect(New Incident);
        $this->incidents_announcements = $profesor->incidents_announcements;

        $this->current = Date::now()->startOfMonth();
        $this->start = Date::now()->startOfMonth();
        $this->end = Date::now()->endOfMonth();

        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();
        $this->tabActive = 'inteviews';
    }

    public function render()
    {
        return view('livewire.profesor.incident.interview.index-component');
    }

    public function viewDetails($date)
    {
        $this->incidents = $this->profesor->incidents_announcements->where('date_announcement',$date);
        $this->modeCalendar = true;
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

    public function closeDetails()
    {
        $this->cleanView();
        $this->modeCalendar = true;
        $this->modeDetails = false;
    }

    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeView = false;
        $this->modeCalendar = false;
        $this->modeDetails = false;
    }
}
