<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Admin\CreateProfileRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//models
use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\Models\sys\SelectOpt;

class ProfileController extends Controller
{

    /*
        Constructor, verifica login del usuario - Profile
    */
    public function __construct(){

        $this->middleware('auth');

    }

    //usada para el softdelete
 	protected $dates = ['deleted_at'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profiles = Profile::OrderBy('id','DESC')
            // ->username($arr_get)
            ->with('User')
            //->with('rols')
            ->get();

        // dd($profiles);

        return view('admin.profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User;
        $user_list = User::select('users.*')
                ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
                ->whereNull('profiles.user_id')
                ->OrWhere('profiles.deleted_at','<>',NULL)
                // ->whereNotNull('profiles.deleted_at')
                ->orderby('users.username','asc')
                ->pluck('username', 'id');
                // ->prepend('Seleccionar','');

        return view('admin.profiles.create',compact('user','user_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProfileRequest $request)
    {

        $profile = Profile::create($request->all());

        $messenge = trans('db_oper_result.profile_create_ok');
        
        Session::flash('operp_ok',$messenge);

        return redirect()->route('profiles.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profile = Profile::findOrFail($id);

        return view('admin.profiles.show',compact('profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $profile = Profile::findOrFail($id);

        // dd($profile,$user);

        return view('admin.profiles.edit',compact('profile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, $id)
    {
        
        // dd($request);
        
        $profile = Profile::findOrFail($id);
        
        $profile->fill($request->all());

        $profile->save();

        $messenge = trans('db_oper_result.update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('profiles.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {

        $profile = Profile::findOrFail($id);

        $profile->delete();

        $messenge = 'Operación completada correctamente';
        $operation= 'delete';

        if($request->ajax()){

            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);

        }

        // $messenge = 'Operación completada correctamente';
        
        // Session::flash('operp_ok',$messenge);

        return redirect()->route('profile.index');
    }
}
