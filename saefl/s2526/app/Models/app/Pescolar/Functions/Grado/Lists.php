<?php
namespace App\Models\app\Pescolar\Functions\Grado;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_pestudio_grado_manage($manager_id) /* usada para llenar los objetos de formularios select*/
    {
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
        
        $datas_grados = collect();
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

    // public static function list_pestudio_grado_leader($leader_id) /* usada para llenar los objetos de formularios select*/
    // {
    //     $pestudios = Pestudio::where('manager_id',$leader_id)->active('true')->get(); //dd($pestudios);
    //     $datas_grados = collect();
    //     foreach ($pestudios as $pestudio) {
    //         $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
    //     }
    //     return $datas_grados;
    // }

    public static function list_pestudio_grado($pestudio_id = null) /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true');
        $pestudios = ($pestudio_id) ? $pestudios->where('id',$pestudio_id) : $pestudios ;
        $pestudios = $pestudios->get();
        $datas_grados = collect();
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

    public static function list_pestudio_grado_all() /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::all(); //dd($pestudios);
        $datas_grados = collect();
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

    public static function list_grado() /* usada para llenar los objetos de formularios select*/
    {
        return Grado::active('true')->pluck('name','id');
    }

    public function scopeActive($query, $flag='true')
    {
        return $query->where('grados.status_active', $flag);
    }

    public static function list_pestudio_grado_inscripcion() /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->where('status_inscripcion_active',true)->get(); //dd($pestudios);
        $datas_grados = collect();
        foreach ($pestudios as $pestudio) {
            $datas_grados->put($pestudio->code.'-'.$pestudio->name, $pestudio->getGradosActive()->pluck('name', 'id'));
        }
        return $datas_grados;
    }

}
