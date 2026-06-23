<?php

namespace App\Models\app\Pescolar\Functions\Profesor;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

trait Lists
{

    public static function list_profesors_pestudio($pestudio_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $profesors = Profesor::query()
            ->select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")

            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->orderby('profesors.lastname', 'asc')
            ->groupBy('profesors.id');

        $profesors = ($pestudio_id) ? $profesors->where('pestudios.id', $pestudio_id) : $profesors;

        $profesors = $profesors->pluck('profesor_fullname', 'id');

        return $profesors;
    }

    public static function getProfesorForLeaderId($leader_id): Collection
    {
        return Profesor::query()
            ->select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.leader_id', $leader_id)
            ->where('profesors.status_active', "true")

            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->orderby('profesors.lastname', 'asc')

            ->groupBy('profesors.id')
            ->get();
    }

    public static function list_profesors_leader($leader_id) /* usada para llenar los objetos de formularios select*/
    {
        $profesors = Profesor::query()
            ->select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.leader_id', $leader_id)
            ->where('profesors.status_active', "true")

            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->orderby('profesors.lastname', 'asc')
            ->groupBy('profesors.id');
        $profesors = $profesors->pluck('profesor_fullname', 'id');

        return $profesors;
    }

    public static function getProfesorForManagerIdEducativo($grado_id = null, $manager_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $profesors = Profesor::query()
            ->select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('profesors.status_active', "true")
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->orderby('profesors.lastname', 'asc')
            ->groupBy('profesors.id');

        $profesors = ($grado_id) ? $profesors->where('grados.id', $grado_id) : $profesors;

        if ($manager_id) {
            $profesors =
                $profesors->where(
                    function ($query) use ($manager_id) {
                        $query->orWhere('peducativos.manager_id', $manager_id)
                            ->orWhere('peducativos.assistant_id', $manager_id)
                            ->orWhere('peducativos.deputy_id', $manager_id)
                        ;
                    }
                );
        }

        $profesors = $profesors->get();

        return $profesors;
    }

    public static function getProfesorForManagerId($grado_id = null, $manager_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $profesors = Profesor::query()
            ->select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('profesors.status_active', "true")
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->orderby('profesors.lastname', 'asc')
            ->groupBy('profesors.id');

        $profesors = ($grado_id) ? $profesors->where('grados.id', $grado_id) : $profesors;
        $profesors = ($manager_id) ? $profesors->where('pestudios.manager_id', $manager_id) : $profesors;

        $profesors = $profesors->get(); //dd($manager_id,$profesors);

        return $profesors;
    }

    public static function list_profesors_manage($grado_id = null, $manager_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $profesors = Profesor::query()
            ->select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")

            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('grados.deleted_at')
            ->orderby('profesors.lastname', 'asc')
            ->groupBy('profesors.id');

        $profesors = ($grado_id) ? $profesors->where('grados.id', $grado_id) : $profesors;
        $profesors = ($manager_id) ? $profesors->where('pestudios.manager_id', $manager_id) : $profesors;

        $profesors = $profesors->pluck('profesor_fullname', 'id');

        return $profesors;
    }

