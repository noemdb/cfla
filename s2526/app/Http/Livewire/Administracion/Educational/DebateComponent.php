<?php
namespace App\Http\Livewire\Administracion\Educational;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use Livewire\Component;
use Livewire\WithFileUploads;

class DebateComponent extends Component
{
    use WithFileUploads;
    use DebateTrait;
    public Debate $debate;

    public $mode, $attachment;
    public $debate_competition, $competition_id, $debate_id, $pevaluacion_id;
    public $list_comment, $list_grado, $list_seccion, $list_pevaluacion;
    public $questions;

    protected $listeners = [
        'showSwal', 'alertConfirm', 'alertQuestion', 'debate_delete',
    ];

    public function mount($competition_id)
    {
        $debate_competition = DebateCompetition::find($competition_id); //dd($debate_competition);
        if ($debate_competition) {
            $this->mode               = 'index';
            $this->list_grado         = Grado::list_pestudio_grado();
            $this->list_seccion       = collect();
            $this->list_pevaluacion   = collect();
            $this->list_comment       = Debate::COLUMN_COMMENTS;
            $this->debate             = new Debate;
            $this->competition_id     = $competition_id;
            $this->debate_competition = $debate_competition;

        }
    }

    public function updatedDebateGradoId($value)
    {
        $this->list_seccion     = Seccion::list_seccion_grado($value);
        $pevaluacions           = Pevaluacion::getPevaluacionsForGrado($value);
        $this->list_pevaluacion = $pevaluacions->pluck('grado_asignatura_name', 'id');
    }

    public function render()
    {
        $debates = Debate::where('competition_id', $this->competition_id)->get();
        return view('livewire.administracion.educational.debate-component', [
            'debates' => $debates,
        ]);
    }

    public function save()
    {
        $this->validate();
        $this->debate->competition_id = $this->competition_id;
        $this->upAttachment();
        $this->debate->save();
        $this->debate_id = $this->debate->id;

        $title = '¡Excelente, buen trabajo! ';
        $html  = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);

        $this->attachment = null;
        $this->debate     = new Debate;
        $this->mode       = 'index';
    }

    public function create()
    {
        $this->mode          = 'create';
        $this->debate        = new Debate;
        $this->debate->token = Debate::genToken(45, 5);
        // $this->debate->grado_id = null;
        // $this->list_grado = Grado::list_pestudio_grado();
    }

    public function edit($id)
    {
        $this->debate       = Debate::findOrFail($id);
        $this->mode         = 'edit';
        $this->list_seccion = Seccion::list_seccion_grado($this->debate->grado_id);

        $pevaluacions = Pevaluacion::getPevaluacionsForGrado($this->debate->grado_id);
        //dd($pevaluacions);

        $this->list_pevaluacion = $pevaluacions->pluck('full_name', 'id');
        //dd($this->list_pevaluacion);
    }

    public function setModeQuestions($id)
    {
        $debate = Debate::find($id);
        if ($debate) {
            $this->debate    = $debate;
            $this->questions = $debate->questions;
            $this->mode      = 'questions';
            $this->debate_id = $id;
        }
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
        $this->debate->attachment = ($this->attachment) ? $this->attachment->store('competitions', 'educationals') : $this->debate->attachment;
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

    public function alertQuestion($id, $method)
    {
        $mailer = Debate::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:question', [
            'type'    => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text'    => 'Sí realizas esta operación, no la podrás revertir.',
            'id'      => $mailer->id,
            'method'  => $method,
        ]);
    }

    public function debate_delete($id)
    {
        $item = Debate::findOrFail($id);
        $item->delete();
        $title = '¡Excelente, buen trabajo! ';
        $html  = 'Operación realizada exitosamente';
        $this->showSwal($title, $html);
    }

}
