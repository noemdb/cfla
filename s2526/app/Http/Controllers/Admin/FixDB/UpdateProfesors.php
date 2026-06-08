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
use App\Models\app\Pescolar\Profesor;

trait UpdateProfesors {

    public static function user_update_email_profesors_email()
    {
        $profesors = Profesor::all();
        $data = collect();
        foreach ($profesors as $profesor) {
            if ($profesor->email) {
                $unique = User::where('email',$profesor->email)->first();
                if (empty($unique)) {
                    $user = $profesor->user;
                    if ($user) {
                        $user->email = $profesor->email;
                        $user->save();
                        $data->push($user);
                    }
                }
            }
        }
        dd($data);
    }

    public static function update_card_number_profesors()
    {
        $profesors = Profesor::all();
        $data = collect();
        foreach ($profesors as $profesor) {
            $user = $profesor->user;
            if ($user) {
                $profile = $user->profile;
                if ($profile) {
                    $profile->update(['card_number'=>$profesor->ci_profesor]);
                    $data->push($profile);
                }
            }
        }
        dd($data);
    }


    public static function user_update_email_profesors()
    {
        $datas = collect();

        $file = "updateGSEmail";
        $folder = "profesors";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $arr_csv) {
            $ci_profesor = (array_key_exists('ci_profesor', $arr_csv)) ? $arr_csv['ci_profesor'] : null ;
            $profesor = Profesor::where('ci_profesor',$ci_profesor)->first();
            if ($profesor) {
                $gsemail = (array_key_exists('gsemail', $arr_csv)) ? $arr_csv['gsemail'] : null ;
                if ($gsemail) {
                    $user = $profesor->user;
                    if ($user) {
                        $arr = ['email'=>$gsemail]; //dd($arr);
                        $user->fill($arr);
                        $user->save();
                        $datas->push($user);
                    }
                }
            }
        }

        dd($csvFile,$arr_data,$datas);

    }

    public static function update_gemail_profesors_ci()
    {
        $datas = collect();

        $file = "updateGSEmailCI";
        $folder = "profesors";
        $csvFile = public_path().'/csv/2324/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $profesor_csv) {
            $ci_profesor = isset($profesor_csv['ci_profesor']) ? $profesor_csv['ci_profesor'] : null ;
            $profesor = Profesor::where('ci_profesor',$ci_profesor)->first(); //dd($profesor_csv,$ci_profesor,$profesor);
            if ($profesor) {
                $gsemail = $profesor_csv['gsemail'];
                if (! empty($gsemail)) {
                    $unique = Profesor::where('gsemail',$gsemail)->first();
                    if (empty($unique)) {
                        $arr = ['gsemail'=>$profesor_csv['gsemail']]; //dd($arr);
                        $profesor->fill($arr);
                        $profesor->save();
                        $datas->push($profesor);
                    } else {
                        $unique->gsemail = null;
                        $unique->save();
                        $profesor->gsemail = $gsemail;
                        $profesor->save();
                    }
                    
                }
            }
        }

        dd($csvFile,$arr_data,$datas);

        // print_r($users);
    }

    public static function update_gemail_profesors()
    {
        $datas = collect();

        $file = "updateGSEmail";
        $folder = "profesors";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $profesor_csv) {
            $ci_profesor = isset($profesor_csv['ci_profesor']) ? $profesor_csv['ci_profesor'] : null ;
            $profesor = Profesor::where('ci_profesor',$ci_profesor)->first(); //dd($profesor_csv,$ci_profesor,$profesor);
            if ($profesor) {
                if (isset($profesor_csv['gsemail'])) {
                    $arr = ['gsemail'=>$profesor_csv['gsemail']]; //dd($arr);
                    $profesor->fill($arr);
                    $profesor->save();
                    $datas->push($profesor);
                }
            }
        }

        dd($csvFile,$arr_data,$datas);

        // print_r($users);
    }

    public static function users_profesors()
    {
        //id;user_id;username;ci_profesor;lastname;name;gsemail

        $datas = collect();

        $file = "updateGSEmail";
        $folder = "profesors";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $profesor_csv) {

            $ci_profesor = isset($profesor_csv['ci_profesor']) ? $profesor_csv['ci_profesor'] : null ;
            $profesor = Profesor::where('ci_profesor',$ci_profesor)->first(); //dd($profesor_csv,$ci_profesor,$profesor);

            if ($profesor) {

                $gsemail = (isset($profesor_csv['gsemail'])) ? $profesor_csv['gsemail'] : null ;

                if ($gsemail) {

                    $username = isset($profesor_csv['username']) ? $profesor_csv['username'] : null ;
                    $user = User::where('username',$username)->first();

                    if ($user) {
                        $user->fill(['email'=>$gsemail]); $user->save();
                        $profesor->fill(['user_id'=>$user->id]); $profesor->save();

                        $datas->push($user);
                    }
                }
            }
        }

        dd('FilePacth:',$csvFile,'array_csv:',$arr_data,'users:',$datas);

        // print_r($users);
    }
}
