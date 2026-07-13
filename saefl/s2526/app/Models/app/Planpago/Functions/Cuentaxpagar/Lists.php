<?php
namespace App\Models\app\Planpago\Functions\Cuentaxpagar;

use App\Models\app\Planpago;
use App\Models\app\Planpago\Cuentaxpagar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait Lists {

    public static function list_cuentaxpagar_simple() /* usada para llenar los objetos de formularios select*/
    {
        return Cuentaxpagar::select('cuentaxpagars.name as name', 'cuentaxpagars.id as id')
        ->where('cuentaxpagars.type','GENERAL')
        ->where('cuentaxpagars.status_active','true')
        ->wherenull('cuentaxpagars.deleted_at')
        ->orderby('cuentaxpagars.name','asc')
        ->pluck('name', 'id');
    }

    public static function list_cuentaxpagar_date_last() /* usada para llenar los objetos de formularios select*/
    {
        $pescolar_finicial = Session::get('pescolar_finicial'); //dd($finicial);
        $ano = Carbon::parse($pescolar_finicial)->format('Y'); //dd($pescolar,$ano);
        $meses = collect();
        $list = collect();

        $meses->put($ano.'-09-30','SEPTIEMBRE');
        $meses->put($ano.'-10-31','OCTUBRE');
        $meses->put($ano.'-11-30','NOVIEMBRE');
        $meses->put($ano.'-12-31','DICIEMBRE');
        $meses->put(($ano+1).'-01-31','ENERO');
        $meses->put(($ano+1).'-02-28','FEBRERO');
        $meses->put(($ano+1).'-03-31','MARZO');
        $meses->put(($ano+1).'-04-30','ABRIL');
        $meses->put(($ano+1).'-05-31','MAYO');
        $meses->put(($ano+1).'-06-30','JUNIO');
        $meses->put(($ano+1).'-07-31','JULIO');
        $meses->put(($ano+1).'-08-31','AGOSTO');

        return $meses;
    }

    public static function list_cuentaxpagar_date() /* usada para llenar los objetos de formularios select*/
    {
        $pescolar_finicial = Session::get('pescolar_finicial'); //dd($inicial);
        $ano = Carbon::parse($pescolar_finicial)->format('Y'); //dd($pescolar,$ano);
        $meses = collect();
        $list = collect();

        $meses->put($ano.'-09-01','SEPTIEMBRE');
        $meses->put($ano.'-10-01','OCTUBRE');
        $meses->put($ano.'-11-01','NOVIEMBRE');
        $meses->put($ano.'-12-01','DICIEMBRE');
        $meses->put(($ano+1).'-01-01','ENERO');
        $meses->put(($ano+1).'-02-01','FEBRERO');
        $meses->put(($ano+1).'-03-01','MARZO');
        $meses->put(($ano+1).'-04-01','ABRIL');
        $meses->put(($ano+1).'-05-01','MAYO');
        $meses->put(($ano+1).'-06-01','JUNIO');
        $meses->put(($ano+1).'-07-01','JULIO');
        $meses->put(($ano+1).'-08-01','AGOSTO');

        return $meses;
    }

    public static function list_cuentaxpagar() /* usada para llenar los objetos de formularios select*/
    {
        $planpagos = Planpago::all()->sortBy('name');

        $list_cuentaxpagar = collect();
        foreach ($planpagos as $planpago) {
            if (!empty($planpago->cuentaxpagars->count())) {
                $pluck=
                    DB::table('cuentaxpagars')
                    ->select('cuentaxpagars.*',DB::raw("CONCAT(cuentaxpagars.name,' || ',DATE_FORMAT(cuentaxpagars.date_expiration, '%d-%m-%Y')) as fullname"))
                    ->where('cuentaxpagars.planpago_id',$planpago->id)
                    ->where('type','GENERAL')
                    ->whereNull('cuentaxpagars.deleted_at')
                    ->OrderBy('cuentaxpagars.date_expiration','asc')
                    ->pluck('fullname', 'id');
                $list_cuentaxpagar->put($planpago->name,$pluck);
            }
        }

        return $list_cuentaxpagar;
    }

}
