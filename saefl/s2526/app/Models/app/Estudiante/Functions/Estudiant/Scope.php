<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use Illuminate\Support\Facades\DB;

trait Scope {
    //scope
    public function scopeWidthInscripcion($query, $fecha=null)
    {
        $query =  $query->select('estudiants.*')
                    ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    // ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->where('seccions.status_active','true')
                    ->where('planpagos.status_inscription_affects','true')
                    ;
        if ($fecha) {
            $query = $query->WhereDate('inscripcions.created_at','<=',$fecha);
        }
        return $query ;
    }
    public function scopeActive($query, $flag='true')
    {
        return $query->where('estudiants.status_active', $flag);
    }

    public function scopeGrado($query, $id)
    {
        return $query->select('grados.id as grado_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->where('grados.id', $id);
    }

    public function scopeGenero($query, $flag)
    {
        return $query->where('estudiants.gender', $flag);
    }

    public function scopeName($query, $arr_dat)
    {
        //añade condicion para el username
        if(trim($arr_dat['search'])!=""){
            $str_search = ($arr_dat['search']=="&ALL") ? "" : $arr_dat['search'];

            $arr_search = explode(" ", $str_search);
            $count_arr_search = count($arr_search);

            // dd($arr_search,$count_arr_search);

            $query->select('estudiants.*');
            $query->join('representants', 'representants.id', '=', 'estudiants.representant_id');

            switch ($count_arr_search) {
                case (1):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$str_search."%");
                    break;
                case (2):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    break;
                case (3):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[2]."%");
                    break;
                case (4):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[2]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[3]."%");
                    break;
                case (5):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[2]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[3]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[4]."%");
                    break;
                case (6):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[2]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[3]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[4]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[5]."%");
                    break;
                case (7):
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[0]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[1]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[2]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[3]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[4]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[5]."%");
                    $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_search[6]."%");
                    break;
            }

            // $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$str_search."%");

            $query->orWhere('estudiants.ci_estudiant', 'like', "%".$str_search."%")->orWhere('representants.ci_representant', 'like', "%".$str_search."%");
        }
        return $query;
    }

    public function scopeSearch($query, $name)
    {
        //añade condicion para el username
        $arr_name = explode(" ", $name);
        $count = count($arr_name);

        $query->select('estudiants.*');

        switch ($count) {
            case (1):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                break;
            case (2):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                break;
            case (3):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[2]."%");
                break;
            case (4):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[2]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[3]."%");
                break;
            case (5):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[2]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[3]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[4]."%");
                break;
            case (6):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[2]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[3]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[4]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[5]."%");
                break;
            case (7):
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[0]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[1]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[2]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[3]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[4]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[5]."%");
                $query->Where(DB::raw('concat(estudiants.lastname, " ",estudiants.name )'), 'like', "%".$arr_name[6]."%");
                break;
        }
        return $query;
    }

}
