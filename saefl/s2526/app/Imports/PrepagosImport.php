<?php

namespace App\Imports;

use App\Imports\Functions\Prepagos\PrepagoValidateCSV;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Planpago\Mbancario;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Prepago;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use \PhpOffice\PhpSpreadsheet\Shared\Date as DateExcel;

class PrepagosImport implements ToCollection
{
    use Importable;
    use PrepagoValidateCSV;

    private $rows = 0;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {

        return new Collection([
            'ci_estudiant' => $rows[0],
            'nota' => $rows[1],
        ]);
    }

    public function getCollectPathF2($filePath = null)
    {
        // dd('getCollectPathF2');

        $importDataArr = $this->toArray($filePath); //dd($importDataArr);

        $prepagos = collect();

        if(count($importDataArr)){

            foreach ($importDataArr as $sheet => $rows) {

                // dd($sheet,$rows);

                if ($sheet==0) {

                    $header = array_shift($rows);

                    // dd($header,$rows);

                    $datas = collect();
                    $errors = collect();
                    foreach ($rows as $index => $value) {
                        // $index = $key + 1;
                        $data = collect();
                        $error = collect();
                        // dd($header,$index,$value);

                        switch ($value[24]) {
                            case 'UNA TRANSFERENCIA':
                                $offset = 25;
                                $offset_feha = 26;
                                $offset_bco_dtn = 27;
                                $offset_bco_orig = 91;
                                $offset_ingreso = 30;
                                $offset_telf_dtn = 92;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                            break;

                            case 'DOS  TRANSFERENCIAS':
                                $offset = 88;
                                $offset_feha = 89;
                                $offset_bco_dtn = 90;
                                $offset_bco_orig = 103;
                                $offset_ingreso = 93;
                                $offset_telf_dtn = 104;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                                $offset = 95;
                                $offset_feha = 96;
                                $offset_bco_dtn = 90;
                                $offset_bco_orig = 98;
                                $offset_ingreso = 99;
                                $offset_telf_dtn = 100;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                            break;

                            case 'TRES TRANSFERENCIAS':

                                // dd($header,$index,$value);

                                $offset = 105;
                                $offset_feha = 106;
                                $offset_bco_dtn = 108;
                                $offset_bco_orig = 107;
                                $offset_ingreso = 109;
                                $offset_telf_dtn = 110;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                                $offset = 111;
                                $offset_feha = 112;
                                $offset_bco_dtn = 114;
                                $offset_bco_orig = 113;
                                $offset_ingreso = 115;
                                $offset_telf_dtn = 100;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                                $offset = 122;
                                $offset_feha = 121;
                                $offset_bco_dtn = 119;
                                $offset_bco_orig = 120;
                                $offset_ingreso = 118;
                                $offset_telf_dtn = 123;
                                $datas = $this->getDataRepresentant($value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_dtn,$offset_bco_dtn,$offset_feha);
                                $prepagos->push($datas); //dd($prepagos);

                            break;

                        }


                    }

                }

            }
        }

        return $prepagos;
    }

