<?php

namespace App\Http\Livewire\Movile\Evaluacion;

use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ExecutionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-5';
    protected $listeners = [ 'showSwal','alertConfirm','alertQuestion' ];

    public $evaluacions;
    public $user,$pestudios;
    public $list_grado,$list_seccion,$list_lapso,$list_profesor,$list_pensum;
    public $manager_id,$profesor_id,$grado_id,$seccion_id,$lapso_id,$pensum_id;
    public $finicial,$ffinal,$status_execution;    
    public $showGuiaModal = false;

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->user = $user;
        $this->manager_id = $user->id;
        $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();

        $this->list_grado = Pestudio::list_pestudio_grado_manage($this->manager_id); //dd($this->list_grado);
        $this->list_seccion = collect();
        $this->list_pensum = Pensum::list_pestudio_pensum_manage($this->grado_id,$this->manager_id); //dd($this->list_pensum);
        $this->list_profesor = Profesor::list_profesors_manage($this->grado_id,$this->manager_id);
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $this->evaluacions = collect();
    }

    public function render()
    {
        return view('livewire.movile.evaluacion.execution-component', [
            'evaluaciones' => $this->getBaseQuery()->paginate(10) // Menos registros para móvil
        ]);
    }

    // Método toggle para controlar el modal
    public function toggleGuiaModal()
    {
        $this->showGuiaModal = !$this->showGuiaModal;
    }

    /**
     * Query base optimizado usando relaciones Eloquent
     */
    private function getBaseQuery()
    {
        return Evaluacion::with([
            'pevaluacion.seccion',
            'pevaluacion.lapso', 
            'pevaluacion.profesor',
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.pensum.pestudio.peducativo',
            'boletins'
        ])
        ->whereHas('pevaluacion.pensum.pestudio.peducativo', function($query) {
            $query->where(function($q) {
                $q->where('manager_id', $this->manager_id)
                  ->orWhere('assistant_id', $this->manager_id)
                  ->orWhere('deputy_id', $this->manager_id);
            });
        })
        ->when($this->grado_id, function($query) {
            $query->whereHas('pevaluacion.pensum', function($q) {
                $q->where('grado_id', $this->grado_id);
            });
        })
        ->when($this->seccion_id, function($query) {
            $query->whereHas('pevaluacion', function($q) {
                $q->where('seccion_id', $this->seccion_id);
            });
        })
        ->when($this->lapso_id, function($query) {
            $query->whereHas('pevaluacion', function($q) {
                $q->where('lapso_id', $this->lapso_id);
            });
        })
        ->when($this->pensum_id, function($query) {
            $query->whereHas('pevaluacion', function($q) {
                $q->where('pensum_id', $this->pensum_id);
            });
        })
        ->when($this->profesor_id, function($query) {
            $query->whereHas('pevaluacion', function($q) {
                $q->where('profesor_id', $this->profesor_id);
            });
        })
        ->when($this->finicial, function($query) {
            $query->where('fecha', '>=', $this->finicial);
        })
        ->when($this->ffinal, function($query) {
            $query->where('fecha', '<=', $this->ffinal);
        })
        ->when(!is_null($this->status_execution), function($query) {
            $query->where('status_execution', $this->status_execution);
        })
        ->orderBy('created_at')
        ->select('evaluacions.*');
    }

    public function search()
    {
        $this->resetPage();
    }

    public function updatedGradoId($value)
    {
        if ($value) {
            $this->list_seccion = Seccion::list_seccion_grado($this->grado_id);
            $this->list_pensum = Pensum::list_pestudio_pensum_manage($this->grado_id,$this->manager_id);
            $this->list_profesor = Profesor::list_profesors_manage($this->grado_id,$this->manager_id);
        } else {
            $this->list_seccion = collect();
        }
        $this->resetPage();
    }

    // Métodos adicionales para resetear paginación cuando cambien otros filtros
    public function updatedSeccionId($value) { $this->resetPage(); }
    public function updatedLapsoId($value) { $this->resetPage(); }
    public function updatedPensumId($value) { $this->resetPage(); }
    public function updatedProfesorId($value) { $this->resetPage(); }
    public function updatedFinicial($value) { $this->resetPage(); }
    public function updatedFfinal($value) { $this->resetPage(); }
    public function updatedStatusExecution($value) { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset([
            'grado_id',
            'seccion_id', 
            'lapso_id',
            'pensum_id',
            'profesor_id',
            'finicial',
            'ffinal',
            'status_execution'
        ]);
        
        // Resetear también las listas dependientes
        $this->list_seccion = collect();
        $this->list_pensum = Pensum::list_pestudio_pensum_manage(null, $this->manager_id);
        $this->list_profesor = Profesor::list_profesors_manage(null, $this->manager_id);
        
        // Ejecutar búsqueda con filtros limpios
        $this->search();
        
        // Emitir notificación de éxito
        $this->emit('swal', [
            'title' => 'Filtros Reiniciados',
            'text' => 'Todos los filtros han sido limpiados correctamente',
            'icon' => 'success',
            'timer' => 2000
        ]);
    }

    public function change($id, $status)
    {
        $evaluacion = Evaluacion::find($id);
        $evaluacion->status_execution = $status;
        $evaluacion->save();

        $title = '¡Excelente, buen trabajo! ';
        $text = $status 
                ? 'La evaluación ha sido marcada como EJECUTADA' 
                : 'La evaluación ha sido marcada como PENDIENTE';
        $this->showSwal($title,$text);
        
        $this->search();
    }

    public function showSwal($title,$text,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'text' => $text,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>true,
            'position'=>'top-end',
        ]);
    }
}