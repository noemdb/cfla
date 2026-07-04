<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Planpago\Payment;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function countxday(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos'; //dd($range);

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10)->startOfMonth();
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subDays($range);
            $ffinal = Carbon::now()->endOfMonth(); //dd($finicial,$ffinal);
        }

        $data = Payment::select(DB::raw('count(payments.id) as value'),
            DB::raw('DAY(payments.created_at) as day'),
            DB::raw("DATE_FORMAT(payments.created_at, '%m-%d') as date"))
            ->Where('payments.created_at', '>=', $finicial)
            ->Where('payments.created_at', '<=', $ffinal)
            ->groupby('date')
            ->orderBy('date', 'asc')
            ->get();
        // dd($data);

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('date');
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('m-d', $value);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        // dd($labels, $label_month, $values,$finicial,$ffinal);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Núm. Reportes",
                    "backgroundColor"=>"rgba(128, 160, 128,0.2)",
                    "borderColor"=>"rgba(128, 160, 128,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