    public function getDataRepresentant($arr_value,$index,$offset,$offset_bco_orig,$offset_ingreso,$offset_telf_oti,$offset_bco_dtn,$offset_feha)
    {
        $data = collect();
        $error = collect();
        $errors = collect();
        $datas = collect();
        $format_date = 'Y-m-d';

        $marcaTemporal = (is_numeric($arr_value[0])) ? DateExcel::excelToDateTimeObject($arr_value[0]) : $arr_value[0]; //dd($marcaTemporal); //Marca temporal
        $fecha = $marcaTemporal->format('d-m-Y');
        $data_representant_name = (!empty($arr_value[1])) ? $arr_value[1]: null ; //NOMBRE COMPLETO DEL REPRESENTANTE

        switch ($arr_value[5]) {
            case 'UN ESTUDIANTE': $offsetEst = 6; $data_estudiant_name_full = $arr_value[$offsetEst].' - '.$arr_value[$offsetEst+1].';';break;
            case 'DOS ESTUDIANTES': $offsetEst = 9; $data_estudiant_name_full = $arr_value[$offsetEst].' - '.$arr_value[$offsetEst+1].' ; '.$arr_value[$offsetEst+3].' - '.$arr_value[$offsetEst+4].' ; ';break;
            case 'TRES ESTUDIANTES': $offsetEst = 9; $data_estudiant_name_full = $arr_value[$offsetEst].' - '.$arr_value[$offsetEst+1].' ; '.$arr_value[$offsetEst+3].' - '.$arr_value[$offsetEst+4].' ; '.$arr_value[$offsetEst+6].' - '.$arr_value[$offsetEst+7];break;
            default: $offsetEst = 6; break;
        }

        $data_estudiant_name = (!empty($arr_value[$offsetEst])) ? $arr_value[$offsetEst]: null ; //NOMBRE COMPLETO DEL ESTUDIANTE
        $data_ci_estudiant = (!empty($arr_value[$offsetEst+1])) ? intval($arr_value[$offsetEst+1]):null ; //CI COMPLETO DEL ESTUDIANTE

        // $data_estudiant_name_full = $arr_value[$offsetEst] . ' ; ' . $arr_value[$offsetEst+3] . ' ; '. ' ; ' . $arr_value[$offsetEst+6] ; //NOMBRE COMPLETO DEL ESTUDIANTE
        // $data_ci_estudiant_full = $arr_value[$offsetEst+1] . ' ; ' . $arr_value[$offsetEst+4] . ' ; '. ' ; ' . $arr_value[$offsetEst+7] ; //NOMBRE COMPLETO DEL ESTUDIANTE

        $data_representant_ci = (!empty($arr_value[2])) ? intval($arr_value[2]): null ; //dd($value[2],$representant_ci); //NOMBRE COMPLETO DEL REPRESENTANTE
        $representant_email_1 = (!empty($arr_value[3])) ? $arr_value[3]: null ; //dd($value[2],$representant_ci); //Correo electrónico N1
        $representant_email_2 = (!empty($arr_value[102])) ? $arr_value[102]: null ; //dd($value[2],$representant_ci); //Correo electrónico N1
        $representant_phones = (!empty($arr_value[4])) ? $arr_value[4]: null ; //dd($value[2],$representant_ci); //TELEFONO(S)
        $comments = (!empty($arr_value[101])) ? $arr_value[101]: null ; //dd($value[2],$representant_ci); //TELEFONO(S)

        $estudiant_name = $data_estudiant_name;
        $ci_estudiant = $data_ci_estudiant;

        $representant = Representant::where('ci_representant',$data_representant_ci)->first();
        if (empty($representant)) {
            $arr_string = [ 'search'=>$data_representant_name];
            // $representant = Representant::name($arr_string)->active('true')->first();
        }

        if (empty($representant)) {

            $estudiant = Estudiant::where('ci_estudiant',$data_ci_estudiant)->first();
            if (empty($estudiant)) {
                $arr_string = [ 'search'=>$data_estudiant_name];
                // $estudiant = Estudiant::name($arr_string)->active('true')->first();
                //if ($data_representant_ci==13494942) { dd($estudiant_name,$data_ci_estudiant,$arr_string,$estudiant); }
            }

            if ($estudiant) {
                $representant = ($estudiant->representant) ? $estudiant->representant : null;
                $estudiant_id = $estudiant->id;
                $ci_estudiant = $estudiant->ci_estudiant;
                $estudiant_name = $estudiant->fullname;
            }
            else {
                $error->put('ci_estudiant',$data_ci_estudiant);
                $error->put('code','CENE');
                $error->put('messenge','ESTUDIANTE NO ENCONTRADO');
                $error->put('class','danger');
                $error->put('index',$index);
                $errors->push($error);
                $error = collect();
                $ci_estudiant = '['.$ci_estudiant.']';
                $estudiant_name = '['.$estudiant_name.']';
            }
        }

        if ($representant) {
            $representant_id = $representant->id;
            $representant_ci = $representant->ci_representant;
            $representant_name = $representant->name;
        }
        else {
            $error->put('ci_representant',$data_representant_ci);
            $error->put('code','CINE');
            $error->put('messenge','REPRESENTANTE NO ENCONTRADO');
            $error->put('class','warning');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect();
            $representant_id = null;
            $representant_ci = '['.$data_representant_ci.']';
            $representant_name = '['.$data_representant_name.']';
        }

        $bco_id_arr = ['BANCARIBE (7221)'=>3,'PAGO MOVIL BANCARIBE (8524)'=>6,'BANCO DEL TESORO (0279)'=>2];
        $method_pay_id_arr = ['BANCARIBE (7221)'=>3,'PAGO MOVIL BANCARIBE (8524)'=>5,'BANCO DEL TESORO (0279)'=>3];
        $data_number_i_pay = (!empty($arr_value[$offset])) ? intval($arr_value[$offset]):null ;
        $data_date_transaction = (is_numeric($arr_value[$offset_feha])) ? DateExcel::excelToDateTimeObject($arr_value[$offset_feha])->format($format_date) : $arr_value[$offset_feha];
        $data_banco_name = (!empty($arr_value[$offset_bco_dtn])) ? $arr_value[$offset_bco_dtn]:null ;
        $data_banco_id = (array_key_exists($data_banco_name, $bco_id_arr)) ? $bco_id_arr[$data_banco_name] : null ;

        $data_method_pay_id = (array_key_exists($data_banco_name, $method_pay_id_arr)) ? $method_pay_id_arr[$data_banco_name] : null ;
        $data_ingreso_ammount = (!empty($arr_value[$offset_ingreso])) ? $arr_value[$offset_ingreso]:null ;
        $data_banco_ori_name = (!empty($arr_value[($offset_bco_orig)])) ? $arr_value[($offset_bco_orig)]:null ;
        $data_telf_ori = (!empty($arr_value[$offset_telf_oti])) ? $arr_value[$offset_telf_oti]:null ;

        $number_i_pay = $data_number_i_pay;
        $ingreso_ammount = $data_ingreso_ammount;
        $date_transaction = $data_date_transaction;
        $banco_id = $data_banco_id;

        $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();
        if ($ingreso) {
            $error->put('code','NRUA');
            $error->put('messenge','NUMERO DE REFERENCIA ASOCIADO');
            $error->put('class','danger');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect();
        }
        else{

            $mbancario = Mbancario::Where('number_i_pay',$number_i_pay)->first();
            // $mbancario = ($mbancario) ? $mbancario : Mbancario::Where('number_i_pay','like', "%".substr($number_i_pay,-8)."%")->first() ;
            if (!$mbancario) {
                $error->put('code','NREX');
                $error->put('messenge','NUMERO DE REFERENCIA NO SE ENCONTRO EN MOVIMIENTOS BANCARIOS CSV');
                $error->put('class','danger');
                $error->put('index',$index);
                $errors->push($error);
                $error = collect();
                $date_transaction = $data_date_transaction;
                $ingreso_ammount = $data_ingreso_ammount;
                $banco_id = $data_banco_id;
            }
            else{
                $number_i_pay = $mbancario->number_i_pay;
                $ingreso_ammount = $mbancario->ingreso_ammount;
                $banco_id = $mbancario->banco_id;
                $date_transaction = $mbancario->date_transaction->format($format_date);

                if ($ingreso_ammount <> $data_ingreso_ammount) {
                    $error->put('code','INMO');
                    $error->put('messenge','INCONSISTENCIA EN EL MONTO');
                    $error->put('class','info');
                    $error->put('index',$index);
                    $errors->push($error);
                    $error = collect();
                    $ingreso_ammount = $ingreso_ammount .' ['.$data_ingreso_ammount .']' ;
                }
                if ($date_transaction <> $data_date_transaction) {
                    $error->put('code','INFE');
                    $error->put('messenge','INCONSISTENCIA EN LA FECHA');
                    $error->put('class','secondary');
                    $error->put('index',$index);
                    $errors->push($error);
                    $error = collect();
                    // $date_transaction = $date_transaction .' ['.$data_date_transaction .']' ;
                }
            }
        }

        $ingreso = Prepago::Where('number_i_pay',$number_i_pay)->first();
        if ($ingreso) {
            $error->put('code','NRR');
            $error->put('messenge','NUMERO DE REFERENCIA REGISTRADO');
            $error->put('class','danger');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect();
        }

        $data->put('index',$index);
        $data->put('marcaTemporal',$marcaTemporal);
        $data->put('fecha',$fecha);
        $data->put('representant_id',$representant_id);
        $data->put('representant_ci',$representant_ci);
        $data->put('representant_name',$representant_name);
        $data->put('estudiant_name',$estudiant_name);
        $data->put('ci_estudiant',$ci_estudiant);
        $data->put('data_estudiant_name',$data_estudiant_name);
        $data->put('data_ci_estudiant',$data_ci_estudiant);
        $data->put('data_estudiant_name_full',$data_estudiant_name_full);
        // $data->put('data_ci_estudiant_full',$data_ci_estudiant_full);
        $data->put('representant_email_1',$representant_email_1);
        $data->put('representant_phones',$representant_phones);
        $data->put('representant_email_2',$representant_email_2);
        // $data->put('ingreso_observations',$representant_phones.' - '.$representant_email_1.' - '.$representant_email_2.' - ');
        $data->put('contact',$representant_phones.' - '.$representant_email_1.' - '.$representant_email_2.' - ');
        $data->put('comments',$comments);

        $data->put('number_i_pay',$number_i_pay);
        $data->put('date_transaction',$date_transaction);
        $data->put('data_banco_name',$data_banco_name);
        $data->put('banco_id',$banco_id);
        $data->put('method_pay_id',$data_method_pay_id);
        $data->put('ingreso_ammount',$ingreso_ammount);

        $data->put('data_banco_ori_name',$data_banco_ori_name);
        $data->put('data_telf_ori',$data_telf_ori);

        $datas->put('datas',$data);
        $datas->put('errors',$errors);
        $datas->put('rows_ok',(count($errors)>0) ? false:true );
        $datas->put('count_errors',count($errors));

        // return $datas->toArray();
        return $datas;

    }

