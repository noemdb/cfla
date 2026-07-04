<?php
namespace App\Models\app\Pescolar\Functions\Pestudio;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_pestudio() /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get()->pluck('name', 'id');

        return $pestudios;
    }

    public static function getPestudios($user_id)
    {
        $pestudios = Pestudio::select('pestudios.*')
            ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
            ->where('peducativos.status_active','true')
            ->where('pestudios.status_active','true')
            ->where(
                function($query) use ($user_id) {
                    $query->orWhere('peducativos.manager_id',$user_id)
                        ->orWhere('peducativos.assistant_id',$user_id)
                        ->orWhere('peducativos.deputy_id',$user_id)
                        ;
                })
            ->get()
            ;
        return $pestudios;
    }

    public static function list_pestudio_grado_manage($manager_id) /* usada para llenar los objetos de formularios select*/
    {
        $datas_grados = collect();
        $pestudios = Pestudio::select('pestudios.*')
        ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
        ->where('peducativos.status_active','true')
        ->where('pestudios.status_active','true')
        ->where(
            function($query) use ($manager_id) {
                $query->orWhere('peducativos.manager_id',$manager_id)
                    ->orWhere('peducativos.assistant_id',$manager_id)
                    ->orWhere('peducativos.deputy_id',$manager_id)
                    ;
            })
        ->orderBy('peducativos.order')
        ->get()
        ;
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

}
