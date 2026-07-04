<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\Control;

use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\HistoricoNota;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait FixProfesor {

    public function create_users_profesors(Request $request)
    {
        $profesors = Profesor::select('profesors.*')
        ->leftjoin('users', 'users.id', '=', 'profesors.user_id')
        ->whereNull('users.id')
        ->get(); //dd($profesors);
        $datas = collect();       

        foreach ($profesors as $profesor) {
            
            $data = collect();
            $arr_name = explode(" ", $profesor->name);
            $arr_lastname = explode(" ", $profesor->lastname);
            $str_ci = substr($profesor->ci_profesor,-2,2);

            $user = strtolower($arr_name[0][0].$arr_lastname[0].$str_ci);
            $email = $user.'@saefl.test' ;
            $profesor_id = $profesor->id;
            $password = bcrypt($user);

            $item = User::where('username',$user)->first();

            $arr = [
                'username' => $user,
                'password' => $password,
                'email' => $email,
                'is_active' => 'enable',
                'created_at'=> Carbon::now(),
                'remember_token' => Str::random(60),
            ];

            if ($item) {
                $item->fill($arr);
                $item->save();
                $user_id=$item->id;
            } else {
                $user_id = DB::table('users')->insertGetId($arr);
            }
            $data->put('users',$arr);

            $arr = [
                'firstname' => $profesor->name,
                'lastname' => $profesor->lastname,
                'url_img' => "images/avatar/user_default.png",
                'user_id' => $user_id,
                'created_at' => Carbon::now(),
            ];
            $item = Profile::where('user_id',$user_id)->first();
            if ($item) {
                $item->fill($arr);
                $item->save();
            } else {
                DB::table('profiles')->insert($arr);
            }            
            $data->put('profiles',$arr);

            $firstDay = Carbon::now()->firstOfYear();
            $finicial = $firstDay->format('Y-m-d');
            $endDay = $firstDay->addYear()->endOfYear();
            $ffinal = $endDay->format('Y-m-d'); //dd($finicial,$ffinal);
            
            $arr = [
                'area' => "PROFESORADO",
                'rol' => "PROFESOR",
                'descripcion' => "Profesor de la institución",
                'finicial' => $finicial,
                'ffinal' => $ffinal,
                'user_id' => $user_id,
                'created_at' => Carbon::now(),
            ];
            $item = Rol::where('user_id',$user_id)->first();
            if ($item) {
                $item->fill($arr);
                $item->save();
            } else {
                DB::table('rols')->insert($arr);
            } 
            $data->put('rols',$arr);

            $update = Profesor::where('id',$profesor->id)->update(['user_id'=>$user_id]);
            $data->put('profesor',$profesor);
            $datas->push($data);
        }
        dd($datas);
    }

    public function fix_profesor_delete(Request $request)
    {
        $profesors = Profesor::withTrashed()->get();
        // dd($profesors);
        $datas = collect([]);
        foreach ($profesors as $profesor) {

            if ( !empty($profesor->deleted_at) ) {

                if ( !empty($profesor->pevaluacions->count()) )
                {
                    $profesor->fill(['deleted_at'=>null,'status_active'=>'false']);
                    $profesor->save();
                    $datas->push($profesor);
                }
                else
                {
                    $profesor->fill(['status_active'=>'false']);
                    $profesor->save();
                }

                DB::commit();

            }

        }

        dd($datas);
    }

    public function fix_pass_users_profesors(Request $request)
    {
        $profesors = Profesor::active('true')->get();

        foreach ($profesors as $profesor) {

            $arr_name = explode(" ", $profesor->name);
            $arr_lastname = explode(" ", $profesor->lastname);
            $str_ci = substr($profesor->ci_profesor,-2,2);

            $user = strtolower($arr_name[0][0].$arr_lastname[0].$str_ci);

            $password = bcrypt($user);

            $update = User::where('id',$profesor->user_id)->update(['password'=>$password]);

            DB::commit();
        }
    }
}
