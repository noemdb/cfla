<?php
namespace App\Models\app\Estudiante\Functions\Representants;

use Illuminate\Support\Facades\DB;

trait Scope {

    public function scopeName($query, $arr_dat)
    {
        //añade condicion para el username
        if(trim($arr_dat['search'])!=""){
            $str_search = ($arr_dat['search']=="&ALL") ? "" : $arr_dat['search'];

            $arr_search = explode(" ", $str_search);
            $count_arr_search = count($arr_search);

            switch ($count_arr_search) {
                case (1):
                    $query->Where('representants.name', 'like', "%".$str_search."%");
                    break;
                case (2):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    break;
                case (3):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[2]."%");
                    break;
                case (4):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[2]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[3]."%");
                    break;
                case (5):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[2]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[3]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[4]."%");
                    break;
                case (6):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[2]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[3]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[4]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[5]."%");
                    break;
                case (7):
                    $query->Where('representants.name', 'like', "%".$arr_search[0]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[1]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[2]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[3]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[4]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[5]."%");
                    $query->Where('representants.name', 'like', "%".$arr_search[6]."%");
                    break;
            }

            $query->orWhere('representants.ci_representant', 'like', "%".$str_search."%");
        }
        return $query;
    }

    public function scopeActive($query, $flag)
    {
        return $query->where('status_active', $flag);
    }

}
