<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago\SyncData;

class SyncDataController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_common']);
    }

    public function index()
    {
        $user = Auth::user();
        $sync_datas = SyncData::where('user_id',$user->id)->get(); //dd($sync_data);
        $lapsos = Lapso::all();
        $grados = Grado::all();
        return view('administracion.configuraciones.sync_datas.index',compact('user','sync_datas','lapsos','grados'));
    }
}
