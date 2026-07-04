<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\MbancariosImport;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\Mbancario;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MbancarioController extends Controller
{
    public function crud(Request $request)
    {
        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null  ;

        $mbancarios = collect();

        if (count($request->all())>0) {

            $mbancarios = Mbancario::select('mbancarios.*');
            $mbancarios = (isset($finicial)) ? $mbancarios->wheredate('mbancarios.date_transaction','>=',$finicial) : $mbancarios;
            $mbancarios = (isset($ffinal)) ? $mbancarios->wheredate('mbancarios.date_transaction','<=',$ffinal) : $mbancarios;
            $mbancarios = (isset($number_i_pay)) ? $mbancarios->where('mbancarios.number_i_pay',$number_i_pay) : $mbancarios;
            $mbancarios = (isset($banco_id)) ? $mbancarios->where('mbancarios.banco_id',$banco_id) : $mbancarios;

            $mbancarios = $mbancarios->get();
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.mbancarios.crud', compact('mbancarios','finicial','ffinal','banco_id','number_i_pay','list_banco'));
    }

    public function cargaCSV(Request $request)
    {
        $file = (!empty($request->file('file_csv'))) ? $request->file('file_csv'): null;
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id: null;

        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $file_name = (!empty($request->file_name)) ? $request->file_name:null;
        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"ISO-8859-1";
        $mbancariosCSV = collect();
        $total_errors = 0;
        $total_row_fix = 0;

        if ($file_path) {

            $mbancariosImport = new MbancariosImport;
            $import = $mbancariosImport->getCollectPath($file_path,$delimiter,$input_encoding);

            $mbancariosCSV = $import['mbancariosCSV'];
            $total_errors = $import['total_errors'];
            $total_row_fix = $import['total_row_fix'];

            // dd($import);
        }

        $list_banco = Banco::list_public_bancos();

        return view('administracion.mbancarios.carga_csv', compact('mbancariosCSV','total_errors','total_row_fix','banco_id','list_banco','file_name','delimiter','input_encoding'));
    }

    public function cargaCSVPost(Request $request)
    {
        $file = $request->file('file_csv');
        $file_name = $file->getClientOriginalName();
        $file_path = $file->storeAs('tmp', Str::random(40) . '.' . $file->getClientOriginalExtension());

        $delimiter = (!empty($request->delimiter)) ? $request->delimiter:";";
        $input_encoding = (!empty($request->input_encoding)) ? $request->input_encoding:"UTF-8";

        $inputs = [
            'file_name'=>$file_name,
            'file_path'=>$file_path,
            'delimiter'=>$delimiter,
            'input_encoding'=>$input_encoding
        ];

        return redirect()->route('administracion.mbancarios.carga.csv',$inputs);

    }

    public function storeCSV(Request $request)
    {
        // dd($request->all());

        $banco_id = (!empty($request->banco_id)) ? $request->banco_id: null;
        $number_i_pay_arr = (is_array($request->number_i_pay)) ? $request->number_i_pay: array();
        $ingreso_ammount_arr = (is_array($request->ingreso_ammount)) ? $request->ingreso_ammount: array();
        $date_transaction_arr = (is_array($request->date_transaction)) ? $request->date_transaction: array();

        if ( $banco_id && !empty( count($number_i_pay_arr) ) && !empty( count($ingreso_ammount_arr) ) && !empty( count($date_transaction_arr) ) ) {

            $banco = Banco::findOrFail($banco_id);

            $datas = collect();
            $errors = collect();

            foreach ($number_i_pay_arr as $k => $number_i_pay) {

                $number_i_pay = $number_i_pay_arr[$k];
                $ingreso_ammount = $ingreso_ammount_arr[$k];
                $date_transaction = $date_transaction_arr[$k];
                $arr = [
                    'banco_id'=>$banco_id,
                    'number_i_pay'=>$number_i_pay,
                    'ingreso_ammount'=>$ingreso_ammount,
                    'date_transaction'=>$date_transaction,
                ];

                $mbancario = Mbancario::Where('number_i_pay',$number_i_pay)->first();
                $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();

                if (empty($mbancario) && empty($ingreso)) {
                    $create = Mbancario::create($arr);
                    $datas->push($arr);
                    // usleep(2000);
                }
                else {
                    $error = collect();
                    $error->put('key',$number_i_pay);
                    $errors->push($error);
                }
            }

            // dd($datas,$errors);

            $messenge = 'Buen trabajo! La carga Fue realizada éxitosamente. '.$datas->count().'. nuevos registros';
            Session::flash('operp_ok',$messenge);
            if ($errors->count() > 0) {
                Session::flash('db_errors',$errors);
            }

        }

        return redirect()->route('administracion.mbancarios.carga.csv');

        // dd($datas);

    }

    public function destroy($id, Request $request)
    {
        $mbancario = Mbancario::findOrFail($id);
        $mbancario->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.mbancarios.carga.csv');
    }
}
