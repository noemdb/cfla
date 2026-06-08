<?php

namespace App\Http\Controllers\Administracion\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Profesor;

class ValidateExistController extends Controller
{
    public function studiant_ci(Request $request)
    {
        $ci_estudiant = $request->ci_estudiant;
        sleep(1);
        if (!empty($ci_estudiant)) {
    
            $result = Estudiant::where('ci_estudiant',$ci_estudiant)->get();            
        
            if (!empty($result->count())) {
                return '<i title="Número de cédula no disponible." class="fas fa-window-close text-danger"></i>';
            } 
            else {
                return '<i title="Número de Cédula disponible." class="fa fa-check text-success" aria-hidden="true"></i>';
            }            
        }
    }
    public function ci_profesor(Request $request)
    {
        $ci_profesor = $request->ci_profesor;
        sleep(1);
        if (!empty($ci_profesor)) {
    
            $result = Profesor::where('ci_profesor',$ci_profesor)->get();            
        
            if (!empty($result->count())) {
                return '<i title="Número de cédula no disponible." class="fas fa-window-close text-danger"></i>';
            } 
            else {
                return '<i title="Número de Cédula disponible." class="fa fa-check text-success" aria-hidden="true"></i>';
            }           
        }
    }

}
