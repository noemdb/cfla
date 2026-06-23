<?php
namespace App\Models\app\Planpago\Functions\Planpago;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Planpago;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_planpago() /* usada para llenar los objetos de formularios select*/
    {
        return Planpago::select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');
    }

    public static function list_planpago_inscription() /* usada para llenar los objetos de formularios select*/
    {
        return Planpago::visible()->select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');
    }

}
