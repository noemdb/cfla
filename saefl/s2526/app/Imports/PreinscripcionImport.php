<?php

namespace App\Imports;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Preinscripcion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use \PhpOffice\PhpSpreadsheet\Shared\Date as DateExcel;

class PreinscripcionImport implements ToCollection
{
    use Importable;
    
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    public function getCollectPathF2($filePath = null)
    {
        // dd('getCollectPathF2');
        
        $importDataArr = $this->toArray($filePath); //dd($importDataArr);

        $preinscripcions = collect();

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
                        
                        switch ($value[5]) {
                            case 'UN ESTUDIANTE': 
                                $offset = 6;
                                $offset_ge = 147;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd($preinscripcions);

                            break;

                            case 'DOS ESTUDIANTES': 
                                $offset = 9;
                                $offset_ge = 148;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd($preinscripcions);
                                
                                $offset = 12;
                                $offset_ge = 149;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd($preinscripcions);

                                break;
                            case 'TRES ESTUDIANTES':                                 
                                $offset = 15;
                                $offset_ge = 149;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd($preinscripcions);
                                
                                $offset = 18;
                                $offset_ge = 150;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd($preinscripcions);

                                $offset = 21;
                                $offset_ge = 151;
                                $datas = $this->getDataEstudiants($value,$index,$offset,$offset_ge);
                                $preinscripcions->push($datas); //dd('TRES ESTUDIANTES',$preinscripcions);

                                break;
                            // default:  $how_many_students = 1; break;
                        }

                        
                    }

                }                

            }
        }

        return $preinscripcions;
    }   

    public function getDataEstudiants($arr_value,$index,$offset,$offset_ge)
    {
        $data = collect();
        $error = collect(); 
        $errors = collect(); 
        $datas = collect();

        $marcaTemporal = (is_numeric($arr_value[0])) ? DateExcel::excelToDateTimeObject($arr_value[0]) : $arr_value[0]; //dd($marcaTemporal); //Marca temporal
        $fecha = (is_numeric($arr_value[0])) ? DateExcel::excelToDateTimeObject($arr_value[0])->format('d-m-Y') : $arr_value[0]; //dd($marcaTemporal); //Marca temporal
        // $fecha = $marcaTemporal->format('d-m-Y');
        $representant_name = (!empty($arr_value[1])) ? $arr_value[1]: null ; //NOMBRE COMPLETO DEL REPRESENTANTE
        $representant_ci = (!empty($arr_value[2])) ? intval($arr_value[2]): null ; //dd($value[2],$representant_ci); //NOMBRE COMPLETO DEL REPRESENTANTE 
        $representant_email_1 = (!empty($arr_value[3])) ? $arr_value[3]: null ; //dd($value[2],$representant_ci); //Correo electrónico N1
        $representant_email_2 = (!empty($arr_value[102])) ? $arr_value[102]: null ; //dd($value[2],$representant_ci); //Correo electrónico N1
        $representant_phones = (!empty($arr_value[4])) ? $arr_value[4]: null ; //dd($value[2],$representant_ci); //TELEFONO(S)
        $comments = (!empty($arr_value[101])) ? $arr_value[101]: null ; //dd($value[2],$representant_ci); //TELEFONO(S)

        $data_estudiant_name = (!empty($arr_value[$offset])) ? $arr_value[$offset]:null ; 
        $data_ci_estudiant = (!empty($arr_value[($offset+1)])) ? intval($arr_value[($offset+1)]):null ; 
        $data_grado_name = (!empty($arr_value[($offset+2)])) ? $arr_value[($offset+2)]:null ; $data_grado_name = preg_replace('/\s+/', ' ', $data_grado_name);
        $data_grupo_estable_name = (!empty($arr_value[$offset_ge])) ? $arr_value[$offset_ge]:null ; 
        $estudiant_id = null; 
        $ci_estudiant = null;
        $estudiant_name = null;
        $grado_id = null;

        $estudiant = Estudiant::where('ci_estudiant',$data_ci_estudiant)->first();
        if (empty($estudiant)) {
            $arr_string = [ 'search'=>$data_estudiant_name];
            $estudiant = Estudiant::name($arr_string)->active('true')->first();
        }
        if ($estudiant) {
            $estudiant_id = $estudiant->id;
            $ci_estudiant = $estudiant->ci_estudiant;
            $estudiant_name = $estudiant->fullname;
        }
        else {
            $error->put('ci_estudiant',$data_ci_estudiant);
            $error->put('code','CDE');
            $error->put('messenge','CEDULA NO ENCONTRADA');
            $error->put('class','danger');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect(); 
            $ci_estudiant = '['.$data_ci_estudiant.']';
            $estudiant_name = '['.$data_estudiant_name.']';            
        }

        $grado = Grado::where('description',$data_grado_name)->first();
        if ($grado) {
            $data_grado_name = $grado->name;
            $grado_id = $grado->id;
        }
        else {
            $error->put('data_grado_name',$data_grado_name);
            $error->put('code','NGNE');
            $error->put('messenge','NIVEL/GRADO NO ENCONTRADO');
            $error->put('class','warning');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect(); 
            $data_grado_name = '['.$data_grado_name.']';
            $grado_id = NULL;
            // dd($errors);
        }

        $inscripcion = Inscripcion::where('estudiant_id',$estudiant_id)->first();
        if ($inscripcion && $estudiant) {
            $error->put('code','INSC');
            $error->put('messenge','Inscripción Académica registrada');
            $error->put('class','success');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect(); 
        }

        $preinscripcion = Preinscripcion::where('estudiant_id',$estudiant_id)->first();
        if ($estudiant && $preinscripcion) {
            $error->put('code','PINS');
            $error->put('messenge','Con Preinscripción registrada');
            $error->put('class','info');
            $error->put('index',$index);
            $errors->push($error);
            $error = collect(); 
        }

        $grupo_estable_arr = ['CREACION Y ARTE'=>2,'MUSICA Y CANTO'=>2,'GASTRONOMIA'=>141,'GASTRONOMIA'=>141,'INFORMATICA (DISPONIBLE SOLO DE 4TO Y 5TO AÑO)'=>144,'PRIMEROS AUXILIOS'=>20];
        $grupo_estable_name = ($data_grupo_estable_name) ? $data_grupo_estable_name : null;
        $grupo_estable_id = (array_key_exists($data_grupo_estable_name, $grupo_estable_arr)) ? $grupo_estable_arr[$data_grupo_estable_name] : null ;

        
        $data->put('index',$index);
        $data->put('marcaTemporal',$marcaTemporal);
        $data->put('fecha',$fecha);
        $data->put('representant_ci',$representant_ci);
        $data->put('representant_name',$representant_name);
        $data->put('representant_email_1',$representant_email_1);
        $data->put('representant_phones',$representant_phones);
        $data->put('representant_email_2',$representant_email_2);
        $data->put('comments',$comments);

        $data->put('estudiant_id',$estudiant_id);
        $data->put('ci_estudiant',$ci_estudiant);
        $data->put('estudiant_name',$estudiant_name);
        $data->put('data_grado_name',$data_grado_name);
        $data->put('grupo_estable_id',$grupo_estable_id);
        $data->put('grupo_estable_name',$grupo_estable_name);
        $data->put('grado_id',$grado_id);
        $data->put('errors',$errors);        
        
        $datas->put('datas',$data);
        $datas->put('errors',$errors);
        $datas->put('rows_ok',(count($errors)>0) ? 0:1 );
        $datas->put('count_errors',count($errors));

        // return $datas->toArray();
        return $datas;

    }
}
