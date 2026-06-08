<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Estudiant;
use App\Models\app\Estudiant\Representant;
use App\Models\app\Pescolar\Profesor;
use App\Models\sys\Rol;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait UpdateUsers {

    public static function update_work_id_users()
    {
        $datas = collect();

        $file = "updateWorkId";
        $folder = "2122/profesors";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv'; //dd($csvFile);
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);

        foreach ($arr_data as $k => $row) {
            $work_id = $row['work_id'];
            $user_work = User::where('work_id',$work_id)->first();
            $ident = $row['ident'];
            $user_ident = User::where('ident',$ident)->first();
            $number_id = $row['ci_profesor'];
            $user_number = User::where('ident',$ident)->first();
            if (empty($user_work) && empty($user_ident) && empty($user_number)) {
                $profesor = Profesor::where('ci_profesor',$row['ci_profesor'])->first();
                if ($profesor) {
                    if ($work_id) {
                        $user = $profesor->user;
                        if ($user) {
                            $arr = ['work_id'=>$work_id,'ident'=>$ident,'number_id'=>$profesor->ci_profesor]; //dd($arr);
                            $user->fill($arr);
                            $user->save();
                            $datas->push($user);
                        }
                    }
                } else {
                    $name = $row['name'];
                    $arr_name = explode(" ", $name);
                    $firstName = (array_key_exists(0,$arr_name)) ? $arr_name[0]: null ;
                    $lastName = (array_key_exists(1,$arr_name)) ? $arr_name[1]: null ;
                    $lastName = (array_key_exists(2,$arr_name)) ? $lastName.' '.$arr_name[2]: $lastName ;
                    $lastName = (array_key_exists(3,$arr_name)) ? $lastName.' '.$arr_name[3]: $lastName ;
                    $lastName = (array_key_exists(4,$arr_name)) ? $lastName.' '.$arr_name[4]: $lastName ;
                    $str_ci = rand(100,1000);

                    $username = replace_tilde(strtolower(str_replace(' ','.',$name).$str_ci));
                    $email = $username.'@saefl.test' ;

                    $id = DB::table('users')->insertGetId([
                        'username' => $username,
                        'password' => bcrypt($username),
                        'email' => $email,
                        'work_id'=>$work_id,
                        'ident'=>$ident,
                        'number_id'=>$number_id,
                        'is_active' => 'enable',
                        'created_at'=> Carbon::now(),
                        'remember_token' => Str::random(10),
                    ]);
                    DB::table('profiles')->insert([
                        'firstname' => $firstName,
                        'lastname' => $lastName,
                        'url_img' => "images/avatar/user_default.png",
                        'user_id' => $id,
                        'created_at' => Carbon::now(),
                    ]);
                    DB::table('rols')->insert([
                        'area' => "ADMINISTRACION",
                        'rol' => "PERSONAL",
                        'descripcion' => "Personal de la institución",
                        'finicial' => Carbon::now()->year."0101",
                        'ffinal' => Carbon::now()->addYear(1)->year."0931",
                        'user_id' => $id,
                        'created_at' => Carbon::now(),
                    ]);
                    $user = User::where('id',$id)->first();
                    $datas->push($user);

                }
            }
        }

        dd($arr_data,$datas);

        // print_r($users);
        //"/home/nuser/code/s2122/public/csv/2122/estudiants/updateGSEmail.csv"
        //"/home/nuser/code/s2122/public/csv/2122/estudiants/updateGSEmail.csv
    }


    public static function update_assit_schedule_id_rol()
    {
        $datas = collect();
        $assit_schedule = AssitSchedule::first();
        if ($assit_schedule) {
            $rols = Rol::select('rols.*')
            ->join('users', 'users.id', '=', 'rols.user_id')
            ->whereNotNull('users.work_id')
            ->get(); //dd($rols);
            foreach ($rols as $rol) {
                $arr = ['assit_schedule_id'=>$assit_schedule->id]; //dd($arr);
                $rol->update($arr);
                $datas->push($rol);
            }
        }

        dd($assit_schedule,$datas);
        return $datas;

    }

    public static function update_user_to_number_id()
    {
        $datas = collect();

        $users = User::all();

        foreach ($users as $user) {
            $profile = $user->profile;
            if ($profile) {
                $ci = $profile->card_number;
                if ($ci) {
                    $number_id = $user->number_id;
                    if (empty($number_id)) {
                        $user->number_id = $number_id;
                        $user->save();
                        $datas->push($user);
                    }
                }
            }
        }

        dd($datas);
    }

}
