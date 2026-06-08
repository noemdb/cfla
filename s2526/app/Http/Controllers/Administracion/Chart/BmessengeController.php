<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Bot\Bmessege;
use App\Models\app\Bot\Boption;
use App\User;
use Illuminate\Support\Facades\Auth;

class BmessengeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_common']);
    }

    public function options(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10)->startOfMonth();
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subDays($range);
            $ffinal = Carbon::now()->endOfMonth(); //dd($finicial,$ffinal);
        }

        $user = User::findOrFail(Auth::user()->id) ;
        $bmesseges = Bmessege::getforArea($user->area,$user->isAdmin())->Where('created_at', '>=', $finicial)->Where('created_at', '<=', $ffinal);

        //$bmesseges = Bmessege::orderBy('created_at')->Where('created_at', '>=', $finicial)->Where('created_at', '<=', $ffinal)->get(); //dd($bmesseges);
        $data = array();
        $labels = array();
        $values = array();

        foreach ($bmesseges as $bmessege) {
            $message = $bmessege->message;
            switch ($message) {
                case 1: $data['Opción 1'] = (array_key_exists('Opción 1',$data)) ? ++$data['Opción 1'] : 1;  break;
                case 2: $data['Opción 2'] = (array_key_exists('Opción 2',$data)) ? ++$data['Opción 2'] : 1;  break;
                case 3: $data['Opción 3'] = (array_key_exists('Opción 3',$data)) ? ++$data['Opción 3'] : 1;  break;
                case 4: $data['Opción 4'] = (array_key_exists('Opción 4',$data)) ? ++$data['Opción 4'] : 1;  break;
                case 5: $data['Opción 5'] = (array_key_exists('Opción 5',$data)) ? ++$data['Opción 5'] : 1;  break;
                case 6: $data['Opción 6 - TDC'] = (array_key_exists('Opción 6 - TDC',$data)) ? ++$data['Opción 6 - TDC'] : 1;  break;

                default:
                    $patron = "/[0-9]{6,8}/";
                    // if (ctype_digit($message) && strlen($message) >= 6 && strlen($message) <= 8 ) {
                    if (preg_match($patron,$message) ) {
                        $data['Opción 7 - Consulta de saldo'] = (array_key_exists('Opción 7 - Consulta de saldo',$data)) ? ++$data['Opción 7 - Consulta de saldo'] : 1;  break;
                    } else {
                        $data['Otros comentarios'] = (array_key_exists('Otros comentarios',$data)) ? ++$data['Otros comentarios'] : 1;  break;
                    }
                    break;
            }
        }

        ksort($data);
        foreach ($data as $k => $v) {
            $labels[] = $k;
            $values[] = $v;
        }

        // dd($values);

        // $labels = $inscripcions->pluck('gender');
        // $values = $inscripcions->pluck('gender_count');
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Opciones",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        //dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }

}
