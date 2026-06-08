<?php
namespace App\Models\app\Planpago\Functions\RegistroPago;

trait Scope {
    public function scopeName($query, $arr_dat)
    {
        //añade condicion para el username
        if(trim($arr_dat['search'])!=""){
            $search = ($arr_dat['search']=="&ALL") ? "" : $arr_dat['search'];
            $query->where('estudiants.name', 'like', "%".$search."%")
                    ->orWhere('estudiants.lastname', 'like', "%".$search."%")
                    ->orWhere('estudiants.ci_estudiant', 'like', "%".$search."%")
                    ->orWhere('ingresos.person_bill_ci', 'like', "%".$search."%")
                    ->orWhere('ingresos.person_bill_name', 'like', "%".$search."%")
                    ->orWhere('bancos.name', 'like', "%".$search."%")
                    ;
        }
        return $query;
    }

}
