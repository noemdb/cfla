<?php

namespace App\Http\Controllers\Administracion\Bot;

use App\Http\Controllers\Controller;
use App\Models\app\Bot\Bmain;
use App\Models\app\Bot\Boption;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BoptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_common']);
        $this->middleware(['create','store'])->only('is_admin');
    }

    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id) ;
        $boptions = Boption::getforArea($user->area,$user->isAdmin());
        $list_comment = Boption::COLUMN_COMMENTS;
        return view('administracion.autoresponders.boptions.index',compact('boptions','list_comment'));
    }

    public function edit($id)
    {
        $boption = Boption::findOrFail($id);
        $list_comment = Boption::COLUMN_COMMENTS;
        $compact = ['boption','list_comment'];
        return view('administracion.autoresponders.boptions.edit',compact($compact));
    }

    public function update(Request $request, $id)
    {
        $boption = Boption::findOrFail($id);
        $boption->fill($request->all());
        $boption->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.autoresponders.boptions.edit',$id);
    }

    public function create(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id) ;
        $list_bmains = Bmain::list_bmains();
        $list_comment = Boption::COLUMN_COMMENTS;
        return view('administracion.autoresponders.boptions.create',compact('list_comment','list_bmains'));
    }

    public function store(Request $request)
    {
        $bmain = Boption::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.autoresponders.boptions.index');
    }
    public function destroy($id, Request $request)
    {
        $boption = Boption::findOrFail($id);
        $boption->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.autoresponders.boptions.index');
    }
}
