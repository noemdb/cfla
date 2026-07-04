<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\DB;

use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Census;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\HistoricoNota;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

trait FixDB {

    public function update_fill_phone_representant()
    {
        $datas = collect();
        $representants = DB::table('representants')
        ->select('representants.*','enrollments.phone_representant')
        ->join('enrollments', 'representants.ci_representant', '=', 'enrollments.ci_representant')
        ->leftjoin('catchments', 'representants.ci_representant', '=', 'catchments.representant_ci')
        ->leftjoin('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->get();

        foreach ($representants as $item) {
            $representant = Representant::find($item->id);
            $representant->phone_old = $representant->phone;
            $representant->phone = $item->phone_representant;
            $representant->whatsapp = $item->phone_representant;
            $representant->save();
            //if($representant->ci_representant == '14443713') dd($representant);

            $datas->push($representant);
        }
        dd($datas->take(10));
        // representant_phone
    }

    public static function importCatchment()
    {
        $datas = collect();
        $catchments = Catchment::select('catchments.*')
        ->join('catchment_interviews', 'catchments.id', '=', 'catchment_interviews.catchment_id')
        ->where('catchment_interviews.accepted',true)
        //->where('catchment_interviews.identification_number','7593106')
        ->get(); //dd($catchments);

        foreach ($catchments as $item) {
            $data = collect();
            $representant = Representant::where('ci_representant',$item->representant_ci)->first();
            if (! $representant) {
                $arr = [
                    'ci_representant'=>$item->representant_ci,
                    'name'=>$item->representant_lastname.' '.$item->representant_name,
                    'phone'=>$item->representant_phone,
                    'cellphone'=>$item->representant_phone,
                    'email'=>$item->email,
                ];
                $representant = Representant::create($arr);
                $data->put('representant',$representant);
            }
            $user_id = $representant->setCreateUserGetId();

            $estudiant = Estudiant::where('lastname',$item->lastname)->where('name',$item->firstname)->where('representant_id',$representant->id)->first();
            if (!$estudiant) {
                $ci_estudiant = Carbon::now()->timestamp;
                $estudiant = Estudiant::create([
                    'planpago_id'=>1,//ninguno
                    'ci_estudiant'=> $ci_estudiant,
                    'lastname'=>$item->lastname,
                    'name'=>$item->firstname,
                    'gender'=>$item->gender,
                    'date_birth'=>$item->date_birth,
                    'country_birth'=>$item->country_foreign,
                    'dir_address'=>$item->direction,
                    'representant_id'=>$representant->id,
                    'status_active'=>'true',
                    ]
                );
                $data->put('estudiant',$estudiant);
            }
            $user_id = $estudiant->setCreateUserGetId();

            $grado = Grado::find($item->grade);
            if ($grado) {
                $seccion = Seccion::where('grado_id',$grado->id)->where('name','U')->first();
                if ($seccion) {
                    $inscripcion = Inscripcion::where('estudiant_id',$estudiant->id)->first();
                    if (! $inscripcion) {
                        $inscripcion = Inscripcion::create([
                            'tipo_id'=>1,
                            'seccion_id'=>$seccion->id,
                            'estudiant_id'=>$estudiant->id,
                            'escolaridad_id'=>1,
                            'programacion_id'=>1,
                            'grupo_estable_id'=>null,
                            'observations'=>'Nuevo ingreso, asignado a la sección U',
                        ]);
                        $data->put('inscripcion',$inscripcion);
                    }

                    if ($inscripcion) {
                        $prosecucion = Prosecucion::where('estudiant_id',$inscripcion->estudiant_id)->first();
                        if (!$prosecucion) {
                            $prosecucion = Prosecucion::create([
                                'seccion_id'=>$seccion->id,
                                'estudiant_id'=>$estudiant->id,
                                'observations'=>'Nuevo ingreso, asignado a la sección U',
                            ]);
                        }
                    }
                }
            }
            if (!$data->empty()) {
                $datas->push($data); //dd($datas);
            }
        }
        //dd($datas);
    }


    public function catchments_fill()
    {
        $catchments = DB::connection('s2324')
        ->table('catchments')
        ->select('catchments.*')
        ->get();

        foreach ($catchments as $item) {
            $arr = (array) $item;
            unset($arr['id']);
            $catchment = New Catchment();
            $catchment->fill($arr);
            $catchment->save();

            $interview = DB::connection('s2324')
            ->table('catchment_interviews')
            ->select('catchment_interviews.*')
            ->where('catchment_interviews.catchment_id',$item->id)
            ->first();
            $arr = (array) $interview;
            $arr['catchment_id']=$catchment->id;
            $interview = New CatchmentInterview();
            $interview->fill($arr);
            $interview->save();
        }

        $interviews = DB::connection('s2324')
        ->table('catchment_interviews')
        ->select('catchment_interviews.*')
        ->whereNull('catchment_interviews.catchment_id')
        ->get();
        foreach ($interviews as $item) {
            $arr = (array) $item;
            $interview = New CatchmentInterview();
            $interview->fill($arr);
            $interview->save();
        }
    }

    public function toadmit_regular()
    {
        $arr_id = [1124,1125,1127,1128,1149,1152,1154,1176,1183,1184,1188,1192,1197,1199,1211,1214,1215,1216,1219,1220,1222,1227,1228,1230,1232,1233,1234,1246,1248,1260,1303,1321,1147,1153,1155,1156,1157,1159,1182,1189,1191,1194,1195,1196,1198,1200,1204,1205,1206,1207,1208,1209,1212,1217,1221,1223,1225,1226,1231,1237,1238,1239,1241,1252,1264,1286,1304,1126,1146,1160,1175,1181,1185,1186,1187,1190,1193,1201,1202,1203,1210,1213,1218,1224,1229,1235,1236,1240,1242,1247,1249,1250,1251,1253,1261,1268,1269,1270,1271,1277,1308,1319,1129,1048,1018,1079,1032,1051,1139,1148,1020,1172,1245,1056,1254,1013,1019,1039,1033,1059,1057,1037,1099,1038,1272,1091,1028,1090,1081,1083,1285,1288,1055,1054,1309,1316,1317,1025,1040,1014,1017,1053,1015,1029,1041,1043,1060,1061,1016,1047,1158,1046,1026,1035,1255,1085,1089,1044,1062,1031,1030,1049,1265,1082,1084,1034,1273,1042,1093,1052,1011,1050,1021,1036,921,986,925,937,952,963,926,1117,960,962,961,964,993,939,1101,994,1064,1110,924,947,972,914,1063,915,1004,954,918,997,923,917,996,949,946,985,928,932,948,1298,1112,1130,992,953,958,930,1065,981,999,959,1003,944,942,1141,933,934,922,1094,1102,951,927,916,955,931,936,1119,957,1121,1092,956,950,929,919,943,1301,940,938,1313,1132,1135,1136,864,824,801,832,871,872,873,828,829,833,816,813,812,806,1106,847,1244,908,879,803,819,987,820,848,885,826,875,850,1289,1290,852,971,1315,1133,1134,975,886,887,839,811,868,815,973,878,840,836,863,851,870,831,823,817,805,845,979,822,843,988,974,1280,1281,1108,1118,970,978,809,889,865,876,1096,12,15,1122,856,26,54,62,13,1140,1150,24,4,8,1005,20,34,1072,18,47,33,1007,966,857,5,59,49,976,40,28,58,3,854,38,31,2,21,1116,46,1000,10,16,37,39,53,66,1145,1086,51,1167,29,64,65,41,32,14,36,30,980,63,22,23,1,897,27,60,1284,56,1296,7,44,858,57,1097,893,77,91,104,112,1070,137,140,87,93,99,100,105,123,1137,131,862,861,76,94,119,135,80,132,102,95,96,67,121,124,90,89,114,92,101,139,1282,78,83,1294,98,1103,97,107,108,122,125,126,128,1068,1104,72,71,1256,1257,69,1069,894,136,86,70,116,860,115,79,106,895,141,103,110,88,881,1123,129,142,133,75,166,201,143,149,159,180,193,1138,1142,1151,185,167,178,150,164,209,191,161,1107,187,199,1067,189,212,1267,1276,192,145,158,204,182,1291,1302,1306,1310,173,148,171,203,207,1111,156,165,188,1143,147,1114,196,1166,1171,183,184,1258,154,194,190,176,900,899,967,1120,1274,198,170,160,1295,1297,1300,146,1312,1314,1098,224,263,251,255,260,269,1243,214,969,279,248,217,258,1262,276,220,977,267,229,236,221,1045,901,243,249,245,278,281,235,234,266,240,215,262,222,1318,1144,968,219,230,247,250,264,265,277,1074,242,1180,239,218,225,273,1076,246,259,227,252,228,216,244,280,231,232,271,1075,1105,882,989,282,257,241,226,283,903,334,335,352,326,342,320,991,298,299,318,355,301,313,323,311,888,305,319,1113,289,1275,324,286,292,330,331,1283,306,1292,1305,1307,304,341,333,285,291,300,354,1164,308,1115,1077,343,314,321,328,329,344,1168,1170,1174,1177,339,990,288,892,904,312,353,890,340,303,310,351,302,337,1278,1279,309,295,327,1293,296,338,307,1320,1163,413,419,426,364,380,383,906,397,416,1178,1179,395,1088,371,393,366,382,403,369,410,381,356,400,402,428,363,412,424,376,431,429,417,372,404,360,1162,1165,1087,396,405,375,379,386,430,1169,1173,392,374,394,368,373,367,414,415,1263,423,407,427,411,361,357,384,425,902,370,421,420,391,406,1311,390];
        $datas = collect();
        $arr_administrativas=Array();
        $arr_inscripcions=Array();
        foreach ($arr_id as $k=>$id) {
            $data = collect();
            $estudiant = Estudiant::where('id',$id)->first();
            if ($estudiant) {
                if (empty($estudiant->administrativa)) {
                    $arr = [
                        'estudiant_id'=> $estudiant->id ,
                        'user_id'=> Auth::id() ,
                        'planpago_id'=> 1,
                    ];
                    $administrativa = Administrativa::create($arr);
                    $data->put('administrativa',$arr);
                    $arr_administrativas[]=$arr;
                }

                if (empty($estudiant->inscripcion)) {
                    $seccion = Seccion::where('name','U')->first();
                    $arr = [
                        'tipo_id'=> 1 ,
                        'seccion_id'=> $seccion->id,
                        'estudiant_id'=> $estudiant->id,
                        'escolaridad_id'=> 1,
                        'programacion_id'=> 1,
                    ];
                    $inscripcion = Inscripcion::create($arr);
                    $data->put('inscripcion',$arr);
                    $arr_inscripcions[]=$arr;
                }

                if ($data->isNotEmpty()) {
                    $datas->push($data);
                }
            }
        }
        dd($datas,$arr_administrativas,$arr_inscripcions);
    }

    public function fill_student_record()
    {
        $enrollments = Enrollment::all();
        $datas = collect();
        $estudiants_no = collect();

        foreach ($enrollments as $enrollment) {
            $estudiant = Estudiant::where('ci_estudiant',$enrollment->ci_estudiant)->first();
            if ($estudiant) {

                $item = Enrollment::find($enrollment->id);
                $item->estudiant_id = $estudiant->id;
                $item->save();

                $data = collect();
                $arr = $item->toArray();

                $student_record = StudentRecord::where('estudiant_id',$estudiant->id)->first();
                if ($student_record) {
                    $student_record->fill($arr); $student_record->save(); DB::commit(); //dd($student_record);
                    $data->put('student_record',$student_record);
                    $datas->push($data);
                } else {
                    $student_record = StudentRecord::create($arr); DB::commit(); //dd($student_record);
                    $data->put('student_record',$student_record);
                    $datas->push($data);
                }
            }
        }
        dd($datas,$estudiants_no);
    }

    public function fill_enrollments_estudiant_id()
    {
        $enrollments = Enrollment::all();
        $datas = collect();
        $estudiants_no = collect();
        foreach ($enrollments as $enrollment) {
            $data = collect();
            $estudiant = Estudiant::where('ci_estudiant',$enrollment->ci_estudiant)->first();
            if ($estudiant) {
                $data = collect();
                $item = Enrollment::find($enrollment->id);
                $item->estudiant_id = $estudiant->id;
                $item->save();
                $data->put('estudiant',$estudiant);
                $data->put('enrollment',$enrollment);
                $datas->push($data);
            } else {
                $estudiants_no->push($enrollment);
            }
        }
        dd($datas,$estudiants_no);
    }

    public function fill_estudiants_enrollments()
    {
        $enrollments = Enrollment::all();
        $datas = collect();
        $estudiants_no = collect();
        foreach ($enrollments as $enrollment) {
            $data = collect();
            $estudiant = Estudiant::where('ci_estudiant',$enrollment->ci_estudiant)->first();
            if ($estudiant) {
                $data = collect();
                $item = $estudiant = Estudiant::find($estudiant->id);

                // if (! $estudiant->gender) {
                //     $item = $estudiant = Estudiant::find($estudiant->id);
                //     $item->gender = $enrollment->gender;
                //     $item->update();
                //     dd($item,$estudiant,$enrollment);
                // }

                $item->gender = (empty($estudiant->gender)) ? $enrollment->gender : $estudiant->gender ;
                $item->date_birth = (empty($estudiant->date_birth)) ? $enrollment->date_birth : $estudiant->date_birth ;
                $item->city_birth = (empty($estudiant->city_birth)) ? $enrollment->city_birth : $estudiant->city_birth ;
                $item->town_hall_birth = (empty($estudiant->town_hall_birth)) ? $enrollment->town_hall_birth : $estudiant->town_hall_birth ;
                $item->state_birth = (empty($estudiant->state_birth)) ? $enrollment->state_birth : $estudiant->state_birth ;
                $item->country_birth = (empty($estudiant->country_birth)) ? $enrollment->country_birth : $estudiant->country_birth ;
                $item->dir_address = (empty($estudiant->dir_address)) ? $enrollment->dir_address : $estudiant->dir_address ;
                $item->save();

                $data->put('item',$item);
                $data->put('estudiant',$estudiant);
                $data->put('enrollment',$enrollment);
                $datas->push($data);
            } else {
                $estudiants_no->push($enrollment);
            }
        }
        dd($datas,$estudiants_no);
    }






//////////////////////////////////////////////////////////////////////////////

    public function fill_enrollments()
    {
        // $file = "enrollmentsJ";
        $file = "enrollments";
        $folder = "2425";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        $ci_representant_create = Array();
        $ci_estudiant_create = Array();
        $ci_estudiant_exist = Array();
        $inscripcion = Array();
        $administrativa = Array();

        $data = collect();
        $datas = collect();

        foreach ($arr_data as $k => $v) {

            $representant = Representant::where('ci_representant',$v['ci_representant'])->first(); //dd($representant);

            if (empty($representant)) {
                $arr = [
                    'ci_representant'=> $v['ci_representant'] ,
                    'name'=> $v['name_representant'],
                    'phone'=> $v['phone_representant'],
                    'email'=>  $v['email_representant'],
                    'status_active'=>  'true',
                    'status_blacklist'=>  'false',
                    'status_adviders'=>  'false',
                ]; // dd($arr);
                $representant = Representant::create($arr);
                $data->put('representant',$representant);
                $ci_representant_create[] = $v['ci_representant'];
            }

            $estudiant = Estudiant::where('ci_estudiant',$v['ci_estudiant'])->first();
            if (empty($estudiant)) {
                $arr = [
                    'planpago_id'=> 1 ,
                    'type_ci_id'=> 1 ,
                    'ci_estudiant'=> $v['ci_estudiant'] ,
                    'name'=> $v['name'] ,
                    'lastname'=> $v['lastname'] ,
                    'representant_id'=> $representant->id,
                    'representant_ci'=> $representant->ci_representant,
                    'gender'=> $v['gender'] ,
                    'date_birth'=> $v['date_birth'] ,
                    'city_birth'=> $v['town_hall_birth'] ,
                    'state_birth'=> $v['state_birth'] ,
                    'country_birth'=> $v['country_birth'] ,
                    'dir_address'=> $v['dir_address'] ,
                    'status_active'=>  'true',
                    'status_blacklist'=>  $representant->status_blacklist,
                ];
                $estudiant = Estudiant::create($arr);
                $data->put('estudiant',$arr);
                $ci_estudiant_create[] = $v['ci_estudiant'];
            } else {
                $arr = [
                    'gender'=> $v['gender'] ,
                    'date_birth'=> $v['date_birth'] ,
                    'city_birth'=> $v['town_hall_birth'] ,
                    'state_birth'=> $v['state_birth'] ,
                    'country_birth'=> $v['country_birth'] ,
                    'dir_address'=> $v['dir_address'] ,
                    'status_active'=>  'true'
                ];
                 $estudiant->fill($arr);
                 $estudiant->save();
            }

            if (empty($estudiant->administrativa)) {
                $arr = [
                    'estudiant_id'=> $estudiant->id ,
                    'user_id'=> 1 ,
                    'planpago_id'=> 1,
                ];
                $administrativa = Administrativa::create($arr);
                $data->put('administrativa',$arr);
            }

            if (empty($estudiant->inscripcion)) {
                $seccion = Seccion::where('grado_id',$v['grado_id'])->where('name','U')->first(); //dd($seccion);
                $arr = [
                    'tipo_id'=> 1 ,
                    'seccion_id'=> $seccion->id,
                    'estudiant_id'=> $estudiant->id,
                    'escolaridad_id'=> 1,
                    'programacion_id'=> 1,
                ];
                $inscripcion = Inscripcion::create($arr);
                $data->put('inscripcion',$arr);
            }

            $enrollments = Enrollment::where('ci_estudiant',$v['ci_estudiant'])->first();
            if (empty($enrollments)) {
                $enrollment = Enrollment::create($v);
                $data->put('enrollment',$v);
            }

            $datas->push($data); //dd($datas,$data);

        }
        dd($datas,$ci_representant_create,$ci_estudiant_create,$inscripcion,$administrativa);
    }

    public function fillAbono()
    {
        $file = "abonos";
        $folder = "2425";
        // $folder = "2021";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        $data = collect();
        $datas = collect();

        foreach ($arr_data as $k => $v) {
            $ingreso = Ingreso::where('number_i_pay',$v['number_i_pay'])->first(); //dd($ingreso,$v['number_i_pay']);
            if (empty($ingreso)) {
                $representant = Representant::where('id',$v['representant_id'])->first();
                if ($representant) {
                    $estudiant = $representant->estudiants->first();
                    $arr = [
                        'representant_id'=>$representant->id,
                        'estudiant_id'=>$estudiant->id,
                        'method_pay_id' => $v['method_pay_id'],
                        'banco_id' => $v['banco_id'],
                        'number_i_pay' => $v['number_i_pay'],
                        'date_transaction' => $v['date_transaction'],
                        'date_payment' => $v['date_payment'],
                        'ingreso_ammount' => $v['ingreso_ammount'],
                        'exchange_ammount' => $v['exchange_ammount'],
                        'ingreso_observations' => $v['ingreso_observations'],
                        'person_bill_ci' => $representant->ci_representant,
                        'person_bill_name' =>$representant->name,
                    ];

                    $ingreso =  Ingreso::create($arr);
                    $data->put('arr_ingreso',$arr);

                    $arr = [
                        'representant_id' => $estudiant->representant->id,
                        'estudiant_id' => $estudiant->id,
                        'ingreso_id' => $ingreso->id,
                        'abono_description' => $v['ingreso_observations'],
                    ];
                    $abono = Abono::create($arr);

                    // DB::table('abonos')->insert($arr);


                    $data->put('arr_abono',$arr);

                    $datas->push($data);
                }

            }

        }
        dd($datas);

    }

    public function fillCAF()
    {
        $file = "cafs";
        $folder = "2526";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        $data = collect();
        $datas = collect();

        foreach ($arr_data as $k => $v) {

            $representant = Representant::where('ci_representant',$v['representant_ci'])->first();
            $exchange_ammount = $v['exchange_ammount'];
            if ($representant && $v['exchange_ammount'] >= 1) {
                $estudiant = $representant->estudiants->first();

                if ($estudiant) {

                    $arr = [
                        'representant_id'=>$representant->id,
                        'estudiant_id'=>$estudiant->id,
                        'registro_pago_id'=>null,
                        'ingreso_id'=>null,
                        'credito_a_favor_ids'=>null,
                        'credito_description'=>'CAF PERIODO0 ANTERIOR',
                        'credito_observations'=>'CAF PERIODO0 ANTERIOR',
                        'credito_ammount'=> $v['ammount'],
                        'exchange_rate_id'=>null,
                        'exchange_ammount'=> $v['exchange_ammount']
                    ];

                    DB::table('credito_a_favors')->insert($arr);

                    // DB::table('abonos')->insert($arr);

                    $data->put('arr_abono',$arr);

                    $datas->push($data);

                }
            }

        }
        dd($datas);

    }

    public function fillDAA()
    {
        $file = "saldos";
        $folder = "2526";
        $fecha = '2025-07-31';
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);
        $exchange_rate = ExchangeRate::where('date',$fecha)->first(); //dd($arr_data , $exchange_rate);
        $exchange_rate_ammount = ($exchange_rate) ? $exchange_rate->ammount : 5.78 ;
        $planpago_id = 100;

        $data = collect();
        $datas = collect();

        foreach ($arr_data as $k => $v) {
            $representant = Representant::where('ci_representant',$v['representant_ci'])->first();
            $exchange_ammount = floatval($v['concepto_ammount']);
            if ($representant) {
                $estudiant = $representant->estudiants->first();

                if ($estudiant) {

                    $arr_data = [
                        'planpago_id' => $planpago_id,
                        'name' => 'DEUDA PERIODO ANTERIOR',
                        'type' => 'INDIVIDUAL',
                        'estudiant_id' => $estudiant->id,
                        'date_expiration' => '20250731',
                        'description' => 'SALDO PENDIENTE DEL REPRESENTANTE CORRESPONDIENTE AL PERIODO ESCOLAR 2025 2026',
                        'created_at'=> Carbon::now()
                    ];

                    $arr_conditions = [
                        'planpago_id' => $planpago_id,
                        'estudiant_id' => $estudiant->id
                    ];

                    DB::table('cuentaxpagars')->updateOrInsert($arr_conditions,$arr_data);
                    $data->put('cuentaxpagars',$arr_data);

                    $cuentaxpagar = DB::table('cuentaxpagars')->select('cuentaxpagars.*')->where($arr_conditions)->first();

                    if ($cuentaxpagar) {

                        $arr_data = [
                            'cuentaxpagar_id' => $cuentaxpagar->id,
                            'nom_concepto_pago_id' => 3,
                            'concepto_description' => 'SALDO PENDIENTE DEL REPRESENTANTE CORRESPONDIENTE AL PERIODO ESCOLAR 2021 2022',
                            'concepto_observations' => 'CARGA DE DATOS AUTOMATIZADA',
                            'concepto_ammount' => $exchange_rate_ammount * $exchange_ammount,
                            'exchange_ammount' => $exchange_ammount,
                            'status_discount' => 'false',
                            'created_at'=> Carbon::now()
                        ];

                        $arr_conditions = [
                            'cuentaxpagar_id' => $cuentaxpagar->id,
                        ];

                        DB::table('concepto_pagos')->updateOrInsert($arr_conditions,$arr_data);
                        $data->put('concepto_pagos',$arr_data);

                        $concepto_pago = DB::table('concepto_pagos')->select('concepto_pagos.*')->where($arr_conditions)->first();

                        if ($concepto_pago) {
                            $arr_data = [
                                'estudiant_id'=> $estudiant->id,
                                'user_id'=> 1,
                                'planpago_id'=> $planpago_id,
                                'created_at'=> Carbon::now()
                            ];
                            $arr_conditions = [
                                'estudiant_id' => $estudiant->id
                            ];
                            DB::table('administrativas')->updateOrInsert($arr_conditions,$arr_data);
                            $data->put('administrativas',$arr_data);
                        }

                    }

                    $datas->push($data);

                }
            }

        }
        dd($datas);

    }