    public function getCollectPath($filePath = null,$delimiter=';',$input_encoding='UTF-8',$fecha_limit="2000-01-01")
    {

        Config::set('excel.imports.csv.delimiter', $delimiter);
        Config::set('excel.imports.csv.input_encoding', $input_encoding);
        $import_data = $this->toCollection($filePath);

        $datas = collect();
        $total_errors = 0;
        $total_row_fix = 0;
        $fecha_limit_obj = Date::createFromFormat('Y-m-d',$fecha_limit);

        if($import_data->count()){

            foreach ($import_data as $key => $value) {

                // dd($value);

                foreach ($value as $k => $v) {
                    // dd($k,$v);
                    if ($k>0) {

                        $data = collect();
                        $errors = collect();

                        $banco_name_xls = (isset($v[0])) ? $v[0]:null;
                        $method_pay_name_xls = (isset($v[1])) ? $v[1]:null;
                        $date = (isset($v[2])) ? $v[2]:null;
                        $ingreso_ammount_xls = (isset($v[3])) ? $v[3]:null;
                        $number_i_pay_xls = (isset($v[4])) ? $v[4]:null;
                        $ci_representant_xls = (isset($v[5])) ? $v[5]:null;
                        $representant_name_xls = (isset($v[6])) ? $v[6]:null;
                        $estudiante_fullname_xls = (isset($v[7])) ? $v[7]:null;
                        $telefono_xls = (isset($v[8])) ? $v[8]:null;
                        $meses_xls = (isset($v[9])) ? $v[9]:null;
                        $comment_xls = (isset($v[10])) ? $v[10]:null;

                        $method_pay_id = $this->fix_method_pay($method_pay_name_xls);

                        $banco_id = $this->fix_banco_id($banco_name_xls,$method_pay_id);

                        $ingreso_ammount = $this->fix_monto($ingreso_ammount_xls);

                        $number_i_pay = $this->fix_referencia($number_i_pay_xls);

                        $date_fix = $this->fix_fecha($date);
                        $date_obj = (validateDate($date_fix,'d-m-Y')) ? Date::createFromFormat('d-m-Y',$date_fix) : Date::createFromFormat('Y-m-d','3000-12-31');
                        $date_transaction = (validateDate($date_fix,'d-m-Y')) ? Date::createFromFormat('d-m-Y',$date_fix)->format('Y-m-d') : $date ;
                        if (!validateDate($date_transaction,'Y-m-d')) {
                            $error = collect();
                            $error->put('messenge','Inconsistencia en la fecha');
                            $error->put('value',$date_transaction);
                            $error->put('class','secondary');
                            $errors->push($error);
                        }

                        $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();
                        if ($ingreso) {
                            $error = collect();
                            $error->put('messenge','Número de referencia ya fue usado');
                            $error->put('value',$number_i_pay);
                            $error->put('class','danger');
                            $errors->push($error);
                        }
                        else{
                            $mbancario = Mbancario::Where('number_i_pay',$number_i_pay)->first();
                            if (!$mbancario) {
                                $error = collect();
                                $error->put('messenge','Número de referencia no se encontró');
                                $error->put('value',$number_i_pay);
                                $error->put('class','warning');
                                $errors->push($error);
                            }
                            else{
                                if ($mbancario->ingreso_ammount <> $ingreso_ammount) {
                                    $error = collect();
                                    $error->put('messenge','Inconsistencia en el monto');
                                    $error->put('value',$ingreso_ammount.' | |'.$mbancario->ingreso_ammount);
                                    $error->put('class','danger');
                                    $errors->push($error);
                                }
                            }
                        }

                        $representant = Representant::where('ci_representant',$ci_representant_xls)->first();
                        if (empty($representant)) {
                            $error = collect();
                            $error->put('messenge','Cédula del Representante no encontrada');
                            $error->put('value',$ci_representant_xls . ' '.$representant_name_xls);
                            $error->put('class','info');
                            $errors->push($error);
                        }

                        $method_pay = MetodoPago::where('id',$method_pay_id)->first();
                        if (empty($method_pay)) {
                            $error = collect();
                            $error->put('messenge','Método de pago no encontrado');
                            $error->put('value',$method_pay_name_xls);
                            $error->put('class','warning');
                            $errors->push($error);
                        }

                        $banco = Banco::where('id',$banco_id)->first();
                        if (empty($banco)) {
                            $error = collect();
                            $error->put('messenge','Banco no encontrado');
                            $error->put('value',$banco_name_xls);
                            $error->put('class','info');
                            $errors->push($error);
                        }

                        $ingreso_ammount = $this->fix_monto($ingreso_ammount_xls);
                        if ($ingreso_ammount<=0) {
                            $error = collect();
                            $error->put('messenge','Inconsistecia en el monto');
                            $error->put('value',$ingreso_ammount);
                            $error->put('class','danger');
                            $errors->push($error);
                        }

                        if ($date_obj >= $fecha_limit_obj) {

                            $data->put('fila', $k);
                            $data->put('method_pay_id', $method_pay_id);
                            $data->put('method_pay_name', ($method_pay) ? $method_pay->name:null );
                            $data->put('banco_id', $banco_id);
                            $data->put('banco_name', ($banco) ? $banco->name:null );
                            $data->put('number_i_pay', $number_i_pay);
                            $data->put('date_transaction', $date_transaction);
                            $data->put('ingreso_ammount', $ingreso_ammount);
                            $data->put('representant_id', ($representant) ? $representant->id : null );
                            $data->put('representant_name', ($representant) ? $representant->name : $representant_name_xls );
                            $data->put('ci_representant', ($representant) ? $representant->ci_representant : $ci_representant_xls );
                            $data->put('estudiante_fullname_xls', $estudiante_fullname_xls);
                            $data->put('telefono', $telefono_xls);
                            $data->put('meses_xls', $meses_xls);
                            $data->put('comment', $comment_xls);

                            $data->put('errors', $errors);
                            $datas->push($data);
                            $total_errors += $errors->count();
                            $total_row_fix = ($errors->isEmpty()) ? ($total_row_fix + 1) : $total_row_fix ;

                        }
                    }
                }

            }
        }

        $result = collect(['prepagosCSV'=>$datas,'total_errors'=>$total_errors,'total_row_fix'=>$total_row_fix]);

        return $result;
    }

