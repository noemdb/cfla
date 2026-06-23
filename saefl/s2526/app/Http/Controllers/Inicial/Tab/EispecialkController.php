<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Profesor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EispecialkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_inicial']);
    }

    public function index()
    {
        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        return view('inicials.eispecialks.index',compact('profesor'));
    }

    public function format($id, Request $request)
    {
        $eispecialk = Eispecialk::findOrFail($id);
        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('livewire.inicial.formats.eispecialks.index',compact('profesor','eispecialk','institucion','fecha'));
    }
}
