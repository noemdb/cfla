<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Pestudio;

class AreaConocimientoController extends Controller
{
    public function promedio_x_area(Request $request)
    {
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;
        $pestudio_id = (!empty($request->pestudio_id)) ? $request->pestudio_id:null;
        $lapso_id = (!empty($request->range)) ? $request->range:null;
        $pestudio = Pestudio::find($pestudio_id);
        $ChartDataSQL = [];

        $area_conocimientos = AreaConocimiento::select('area_conocimientos.*')
            ->join('campo_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id');

        if ($pestudio_id) {
            $area_conocimientos = $area_conocimientos->join('asignaturas', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
                ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')
                ->where('pestudios.id',$pestudio_id);
        }

        $area_conocimientos = $area_conocimientos->groupby('area_conocimientos.id')->get();

        if ($area_conocimientos->IsNotEmpty()) {

            $labels = $area_conocimientos->pluck('code');

            foreach ($area_conocimientos as $area_conocimiento) {
                $values[] = $area_conocimiento->getPromedio($lapso_id);
            }

            // for ($i=0; $i < count($labels) ; $i++) {
            //     $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 0.6)';
            // }

            $ChartDataSQL = [
                'labels'=>$labels,
                'datasets'=>[
                    [
                        "label"=>"Áreas de Conocimiento",
                        "backgroundColor"=>"rgba(".$pestudio->rgb_color.",0.4)",
                        "borderColor"=>"rgba(".$pestudio->rgb_color.",1)",
                        "borderWidth"=>2,
                        "data"=>$values
                    ]
                ]
            ];
        }

        return json_encode($ChartDataSQL);
    }
}
