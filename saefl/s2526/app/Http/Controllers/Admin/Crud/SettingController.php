<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Admin\CreateSettingRequest;
use App\Http\Requests\Admin\UpdateSettingRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//models
use App\User;
use App\Models\sys\Setting;
use App\Models\sys\SelectOpt;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::OrderBy('id','DESC')
            // ->with('User')
            // ->with('Profile')
            ->get();

        return view('admin.settings.index', compact('settings'));
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

        //lista para los name
        $name_list = SelectOpt::select('select_opts.*')
            ->where('table','settings')
            ->where('view','settings.create')
            ->where('name','name')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los value
        $value_list = SelectOpt::select('select_opts.*')
            ->where('table','settings')
            ->where('view','settings.create')
            ->where('name','value')
            ->orderby('value')
            ->pluck('value','key');

        // dd($user_list,$estado_list,$tipo_list);

        return view('admin.settings.create',compact('user_list','name_list','value_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSettingRequest $request)
    {
        $setting = Setting::create($request->all());

        $messenge = trans('db_oper_result.create_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('settings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $setting = Setting::findOrFail($id);

        // dd($task);

        return view('admin.settings.show',compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $username = $setting->user->username;

        $user_list = User::OrderBy('id','DESC')
            ->pluck('username','id'); 

        //lista para los name
        $name_list = SelectOpt::select('select_opts.*')
            ->where('table','settings')
            ->where('view','settings.create')
            ->where('name','name')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los value
        $value_list = SelectOpt::select('select_opts.*')
            ->where('table','settings')
            ->where('view','settings.create')
            ->where('name','value')
            ->orderby('value')
            ->pluck('value','key');

        return view('admin.settings.edit',compact('setting','username','user_list','name_list','value_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $setting->fill($request->all());

        $setting->save();

        $messenge = trans('db_oper_result.update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('settings.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $setting = Setting::findOrFail($id);

        $setting->delete();

        $messenge = 'Operación completada correctamente';

        if($request->ajax()){

            return $messenge;

        }

        Session::flash('operp_ok',$messenge);

        return redirect()->route('settings.index');
    }
}
