<?php

namespace App\Imports;

use App\Models\app\Pescolar;
use DB;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Models\app\Assistcontrol\AssitAttendance;
// use Illuminate\Support\Facades\DB;

class AssitAttendanceImport implements ToCollection, WithCustomCsvSettings
{
    use Importable;

    private $rows = 0;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        return new Collection([
            'id' => $rows[0],
            'eId' => $rows[1],
        ]);
    }

    public function toCollectionGet($filePath = null,$delimiter=',',$input_encoding='UTF-8')
    {
        Config::set('excel.imports.csv.delimiter', $delimiter);
        Config::set('excel.imports.csv.input_encoding', $input_encoding);
        $import_data = $this->toCollection($filePath); //dd($import_data->shift(),$filePath);
        $import_data = $import_data->shift(); //first sheet only
        $pescolar = Pescolar::first();

        // $collections = collect();
        $collections = new Collection();

        if($import_data->count()){

            $header = $import_data->shift(); //dd($header, $import_data);
            $arr = array();
            foreach ($import_data as $k => $v) {
                $work_id = $v[1];
                $date = $v[3];
                $time = $v[4];
                $ident = ($v[0][0]=="'") ? substr($v[0],1) : $v[0] ;
                $date_time = Carbon::parse($date.' '.$time);
                $timestamp = strtotime($date_time);

                $attendance = AssitAttendance::where('timestamp',$timestamp)->first();
                $staus = ($attendance) ? true:false ;

                if ($work_id >= 1 && $date >= $pescolar->date_work) {
                    $arr = [
                        'ident'=> $ident,
                        'work_id'=> $work_id,
                        'card_no'=> $v[2],
                        'date'=> $date,
                        'time'=> $v[4],
                        'timestamp'=> $timestamp,
                        'in_out'=> $v[5],
                        'event_string'=> ($v[6]=='30038') ? 'Huella Dactilar':'Otros' ,
                        'event_code'=> $v[6],
                        'date_time'=> $date_time,
                        'status_registrer'=> $staus,
                    ];
                    $user = User::where('work_id',$work_id)->first();
                    if ($user) {
                        $profile = $user->profile;
                        if ($profile) {
                            $arr ['firstname'] = $profile->firstname;
                            $arr ['lastname'] = $profile->lastname;
                        }
                    }
                    $collections->push( (object) $arr);
                }

            }

        }
        //dd($collections);
        return $collections;

    }


    public function toCollectionStore($filePath = null,$delimiter=',',$input_encoding='UTF-8')
    {
        Config::set('excel.imports.csv.delimiter', $delimiter);
        Config::set('excel.imports.csv.input_encoding', $input_encoding);
        $import_data = $this->toCollection($filePath); //dd($import_data->shift(),$filePath);
        $import_data = $import_data->shift(); //first sheet only
        $pescolar = Pescolar::first();

        // $collections = collect();
        $collections = new Collection();

        if($import_data->count()){

            $header = $import_data->shift(); //dd($header, $import_data);
            $arr = array();
            foreach ($import_data as $k => $v) {
                $work_id = $v[1]; $date = $v[3]; $time = $v[4]; $in_out = $v[5]; $event_code = $v[6];
				$card_id = ($v[2][0]=="'") ? substr($v[2],1) : $v[0] ;
				$ident = ($v[0][0]=="'") ? substr($v[0],1) : $v[0] ;

                $date_time = Carbon::parse($date.' '.$time);
                $timestamp = strtotime($date_time);

				$user = User::where('work_id',$work_id)->first();
				$attendance = AssitAttendance::where('timestamp',$timestamp)->first();

                if (empty($attendance) && $user && $work_id >= 1 && $date >= $pescolar->date_work && isTimestamp($timestamp)) {
					$arr = [
						'user'=>$ident,
						'work_id'=>$work_id,
						'card_id'=>$card_id,
						'date'=>$date,
						'time'=>$time,
						'timestamp'=>$timestamp,
						'in_out'=>$in_out,
						'event_code'=>$event_code
					]; //dd($arr);

                    DB::table('assit_attendances')->insert([$arr]);

                    // if ($work_id==53 && $date=='2021-10-25') dd($k,$v,$arr);

					// $register[] = AssitAttendance::create($arr);

                    $collections->push( (object) $arr);
                }

            }

        }
        //dd($collections);
        return $collections;

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
