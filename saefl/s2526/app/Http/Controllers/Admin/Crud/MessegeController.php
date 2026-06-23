<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//validation request
use App\Http\Requests\Admin\CreateMessegeRequest;
use App\Http\Requests\Admin\UpdateMessegeRequest;

//models
use App\User;
use App\Models\sys\Messege;
use App\Models\sys\SelectOpt;
// use App\Models\sys\Profile;
// use App\Models\sys\Rol;

class MessegeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messeges = Messege::OrderBy('id','DESC')
            // ->with('User')
            // ->with('Profile')
            ->get();

        return view('admin.messeges.index', compact('messeges'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_list = User::OrderBy('id','DESC')
            ->pluck('username','id'); 

        //lista para los estados
        $estado_list = SelectOpt::select('select_opts.*')
            ->where('table','messeges')
            ->where('view','messeges.create')
            ->where('name','estado')
            ->orderby('value')
            ->pluck('value','key');
        
        //lista para los tipo
        $tipo_list = SelectOpt::select('select_opts.*')
            ->where('table','messeges')
            ->where('view','messeges.create')
            ->where('name','tipo')
            ->orderby('value')
            ->pluck('value','key');

        //dd($user_list,$estado_list,$tipo_list);

        return view('admin.messeges.create',compact('user_list','tipo_list','estado_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMessegeRequest $request)
    {

        $messege = Messege::create($request->all());

        $messenge = trans('db_oper_result.create_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('messeges.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $messege = Messege::findOrFail($id);

        return view('admin.messeges.show',compact('messege'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
