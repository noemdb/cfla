<?php

namespace App\Http\Livewire\Inicial;

use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\app\Inicial\Eilearningarea;
use App\Models\app\Inicial\Eilearningexpectation;

class EifinalkComponent extends Component
{
    // 🔑 Propiedades principales
    public $profesor_id;
    public $eifinalk;
    public $selected_id = null;
    public $showModal = false; // Flag para controlar el modal
    public $activeTab = 'informesList'; // Propiedad para controlar el tab activo

    // 📝 Campos del formulario
    public $order;
    public $pevaluacion_id;
    public $lapso_id;
    public $estudiant_id;
    public $title;
    public $context_group;
    public $planing_eject;
    public $featured_project;
    public $special_activities;
    public $achievements;
    public $individual_observations;
    public $family_participation;
    public $conclusions;
    public $recommendations;
    public $expected_learnings;
    public $specialist_observation;

    // 📋 Listas y colecciones
    public $list_comment;
    public $list_pevaluacion;
    public $pevaluacions;
    public $learning_areas;
    public $selected_expectations = [];

    // 🔍 Filtros
    public $filter_pevaluacion = '';
    public $filter_estudiant = '';
    public $filter_title = '';
    public $filter_pevaluacion_estudiantes = '';
    public $filter_estudiant_search = '';
    public $estudiantes = [];

    /**
     * Reglas de validación para el formulario
     * @return array
     */
    protected function rules()
    {
        return [
            'order' => 'required',
            'pevaluacion_id' => 'required|exists:pevaluacions,id',
            'estudiant_id' => 'required|exists:estudiants,id',
            'title' => 'required|string|max:191',
            'context_group' => 'nullable|string',
            'planing_eject' => 'nullable|string',
            'featured_project' => 'nullable|string',
            'special_activities' => 'nullable|string',
            'achievements' => 'nullable|string',
            'individual_observations' => 'nullable|string',
            'family_participation' => 'nullable|string',
            'conclusions' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'expected_learnings' => 'nullable|string',
            'specialist_observation' => 'nullable|string',
        ];
    }

    /**
     * Actualiza las áreas de aprendizaje cuando cambia la evaluación
     */
    public function updatedPevaluacionId($value)
    {
        if ($value) {
            $pevaluacion = Pevaluacion::find($value);
            if ($pevaluacion) {
                $this->lapso_id = $pevaluacion->lapso_id;
                $grado = $pevaluacion->grado;
                if ($grado) {
                    $this->learning_areas = Eilearningarea::with('expectations')
                        ->where('grado_id', $grado->id)
                        ->get();
                } else {
                    $this->learning_areas = collect();
                }
            } else {
                $this->learning_areas = collect();
                $this->lapso_id = null;
            }
        } else {
            $this->learning_areas = collect();
        }
    }

    public function updatedFilterPevaluacionEstudiantes($value)
    {
        if ($value) {
            $pevaluacion = Pevaluacion::find($value);
            $this->lapso_id = $pevaluacion->lapso_id;
            $this->estudiantes = $pevaluacion ? $pevaluacion->estudiants : collect();
        } else {
            $this->estudiantes = collect();
            $this->lapso_id = null;
        }
    }

    /**
     * Inicializa el componente
     */
    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $profesor = ($user->IsInicial()) ? $user->profesor : null;
        $this->profesor_id = ($profesor) ? $profesor->id : null;

        $this->close();
        $this->list_comment = Eifinalk::COLUMN_COMMENTS;

