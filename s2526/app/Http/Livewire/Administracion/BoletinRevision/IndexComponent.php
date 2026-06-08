<?php

namespace App\Http\Livewire\Administracion\BoletinRevision;

use Livewire\Component;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\BoletinRevision;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\DB;

class IndexComponent extends Component
{
    public $grado_id = null;
    public $seccion_id = null;

    /**
     * index  -> listado de estudiantes
     * crud   -> listado de revisiones
     * create -> formulario de revisión para un estudiante
     */
    public $mode = 'index';

    public $list_grado;
    public $list_seccion;
    public $pensums;
    public $baremo;

    // ==== CONTEXTO PARA CREATE ====
    public $createEstudiant;
    public $createPensums;
    public $createEscala;
    public $createListPensum;
    public $createListProfesor;
    public $createListNota = [];
    public $createListComment = [];
    public $list_comment = [];
    public $list_types = [];
    public $createBoletinRevisions;
    public $selected_revision_id = null; // For edit mode

    public $grades = []; // [student_id][pensum_id] => ['nota_pf' => ..., 'literal' => ..., 'is_aplazada' => ...]

    // ==== CAMPOS DEL FORMULARIO DE REVISIÓN (Livewire) ====
    public $pensum_id;
    public $profesor_id;
    public $numero;
    public $nota;
    public $description;
    public $observations;
    public $status_active = 'true';
    public $type = 'REVISION'; // Default value

    protected $rules = [
        'pensum_id'    => 'required|exists:pensums,id',
        'profesor_id'  => 'required|exists:profesors,id',
        'numero'       => 'required|integer|min:1',
        'nota'         => 'required',
        'description'  => 'nullable|string|max:1000',
        'observations' => 'nullable|string|max:1000',
        'status_active' => 'required|string|in:true,false',
        'type' => 'required|string',
    ];

    public function mount($gradoId = null, $seccionId = null, $mode = null)
    {
        $this->grado_id   = $gradoId ?: null;
        $this->seccion_id = $seccionId ?: null;
        $this->mode       = $mode ?: 'index';

        $this->baremo = new Baremo();

        $this->list_grado             = collect();
        $this->list_seccion           = collect();
        $this->pensums                = collect();
        $this->createEstudiant        = null;
        $this->createPensums          = collect();
        $this->createEscala           = null;
        $this->createListPensum       = collect();
        $this->createListProfesor     = collect();
        $this->createListNota         = [];
        $this->createListComment      = [];
        $this->createBoletinRevisions = collect();

        $this->loadListGrado();
        $this->loadListSeccion();
        $this->loadPensums();

        $this->list_comment = BoletinRevision::COLUMN_COMMENTS;
        $this->list_types = BoletinRevision::TYPES_LIST;
    }

    public function updatedGradoId()
    {
        $this->seccion_id = null;
        $this->loadListSeccion();
        $this->loadPensums();

        if ($this->mode === 'create') {
            $this->mode = 'index';
        }
    }

    public function updatedSeccionId()
    {
        if ($this->mode === 'create') {
            $this->mode = 'index';
        }
    }

    public function setMode(string $mode)
    {
        if (in_array($mode, ['index', 'crud'])) {
            $this->mode = $mode;
        }
    }

    protected function loadListGrado()
    {
        $this->list_grado = Grado::list_pestudio_grado();
    }

    protected function loadListSeccion()
    {
        if ($this->grado_id) {
            $this->list_seccion = Seccion::active("true")->where('grado_id', $this->grado_id)
                ->pluck('name', 'id');
        } else {
            $this->list_seccion = collect();
        }
    }

    protected function loadPensums()
    {
        if ($this->grado_id) {
            $grado = Grado::find($this->grado_id);
            $this->pensums = $grado ? $grado->pensums : collect();
        } else {
            $this->pensums = collect();
        }
    }

    protected function getEstudiants()
    {
        $estudiants = collect();

        if ($this->grado_id || $this->seccion_id) {
            $query = Estudiant::select('estudiants.*')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')

                ->where('estudiants.status_active', '=', 'true')
                ->with(['inscripcion.seccion.grado.pestudio.escala', 'boletin_revisions']);

            if ($this->grado_id) {
                $query->where('grados.id', $this->grado_id);
            }

            if ($this->seccion_id) {
                $query->where('seccions.id', $this->seccion_id);
            }

            $estudiants = $query->get();
        }

        return $estudiants;
    }

