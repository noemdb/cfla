<?php

namespace App\Http\Controllers\Representant\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;

// Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Models\app\Estudiante\Ingreso;

class BancosController extends Controller
{
    public $representant;
    public function __construct()
    {
        $this->middleware(['auth', 'is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id', Auth::user()->id)->first();
            return $next($request);
        }]);
    }

    public function IngresoXMonth(Request $request)
    {

        $range = ($request->input('range') != null) ? $request->input('range') : 'Todos';

        if ($range == 'Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        } else {
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $data = Ingreso::select(DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'), DB::raw('count(id) as value'), DB::raw('MONTH(date_transaction) as month'))
            ->Where('date_transaction', '>=', $finicial)
            ->Where('date_transaction', '<=', $ffinal)
            ->where('ingresos.representant_id', $this->representant->id)
            ->wherenull('deleted_at')
            ->groupby('month')
            ->orderBy('date_transaction', 'asc');
        // ->orderBy('month', 'asc');

        $data = $data->get();

        //dd($usersmonth);

        //INI nombre de los meses en español
        $labels = $data->pluck('month');
        $label_month = [];
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('!m', $value);
            $label_month[] = ucfirst($dateObj->format('F'));
        }
        $values = $data->pluck('sum_ingreso_ammount');

        foreach ($values as $k => $v) {
            $values[$k] = round($v, 2);
        }
        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels' => $label_month,
            'datasets' => [
                [
                    "label" => "Monto ingresado",
                    "backgroundColor" => "rgba(0, 61, 15,0.2)", //#D4EDDA
                    "borderColor" => "rgba(0, 61, 15,1)",
                    "borderWidth" => 2,
                    "data" => $values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