        if ($profesor) {
            $this->list_pevaluacion = Pevaluacion::list_pevaluacion($profesor->id);
            $this->pevaluacions = Pevaluacion::where('profesor_id', $this->profesor_id)
                ->with(['seccion', 'lapso', 'pensum.grado']) // Cambiamos a cargar la relación correcta
                ->get();

            // Inicialmente las áreas de aprendizaje estarán vacías hasta que se seleccione una evaluación
            $this->learning_areas = collect();
        } else {
            $this->list_pevaluacion = collect();
            $this->pevaluacions = collect();
            $this->learning_areas = collect();
        }
    }

    /**
     * Renderiza la vista del componente
     */
    public function render()
    {
        $query = Eifinalk::with(['pevaluacion', 'estudiant', 'expectations.area']);

        if ($this->profesor_id) {
            $query->byProfesor($this->profesor_id);

            // Aplicar filtros
            if ($this->filter_pevaluacion) {
                $query->where('pevaluacion_id', $this->filter_pevaluacion);
            }

            if ($this->filter_estudiant) {
                $query->whereHas('estudiant', function ($q) {
                    $q->where('name', 'like', '%' . $this->filter_estudiant . '%')
                        ->orWhere('ci_estudiant', 'like', '%' . $this->filter_estudiant . '%');
                });
            }

            if ($this->filter_title) {
                $query->where('title', 'like', '%' . $this->filter_title . '%');
            }

            // $query->orderBy('created_at', 'desc');
            $query->orderBy('order', 'asc');
            $eifinalks = $query->get();
        } else {
            $eifinalks = collect();
        }

        return view('livewire.inicial.eifinalk-component', [
            'eifinalks' => $eifinalks,
            'pevaluacions' => $this->pevaluacions,
        ]);
    }

    /**
     * Abre el modal para crear un nuevo registro
     */
    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    /**
     * Cierra el modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInput();
    }

    /**
     * Edita un informe final existente
     * @param int $id ID del informe final
     */
    public function edit($id)
    {
        $record = Eifinalk::findOrFail($id);
        $this->selected_id = $id;
        $this->fill($record->toArray());

        // Cargar las áreas de aprendizaje basadas en el grado de la evaluación
        if ($record->pevaluacion) {
            $grado = $record->pevaluacion->grado;
            if ($grado) {
                $this->learning_areas = Eilearningarea::with('expectations')
                    ->where('grado_id', $grado->id)
                    ->get();
            }
        }

        // Cargar las expectativas seleccionadas
        $this->selected_expectations = $record->expectations->pluck('id')->toArray();

        $this->showModal = true;
    }

    /**
     * Actualiza un informe final existente
     */
    public function update()
    {
        $this->validate();

        if ($this->selected_id) {
            $record = Eifinalk::findOrFail($this->selected_id);

            // Actualizar los datos básicos
            $record->update($this->modelData());

            // Actualizar las expectativas seleccionadas
            $expectationsData = [];
            if (!empty($this->selected_expectations)) {
                foreach ($this->selected_expectations as $expectationId) {
                    $expectation = Eilearningexpectation::find($expectationId);
                    if ($expectation) {
                        $expectationsData[$expectationId] = [
                            'eilearningarea_id' => $expectation->eilearningarea_id,
                            'pevaluacion_id' => $this->pevaluacion_id
                        ];
                    }
                }
            }

            // Sincronizar las expectativas (esto eliminará las que no estén en el array)
            $record->expectations()->sync($expectationsData);

            $this->resetInput();
            $this->showModal = false;
            session()->flash('message', 'Informe final actualizado.');
        }
    }

    /**
     * Crea un nuevo informe final
     */
    public function save()
    {
        $this->validate();

        // Crear el informe final
        $eifinalk = Eifinalk::create($this->modelData());

        // Guardar las expectativas seleccionadas
        if (!empty($this->selected_expectations)) {
            $expectationsData = [];
            foreach ($this->selected_expectations as $expectationId) {
                $expectation = Eilearningexpectation::find($expectationId);
                if ($expectation) {
                    $expectationsData[$expectationId] = [
                        'eilearningarea_id' => $expectation->eilearningarea_id,
                        'pevaluacion_id' => $this->pevaluacion_id
                    ];
                }
            }
            $eifinalk->expectations()->attach($expectationsData);
        }

        $this->close();
        $this->emit('eifinalkAdded');
    }

    /**
     * Elimina un informe final
     * @param int $id ID del informe final
     */
    public function delete($id)
    {
        Eifinalk::findOrFail($id)->delete();
        session()->flash('message', 'Informe eliminado.');
    }

    /**
     * Prepara los datos del modelo para guardar/actualizar
     * @return array
     */
    private function modelData()
    {
        return [
            'order' => $this->order,
            'pevaluacion_id' => $this->pevaluacion_id,
            'estudiant_id' => $this->estudiant_id,
            'title' => $this->title,
            'context_group' => $this->context_group,
            'planing_eject' => $this->planing_eject,
            'featured_project' => $this->featured_project,
            'special_activities' => $this->special_activities,
            'achievements' => $this->achievements,
            'individual_observations' => $this->individual_observations,
            'family_participation' => $this->family_participation,
            'conclusions' => $this->conclusions,
            'recommendations' => $this->recommendations,
            'specialist_observation' => $this->specialist_observation,
        ];
    }

    /**
     * Resetea los campos del formulario
     */
    private function resetInput()
    {
        $this->reset([
            'order',
            'lapso_id',
            'pevaluacion_id',
            'estudiant_id',
            'title',
            'context_group',
            'planing_eject',
            'featured_project',
            'special_activities',
            'achievements',
            'individual_observations',
            'family_participation',
            'conclusions',
            'recommendations',
            'specialist_observation',
            'selected_id',
            'learning_areas',
            'selected_expectations',
        ]);
    }

    /**
     * Cierra el formulario y resetea los campos
     */
    public function close()
    {
        $this->closeModal();
    }
}
