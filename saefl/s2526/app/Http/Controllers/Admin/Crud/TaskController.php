<?php

namespace App\Http\Controllers\Admin\Crud;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Admin\CreateTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//models
use App\User;
use App\Models\sys\Task;
use App\Models\sys\SelectOpt;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::OrderBy('id','DESC')
            ->with('User')
            // ->with('Profile')
            ->get();

        //dd($tasks);

        return view('admin.tasks.index', compact('tasks'));
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
            ->where('table','tasks')
            ->where('view','tasks.create')
            ->where('name','estado')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los tipo
        $tipo_list = SelectOpt::select('select_opts.*')
            ->where('table','tasks')
            ->where('view','tasks.create')
            ->where('name','tipo')
            ->orderby('value')
            ->pluck('value','key');

        // dd($user_list,$estado_list,$tipo_list);

        return view('admin.tasks.create',compact('user_list','tipo_list','estado_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {

        $ntask = Task::where('user_id',$request->user_id)->count();

        $data = $request->all();

        $data['codigo'] = date('Y').$ntask;

        $task = Task::create($data);

        $messenge = trans('db_oper_result.create_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('tasks.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);

        // dd($task);

        return view('admin.tasks.show',compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        $user_list = User::OrderBy('id','DESC')
            ->pluck('username','id');     

        //lista para los estados
        $estado_list = SelectOpt::select('select_opts.*')
            ->where('table','tasks')
            ->where('view','tasks.create')
            ->where('name','estado')
            ->orderby('value')
            ->pluck('value','value');
        
        //lista para los tipo
        $tipo_list = SelectOpt::select('select_opts.*')
            ->where('table','tasks')
            ->where('view','tasks.create')
            ->where('name','tipo')
            ->orderby('value')
            ->pluck('value','key');

        // dd($user_list,$estado_list,$tipo_list);

        return view('admin.tasks.edit',compact('task','user_list','tipo_list','estado_list'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);

        $task->fill($request->all());

        // $task->codigo = '000';

        $task->save();

        $messenge = trans('db_oper_result.update_ok');

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        return redirect()->route('tasks.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $task = Task::findOrFail($id);

        $task->delete();

        $messenge = trans('db_oper_result.delete_ok');

        $operation= 'delete';

        if($request->ajax()){

            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);

        }

        Session::flash('operp_ok',$messenge);

        return redirect()->route('tasks.index');
    }
}