    public function fillDAAEstudiant()
    {
        $file = "saldosEstudiant";
        $folder = "2526";
        $fecha = '2025-07-31';
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);
        $exchange_rate = ExchangeRate::where('date',$fecha)->first(); //dd($arr_data , $exchange_rate);
        $exchange_rate_ammount = ($exchange_rate) ? $exchange_rate->ammount : 5.78 ;
        $planpago_id = 100;

        $data = collect();
        $datas = collect();

        foreach ($arr_data as $k => $v) {
            $estudiant = Estudiant::where('ci_estudiant',$v['ci_estudiant'])->first();
            $exchange_ammount = floatval($v['concepto_ammount']);
            if ($estudiant) {

                if ($estudiant) {

                    $arr_data = [
                        'planpago_id' => $planpago_id,
                        'name' => 'DEUDA PERIODO ANTERIOR',
                        'type' => 'INDIVIDUAL',
                        'estudiant_id' => $estudiant->id,
                        'date_expiration' => '20250731',
                        'description' => 'SALDO PENDIENTE DEL REPRESENTANTE CORRESPONDIENTE AL PERIODO ESCOLAR 2025 2026',
                        'created_at'=> Carbon::now()
                    ];

                    $arr_conditions = [
                        'planpago_id' => $planpago_id,
                        'estudiant_id' => $estudiant->id
                    ];

                    DB::table('cuentaxpagars')->updateOrInsert($arr_conditions,$arr_data);
                    $data->put('cuentaxpagars',$arr_data);

                    $cuentaxpagar = DB::table('cuentaxpagars')->select('cuentaxpagars.*')->where($arr_conditions)->first();

                    if ($cuentaxpagar) {

                        $arr_data = [
                            'cuentaxpagar_id' => $cuentaxpagar->id,
                            'nom_concepto_pago_id' => 3,
                            'concepto_description' => 'SALDO PENDIENTE DEL REPRESENTANTE CORRESPONDIENTE AL PERIODO ESCOLAR 2021 2022',
                            'concepto_observations' => 'CARGA DE DATOS AUTOMATIZADA',
                            'concepto_ammount' => $exchange_rate_ammount * $exchange_ammount,
                            'exchange_ammount' => $exchange_ammount,
                            'status_discount' => 'false',
                            'created_at'=> Carbon::now()
                        ];

                        $arr_conditions = [
                            'cuentaxpagar_id' => $cuentaxpagar->id,
                        ];

                        DB::table('concepto_pagos')->updateOrInsert($arr_conditions,$arr_data);
                        $data->put('concepto_pagos',$arr_data);

                        $concepto_pago = DB::table('concepto_pagos')->select('concepto_pagos.*')->where($arr_conditions)->first();

                        if ($concepto_pago) {
                            $arr_data = [
                                'estudiant_id'=> $estudiant->id,
                                'user_id'=> 1,
                                'planpago_id'=> $planpago_id,
                                'created_at'=> Carbon::now()
                            ];
                            $arr_conditions = [
                                'estudiant_id' => $estudiant->id
                            ];
                            DB::table('administrativas')->updateOrInsert($arr_conditions,$arr_data);
                            $data->put('administrativas',$arr_data);
                        }

                    }

                    $datas->push($data);

                }
            }

        }
        dd($datas);

    }


    public function db_random()
    {
        Artisan::call('db:mysqldump');

        $estudiants = Estudiant::all();
        $representants = Representant::all();
        $profesors = Profesor::all();

        $faker = Factory::create('es_ES');

        $datas_estudiants = collect([]);
        $datas_representants = collect([]);
        $datas_profesors = collect([]);

        foreach ($estudiants as $estudiant) {
            $name = mb_strtoupper($faker->firstName) . ' ' .mb_strtoupper($faker->firstName);
            $lastname = mb_strtoupper($faker->lastName) . ' ' .mb_strtoupper($faker->lastName);
            $ci_estudiant = mt_rand(6252188, 65422846);
            $gsemail = str_replace(' ', '.', mb_strtolower($name.'.'.$lastname.'.'.$ci_estudiant.'@uefrayluisamigosf.com'));
            $arr = [ 'name'=>$name, 'lastname'=>$lastname, 'ci_estudiant'=>$ci_estudiant, 'gsemail'=>$gsemail];
            $estudiant->fill($arr); $estudiant->save(); DB::commit();
            $datas_estudiants->push($arr);
        }
        foreach ($representants as $representant) {
            $name = mb_strtoupper($faker->lastName) . ' ' .mb_strtoupper($faker->lastName) . ' ' .mb_strtoupper($faker->firstName) . ' ' .mb_strtoupper($faker->firstName);
            $ci_estudiant = mt_rand(6252188, 65422846);
            $arr = [ 'name'=>$name, 'ci_estudiant'=>$ci_estudiant];
            $representant->fill($arr); $representant->save(); DB::commit();
            $datas_representants->push($arr);
        }

        foreach ($profesors as $profesor) {
            $name = mb_strtoupper($faker->firstName) . ' ' .mb_strtoupper($faker->firstName);
            $lastname = mb_strtoupper($faker->lastName) . ' ' .mb_strtoupper($faker->lastName);
            $ci_profesor = mt_rand(6252188, 65422846);
            $arr = [ 'name'=>$name, 'lastname'=>$lastname,'ci_profesor'=>$ci_profesor];
            $profesor->fill($arr); $profesor->save(); DB::commit();
            $datas_profesors->push($arr);
        }

        // dd($datas_estudiants,$datas_representants,$datas_profesors);
    }

    public function db_random_boletin()
    {
        // Artisan::call('db:mysqldump');

        $estudiants = Estudiant::all();

        foreach ($estudiants as $estudiant) {
            $pevaluacions = $estudiant->pevaluacions; //dd($pevaluacions);
            foreach ($pevaluacions as $pevaluacion) {
                $evaluacions = $pevaluacion->evaluacions; //dd($evaluacions);
                foreach ($evaluacions as $evaluacion) {
                    $boletin = Boletin::where('estudiant_id',$estudiant->id)->where('evaluacion_id',$evaluacion->id)->first(); //dd($boletin);
                    if (empty($boletin)) {
                        $inferior = 1;
                        $superior = ($estudiant->status_baremo) ? 5 : 20 ; //dd($inferior,$superior);
                        $create = Boletin::create(
                            [
                                'estudiant_id'=>$estudiant->id,
                                'evaluacion_id'=>$evaluacion->id,
                                'nota'=>rand(10,20)
                            ]
                        );
                    }

                }
            }
        }

        // dd($datas_estudiants,$datas_representants,$datas_profesors);
    }
}
