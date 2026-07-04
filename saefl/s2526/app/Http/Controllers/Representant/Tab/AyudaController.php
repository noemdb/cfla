<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Manual\Ayuda;
use Illuminate\Support\Facades\Auth;

class AyudaController extends Controller
{
    protected $representant,$estudiants,$list_comment;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Representant::COLUMN_COMMENTS;
            $this->estudiants = ($this->representant) ? $this->representant->estudiants : null;
            return $next($request);
        }]);
    }
    public function index(Request $request)
    {
        $search = (!empty($request->search)) ? $request->search:null;
        $representant = $this->representant;
        $ayudas = Ayuda::all();

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.ayudas.index', compact('search','ayudas','list_comment'));
    }
}
