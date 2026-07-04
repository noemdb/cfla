<?php

namespace App\Http\Controllers\Administracion\Bot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\app\Bot\Bmain;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BmainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_common']);
        $this->middleware(['create','store'])->only('is_admin');
        // $this->middleware('store')->only('is_admin');
    }

    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id) ;
        $bmains = Bmain::getforArea($user->area,$user->isAdmin());
        $list_comment = Bmain::COLUMN_COMMENTS;
        return view('administracion.autoresponders.bmains.index',compact('bmains','list_comment'));
    }

    public function create(Request $request)
    {
        $list_area = Bmain::list_area();
        $list_comment = Bmain::COLUMN_COMMENTS;
        return view('administracion.autoresponders.bmains.create',compact('list_comment','list_area'));
    }

    public function store(Request $request)
    {
        $bmain = Bmain::create($request->all());

        $user = User::findOrFail(Auth::user()->id) ;
        $bmains = Bmain::getforArea($user->area,$user->isAdmin());
        $list_comment = Bmain::COLUMN_COMMENTS;

        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.autoresponders.bmains.index',compact('bmains','list_comment'));
    }

    public function edit($id)
    {
        $bmain = Bmain::findOrFail($id);
        $list_area = Bmain::list_area();
        $list_comment = Bmain::COLUMN_COMMENTS;
        $compact = ['bmain','list_comment','list_area'];
        return view('administracion.autoresponders.bmains.edit',compact($compact));
    }

    public function update(Request $request, $id)
    {
        $bmain = Bmain::findOrFail($id);
        $bmain->fill($request->all());
        $bmain->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.autoresponders.bmains.edit',$id);
    }

    public function Metachat(Request $request)
    {
        return view('administracion.autoresponders.metas.index');
    }

    public function MetachatWS(Request $request)
    {
        return view('administracion.autoresponders.metas.indexWS');
    }

}
