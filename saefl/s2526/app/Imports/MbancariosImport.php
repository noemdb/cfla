<?php

namespace App\Imports;

use App\Imports\Functions\Mbancarios\ValidateCSV;
use App\Models\app\Estudiante\Ingreso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Models\app\Planpago\Mbancario;
use App\Models\app\Planpago\Prepago;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Facades\Excel;
use \PhpOffice\PhpSpreadsheet\Shared\Date as DateExcel;
// use Excel;

class MbancariosImport implements ToCollection , WithCustomCsvSettings
{
    use Importable;
    use ValidateCSV;
    private $rows = 0;

    protected $delimiter = ';';

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

    public function toCollectionFix($filePath = null, string $disk = null, string $readerType = null): Collection
    {
        // dd($this);

        // $importXLS = $this->toArray($filePath);
        $importXLS = $this->toCollection($filePath);
        $datas = collect();

        foreach ($importXLS as $ingreso) {

            $header = array_shift($ingreso);

            dd($header);

            foreach ($ingreso as $k => $v) {

                $data = collect();

                $date_transaction_xls = (is_int($v[0])) ? DateExcel::excelToDateTimeObject($v[0])->format('Y-m-d') : $v[0];
                $number_i_pay_xls = $v[1];
                $ingreso_ammount_xls = $v[2];
                $class = null;

                if (!empty($date_transaction_xls) && !empty($number_i_pay_xls) && !empty($ingreso_ammount_xls)) {

                    $ingreso = Ingreso::Where('number_i_pay',$number_i_pay_xls)->first();
                    $error_number_i_pay = ($ingreso) ? true:false ;
                    $class = ($error_number_i_pay) ? 'warning ' : $class ;

                    $mbancario = Mbancario::Where('number_i_pay',$number_i_pay_xls)->first();
                    $error_mbancario_number_i_pay = ($mbancario) ? true:false ;
                    $class = ($error_mbancario_number_i_pay) ? 'info ' : $class ;

                    $error_ingreso_ammount = (!is_numeric($ingreso_ammount_xls)) ? true:false ;
                    $class = ($error_ingreso_ammount) ? 'secondary ' : $class ;

                    $error_date_transaction_xls = (!validateDate($date_transaction_xls, 'Y-m-d')) ? true:false ;
                    $class = ($error_date_transaction_xls) ? 'danger ' : $class ;

                    $state_error = ($error_number_i_pay || $error_mbancario_number_i_pay || $error_ingreso_ammount || $error_date_transaction_xls )  ? true : false ;

                    $data->put('number_i_pay', $number_i_pay_xls);
                    $data->put('error_number_i_pay', $error_number_i_pay);
                    $data->put('error_mbancario_number_i_pay', $error_mbancario_number_i_pay);

                    $data->put('date_transaction', $date_transaction_xls);
                    $data->put('error_date_transaction_xls', $error_date_transaction_xls);

                    $data->put('ingreso_ammount', $ingreso_ammount_xls);
                    $data->put('error_ingreso_ammount', $error_ingreso_ammount);
                    $data->put('state_error', $state_error);

                    $data->put('class', $class);

                    $datas->push($data);

                }
            }

        }

        return $datas;
    }

    public function getCollectPath($filePath = null,$delimiter=';',$input_encoding='UTF-8')
    {

        //dd($this);
        Config::set('excel.imports.csv.delimiter', $delimiter);
        Config::set('excel.imports.csv.input_encoding', $input_encoding);
        $import_data = $this->toCollection($filePath); //dd($filePath);
        // $header = $import_data->shift();
        $datas = collect();
        $total_errors = 0;
        $total_row_fix = 0;

        if($import_data->count()){

            foreach ($import_data as $key => $value) {

                // dd($value);

                foreach ($value as $k => $v) {
                    // dd($k,$v);
                    if ($k>0) {

                        $data = collect();
                        $errors = collect();
                        $date = (isset($v[0])) ? $v[0]:null;
                        $referencia = (isset($v[1])) ? $v[1]:null;
                        $monto = (isset($v[2])) ? $v[2]:null;

                        // $referencia = $v[1];
                        // $monto = $v[2];

                        $date_fix = $this->fix_fecha($date);

                        $date_transaction = (validateDate($date_fix,'d-m-Y')) ? Date::createFromFormat('d-m-Y',$date_fix)->format('Y-m-d') : $date;

                        if (!validateDate($date_transaction,'Y-m-d')) {
                            $error = collect();
                            $error->put('messenge','Inconsistencia en la fecha');
                            $error->put('value',$date_transaction);
                            $error->put('alterno',$date_fix);
                            $error->put('class','secondary');
                            $errors->push($error);
                        }

                        $number_i_pay = $this->fix_referencia($referencia);
                        $ingreso = Ingreso::Where('number_i_pay',$number_i_pay)->first();
                        $mbancario = Mbancario::Where('number_i_pay',$number_i_pay)->first();
                        if ($ingreso || $mbancario) {
                            $error = collect();
                            $error->put('messenge','Número de referencia ya registrado');
                            $error->put('value',$number_i_pay);
                            $error->put('class','danger');
                            $errors->push($error);
                        }

                        $searh = $datas->where('number_i_pay',$number_i_pay)->first();
                        if ($searh) {
                            $error = collect();
                            $error->put('messenge','Número de referencia dulpicado');
                            $error->put('value',$number_i_pay);
                            $error->put('class','warning');
                            $errors->push($error);
                        }

                        $ingreso_ammount = $this->fix_monto($monto);
                        // if (!is_numeric($ingreso_ammount)) {
                        if ($ingreso_ammount<=0) {
                            $error = collect();
                            $error->put('messenge','Inconsistecia en el monto');
                            $error->put('value',$monto);
                            $error->put('alterno',$ingreso_ammount);
                            $error->put('class','danger');
                            $errors->push($error);
                        }

                        $data->put('number_i_pay', $number_i_pay);
                        $data->put('date_transaction', $date_transaction);
                        $data->put('ingreso_ammount', $ingreso_ammount);
                        $data->put('errors', $errors);
                        $datas->push($data);
                        $total_errors += $errors->count();
                        $total_row_fix = ($errors->isEmpty()) ? ($total_row_fix + 1) : $total_row_fix ;
                    }
                }

            }
        }

        $return = collect(['mbancariosCSV'=>$datas,'total_errors'=>$total_errors,'total_row_fix'=>$total_row_fix]);

        return $return;
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
