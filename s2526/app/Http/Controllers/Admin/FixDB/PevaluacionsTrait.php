<?php

namespace App\Http\Controllers\Admin\FixDB;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\User;
use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;

trait PevaluacionsTrait {


    public static function fix_evaluacions_status_execution()
    {
        $datas = collect();
        $evaluacions = Evaluacion::select('evaluacions.*')
        ->join('boletins', 'evaluacions.id', '=', 'boletins.evaluacion_id')
        ->get(); //dd($evaluacions);
        foreach ($evaluacions as $item) { 
            $evaluacion = Evaluacion::find($item->id);
            $evaluacion->status_execution = true;
            $evaluacion->save();
            $datas->push($evaluacion);
        }
        dd($datas);
    }

    public static function boletins_duplicate() // debe ejecutarse hasta que en datas sea vacío
    {
        $datas = collect();
        $evaluacions = Evaluacion::all();

        foreach ($evaluacions as $evaluacion) {
            $boletins = Boletin::select('boletins.*')
            ->selectRaw('count(boletins.id) as count')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('estudiants', 'estudiants.id', '=', 'boletins.estudiant_id')
            ->where('evaluacions.id',$evaluacion->id)
            ->wherenull('boletins.deleted_at')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('estudiants.deleted_at')
            ->groupBy('estudiants.id')
            ->orderBy('boletins.nota','asc')
            ->get();

            foreach ($boletins as $item) {
                if ($item->count > 1) {
                    $data = collect();
                    $estudiant_id = $item->estudiant_id;
                    $evaluacion_id = $item->evaluacion_id;
                    $boletin = Boletin::find($item->id);
                    $boletin->delete(); 
                    $boletins_others = Boletin::where('estudiant_id',$estudiant_id)->where('evaluacion_id',$evaluacion_id)->get();
                    $data->put('boletin',$boletin);
                    $data->put('boletins_others',$boletins_others);
                    $datas->push($data);
                }
            }
                       
        }
        dd($datas); 
    }

    public static function movePevaluacions()
    {
        $datas = collect();
        $seccions = ['13','14'];
        $pevaluacions = DB::table('pevaluacions')
        ->select('pevaluacions.*')
        ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')

        ->where('seccions.status_active','true')

        ->whereIn('seccions.id',$seccions)

        ->wherenull('pevaluacions.deleted_at')
        ->wherenull('seccions.deleted_at')

        ->get() ; //dd($pevaluacions);

        foreach ($pevaluacions as $item) { 
            $pevaluacion = Pevaluacion::find($item->id);
            if ($pevaluacion) {
                $seccion_id = $item->seccion_id;
                $seccion_id = ($seccion_id == 13) ? 47 : $seccion_id ;
                $seccion_id = ($seccion_id == 14) ? 48 : $seccion_id ;
                $pevaluacion->seccion_id = $seccion_id;
                $pevaluacion->save();
                $datas->push($pevaluacion);
            }
        }
        dd($datas);
    }

    
}