    protected function calculateGrades($estudiants)
    {
        $this->grades = [];
        if ($estudiants->isEmpty()) return;

        $estudiantIds = $estudiants->pluck('id')->toArray();
        $pensumIds = $this->pensums->pluck('id')->toArray();

        if (empty($pensumIds)) return;

        // 1. Fetch raw grades grouped by student, pensum, lapso
        $rawGrades = Boletin::select(
            'boletins.estudiant_id',
            'pevaluacions.pensum_id',
            'pevaluacions.lapso_id',
            'pevaluacions.id as pevaluacion_id',
            DB::raw('count(evaluacions.id) as count_evaluacion'),
            DB::raw('sum(boletins.nota) as sum_nota')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->whereIn('boletins.estudiant_id', $estudiantIds)
            ->whereIn('pevaluacions.pensum_id', $pensumIds)
            ->whereNull('evaluacions.deleted_at')
            ->whereNull('pevaluacions.deleted_at')
            ->groupBy('boletins.estudiant_id', 'pevaluacions.pensum_id', 'pevaluacions.lapso_id', 'pevaluacions.id')
            ->get();

        // 2. Fetch adjustments
        $adjustments = BoletinAjuste::whereIn('estudiant_id', $estudiantIds)
            ->whereIn('pevaluacion_id', $rawGrades->pluck('pevaluacion_id')->unique())
            ->get()
            ->groupBy('estudiant_id');

        // 3. Process grades
        $lapsos = Lapso::all();
        $groupedGrades = $rawGrades->groupBy('estudiant_id');

        foreach ($estudiants as $estudiant) {
            $pestudio = $estudiant->pestudio;
            $escala = $pestudio ? $pestudio->escala : null;
            $aprobacion = ($escala && $escala->aprobacion) ? $escala->aprobacion : 0;

            $studentGrades = $groupedGrades->get($estudiant->id, collect());
            $studentAdjustments = $adjustments->get($estudiant->id, collect());

            foreach ($this->pensums as $pensum) {
                $sum_nota_final = 0;
                $count_lapsos = 0;

                foreach ($lapsos as $lapso) {
                    // Find grade for this lapso/pensum
                    $gradeData = $studentGrades->where('pensum_id', $pensum->id)
                        ->where('lapso_id', $lapso->id)
                        ->first();

                    if ($gradeData) {
                        $avg = $gradeData->sum_nota / $gradeData->count_evaluacion;

                        // Add adjustment
                        $adj = $studentAdjustments->where('pevaluacion_id', $gradeData->pevaluacion_id)->first();
                        $ajusteVal = $adj ? $adj->ajuste : 0;

                        $notaLapso = round($avg, 0) + $ajusteVal; // Assuming decimal=0 as per getNota default
                        $notaLapso = ($notaLapso >= 20) ? 20 : $notaLapso;

                        $sum_nota_final += $notaLapso;
                        $count_lapsos++;
                    }
                }

                $nota_final = ($count_lapsos > 0) ? round($sum_nota_final / $count_lapsos, 0) : 0; // Default decimal=0

                // Literal calculation
                $literal = $this->baremo->getLiteral($pestudio->id ?? null, $nota_final);
                $enable_academic_index = $pensum->asignatura->enable_academic_index ?? 'false';

                $nota_pf = is_numeric($nota_final) ? str_pad($nota_final, 2, "0", STR_PAD_LEFT) : $nota_final;
                $display = ($enable_academic_index == "true") ? $nota_pf : $literal;

                $is_aplazada = (is_numeric($nota_final) && $aprobacion !== '' && $nota_final < $aprobacion);

                $this->grades[$estudiant->id][$pensum->id] = [
                    'nota_valor' => $nota_final,
                    'nota_pf' => $nota_pf,
                    'literal' => $literal,
                    'display' => $display,
                    'is_aplazada' => $is_aplazada
                ];
            }
        }
    }


    protected function getBoletinRevisions()
    {
        $query = BoletinRevision::select('boletin_revisions.*')
            ->join('estudiants', 'estudiants.id', '=', 'boletin_revisions.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->orderBy('estudiants.ci_estudiant', 'asc');

        if ($this->grado_id) {
            $query->where('grados.id', $this->grado_id);
        }

        if ($this->seccion_id) {
            $query->where('seccions.id', $this->seccion_id);
        }

        return $query->get();
    }

    /**
     * Prepara el contexto para el formulario de creación
     * (equivalente al BoletinRevisionController@create)
     */
    public function openCreate(int $estudiantId)
    {
        $estudiant = Estudiant::findOrFail($estudiantId);
        $this->createEstudiant = $estudiant;

        $grado               = $estudiant->grado ?: Grado::first();
        $this->createPensums = $grado ? $grado->pensums : collect();

        $pestudio           = $estudiant->pestudio ?: Pestudio::active()->first();
        $escala             = $pestudio ? $pestudio->escala : null;
        $this->createEscala = $escala;

        $this->createListComment = BoletinRevision::COLUMN_COMMENTS;

        if ($grado) {
            $this->createListPensum = Pensum::list_pestudio_pensum($grado->id);
        } else {
            $this->createListPensum = collect();
        }

        $this->createListProfesor = Profesor::list_profesors();

        $this->createListNota = [];
        if ($escala) {
            $minimo = $escala->minimo;
            $maximo = $escala->maximo;
            $this->createListNota['IN'] = 'IN';
            for ($i = $minimo; $i <= $maximo; $i++) {
                $this->createListNota[$i] = $i;
            }
        }

        $this->createBoletinRevisions = $estudiant->boletin_revisions()->get();

        // Valores por defecto del formulario
        $this->pensum_id    = null;
        $this->profesor_id  = null;
        $this->numero       = 1;
        $this->nota         = null;
        $this->description  = null;
        $this->observations = null;
        $this->status_active = 'true';
        $this->type = 'REVISION';

        $this->mode = 'create';
        $this->selected_revision_id = null;
    }

    public function edit($id)
    {
        $revision = BoletinRevision::findOrFail($id);
        $this->selected_revision_id = $revision->id;

        $this->pensum_id    = $revision->pensum_id;
        $this->profesor_id  = $revision->profesor_id;
        $this->numero       = $revision->numero;
        $this->nota         = $revision->nota;
        $this->description  = $revision->description;
        $this->observations = $revision->observations;
        $this->status_active = $revision->status_active;
        $this->type         = $revision->type;
    }

    public function cancelCreate()
    {
        $this->mode = 'index';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->selected_revision_id = null;
        $this->pensum_id    = null;
        $this->profesor_id  = null;
        $this->numero       = 1;
        $this->nota         = null;
        $this->description  = null;
        $this->observations = null;
        $this->status_active = 'true';
        $this->type = 'REVISION';
    }

    /**
     * Guarda la revisión vía Livewire
     */
    public function save()
    {
        if (!$this->createEstudiant) {
            $this->addError('createEstudiant', 'No se encuentra el estudiante para registrar la revisión.');
            return;
        }

        $this->validate();

        if ($this->selected_revision_id) {
            $revision = BoletinRevision::find($this->selected_revision_id);
            if ($revision) {
                $revision->update([
                    'pensum_id'     => $this->pensum_id,
                    'profesor_id'   => $this->profesor_id,
                    'numero'        => $this->numero,
                    'nota'          => $this->nota,
                    'description'   => $this->description,
                    'observations'  => $this->observations,
                    'status_active' => $this->status_active,
                    'type'          => $this->type,
                ]);
                session()->flash('operp_ok', 'Registro actualizado exitosamente');
            }
            $this->resetForm();
        } else {
            BoletinRevision::create([
                'estudiant_id'  => $this->createEstudiant->id,
                'pensum_id'     => $this->pensum_id,
                'profesor_id'   => $this->profesor_id,
                'numero'        => $this->numero,
                'nota'          => $this->nota,
                'description'   => $this->description,
                'observations'  => $this->observations,
                'status_active' => $this->status_active,
                'type'          => $this->type,
            ]);

            // Opcional: avanzar número y limpiar campos solo en create
            $this->numero       = $this->numero + 1;
            $this->nota         = null;
            $this->description  = null;
            $this->observations = null;
            $this->type         = 'REVISION';

            session()->flash('operp_ok', 'Registro guardado exitosamente');
        }

        // Recarga las revisiones del estudiante
        $this->createBoletinRevisions = $this->createEstudiant->boletin_revisions()->get();
    }

    public function render()
    {
        $estudiants        = collect();
        $boletin_revisions = collect();

        if ($this->mode === 'index') {
            $estudiants = $this->getEstudiants();
            $this->calculateGrades($estudiants);
        } elseif ($this->mode === 'crud') {
            $boletin_revisions = $this->getBoletinRevisions();
        }

        return view('livewire.administracion.boletin-revision.index-component', [
            'estudiants'             => $estudiants,
            'boletin_revisions'      => $boletin_revisions,
            'pensums'                => $this->pensums,
            'list_grado'             => $this->list_grado,
            'list_seccion'           => $this->list_seccion,
            'grado_id'               => $this->grado_id,
            'seccion_id'             => $this->seccion_id,
            'baremo'                 => $this->baremo,
            'createEstudiant'        => $this->createEstudiant,
            'createPensums'          => $this->createPensums,
            'createEscala'           => $this->createEscala,
            'createListPensum'       => $this->createListPensum,
            'createListProfesor'     => $this->createListProfesor,
            'createListNota'         => $this->createListNota,
            'createListComment'      => $this->createListComment,
            'createBoletinRevisions' => $this->createBoletinRevisions,
        ]);
    }
}
