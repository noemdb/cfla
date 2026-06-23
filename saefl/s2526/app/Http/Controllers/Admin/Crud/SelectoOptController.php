<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Admin\CreateSelectOptRequest;
use App\Http\Requests\Admin\UpdateSelectOptRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//models
use App\User;
// use App\Models\sys\SelectOpt;
use App\Models\sys\SelectOpt;

class SelectoOptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $selectopts = SelectOpt::OrderBy('id','DESC')
            ->get();

        return view('admin.selectopts.index', compact('selectopts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.selectopts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSelectOptRequest $request)
    {
        $selectopt = SelectOpt::create($request->all());

        $messenge = trans('db_oper_result.create_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('selectopts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $selectopt = SelectOpt::findOrFail($id);

        return view('admin.selectopts.show',compact('selectopt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $selectopt = SelectOpt::findOrFail($id);

        return view('admin.selectopts.edit',compact('selectopt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSelectOptRequest $request, $id)
    {
        $selectopt = SelectOpt::findOrFail($id);

        $selectopt->fill($request->all());

        $selectopt->save();

        $messenge = trans('db_oper_result.update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('selectopts.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $selectopt = SelectOpt::findOrFail($id);

        $selectopt->delete();

        $messenge = 'Operación completada correctamente';

        if($request->ajax()){

            return $messenge;

        }

        Session::flash('operp_ok',$messenge);

        return redirect()->route('selectopt.index');
    }
}