    public static function listProfesorsIndexado($active = true)
    {
        $query = Profesor::select('id')
            ->selectRaw("CONCAT(lastname, ' ', name) as profesor_fullname")
            ->orderBy('lastname', 'asc')
            ->orderBy('name', 'asc');

        if ($active) {
            $query->where('status_active', 'true');
        }
        return $query->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->profesor_fullname,
                ];
            })
            ->toArray();
    }

    public static function list_profesors($active = true)
    {
        $query = Profesor::select('id')
            ->selectRaw("CONCAT(lastname, ' ', name) as profesor_fullname")
            ->orderBy('lastname', 'asc')  // <-- Ordenar solo por lastname en SQL
            ->orderBy('name', 'asc');     // <-- Opcional: para desempatar si hay mismos apellidos

        if ($active) {
            $query->where('status_active', 'true');
        }

        return $query->pluck('profesor_fullname', 'id');
    }

    public static function list_grado_guia($profesor_id) /* usada para llenar los objetos de formularios select*/
    {
        $profesor = Profesor::findOrFail($profesor_id);

        $grados = DB::table('grados')
            ->select('grados.id as grado_id', 'grados.name as grado_name')
            ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('profesors', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->where('profesors.id', $profesor->id)
            ->where('seccions.status_active', "true")
            ->whereNull('grados.deleted_at')
            ->whereNull('profesors.deleted_at')
            ->whereNull('profesor_guias.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('grados.deleted_at')
            ->groupBy('grados.id')
            ->pluck('grado_name', 'grado_id');

        return $grados;
    }

    public static function list_grado(int $profesor_id, ?bool $planning = null): \Illuminate\Support\Collection
    {
        // Una sola query: obtiene pestudio_name, grado_id, grado_name
        // filtrando por profesor y pestudios activos en un solo JOIN
        $query = DB::table('pevaluacions')
            ->select(
                'pestudios.id   as pestudio_id',
                'pestudios.name as pestudio_name',
                'grados.id      as grado_id',
                'grados.name    as grado_name'
            )
            ->join('pensums',   'pensums.id',   '=', 'pevaluacions.pensum_id')
            ->join('grados',    'grados.id',    '=', 'pensums.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
            ->where('pevaluacions.profesor_id', $profesor_id)
            ->where('pestudios.status_active', 'true')          // scope active('true') equivalente
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->whereNull('grados.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->groupBy('pestudios.id', 'pestudios.name', 'grados.id', 'grados.name')
            ->orderBy('pestudios.name')
            ->orderBy('grados.name');

        // Filtro condicional por planning_module
        if ($planning !== null) {
            $query->where('pestudios.planning_module', $planning);
        }

        // Construir colección anidada: [ 'NombrePestudio' => [ grado_id => grado_name ] ]
        return $query
            ->get()
            ->groupBy('pestudio_name')
            ->map(fn ($rows) => $rows->pluck('grado_name', 'grado_id'));
    }

    public static function list_pestudio_pensum($profesor_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get();

        $profesor = Profesor::findOrFail($profesor_id);

        $datas_asignaturas = collect();

        foreach ($pestudios as $pestudio) {

            $datas_grados = collect();

            foreach ($pestudio->grados as $grado) {
                $asignaturas = DB::table('asignaturas')
                    ->select('pensums.id as pensum_id')
                    ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
                    ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
                    ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')

                    ->where('pensums.grado_id', $grado->id)
                    ->where('pevaluacions.profesor_id', $profesor->id)

                    ->whereNull('pensums.deleted_at')
                    ->whereNull('asignaturas.deleted_at')
                    ->whereNull('pensums.deleted_at');

                // $asignaturas = ($grado_id) ? $asignaturas->where('pensums.grado_id',$grado_id) : $asignaturas ;

                $asignaturas = $asignaturas->pluck('asignatura_fullname', 'pensum_id');

                $datas_grados = ($asignaturas->isNotEmpty()) ? $datas_grados->put($grado->name, $asignaturas) : $datas_grados;
            }

            $datas_asignaturas = ($datas_grados->isNotEmpty()) ? $datas_asignaturas->put($pestudio->name, $datas_grados) : $datas_asignaturas;
        }

        return $datas_asignaturas;
    }

    public static function profesors_incidents()
    {
        $profesors = Profesor::select('profesors.*')
            ->join('incidents', 'profesors.id', '=', 'incidents.profesor_id')
            ->where('incidents.status_active', true)
            ->get();

        // $estudiants = ($fecha) ? $estudiants->whereDate('inscripcions.created_at','<=',$fecha) : $estudiants ;

        return $profesors;
    }

    public function list_pevaluacions($lapso_id = null)
    {
        $list = [];
        $pevaluacions = ($lapso_id) ? $this->pevaluacions->where('lapso_id', $lapso_id) : $this->pevaluacions;
        foreach ($pevaluacions as $pevaluacion) {
            $list[$pevaluacion->id] = $pevaluacion->asignatura_name;
        }
        return $list;
    }

    public function list_evaluacions($pevaluacion_id = null)
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')

            ->where('profesors.id', $this->id)
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('profesors.deleted_at');

        $evaluacions = ($pevaluacion_id) ? $evaluacions->where('pevaluacions.id', $pevaluacion_id) : $evaluacions;

        $evaluacions = $evaluacions->pluck('description', 'id');

        return $evaluacions;
    }
}
