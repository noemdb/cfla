<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateRepresentants {

    public static function toggle_gemail_email_representants()
    {
        $datas = collect();

        $representants = Representant::all();

        foreach ($representants as $representant) {
            $user = $representant->user;
            if ($user) {
                $email = $representant->email;
                if (validate_email($email)) {
                    $noUnique = (User::where('email',$email)->first()) ? false : true ;
                    if ($noUnique) {
                        $user->email = $email;
                        $user->save();
                        $datas->push($user);
                    }                    
                }                
            }
        }

        dd($datas);

        // print_r($users);
    }

    public static function update_gemail_representants()
    {
        $datas = collect();

        $file = "updateGSEmail";
        $folder = "representants";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $arr_csv) {
            $representant = Representant::where('ci_representant',$arr_csv['ci_representant'])->first();
            if ($representant) {
                $gsemail = $arr_csv['gsemail'];
                if ($gsemail) {
                    $arr = ['gsemail'=>$gsemail]; //dd($arr);
                    $representant->fill($arr);
                    $representant->save();
                    $datas->push($representant);
                }
            }
        }

        dd($arr_data,$datas);

        // print_r($users);
    }

    public static function user_update_email_representants()
    {
        $datas = collect();

        $file = "updateGSEmail";
        $folder = "representants";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $arr_csv) {
            $ci_representant = (array_key_exists('ci_representant', $arr_csv)) ? $arr_csv['ci_representant'] : null ;
            if ($ci_representant) {
                $representant = Representant::where('ci_representant',$ci_representant)->first();
                if ($representant) {
                    $gsemail = (array_key_exists('gsemail', $arr_csv)) ? $arr_csv['gsemail'] : null ;
                    if ($gsemail) {
                        $arr = ['email'=>$gsemail]; //dd($arr);
                        $user = $representant->user;
                        if ($user) {
                            $user->fill($arr);
                            $user->save();
                            $datas->push($user);
                            // dd($user,$arr);
                        }
                    }
                }
            }

        }

        dd($arr_data,$datas);

        // print_r($users);
    }

}
