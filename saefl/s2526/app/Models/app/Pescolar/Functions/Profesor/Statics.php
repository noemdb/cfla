<?php
namespace App\Models\app\Pescolar\Functions\Profesor;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Statics {    

    public static function getProfesorForUserId($user_id)
    {
        return Profesor::select('profesors.*')
            ->join('users','users.id','=','profesors.user_id')
            ->where('users.id',$user_id)
            ->first();
    }

    public static function getProfesorGuia()
    {
        return Profesor::select('profesors.*')
            ->join('profesor_guias','profesors.id','=','profesor_guias.profesor_id')
            ->groupBy('profesors.id')
            ->get();
    }

}
