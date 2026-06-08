<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Admin\CreateAlertRequest;
use App\Http\Requests\Admin\UpdateAlertRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//models
use App\User;
use App\Models\sys\Alert;
use App\Models\sys\SelectOpt;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alerts = Alert::OrderBy('id','DESC')
            ->with('User')
            // ->with('Profile')
            ->get();

        return view('admin.alerts.index', compact('alerts'));
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
            ->where('table','alerts')
            ->where('view','alerts.create')
            ->where('name','estado')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los tipo
        $tipo_list = SelectOpt::select('select_opts.*')
            ->where('table','alerts')
            ->where('view','alerts.create')
            ->where('name','tipo')
            ->orderby('value')
            ->pluck('value','key');

        // dd($user_list,$estado_list,$tipo_list);

        return view('admin.alerts.create',compact('user_list','tipo_list','estado_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAlertRequest $request)
    {

        $alert = Alert::create($request->all());

        $messenge = trans('db_oper_result.create_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('alerts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $alert = Alert::findOrFail($id);

        // dd($task);

        return view('admin.alerts.show',compact('alert'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alert = Alert::findOrFail($id);

        $user_list = User::OrderBy('id','DESC')
            ->pluck('username','id'); 

        //lista para los estados
        $estado_list = SelectOpt::select('select_opts.*')
            ->where('table','alerts')
            ->where('view','alerts.create')
            ->where('name','estado')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los tipo
        $tipo_list = SelectOpt::select('select_opts.*')
            ->where('table','alerts')
            ->where('view','alerts.create')
            ->where('name','tipo')
            ->orderby('value')
            ->pluck('value','key');

        // dd($user_list,$estado_list,$tipo_list);

        return view('admin.alerts.edit',compact('alert','user_list','tipo_list','estado_list'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlertRequest $request, $id)
    {
        $alert = Alert::findOrFail($id);

        $alert->fill($request->all());

        $alert->save();

        $messenge = trans('db_oper_result.update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('alerts.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $alert = Alert::findOrFail($id);

        $alert->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){

            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);

        }

        Session::flash('operp_ok',$messenge);

        return redirect()->route('alerts.index');
    }
}
