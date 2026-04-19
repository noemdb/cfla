<?php

namespace App\Livewire\Admin\Educational\Competition;

use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;
use Livewire\Attributes\Layout;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    use WireUiActions;

    public $name, $description, $motive, $date;
    public $showCreateModal = false;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable',
        'motive' => 'nullable',
        'date' => 'required|date',
    ];

    public function toggleStatus($id)
    {
        $competition = DebateCompetition::findOrFail($id);
        $competition->status_active = !$competition->status_active;
        $competition->save();

        $this->notification()->success(
            $title = 'Estado Actualizado',
            $description = "La competición '{$competition->name}' ha sido " . ($competition->status_active ? 'activada' : 'desactivada') . "."
        );
    }

    public function createCompetition()
    {
        $this->validate();

        DebateCompetition::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'motive' => $this->motive,
            'date' => $this->date,
            'token' => DebateCompetition::genToken(),
            'status_active' => false,
        ]);

        $this->reset(['name', 'description', 'motive', 'date', 'showCreateModal']);
        
        $this->notification()->success(
            $title = 'Competición Creada',
            $description = 'La nueva competición ha sido registrada correctamente.'
        );
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $competitions = DebateCompetition::with('user')
            ->orderBy('date', 'desc')
            ->get();

        return view('livewire.admin.educational.competition.index-component', [
            'competitions' => $competitions
        ]);
    }
}
