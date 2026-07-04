<?php

namespace App\Http\Livewire\Profesor\Incident;

use App\Models\app\Pescolar\Lapso;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{

    public $profesor,$lapsos,$lapso_active,$evaluacion_id,$estudiants,$evaluacion,$pevaluacion;
    public $modeMain,$modeLoad;

    public $incidents,$estudiant_id;

    public $tabActive;

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $fecha = Carbon::Now();
        $this->modeMain = true;
        $this->modeLoad = false;
        $this->profesor = $profesor;
        $this->incidents = ($profesor) ? $profesor->incidents : collect();

        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();

        $this->tabActive = 'incidents';
    }

    public function render()
    {
        return view('livewire.profesor.incident.index-component');
    }

    public function close()
    {
        $this->modeMain = true;
        $this->modeLoad = false;
    }

    public function setIncident()
    {
        $this->tabActive = 'incidents';
        $this->emit('loadIncident');
    }

    public function setAgreement()
    {
        $this->tabActive = 'agreements';
        $this->emit('loadAgreement');
    }

    public function setAction()
    {
        $this->tabActive = 'actions';
        $this->emit('loadAction');
    }

    public function setInterview()
    {
        $this->tabActive = 'interview';
        $this->emit('loadInterview');
    }
}


/*
incidentLoad
agreementLoad
interviewLoad
*/
