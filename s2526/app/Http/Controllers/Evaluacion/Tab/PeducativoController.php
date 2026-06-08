<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Peducativo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeducativoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_evaluacion']);
    }

    public function index(Request $request)
    {
        $user = User::find(Auth::id());
        $peducativos = Peducativo::getPeducativos($user->id);
        return view('evaluacions.peducativos.index', compact('peducativos')); 
    }
}
