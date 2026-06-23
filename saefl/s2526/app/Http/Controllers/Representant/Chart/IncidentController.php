<?php

namespace App\Http\Controllers\Representant\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;

// Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Models\app\Incident\Incident;

class IncidentController extends Controller
{
    protected $representant;
    public function __construct()
    {
        $this->middleware(['auth','is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            return $next($request);
        }]);
    }

    public function month(Request $request)
    {
    	// dd($request->all);
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $estudiant_id = ($request->input('amp;estudiant_id')!=null) ? $request->input('amp;estudiant_id') : null; //dd($request->input('estudiant_id'),$range,$estudiant_id);

        $estudiant = Estudiant::findOrFail($estudiant_id);

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months); No query results for model [App\Models\app\Estudiant].

        $data = Incident::select('incidents.estudiant_id',DB::raw('count(id) as value'),DB::raw('MONTH(created_at) as month'))
            ->Where('created_at', '>=', $finicial)
            ->Where('created_at', '<=', $ffinal)
            ->where('incidents.estudiant_id',$estudiant->id)
            // ->wherenull('deleted_at')
            ->groupby('month')
            ->orderBy('created_at', 'asc');
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
        $values = $data->pluck('value');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Incidencias",
                    "backgroundColor"=>"rgba(0, 61, 15,0.2)", //#D4EDDA
                    "borderColor"=>"rgba(0, 61, 15,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
