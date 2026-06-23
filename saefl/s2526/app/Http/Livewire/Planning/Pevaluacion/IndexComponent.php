<?php

namespace App\Http\Livewire\Planning\Pevaluacion;


use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Escala;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Leader;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';
    public $search = '', $paginate = 10, $name;

    use updatedTrait;

    public $sortField = 'pevaluacions.id';
    public $sortDirection = 'asc';

    use updatedTrait;

    // 👇 MÉTODO UNIVERSAL PARA CAMBIAR ORDEN
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingName()
    {
        $this->resetPage();
    }
    public function updatingPestudioId()
    {
        $this->resetPage();
    }
    public function updatingGradoId()
    {
        $this->resetPage();
    }
    public function updatingSeccionId()
    {
        $this->resetPage();
    }
    public function updatingLapsoId()
    {
        $this->resetPage();
    }
    public function updatingPaginate()
    {
        $this->resetPage();
    }

    public $activity, $evaluacions, $objetivo, $comments, $status, $observations, $select_id, $profesor_name;
    public $pestudio_id, $list_pestudio;

    public $modeIndex, $modeAssign, $modeEdit;

    public $manager_id, $leader_id, $pevaluacion_id, $profesor_id, $grado_id, $seccion_id, $lapso_id, $pensum_id, $grupo_estable_id, $description, $lapso_current, $now;
    public $list_grado, $list_seccion, $list_lapso, $list_profesors, $list_profesor, $list_pensum, $list_grupo_estable, $tipo_list, $baremo_apply_list;
    public $user_id, $pestudios, $peducativos, $escala_list, $status_note_report;
    public $list_comment;


    public function updatedPestudioId()
    {
        $this->resetPage();
        if ($this->pestudio_id) {
            $this->list_grado = Grado::list_pestudio_grado($this->pestudio_id);
            $this->list_profesor = Profesor::list_profesors_pestudio($this->pestudio_id);
        } else {
            $this->list_grado = collect();
            $this->list_profesor = collect();
        }
        $this->grado_id = null;
        $this->profesor_id = null;
        $this->seccion_id = null;
    }

    public function updatedGradoId()
    {
        $this->resetPage();
        if ($this->grado_id) {
            $this->list_seccion = Seccion::list_seccion_grado($this->grado_id);
        } else {
            $this->list_seccion = collect();
        }
        $this->seccion_id = null;
    }

    public function setProfesorLists($value = null)
    {
        $profesors = Profesor::orderby('profesors.lastname', 'asc')
            ->select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->orderby('profesor_fullname', 'asc')
            ->where('profesors.status_active', 'true');
        $profesors = ($value) ? $profesors->where('profesors.name', 'like', '%' . $value . '%')->orWhere('profesors.lastname', 'like', '%' . $value . '%') : $profesors;
        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    function mount()
    {
        $user = User::find(Auth::id());
        $this->user_id = $user->id;
        $this->leader_id = $user->id;
        $this->pestudios = Leader::getPestudioForLeader($this->leader_id); //dd($this->pestudios,$this->leader_id);
        $this->peducativos = Leader::getPeducativosForLeader($this->leader_id); //dd($this->peducativos);

        $this->list_grado = Leader::getGradosForLeader($this->leader_id)->pluck('name', 'id'); //dd($this->list_grado);
        $this->list_pensum = Leader::getPensumsForLeader($this->leader_id)->pluck('asignatura_fullname', 'id'); //dd($this->list_pensum);
        $this->list_seccion = collect();
        $this->list_pestudio = $this->pestudios->pluck('name', 'id'); //dd($this->list_pestudio);
        $this->setProfesorLists();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $this->list_grupo_estable = GrupoEstable::list_grupo_estable_full();
        $this->tipo_list = Pevaluacion::pevalaucion_tipo_list();
        $this->baremo_apply_list = Baremo::baremo_apply_list();
        $this->escala_list = Escala::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;

        $this->lapso_current = Lapso::current(); //dd($this->lapso_current);

        $this->now = Carbon::now();

        // $this->list_profesor = Array();dd();
        // $this->list_profesor = Profesor::listProfesorsIndexado(); //dd($this->list_profesor);
        $this->list_profesor = Profesor::list_profesors(); //dd($this->list_profesor);

        $this->evaluacions = collect();

        $this->close();

        //Carga del lapso altual
        $this->lapso_id = $this->lapso_current->id ?? null; //dd($this->lapso_id);

    }

    public function save()
    {
        // Definir reglas base
        $rules = [
            'grado_id' => 'required|integer|exists:grados,id',
            'profesor_id' => 'required|integer|exists:profesors,id',
            'lapso_id' => 'required|integer|exists:lapsos,id',
            'seccion_id' => 'required|integer|exists:seccions,id',
            'pensum_id' => 'required|integer|exists:pensums,id',
            'description' => 'nullable|string|max:500',
            'grupo_estable_id' => 'nullable|integer|exists:grupo_estables,id',
            'status_note_report' => 'nullable|boolean',
        ];

        // Regla de unicidad: evitar duplicados en (lapso, seccion, pensum)

        // dd(

        //     $this->lapso_id,
        //     $this->seccion_id,
        //     $this->pensum_id
        // );
        $uniqueRule = Rule::unique('pevaluacions')
            ->where('lapso_id', $this->lapso_id)
            ->where('seccion_id', $this->seccion_id)
            ->where('pensum_id', $this->pensum_id)
            ->whereNull('deleted_at')
            ;

        // Si es edición, ignorar el registro actual
        if ($this->pevaluacion_id) {
            $uniqueRule->ignore($this->pevaluacion_id);
        }

        // Aplicar la regla de unicidad al campo pensum_id (o a cualquiera; el mensaje se mostrará ahí)
        $rules['pensum_id'] .= '|' . $uniqueRule;

        // Validar con mensajes personalizados
        $messages = [
            'pensum_id.unique' => 'Ya existe un plan de evaluación asignado para esta asignatura, sección y lapso.',
            'grado_id.exists' => 'El grado seleccionado no es válido.',
            'profesor_id.exists' => 'El profesor seleccionado no es válido.',
            'lapso_id.exists' => 'El lapso seleccionado no es válido.',
            'seccion_id.exists' => 'La sección seleccionada no es válida.',
            'pensum_id.exists' => 'La asignatura seleccionada no es válida.',
        ];

        $this->validate($rules, $messages);

        // Crear o actualizar el modelo
        $pevaluacion = $this->pevaluacion_id
            ? Pevaluacion::findOrFail($this->pevaluacion_id)
            : new Pevaluacion();

        $pevaluacion->fill([
            'grado_id' => $this->grado_id,
            'profesor_id' => $this->profesor_id,
            'lapso_id' => $this->lapso_id,
            'seccion_id' => $this->seccion_id,
            'pensum_id' => $this->pensum_id,
            'description' => $this->description,
            'grupo_estable_id' => $this->grupo_estable_id,
            'status_note_report' => (bool) $this->status_note_report,
        ]);

        $pevaluacion->save();

        // Limpiar el formulario
        $this->reset([
            'pevaluacion_id',
            'grado_id',
            'profesor_id',
            'lapso_id',
            'seccion_id',
            'pensum_id',
            'description',
            'grupo_estable_id',
            'status_note_report',
        ]);

        // Mostrar mensaje de éxito
        $this->showSwal(
            '¡Excelente!',
            $this->modeEdit ? 'Asignación actualizada exitosamente.' : 'Asignación creada exitosamente.'
        );

        // Volver al modo índice
        $this->close();
    }

    public function render()
    {
        $name = $this->name;
        $search = $this->search;
        $leaderId = $this->leader_id;

        $filters = array_filter([
            'seccion_id' => $this->seccion_id ?? null,
            'grado_id' => $this->grado_id ?? null,
            'lapso_id' => $this->lapso_id ?? null,
            'pestudio_id' => $this->pestudio_id ?? null,
            'profesor_id' => $this->profesor_id ?? null,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $pevaluacions = Leader::getPevaluacionesForLeader($leaderId, $filters, true, $this->paginate);

        return view('livewire.planning.pevaluacion.index-component', [
            'pevaluacions' => $pevaluacions
        ]);
    }

    public function setAssign()
    {
        $this->close();
        $this->modeAssign = true;
        $this->profesor_name = null;
        $this->setProfesorLists();
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $grado = $pevaluacion->grado;
        $pestudio = $pevaluacion->pestudio;
        $this->pevaluacion_id = $pevaluacion->id;
        $this->grado_id = $grado->id;
        $this->pestudio_id = $pestudio->id;
        $this->list_grado = Grado::list_pestudio_grado($this->pestudio_id);
        $this->list_seccion = Seccion::list_seccion_grado($this->grado_id);
        $this->list_profesor = Profesor::list_profesors_pestudio($this->pestudio_id);

        $this->profesor_id = $pevaluacion->profesor_id;
        $this->lapso_id = $pevaluacion->lapso_id;
        $this->seccion_id = $pevaluacion->seccion_id;
        $this->pensum_id = $pevaluacion->pensum_id;
        $this->grupo_estable_id = $pevaluacion->grupo_estable_id;
        $this->status_note_report = $pevaluacion->status_note_report;
        $this->description = $pevaluacion->description;

        $this->modeIndex = true;
        $this->modeAssign = false;
        $this->modeEdit = true;
        $this->profesor_name = null;
        $this->setProfesorLists();
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeAssign = false;
        $this->modeEdit = false;
        $this->search = null;
        $this->seccion_id = null;
        $this->grado_id = null;
        $this->pensum_id = null;
        $this->lapso_id = null;
        $this->profesor_id = null;
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
        ]);
    }

    public function setIndexMode()
    {
        $this->modeIndex = true;
        $this->modeAssign = false;
        $this->modeEdit = false;
    }

    public function delete($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        if ($pevaluacion) {
            $pevaluacion->delete();
            $this->close();
            $this->pevaluacion_id = null;
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Registro fue eliminado exitosamente';
            $this->showSwal($title, $html);
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'pestudio_id',
            'profesor_id',
            'grado_id',
            'seccion_id',
            'lapso_id',
            'paginate'
        ]);
        $this->resetPage();
    }
}
