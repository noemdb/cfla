<?php

namespace App\Http\Controllers\Administracion\Bot;

use App\Http\Controllers\Controller;
use App\Models\app\Bot\Bmessege;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BmessegeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_common']);
        $this->middleware(['create','store'])->only('is_admin');
    }

    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id) ;
        $bmesseges = Bmessege::getforArea($user->area,$user->isAdmin());
        $list_comment = Bmessege::COLUMN_COMMENTS;
        return view('administracion.autoresponders.bmesseges.index',compact('bmesseges','list_comment'));
    }

}
