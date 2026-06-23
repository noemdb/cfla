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

trait CreateUserDB {

    public static function create_users_representant($take=null)
    {
        $datas = collect();
        $string = 'usuario;contraseña;nombre;ci<br>';

        $representants = Representant::select('representants.*')
        ->active(true)
        ->leftjoin('users', 'users.id', '=', 'representants.user_id')
        ->whereNull('users.id')
        ->orderByRaw('RAND()')
        ->get();

        $representants = ($take) ? $representants->take($take):$representants;
        foreach ($representants as $representant) {
            $data = collect();
            $user_id = $representant->setCreateUserGetId('REPRESENTANTE','REPRESENTANTE');
            $user = User::find($user_id);
            $data->put('username',$user->username);
            $data->put('representant_ci',$representant->ci_representant);
            $data->put('representant_name',$representant->name);
            $datas->push($data);
            $string .= $user->username.';'.$user->username.';'.$representant->name.';'.$representant->ci_representant.'<br>';
        }
        print($string);


    }

    public static function update_users_email_representant()
    {
        $datas = collect();

        $representants = Representant::select('representants.*')
        ->active(true)
        ->leftjoin('users', 'users.id', '=', 'representants.user_id')
        ->whereNotNull('users.id')
        ->orderByRaw('RAND()')
        ->get();

        foreach ($representants as $representant) {
            $user = User::find($representant->user_id);
            if ($user) {
                if ($representant->email) {

                    $unique = User::where('email',$representant->email)->first();
                    if (empty($unique)) {
                        $user->email = $representant->email ;
                        $user->save();
                        $data = collect();
                        $data->put('username',$user->username);
                        $data->put('representant_ci',$representant->ci_representant);
                        $data->put('representant_name',$representant->name);
                        $datas->push($data);
                    }
                }
            }
        }
        print($datas);
    }

    public static function create_users_estudiants($take=null)
    {
        $user = collect();
        $estudiants = Estudiant::select('estudiants.*')
        ->active(true)
        ->leftjoin('users', 'users.id', '=', 'estudiants.user_id')
        ->whereNull('users.id')
        ->orderByRaw('RAND()')
        ->get(); //dd($estudiants);

        $estudiants = ($take) ? $estudiants->take($take) : $estudiants; // dd($estudiants);
        foreach ($estudiants as $estudiant) {
            $user_id = $estudiant->setCreateUserGetId('ESTUDIANTIL','ESTUDIANTE');
            $user->push($estudiant);
        }

        print_r($user);
    }

    public static function create_users_profesors($take=null)
    {
        $profesors = Profesor::select('profesors.*')
        ->active(true)
        ->leftjoin('users', 'users.id', '=', 'profesors.user_id')
        ->whereNull('users.id')
        ->orderByRaw('RAND()')
        ->get(); dd($profesors);

        $users = collect();

        $profesors = ($take) ? $profesors->take($take):$profesors;

        foreach ($profesors as $profesor) {

            $arr_name = explode(" ", $profesor->name);
            $str_ci = rand(100,1000);

            $arr_name = explode(" ", $profesor->name);
            $arr_lastname = explode(" ", $profesor->lastname);

            $firstName = (array_key_exists(0,$arr_name)) ? $arr_name[0]: null ;
            $firstChar = ($firstName) ? $firstName[0]:Str::random(1);
            $lastName = (array_key_exists(0,$arr_lastname)) ? $arr_lastname[0]: Str::random(8) ;

            // $user = strtolower($firstChar.$lastName);
            $user = replace_tilde(strtolower($firstChar.$lastName.$str_ci));
            $email = $user.'@saefl.test' ;

            $password = bcrypt($user);

            $id = DB::table('users')->insertGetId([
                'username' => $user,
                // 'password' => bcrypt($profesor->ci_profesor),
                'password' => $password,
                'email' => $email,
                'is_active' => 'enable',
                'created_at'=> Carbon::now(),
                'remember_token' => Str::random(10),
            ]);
            DB::table('profiles')->insert([
                'firstname' => $profesor->name,
                'lastname' => $profesor->lastname,
                'url_img' => "images/avatar/user_default.png",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            DB::table('rols')->insert([
                'area' => "PROFESORADO",
                'rol' => "PROFESOR",
                'descripcion' => "Profesor de la institución",
                'finicial' => Carbon::now()->year."0101",
                'ffinal' => (Carbon::now()->year+1)."0931",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);

            $users->push($user);

            $update = Profesor::where('id',$profesor->id)->update(['user_id'=>$id]);
            // commit();
        }

        print_r($users);
    }


}
