<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Enrollment;
use App\Models\app\HistoricoNota\Oinstitucion;

class LapsoController extends Controller
{

    public function census_municipio(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $fecha = Carbon::now();
        }else{
            $fecha = Carbon::now()->subDays($range);
        }

        $inscripcions = Enrollment::select('enrollments.town_hall_birth',DB::raw('count(enrollments.town_hall_birth) as institution_count'))
            // ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            // ->where('estudiants.status_active','true')
            // ->wherenull('enrollments.deleted_at')
            // ->Where('inscripcions.created_at', '<=', $fecha)
            // ->Where('created_at', '<=', $ffinal)
            ->groupby('enrollments.town_hall_birth')
            ->orderBy('institution_count', 'desc')
            // ->take(8)
            ->get();

        // dd($inscripcions);

        $labels = $inscripcions->pluck('town_hall_birth');
        $values = $inscripcions->pluck('institution_count');
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Municipio de Nacimiento",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }



    public function census_institution(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $fecha = Carbon::now();
        }else{
            $fecha = Carbon::now()->subDays($range);
        }

        $inscripcions = Enrollment::select('enrollments.institution',DB::raw("SUBSTR(enrollments.institution,1,42) as name_institution"),DB::raw('count(enrollments.institution) as institution_count'))
            // ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            // ->where('estudiants.status_active','true')
            // ->wherenull('enrollments.deleted_at')
            // ->Where('inscripcions.created_at', '<=', $fecha)
            // ->Where('created_at', '<=', $ffinal)
            ->groupby('enrollments.institution')
            ->orderBy('institution_count', 'desc')
            // ->take(8)
            ->get();

        // dd($inscripcions);

        $labels = $inscripcions->pluck('institution');
        $values = $inscripcions->pluck('institution_count');
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Inst.Origen",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function census_grado(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;

        if($range=='Todos'){
            $fecha = Carbon::now();
        }else{
            $fecha = Carbon::now()->subDays($range);
        }

        $inscripcions = Enrollment::select('grados.id','grados.name',DB::raw('count(grados.id) as grado_count'))
            ->join('grados', 'grados.id', '=', 'enrollments.grado_id')
            // ->where('estudiants.status_active','true')
            // ->wherenull('enrollments.deleted_at')
            // ->Where('inscripcions.created_at', '<=', $fecha)
            // ->Where('created_at', '<=', $ffinal)
            ->groupby('grados.id')
            ->orderBy('grado_count', 'desc')
            // ->take(8)
            ->get();

        // dd($inscripcions);

        $labels = $inscripcions->pluck('name');
        $values = $inscripcions->pluck('grado_count');
        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Grados",
                    "backgroundColor"=>$colors,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

}
