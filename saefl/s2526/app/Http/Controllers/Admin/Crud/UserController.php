<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

use Redirect,Response,DB,Config;
// use DataTables;
use Yajra\Datatables\Datatables;

//validation request
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

//models
use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;
use App\Models\sys\SelectOpt;

class UserController extends Controller
{
    public function test()
    {
        dd('123');
        return "select";
    }


    /*
        Constructor, verifica login del usuario - Profile
    */
    public function __construct(){
        $this->middleware(['auth','is_admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cruda()
    {
        $users = User::all();
        $datatables = Datatables::collection($users)
            ->addColumn('btn', 'admin.users.table.actions')
            ->rawColumns(['btn'])
            ->toJson();
        // dd($datatables);
        return $datatables;
    }
    
    public function index()
    {
        $users = User::OrderBy('users.id','DESC')
            // ->with('profile')
            // ->with('rols')
            ->get();

        // dd($users->rols);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $is_active_list = SelectOpt::select('select_opts.*')
        ->where('table','users')
        ->where('name','is_active')
        ->where('view','users.index')
        ->orderby('value')
        ->pluck('value','key');
        // ->prepend('Estado','');

        return view('admin.users.create', compact('is_active_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        Session::flash('operp_ok','Registro guardado exitosamente');

        return redirect()->route('users.create');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // $profile = Profile::Where('user_id',$user->id)->first();

        // $rols = Rol::Where('user_id',$user->id)->get();

        return view('admin.users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $profile = Profile::Where('user_id',$user->id)->first();

        $rols = Rol::Where('user_id',$user->id)->get();

        $is_active_list = SelectOpt::select('select_opts.*')
            ->where('table','users')
            ->where('name','is_active')
            ->where('view','users.index')
            ->orderby('value')
            ->pluck('value','key');
            // ->prepend('Seleccionar','');

        return view('admin.users.edit',compact('user','profile','rols','is_active_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->fill($request->all());

        $user->save();

        $messenge = trans('db_oper_result.user_update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('users.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);

        $user->profile()->delete();
        $user->rols()->delete();
        $user->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){

            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);

        }

        Session::flash('operp_ok',$messenge.' -> ('.$user->username.')');

        return redirect()->route('users.index');
    }
}