    public function toCollectionFix($filePath = null, string $disk = null, string $readerType = null): Collection
    {

        $ingresoImport = $this->toArray($filePath);
        $datas = collect();

        foreach ($ingresoImport as $ingreso) {

            $header = array_shift($ingreso);

            foreach ($ingreso as $k => $v) {

                $data = collect();

                $banco_name_xls = $v[0];
                $method_pay_name_xls = $v[1];
                $date_transaction_xls = (is_int($v[2])) ? DateExcel::excelToDateTimeObject($v[2])->format('Y-m-d') : $v[2];
                $ingreso_ammount_xls = $v[3];
                $number_i_pay_xls = $v[4];
                $ci_representant_xls = $v[5];
                $representant_name_xls = $v[6];
                $estudiante_fullname_xls = $v[7];
                $telefono_xls = $v[8];
                $comment_xls = $v[9];
                $status_error = false;
                $class = null;

                if (!empty($banco_name_xls) && !empty($method_pay_name_xls) && !empty($number_i_pay_xls) && !empty($ci_representant_xls)) {

                    switch ($method_pay_name_xls) {
                        case 'Transferencia Bancaria': $method_pay_id=3; break;
                        case 'Pago Móvil': $method_pay_id=5; break;
                        case 'Punto de Venta': $method_pay_id=4; break;
                        case 'Depósito': $method_pay_id=2; break;
                        default: $method_pay_id=null; break;
                    }
                    if ($method_pay_id==5) {
                        $banco_id = 6;
                    }
                    else{
                        switch ($banco_name_xls) {
                            case 'Bancaribe (0114)': $banco_id=3; break;
                            case 'Banco del Tesoro (0163)': $banco_id=2; break;
                            default: $banco_id=null; break;
                        }
                    }

                    $mbancario = Mbancario::Where('number_i_pay',$number_i_pay_xls)->first();
                    $error_mbancario_number_i_pay = (empty($mbancario)) ? true:false ;
                    $class = ($error_mbancario_number_i_pay) ? 'danger ' : $class ;
                    $status_error = ($error_mbancario_number_i_pay) ? true : $status_error;

                    $date_transaction_bco = ($mbancario) ? $mbancario->date_transaction:null ;
                    $error_date_transaction_bco = ($date_transaction_bco <> $date_transaction_xls) ? true:false ;
                    $class = ($error_date_transaction_bco) ? 'warning ' : $class ;
                    $status_error = ($error_date_transaction_bco) ? true : $status_error;

                    $ingreso_ammount_bco = ($mbancario) ? $mbancario->ingreso_ammount:null ;
                    $error_ingreso_ammount_bco = ($ingreso_ammount_bco <> $ingreso_ammount_xls) ? true:false ;
                    $class = ($error_ingreso_ammount_bco) ? 'info ' : $class ;
                    $status_error = ($error_ingreso_ammount_bco) ? true : $status_error;

                    $representant = Representant::where('ci_representant',$ci_representant_xls)->first();
                    $error_representant = ($representant) ? false:true ;
                    $class = ($error_representant) ? 'danger ':$class ;
                    $status_error = ($error_representant) ? true : $status_error;

                    $method_pay = MetodoPago::where('id',$method_pay_id)->first();
                    $error_method_pay = ($method_pay) ? false:true ;
                    $class = ($error_method_pay) ? 'warning ' : $class ;
                    $status_error = ($error_method_pay) ? true : $status_error;

                    $banco = Banco::where('id',$banco_id)->first();
                    $error_banco = ($method_pay) ? false:true ;
                    $class = ($error_banco) ? 'info ' : $class ;
                    $status_error = ($error_banco) ? true : $status_error;

                    $ingreso = Ingreso::Where('number_i_pay',$number_i_pay_xls)->first();
                    $error_number_i_pay = ($ingreso) ? true:false ;
                    $class = ($error_number_i_pay) ? 'secondary ' : $class ;
                    $status_error = ($error_number_i_pay) ? true : $status_error;

                    $prepago = Prepago::Where('number_i_pay',$number_i_pay_xls)->first();
                    $error_prepago_number_i_pay = ($prepago) ? true:false ;
                    $class = ($error_prepago_number_i_pay) ? 'secondary ' : $class ;
                    $status_error = ($error_prepago_number_i_pay) ? true : $status_error;

                    $error_ingreso_ammount = (!is_numeric($ingreso_ammount_xls)) ? true:false ;
                    $class = ($error_ingreso_ammount) ? 'secondary ' : $class ;
                    $status_error = ($error_ingreso_ammount) ? true : $status_error;

                    $error_date_transaction_xls = (!validateDate($date_transaction_xls, 'Y-m-d')) ? true:false ;
                    $class = ($error_date_transaction_xls) ? 'danger ' : $class ;
                    $status_error = ($error_date_transaction_xls) ? true : $status_error;

                    $data->put('representant_id', ($representant) ? $representant->id:null );
                    $data->put('representant_name', ($representant) ? $representant->name:null );
                    $data->put('ci_representant', ($representant) ? $representant->ci_representant:null );
                    $data->put('ci_representant_xls', $ci_representant_xls);
                    $data->put('representant_name_xls', $representant_name_xls);
                    $data->put('error_representant', $error_representant);

                    $data->put('method_pay_id', $method_pay_id);
                    $data->put('method_pay_name', ($method_pay) ? $method_pay->name:null );
                    $data->put('method_pay_name_xls', $method_pay_name_xls);
                    $data->put('error_method_pay', $error_method_pay);
                    $data->put('error_mbancario_number_i_pay', $error_mbancario_number_i_pay);

                    $data->put('banco_id', $banco_id);
                    $data->put('banco_name', ($banco) ? $banco->name:null );
                    $data->put('banco_name_xls', $banco_name_xls);
                    $data->put('error_banco', $error_banco);

                    $data->put('number_i_pay', $number_i_pay_xls);
                    $data->put('error_number_i_pay', $error_number_i_pay);
                    $data->put('error_prepago_number_i_pay', $error_prepago_number_i_pay);

                    $data->put('date_transaction', $date_transaction_xls);
                    $data->put('error_date_transaction_xls', $error_date_transaction_xls);
                    $data->put('date_transaction_bco', $date_transaction_bco);
                    $data->put('error_date_transaction_bco', $error_date_transaction_bco);

                    $data->put('ingreso_ammount', $ingreso_ammount_xls);
                    $data->put('error_ingreso_ammount', $error_ingreso_ammount);
                    $data->put('ingreso_ammount_bco', $ingreso_ammount_bco);
                    $data->put('error_ingreso_ammount_bco', $error_ingreso_ammount_bco);

                    $data->put('telefono', $telefono_xls);
                    $data->put('comment', $comment_xls);
                    $data->put('status_error', $status_error);

                    $data->put('class', $class);

                    $datas->push($data);

                }
            }

        }

        return $datas;
    }

    public function getCsvSettings(): array
    {
        return [
            // 'delimiter'              => ';',
            'enclosure'              => '"',
            'escape_character'       => '\\',
            'contiguous'             => false,
            // 'input_encoding'         => 'ISO-8859-1',
        ];
    }



}
