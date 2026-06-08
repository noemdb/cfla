<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Helpers\Convertidor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\PreinscripcionImport;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Preinscripcion;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PreinscripcionController extends Controller
{
    public function book(Request $request)
    {
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $grados = Grado::Orderby('id','asc')->where('status_active','true')->get();
        $seccions = Seccion::Orderby('id','asc')->get();

        return view('administracion.preinscripcions.book',compact('pestudios','grados','seccions'));

    }

    public function cargaCSV(Request $request)
    {
        $file = $request->file('file_csv');
        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;

        $preinscripcionsCSV = collect();
        $total_errors = null;
        $total_row_fix = null;

        $fecha_limit = '2000-01-01';

        if ($file_path) {
            $PreinscripcionImport = new PreinscripcionImport;
            $preinscripcionsCSV = $PreinscripcionImport->getCollectPathF2($file_path);
        }

        return view('administracion.preinscripcions.carga_csv', compact('preinscripcionsCSV'));
    }

    public function cargaCSVPost(Request $request)
    {
        $file = $request->file('file_csv');
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('tmp', Str::random(40) . '.' . $file->getClientOriginalExtension());

        $inputs = [
            'file_name'=>$file_name,
            'file_path'=>$file_path,
        ];

        return redirect()->route('administracion.preinscripcions.carga.csv',$inputs);

    }

    public function storeCSV(Request $request)
    {
        // dd($request->all());

        // $estudiant_id_arr = (is_array($request->estudiant_id)) ? $request->estudiant_id: array();
        $grado_id_arr = (is_array($request->grado_id)) ? $request->grado_id: array();
        $data_ge_name_arr = (is_array($request->data_ge_name)) ? $request->data_ge_name: array();
        $grupo_estable_id_arr = (is_array($request->grupo_estable_id)) ? $request->grupo_estable_id: array();
        $comments_arr = (is_array($request->comments)) ? $request->comments: array();
        $count = null;

        if ( !empty(count($grado_id_arr)) && !empty( count($grupo_estable_id_arr)) && !empty( count($comments_arr)) ) {

            foreach ($grado_id_arr as $estudiant_id => $grado_id) {

                // $data_ge_name = (array_key_exists($estudiant_id, $data_ge_name_arr)) ? $data_ge_name_arr[$estudiant_id] : null ;
                $grupo_estable_id = (array_key_exists($estudiant_id, $grupo_estable_id_arr)) ? $grupo_estable_id_arr[$estudiant_id] : null ;
                $comments = (array_key_exists($estudiant_id, $comments_arr)) ? $comments_arr[$estudiant_id] : null ;

                if (!empty($grado_id) && !empty($estudiant_id)) {

                    $arr = [
                        'estudiant_id'=>$estudiant_id,
                        'grado_id'=>$grado_id,
                        'grupo_estable_id'=>$grupo_estable_id,
                        'comment'=>$comments,
                    ];

                    $preinscripcion = Preinscripcion::where('estudiant_id',$estudiant_id)->first();

                    if (empty($preinscripcion)) {
                        $create = Preinscripcion::create($arr);
                        $count++;
                    }

                }
            }

        }

        $count_sentence = Convertidor::numToSentence($count);
        $messege = ($count>1) ? 'Prenscripciones guardadas correctamente!!. '.$count_sentence.' ('.$count.') Registros procesados' : 'Inscripción guardada correctamente!!. '.$count_sentence.' ('.$count.') Registro procesado' ;
        Session::flash('operp_ok',$messege);

        return redirect()->route('administracion.preinscripcions.carga.csv');
    }

    public function crud(Request $request)
    {

        $search = (!empty($request->search)) ? $request->search : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $prosecucion_seccion_id     = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id: null;

        $estudiants = collect([]);

        if (count($request->all())>0) {

            $estudiants = Estudiant::select('estudiants.*')
            ->join('preinscripcions', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
            ->where('estudiants.status_active','true')
            ->orderBy('estudiants.ci_estudiant')
            ->groupBy('estudiants.id')
            ;

            if ($search) {
                $search = $request->get('search');
                $arr_get = [ 'search'=>$search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($grado_id) ? $estudiants->where('preinscripcions.grado_id',$grado_id) : $estudiants ;

            $estudiants = ($prosecucion_seccion_id) ? $estudiants->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')->where('prosecucions.seccion_id',$prosecucion_seccion_id) : $estudiants ;

            $estudiants = $estudiants->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();

        $list_prosecucion = Prosecucion::list_prosecucion(); //dd($list_prosecucion);

        return view('administracion.preinscripcions.crud', compact('estudiants','search','list_grado','list_prosecucion','grado_id','prosecucion_seccion_id'));
    }

}
