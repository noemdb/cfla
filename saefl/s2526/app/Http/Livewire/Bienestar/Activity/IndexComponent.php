<?php

namespace App\Http\Livewire\Bienestar\Activity;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4'; // ✅ Bootstrap 4

    public $pestudios = [];
    public $autoridad;
    public $list_comment_autoridad;

    public $profesor_id;
    public $grado_id;
    public $seccion_id;
    public $lapso_id;

    public $list_grado = [];
    public $list_seccion = [];
    public $list_lapso = [];

    // 🔹 Campos del modal
    public $activity_id;
    public $activity_comments;
    public $activity_topic;
    public $activity_thematic;
    public $activity_status;

    public $showSuccess = false;

    protected $updatesQueryString = ['grado_id', 'seccion_id', 'lapso_id'];

    protected $rules = [
        'activity_comments' => 'required|string|max:500',
        'activity_status'   => 'required|boolean',
    ];

    public function updating($property)
    {
        $this->resetPage();
    }

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->autoridad = Autoridad::where('user_id', $user->id)->first();
        $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS ?? [];

        $this->pestudios = Pestudio::getPestudios($user->id);
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_lapso = Lapso::select('name', 'id')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    public function updatedGradoId($value)
    {
        $this->list_seccion = Seccion::where('grado_id', $value)->pluck('name', 'id')->toArray();
        $this->seccion_id = null;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['profesor_id', 'grado_id', 'seccion_id', 'lapso_id']);
        $this->list_seccion = [];
        $this->resetPage();
    }

    /** 🧱 Abre el modal con datos del Activity */
    public function openModal($id)
    {
        $activity = Activity::findOrFail($id);
        $this->activity_id = $activity->id;
        $this->activity_topic = $activity->topic;
        $this->activity_thematic = $activity->thematic;
        $this->activity_comments = $activity->comments;
        $this->activity_status = $activity->status ? true : false;

        $this->dispatchBrowserEvent('show-activity-modal');
    }

    /** 💾 Guarda cambios */
    public function saveComment()
    {
        $this->validate();

        $activity = Activity::find($this->activity_id);
        if ($activity) {
            $activity->comments = $this->activity_comments;
            $activity->status = $this->activity_status;
            $activity->save();

            $this->showSuccess = true;
            $this->dispatchBrowserEvent('hide-activity-modal');
            $this->dispatchBrowserEvent('show-success-toast', [
                'message' => 'Actividad actualizada correctamente.',
            ]);
        }
    }

    public function render()
    {
        $query = Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('asignaturas.name', 'like', '%ÁREA COMPLEMENTARIA INTEGRAL%')
            ->orderBy('pevaluacions.created_at', 'desc');

        if ($this->profesor_id) $query->where('pevaluacions.profesor_id', $this->profesor_id);
        if ($this->grado_id) $query->where('pensums.grado_id', $this->grado_id);
        if ($this->seccion_id) $query->where('pevaluacions.seccion_id', $this->seccion_id);
        if ($this->lapso_id) $query->where('pevaluacions.lapso_id', $this->lapso_id);

        $pevaluacions = $query->paginate(10);

        return view('livewire.bienestar.activity.index-component', [
            'pevaluacions' => $pevaluacions,
        ]);
    }
}
