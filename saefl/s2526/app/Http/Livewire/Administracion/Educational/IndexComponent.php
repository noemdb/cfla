<?php
namespace App\Http\Livewire\Administracion\Educational;

use App\Models\app\Educational\DebateCompetition;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndexComponent extends Component
{
    use WithFileUploads;
    use DebateCompetitionTrait;
    public DebateCompetition $debate_competition;

    public $competition_id;
    public $mode;
    public $attachment;
    public $list_comment;
    public $debates;

    protected $listeners = [
        'showSwal', 'alertConfirm', 'alertQuestion', 'remove',
    ];

    public function mount()
    {
        $this->mode               = 'index';
        $this->list_comment       = DebateCompetition::COLUMN_COMMENTS;
        $this->debate_competition = new DebateCompetition;
    }

    public function render()
    {
        $debate_competitions = DebateCompetition::all();
        return view('livewire.administracion.educational.index-component', [
            'debate_competitions' => $debate_competitions,
        ]
        );
    }

    public function create()
    {
        $this->mode                      = 'create';
        $this->debate_competition        = new DebateCompetition;
        $this->debate_competition->token = DebateCompetition::genToken();
    }

    public function save()
    {
        $this->validate();
        $this->debate_competition->user_id = Auth::id();
        $this->upAttachment();
        $this->debate_competition->save();

        $title = '¡Excelente, buen trabajo! ';
        $html  = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);

        $this->attachment         = null;
        $this->debate_competition = new DebateCompetition;
        $this->mode               = 'index';
    }

    public function edit($id)
    {
        $this->mode               = 'edit';
        $this->debate_competition = DebateCompetition::findOrFail($id);
        $this->competition_id     = $id;
        //dd('hola');
    }

    public function close()
    {
        $this->mode = 'index';
    }

    public function upAttachment()
    {
        $this->validate([
            'attachment' => 'nullable|image|max:1024', // 1MB Max
        ]);
        $this->debate_competition->attachment = ($this->attachment) ? $this->attachment->store('competitions', 'educationals') : $this->debate_competition->attachment;
    }

    public function setModeDebate($id)
    {
        $debate_competition = DebateCompetition::find($id);
        if ($debate_competition) {
            $this->debate_competition = $debate_competition;
            $this->debates            = $debate_competition->debates;
            $this->mode               = 'debate';
            $this->competition_id     = $id;
        }
    }

    public function remove($id)
    {
        $debate_competition = DebateCompetition::findOrFail($id);
        $debate_competition->delete();

        $title = '¡Excelente, buen trabajo! ';
        $html  = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);
        // $this->render();
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title'    => $title,
            'html'     => $html,
            'timer'    => 6000,
            'icon'     => $icon,
            'toast'    => false,
            'position' => 'center',
            'type'     => 'warning',
        ]);
    }

    public function alertConfirm($id)
    {
        $mailer = DebateCompetition::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type'    => 'question',
            'message' => 'Estas seguro? ',
            'text'    => 'Sí realizas esta operación, no la podrás revertir',
            'id'      => $mailer->id,
        ]);
    }

    public function alertQuestion($id, $method)
    {
        $item = DebateCompetition::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:question', [
            'type'    => 'question',
            'message' => 'Estas seguro de eliminar el registro ? ',
            'text'    => 'Sí realizas esta operación, no la podrás revertir.',
            'id'      => $item->id,
            'method'  => $method,
        ]);
    }
}
