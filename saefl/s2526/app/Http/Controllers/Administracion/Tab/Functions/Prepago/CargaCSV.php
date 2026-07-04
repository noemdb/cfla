<?php

namespace App\Http\Controllers\Administracion\Tab\Functions\Prepago;

use App\Helpers\Convertidor;
use App\Imports\PrepagosImport;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Prepago;
use Illuminate\Http\Request;
//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


trait CargaCSV {

    public function PreinscripcionCargaCSV(Request $request)
    {
        $file = $request->file('file_csv');

        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;
        $prepagosCSV = collect();

        if ($file_path) {

            $ingresosImport = new PrepagosImport;
            $prepagosCSV = $ingresosImport->getCollectPathF2($file_path);
        }

        return view('administracion.prepagos.preinscripcions_csv', compact('prepagosCSV'));

    }

    public function PreinscripcionCargaCSVPost(Request $request)
    {
        $file = $request->file('file_csv');
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('tmp', Str::random(40) . '.' . $file->getClientOriginalExtension());

        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"UTF-8";
        $fecha_limit = (!empty($request->fecha_limit)) ? $request->fecha_limit:'2000-01-01';

        $inputs = [
            'file_name'=>$file_name,
            'file_path'=>$file_path,
            'delimiter'=>$delimiter,
            'input_encoding'=>$input_encoding,
            'fecha_limit'=>$fecha_limit
        ];

        // dd($inputs);

        return redirect()->route('administracion.prepagos.preinscripcions.carga.csv',$inputs);

    }

