<?php

namespace App\Http\Controllers\Common\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Estudiant;

class FillSelectController extends Controller
{
    public function gradoByseccion($id)
    {
        return Seccion::where('grado_id','=',$id)->where('status_active','true')->get();
    }
    public function studiantBytype($type)
    {
        return Estudiant::select('name','id', DB::raw("CONCAT(ci_estudiant,' - ',name,' ',lastname) as fullname"))->orderby('fullname')->get();

    }
}
