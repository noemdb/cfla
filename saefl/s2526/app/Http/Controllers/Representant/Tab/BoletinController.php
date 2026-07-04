<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\Auth;

class BoletinController extends Controller
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
            $this->estudiants = ($this->representant) ? $this->representant->estudiants_formaly : null;
            $this->expire_bill_pendientes = ($this->representant) ? $this->representant->exchange_expire_bill_pendientes : null;
            return $next($request);
        }]);
    }
    public function crud(Request $request)
    {
        $pescolar = Pescolar::first();
        $representant = $this->representant;
        $expire_bill_pendientes = $this->expire_bill_pendientes;
        $estudiants = $this->estudiants;
        $lapsos = Lapso::all();

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.boletins.crud', compact('representant','pescolar','estudiants','list_comment','expire_bill_pendientes','lapsos'));
    }
    public function corte(Request $request)
    {
        $pescolar = Pescolar::first();
        $representant = $this->representant;
        $expire_bill_pendientes = $this->expire_bill_pendientes;
        $estudiants = $this->estudiants;
        $lapsos = Lapso::all();

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.boletins.corte', compact('representant','pescolar','estudiants','list_comment','lapsos','expire_bill_pendientes'));
    }
}
