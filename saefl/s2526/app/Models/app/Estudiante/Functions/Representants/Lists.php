<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Representant;

trait Lists {

    public static function list_representant() /* usada para llenar los objetos de formularios select*/
    {
        $list_representant = Representant::select('id', DB::raw("CONCAT(ci_representant,' - ',name) as fullname"))
            ->where('status_active','true')
            ->orderby('ci_representant','asc')
            ->pluck('fullname', 'id');
        return $list_representant;
    }

    public static function representantRdDebt() /* usada para llenar los objetos de formularios select*/
    {
        $representants = Representant::inRandomOrder()->get(); //dd($representants);
        $representant = null;
        foreach ($representants as $item) {
            $exchange_ammount_expire_bill = $item->exchange_ammount_expire_bill;
            if ($exchange_ammount_expire_bill > 0) {
                $representant = $item;
                break;
            }
        }
        return $representant;
    }

}
