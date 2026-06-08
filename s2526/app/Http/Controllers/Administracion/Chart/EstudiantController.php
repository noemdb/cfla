<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

class EstudiantController extends Controller
{
    public function estudiants_municipios(Request $request)
    {
        $estudiant = new Estudiant;
        $municipios_in = $estudiant->getMunicipios("YARACUY","=");
        $municipios_out = $estudiant->getMunicipios("YARACUY","<>");
        $value_out = $municipios_out->sum('count_id');

        // dd($municipios_in,$municipios_out,$value_out);

        $labels = $municipios_in->pluck('town_hall_birth');
        $values = $municipios_in->pluck('count_id');

        $labels = $labels->push('OTROS');
        $values = $values->push($value_out);

        for ($i=0; $i < count($labels) ; $i++) {
            $color = rand(0,255).', '.rand(0,255).', '.rand(0,255);
            $backgroundColor[] = 'rgba('.$color.', 0.6)';
            $borderColor[] = 'rgba('.$color.', 1)';
        }

        // dd($municipios, $labels , $values);

        unset($ChartDataSQL);
        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Estudiantes por Municipio",
                    "backgroundColor"=>$backgroundColor,
                    "borderColor"=>$borderColor,
                    "data"=>$values
                ]
            ]
        ];

        // dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }

    public function estudiants_municipios_pestudio(Request $request)
    {
        $pestudios = Pestudio::active('true')->OrderBy('id')->get();

        $labels = $pestudios->pluck('code');

        $estudiant = new Estudiant;

        $municipios_in = $estudiant->getMunicipios("YARACUY","=");

        foreach ($municipios_in as $municipio) {
            $color = rand(0,255).', '.rand(0,255).', '.rand(0,255);
            $values = $estudiant->getMunicipiosValues($municipio->town_hall_birth);
            $datasets[] = [
                "label"=>$municipio->town_hall_birth,
                "backgroundColor"=>'rgba('.$color.', 0.6)',
                "borderColor"=>'rgba('.$color.', 1)',
                "borderWidth"=>1,
                "data"=>$values
            ];
        }



        $values = $estudiant->getMunicipiosValues(null,"YARACUY","<>");
        $color = rand(0,255).', '.rand(0,255).', '.rand(0,255);

        // dd($labels,$values);

        $datasets[] = [
            "label"=>'OTROS',
            "backgroundColor"=>'rgba('.$color.', 0.6)',
            "borderColor"=>'rgba('.$color.', 1)',
            "borderWidth"=>1,
            "data"=>$values
        ];

        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>$datasets
        ];

        // dd($ChartDataSQL);

        return json_encode($ChartDataSQL);
    }
}
