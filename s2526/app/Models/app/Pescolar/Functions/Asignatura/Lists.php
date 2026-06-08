<?php
namespace App\Models\app\Pescolar\Functions\Asignatura;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_asignatura() /* usada para llenar los objetos de formularios select*/
    {

        $asignaturas = DB::table('asignaturas')
            ->select('asignaturas.id as asignatura_id')
            ->selectRaw("CONCAT(asignaturas.name,' [',asignaturas.code,'] ') as asignatura_fullname")
            ->whereNull('asignaturas.deleted_at')
            ->orderBY('asignaturas.name')
            ->pluck('asignatura_fullname','asignatura_id');

        return $asignaturas;
    }

    public static function list_grado_asignatura() /* usada para llenar los objetos de formularios select*/
    {
        $grados = Grado::all();

        $datas_asignaturas = collect();

        foreach ($grados as $grado) {
            $asignaturas = DB::table('asignaturas')
            ->select('asignaturas.id as asignatura_id')
            ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
            ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('pensums.grado_id',$grado->id)
            ->whereNull('asignaturas.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->pluck('asignatura_fullname','asignatura_id');

            $datas_asignaturas->put($grado->name, $asignaturas);
        }

        return $datas_asignaturas;
    }

    public static function list_pestudio_asignatura($grado_id=null) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get();

        $datas_asignaturas = collect();

        foreach ($pestudios as $pestudio) {

            $datas_grados = collect();

            foreach ($pestudio->grados as $grado) {
                $asignaturas = DB::table('asignaturas')
                ->select('asignaturas.id as asignatura_id')
                ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
                ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->where('pensums.grado_id',$grado->id)
                ->whereNull('asignaturas.deleted_at')
                ->whereNull('pensums.deleted_at')
                ->orderBy('asignaturas.order');

                $asignaturas = ($grado_id) ? $asignaturas->where('pensums.grado_id',$grado_id) : $asignaturas ;

                $asignaturas = $asignaturas->pluck('asignatura_fullname','asignatura_id');

                $datas_grados = ($asignaturas->isNotEmpty()) ? $datas_grados->put($grado->name, $asignaturas) : $datas_grados ;
            }

            $datas_asignaturas = ($datas_grados->isNotEmpty()) ? $datas_asignaturas->put($pestudio->name, $datas_grados) : $datas_asignaturas ;

        }


        return $datas_asignaturas;
    }

}