    public function PreinscripcionStoreCSV(Request $request)
    {

        // dd($request->all());
        $index_arr = (is_array($request->index)) ? $request->index: array();
        $representant_id_arr = (is_array($request->representant_id)) ? $request->representant_id: array();
        $representant_ci_arr = (is_array($request->representant_ci)) ? $request->representant_ci: array();
        $method_pay_id_arr = (is_array($request->method_pay_id)) ? $request->method_pay_id: array();
        $banco_id_arr = (is_array($request->banco_id)) ? $request->banco_id: array();
        $number_i_pay_arr = (is_array($request->number_i_pay)) ? $request->number_i_pay: array();
        $ingreso_ammount_arr = (is_array($request->ingreso_ammount)) ? $request->ingreso_ammount: array();
        $date_transaction_arr = (is_array($request->date_transaction)) ? $request->date_transaction: array();
        $contact_arr = (is_array($request->contact)) ? $request->contact: array();
        $comment_arr = (is_array($request->comment)) ? $request->comment: array();
        $prepagosCSV = collect();
        $count = null;

        // dd($index_arr,$representant_id_arr,$method_pay_id_arr,$number_i_pay_arr,$ingreso_ammount_arr,$date_transaction_arr,$contact_arr,$comment_arr);

        if ( !empty(count($index_arr)) && !empty(count($representant_id_arr)) && !empty( count($method_pay_id_arr)) && !empty( count($number_i_pay_arr)) && !empty( count($ingreso_ammount_arr)) && !empty( count($date_transaction_arr)) && !empty( count($contact_arr)) && !empty( count($comment_arr)) ) {

            $count=0;
            foreach ($index_arr as $k => $index) {

                $representant_id = (array_key_exists($index, $representant_id_arr)) ? $representant_id_arr[$index] : null ;

                $method_pay_id = (array_key_exists($index, $method_pay_id_arr)) ? $method_pay_id_arr[$index] : null ;
                $banco_id = (array_key_exists($index, $banco_id_arr)) ? $banco_id_arr[$index] : null ;
                $number_i_pay = (array_key_exists($index, $number_i_pay_arr)) ? $number_i_pay_arr[$index] : null ;
                $ingreso_ammount = (array_key_exists($index, $ingreso_ammount_arr)) ? $ingreso_ammount_arr[$index] : null ;
                $date_transaction = (array_key_exists($index, $date_transaction_arr)) ? $date_transaction_arr[$index] : null ;
                // $ingreso_observations = (array_key_exists($index, $ingreso_observations_arr)) ? $ingreso_observations_arr[$index] : null ;
                $contact = (array_key_exists($index, $contact_arr)) ? $contact_arr[$index] : null ;
                $comment = (array_key_exists($index, $comment_arr)) ? $comment_arr[$index] : null ;

                $prepago = Prepago::Where('number_i_pay',$number_i_pay)->first();
                $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();
                $representant = Representant::find($representant_id);
                $representant_id = ($representant) ? $representant->id : null;

                // if (empty($prepago) && empty($ingreso) && !empty($representant_id) && !empty($method_pay_id) && !empty($banco_id) && !empty($number_i_pay) && !empty($ingreso_ammount) && !empty($date_transaction)) {
                if (empty($prepago) && empty($ingreso) && !empty($method_pay_id) && !empty($banco_id) && !empty($number_i_pay) && !empty($ingreso_ammount) && !empty($date_transaction)) {

                    $arr = [
                        'representant_id'=>$representant_id,
                        'method_pay_id'=>$method_pay_id,
                        'banco_id'=>$banco_id,
                        'number_i_pay'=>$number_i_pay,
                        'ingreso_ammount'=>$ingreso_ammount,
                        'date_transaction'=>$date_transaction,
                        // 'ingreso_observations'=>$ingreso_observations,
                        'contact'=>$contact,
                        'comment'=>$comment,
                        'status_approved'=>'true'
                    ];

                    $create = Prepago::create($arr);
                    $count++;
                }
            }

        }

        $count_sentence = Convertidor::numToSentence($count);
        $messege = ($count>1) ? 'Prenscripciones guardadas correctamente!!. '.$count_sentence.' ('.$count.') Registros procesados' : 'Inscripción guardada correctamente!!. '.$count_sentence.' ('.$count.') Registro procesado' ;
        Session::flash('operp_ok',$messege);

        return view('administracion.prepagos.preinscripcions_csv', compact('prepagosCSV'));
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function cargaCSV(Request $request)
    {
        $file = $request->file('file_csv');

        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;
        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $fecha_limit = (!empty($request->fecha_limit)) ? $request->fecha_limit:'2000-01-01';
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"ISO-8859-1";
        $prepagosCSV = collect();
        $representants = collect();
        $total_errors = null;
        $total_row_fix = null;

        if (!empty($request->fecha_limit)) {
            $fecha_limit = $request->fecha_limit;
        } else {
            $prepago_last = Prepago::orderBy('date_transaction','desc')->first();//dd($prepago_last);
            $fecha_limit = ($prepago_last) ? $prepago_last->date_transaction : '2000-01-01' ;
        }

        if ($file_path) {

            $ingresosImport = new PrepagosImport;
            $import = $ingresosImport->getCollectPath($file_path,$delimiter,$input_encoding,$fecha_limit);

            $prepagosCSV = $import['prepagosCSV'];
            $total_errors = $import['total_errors'];
            $total_row_fix = $import['total_row_fix'];

            $representants = Representant::all();
        }


        return view('administracion.prepagos.carga_csv', compact('representants','file_name','prepagosCSV','total_errors','total_row_fix','delimiter','input_encoding','fecha_limit'));
    }

    public function cargaCSVPost(Request $request)
    {
        $file = $request->file('file_csv');
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('tmp', Str::random(40) . '.' . $file->getClientOriginalExtension());


        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"UTF-8";
        $fecha_limit = (!empty($request->fecha_limit)) ? $request->fecha_limit:'2000-01-01';

        $inputs = [
            'file_name'=>$file_name,
            'file_path'=>$file_path,
            'delimiter'=>$delimiter,
            'input_encoding'=>$input_encoding,
            'fecha_limit'=>$fecha_limit
        ];

        return redirect()->route('administracion.prepagos.carga.csv',$inputs);

    }

    public function storeCSV(Request $request)
    {
        // dd($request->all());

        $representant_id_arr = (is_array($request->representant_id)) ? $request->representant_id: array();
        $method_pay_id_arr = (is_array($request->method_pay_id)) ? $request->method_pay_id: array();
        $banco_id_arr = (is_array($request->banco_id)) ? $request->banco_id: array();
        $number_i_pay_arr = (is_array($request->number_i_pay)) ? $request->number_i_pay: array();
        $ingreso_ammount_arr = (is_array($request->ingreso_ammount)) ? $request->ingreso_ammount: array();
        $date_transaction_arr = (is_array($request->date_transaction)) ? $request->date_transaction: array();
        $ingreso_observations_arr = (is_array($request->ingreso_observations)) ? $request->ingreso_observations: array();
        $comment_arr = (is_array($request->comment)) ? $request->comment: array();

        if ( !empty(count($representant_id_arr)) && !empty(count($method_pay_id_arr)) && !empty( count($banco_id_arr)) && !empty( count($number_i_pay_arr)) && !empty( count($ingreso_ammount_arr)) && !empty( count($date_transaction_arr)) && !empty( count($ingreso_observations_arr)) && !empty( count($comment_arr)) ) {

            foreach ($representant_id_arr as $k => $representant_id) {

                $representant_id = $representant_id_arr[$k];
                $method_pay_id = $method_pay_id_arr[$k];
                $banco_id = $banco_id_arr[$k];
                $number_i_pay = $number_i_pay_arr[$k];
                $ingreso_ammount = $ingreso_ammount_arr[$k];
                $date_transaction = $date_transaction_arr[$k];
                $ingreso_observations = $ingreso_observations_arr[$k];
                $comment = $comment_arr[$k];

                $prepago = Prepago::Where('number_i_pay',$number_i_pay)->first();
                $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();

                if (empty($prepago) && empty($ingreso)) {

                    $arr = [
                        'representant_id'=>$representant_id,
                        'method_pay_id'=>$method_pay_id,
                        'banco_id'=>$banco_id,
                        'number_i_pay'=>$number_i_pay,
                        'ingreso_ammount'=>$ingreso_ammount,
                        'date_transaction'=>$date_transaction,
                        'ingreso_observations'=>$ingreso_observations,
                        'comment'=>$comment
                    ];

                    $create = Prepago::create($arr);
                }
            }

        }

        $messenge = 'Buen trabajo! La carga Fue realizada éxitosamente';
        Session::flash('operp_ok',$messenge);

        return redirect()->route('administracion.prepagos.carga.csv');
    }


}
