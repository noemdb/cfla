<?php

namespace App\Models\app\Pescolar\Functions\Pensum;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

trait Lists
{

    public static function getGradosActive()
    {
        $pensums = Pensum::select('pensums.*')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->where('grados.status_active', 'true')
            ->get();
        return $pensums;
    }

    public static function list_grado_asignatura() /* usada para llenar los objetos de formularios select*/
    {
        $grados = Grado::all();

        $datas_asignaturas = collect();

        foreach ($grados as $grado) {
            $asignaturas = DB::table('asignaturas')
                ->select('pensums.id as pensum_id')
                ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
                ->join('pensums', 'asignaturas.id', '=', 'asignatura_id')
                ->where('pensums.grado_id', $grado->id)
                ->whereNull('asignaturas.deleted_at')
                ->whereNull('pensums.deleted_at')
                ->pluck('asignatura_fullname', 'pensum_id');

            $datas_asignaturas->put($grado->name, $asignaturas);
        }

        return $datas_asignaturas;
    }

    public static function list_pestudio_pensum($grado_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get();

        $datas_asignaturas = collect();

        foreach ($pestudios as $pestudio) {

            $datas_grados = collect();

            foreach ($pestudio->grados as $grado) {
                if ($grado->status_active == 'true') {
                    $asignaturas = DB::table('asignaturas')
                        ->select('pensums.id as pensum_id')
                        ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
                        ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
                        ->where('pensums.grado_id', $grado->id)
                        ->whereNull('asignaturas.deleted_at')
                        ->whereNull('pensums.deleted_at');

                    $asignaturas = ($grado_id) ? $asignaturas->where('pensums.grado_id', $grado_id) : $asignaturas;

                    $asignaturas = $asignaturas->pluck('asignatura_fullname', 'pensum_id');

                    $datas_grados = ($asignaturas->isNotEmpty()) ? $datas_grados->put($grado->name, $asignaturas) : $datas_grados;
                }
            }

            $pestudio_name = '[' . $pestudio->code . '] ' . $pestudio->name;

            $datas_asignaturas = ($datas_grados->isNotEmpty()) ? $datas_asignaturas->put($pestudio_name, $datas_grados) : $datas_asignaturas;
        }

        return $datas_asignaturas;
    }

    public static function list_pensum_grado($grado_id = null) /* usada para llenar los objetos de formularios select*/
    {
        return
            DB::table('asignaturas')
            ->select('pensums.id as pensum_id')
            ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
            ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('pensums.grado_id', $grado_id)
            ->whereNull('asignaturas.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->pluck('asignatura_fullname', 'pensum_id');
    }

    public static function list_pestudio_pensum_manage($grado_id = null, $manager_id) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::where('manager_id', $manager_id)->active('true')->get();

        $datas_asignaturas = collect();

        foreach ($pestudios as $pestudio) {

            $datas_grados = collect();

            foreach ($pestudio->grados as $grado) {
                if ($grado->status_active == 'true') {
                    $asignaturas = DB::table('asignaturas')
                        ->select('pensums.id as pensum_id')
                        ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as asignatura_fullname")
                        ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
                        ->where('pensums.grado_id', $grado->id)
                        ->whereNull('asignaturas.deleted_at')
                        ->whereNull('pensums.deleted_at');

                    $asignaturas = ($grado_id) ? $asignaturas->where('pensums.grado_id', $grado_id) : $asignaturas;

                    $asignaturas = $asignaturas->pluck('asignatura_fullname', 'pensum_id');

                    $datas_grados = ($asignaturas->isNotEmpty()) ? $datas_grados->put($grado->name, $asignaturas) : $datas_grados;
                }
            }

            $pestudio_name = '[' . $pestudio->code . '] ' . $pestudio->name;

            $datas_asignaturas = ($datas_grados->isNotEmpty()) ? $datas_asignaturas->put($pestudio_name, $datas_grados) : $datas_asignaturas;
        }

        return $datas_asignaturas;
    }
}
