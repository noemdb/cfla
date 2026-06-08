<?php
namespace App\Models\app\Pescolar\Functions\Profesor;

use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Indicators
{

    public function getPorcAprobados($lapso_id = null, $decimal = 2, $pestudio_id = null)
    {
        $boletins =
        Boletin::select('boletins.nota', 'escalas.minimo', 'escalas.maximo', 'escalas.aprobacion')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('escalas', 'escalas.id', '=', 'pevaluacions.escala_id')
            ->where('pevaluacions.profesor_id', $this->id)
        // ->where('pevaluacions.lapso_id',$lapso_id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');
        // ->get();

        $boletins = (! empty($lapso_id)) ? $boletins->where('pevaluacions.lapso_id', $lapso_id) : $boletins;

        $boletins = $boletins->get();

        $aprobados = 0;
        foreach ($boletins as $boletin) {
            if ($boletin->nota >= $boletin->aprobacion) {
                $aprobados = $aprobados + 1;
            }
        }

        return ($boletins->IsNotEmpty()) ? round((100 * $aprobados / $boletins->count()), $decimal) : null;
    }

    public function getPromedio($lapso_id = null, $decimal = 2, $pestudio_id = null)
    {
        $count =
        Boletin::select(
            DB::raw('count(boletins.id) as value'),
            DB::raw('sum(boletins.nota) as sum_nota')
        )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->where('pevaluacions.profesor_id', $this->id)
        // ->where('pevaluacions.lapso_id',$lapso_id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupby('pevaluacions.profesor_id');
        // ->first();

        $count = (! empty($lapso_id)) ? $count->where('pevaluacions.lapso_id', $lapso_id) : $count;

        $count = $count->first();

        return ($count) ? round(($count->sum_nota / $count->value), $decimal) : null;
    }

    public function getBoletins($lapso_id = null, $pestudio_id = null)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'boletins.evaluacion_id', '=', 'evaluacions.id')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('profesors', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('profesors.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('profesors.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');
        $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id', $lapso_id) : $boletins;
        $boletins = ($pestudio_id) ? $boletins->where('pestudios.id', $pestudio_id) : $boletins;
        $boletins = $boletins->get();
        return $boletins;
    }

    public function getBoletinsPeducativo($lapso_id = null, $peducativo_id = null)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'boletins.evaluacion_id', '=', 'evaluacions.id')
            ->join('pevaluacions', 'evaluacions.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('profesors', 'pevaluacions.profesor_id', '=', 'profesors.id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('profesors.id', $this->id)
            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->wherenull('profesors.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at');
        $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id', $lapso_id) : $boletins;
        $boletins = ($peducativo_id) ? $boletins->where('peducativos.id', $peducativo_id) : $boletins;
        $boletins = $boletins->get();
        return $boletins;
    }

    public function getProfesorIREPeducativo($peducativo_id, $lapso_id = null)
    {
        $peducativo = Peducativo::findorFail($peducativo_id);
        $ieePROM    = $peducativo->getProfesorsIEEsPROM($lapso_id);
        $boletins   = $this->getBoletinsPeducativo($lapso_id, $peducativo->id);
        return (isset($ieePROM)) ? ($boletins->count() / $ieePROM) : null;
    }

    public function getProfesorIRE($pestudio_id, $lapso_id = null)
    {
        $pestudio = Pestudio::findorFail($pestudio_id);
        $ieePROM  = $pestudio->getProfesorsIEEsPROM($lapso_id);
        $boletins = $this->getBoletins($lapso_id, $pestudio->id);
        return (isset($ieePROM)) ? ($boletins->count() / $ieePROM) : null;
    }

    public function getProfesorIEE($lapso_id = null, $pestudio_id = null)
    {
        $goal   = $this->goal_notas_load($lapso_id, $pestudio_id);
        $real   = $this->real_notas_load($lapso_id, $pestudio_id);
        $indice = (! empty($goal)) ? $real / $goal : 0;
        return $indice;
    }

    public function getProfesorIEECN($lapso_id = null, $pestudio_id = null)
    {
        $goal   = $this->goal_notas_load_corte($lapso_id, $pestudio_id);
        $real   = $this->real_notas_load_corte($lapso_id, $pestudio_id);
        $indice = (! empty($goal)) ? $real / $goal : 0;
        return $indice;
    }

    public function getProfesorIEECNPeducativo($lapso_id = null, $peducativo_id = null)
    {
        $goal   = $this->goal_notas_load_corte($lapso_id, null, $peducativo_id);
        $real   = $this->real_notas_load_corte($lapso_id, null, $peducativo_id);
        $indice = (! empty($goal)) ? $real / $goal : 0;
        return $indice;
    }

    public function getProfesorIEEForPeducativo($lapso_id = null, $peducativo_id = null)
    {
        $goal   = $this->goal_notas_load($lapso_id, null, $peducativo_id);
        $real   = $this->real_notas_load($lapso_id, null, $peducativo_id);
        $indice = (! empty($goal)) ? $real / $goal : 0;
        return $indice;
    }

    public function goal_notas_load($lapso_id = null, $pestudio_id = null, $peducativo_id = null)
    {
        $lapso = ($lapso_id) ? Lapso::findOrFail($lapso_id) : null;

        $seccions = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('pevaluacions.profesor_id', $this->id)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pestudios.deleted_at')
            ->groupby('pevaluacions.id');

        $seccions = ($lapso) ? $seccions->where('pevaluacions.lapso_id', $lapso->id) : $seccions;

        $seccions = ($pestudio_id) ? $seccions->where('pestudios.id', $pestudio_id) : $seccions;

        $seccions = ($peducativo_id) ? $seccions->where('peducativos.id', $peducativo_id) : $seccions;

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = $seccion->getEstudiants($lapso_id);
            if ($estudiants->isNotEmpty()) {
                $count = $estudiants->count();
                $total = $total + $count * $seccion->count_evaluacions;
            }
        }
        return ($total) ? $total : 0;
    }

    public function goal_notas_load_corte($lapso_id = null, $pestudio_id = null, $peducativo_id = null)
    {
        $lapso        = Lapso::findOrFail($lapso_id);
        $date_cutnote = $lapso->date_cutnote;
        $seccions     = Seccion::select('seccions.id', DB::raw('count(evaluacions.id) as count_evaluacions'))
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('evaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')

            ->where('pevaluacions.profesor_id', $this->id)
            ->where('evaluacions.fecha', '<=', $date_cutnote)
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pestudios.deleted_at')
            ->groupby('pevaluacions.id');

        $seccions = ($lapso) ? $seccions->where('pevaluacions.lapso_id', $lapso->id) : $seccions;
        $seccions = ($pestudio_id) ? $seccions->where('pestudios.id', $pestudio_id) : $seccions;
        $seccions = ($peducativo_id) ? $seccions->where('peducativos.id', $peducativo_id) : $seccions;

        $seccions = $seccions->get();

        $total = 0;
        foreach ($seccions as $seccion) {
            $estudiants = $seccion->getEstudiants($lapso_id);
            if ($estudiants->isNotEmpty()) {
                $count = $estudiants->count();
                $total = $total + $count * $seccion->count_evaluacions;
            }
        }

        return ($total) ? $total : 0;
    }

    public function real_notas_load_corte($lapso_id = null, $pestudio_id = null, $peducativo_id = null)
    {
        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')

            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')

            ->where('pevaluacions.profesor_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('pestudios.deleted_at')
            ->groupby('pevaluacions.profesor_id');

        if (! empty($lapso_id)) {
            $lapso        = Lapso::findOrFail($lapso_id);
            $count        = $count->where('pevaluacions.lapso_id', $lapso->id);
            $date_cutnote = $lapso->date_cutnote;
            if ($date_cutnote) {
                $count = $count->where('inscripcions.created_at', '<=', $date_cutnote);
                $count = $count->where('evaluacions.fecha', '<=', $date_cutnote);
                $count = $count->where('boletins.created_at', '<=', $date_cutnote);
            }
        }

        $count = ($pestudio_id) ? $count->where('pestudios.id', $pestudio_id) : $count;

        $count = ($peducativo_id) ? $count->where('peducativos.id', $peducativo_id) : $count;

        $count = $count->first();

        return ($count) ? $count->value : 0;
    }

    public function real_notas_load($lapso_id = null, $pestudio_id = null, $peducativo_id = null)
    {
        $count = Boletin::select(DB::raw('count(boletins.id) as value'))
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')

            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')

            ->where('pevaluacions.profesor_id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->whereNotNull('boletins.nota')

        //->where('pevaluacions.seccion_id', 20)

            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->wherenull('inscripcions.deleted_at')
            ->wherenull('pestudios.deleted_at')

            ->groupby('pevaluacions.profesor_id');

        $count = ($pestudio_id) ? $count->where('pestudios.id', $pestudio_id) : $count;
        $count = ($peducativo_id) ? $count->where('peducativos.id', $peducativo_id) : $count;

        if (! empty($lapso_id)) {
            $lapso = Lapso::findOrFail($lapso_id);
            $count = $count->where('pevaluacions.lapso_id', $lapso->id);
            if ($lapso->ffinal) {
                $count = $count->where('inscripcions.created_at', '<=', $lapso->ffinal);
                $count = $count->where('boletins.created_at', '<=', $lapso->ffinal);
            }
        }

        $count = $count->first();

        return ($count) ? $count->value : 0;
    }

    public function goal_pevaluacion_load($lapso_id = null, $pestudio_id = null)
    {
        $pevaluacions = $this->pevaluacions;

        $pevaluacions = ($lapso_id) ? $pevaluacions->where('lapso_id', $lapso_id) : $pevaluacions;

        return (! empty($pevaluacions->count())) ? $pevaluacions->count() : null;
    }

    public function real_pevaluacion_load($lapso_id = null, $pestudio_id = null)
    {
        $pevaluacions = $this->pevaluacions;

        $pevaluacions = ($lapso_id) ? $pevaluacions->where('lapso_id', $lapso_id) : $pevaluacions;

        $count_evaluacion = 0;

        foreach ($pevaluacions as $pevaluacion) {
            $count_evaluacion = (! empty($pevaluacion->evaluacions->count())) ? ++$count_evaluacion : $count_evaluacion;
        }

        return $count_evaluacion;
    }

}
