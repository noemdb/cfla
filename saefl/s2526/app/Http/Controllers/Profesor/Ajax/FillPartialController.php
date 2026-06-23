<?php

namespace App\Http\Controllers\Profesor\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Edescriptiva;
use Illuminate\Support\Facades\Auth;

class FillPartialController extends Controller
{

    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function SabanaFull($seccion_id,$pensum_id, Request $request)
    {
        $profesor = $this->profesor;
        $seccion = Seccion::findOrFail($seccion_id);
        $lapsos = Lapso::all();
        $baremo = new Baremo();
        $pensums = $profesor->pensums->where('id',$pensum_id);

        if($request->ajax()){
            return view('profesors.boletins.show.modal.planillas_notas', compact('seccion','pensums','lapsos','baremo'));
        }
    }

    public function EdescriptivaDetails($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);

        $list_comment = Edescriptiva::COLUMN_COMMENTS;

        if($request->ajax()){
            return view('profesors.edescriptivas.show.modal.details', compact('estudiant','list_comment'));
        }
    }

    public function EdescriptivaCreate($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);

        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_lapso->put(null,'Final');

        $list_comment = Edescriptiva::COLUMN_COMMENTS;

        if($request->ajax()){
            return view('profesors.edescriptivas.show.modal.create', compact('estudiant','list_lapso','list_comment'));
        }
    }
}
