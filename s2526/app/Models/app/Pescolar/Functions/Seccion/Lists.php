<?php
namespace App\Models\app\Pescolar\Functions\Seccion;

use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_seccion_grado($grado_id) /* usada para llenar los objetos de formularios select*/
    {
        return ($grado_id) ? Seccion::Where('grado_id',$grado_id)->where('status_active','true')->pluck('name', 'id') : collect();
    }
    

}
