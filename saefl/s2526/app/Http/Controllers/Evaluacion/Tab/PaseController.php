<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Permission\Pase;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class PaseController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$pestudios;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();
            return $next($request);
        }]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manager_id = $this->user->id;
        $pestudios = $this->pestudios;

        $list_comment = Pase::COLUMN_COMMENTS;

        // $pases = Pase::all();
        $pases = Pase::getPasesForManagerId($manager_id);
        return view('evaluacions.pases.index', compact('pases','pestudios','manager_id','list_comment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $list_estudiants = Estudiant::list_pestudio_grado();
        $list_profesor = Profesor::list_profesors();
        $list_pensum = Pensum::list_pestudio_pensum();
        
        $list_type = Pase::list_type();
        $list_motive = Pase::list_motive();
        $list_status = Pase::list_status();
        $list_comment = Pase::COLUMN_COMMENTS;
        $compact = ['list_comment','list_estudiants','list_profesor','list_pensum','list_type','list_motive','list_status'];
        return view('evaluacions.pases.create', compact($compact));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $pase = new Pase();
        $pase->fill($data);
        $pase->user_id = Auth::id();
        $pase->save();
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('evaluacions.permissions.pases.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pase = Pase::findOrfail($id);

        $list_estudiants = Estudiant::list_pestudio_grado();
        $list_profesor = Profesor::list_profesors();
        $list_pensum = Pensum::list_pestudio_pensum();
        
        $list_type = Pase::list_type();
        $list_motive = Pase::list_motive();
        $list_status = Pase::list_status();
        $list_comment = Pase::COLUMN_COMMENTS;
        $compact = ['pase','list_comment','list_estudiants','list_profesor','list_pensum','list_type','list_motive','list_status'];
        return view('evaluacions.pases.edit', compact($compact));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pase = Pase::findOrfail($id);
        $pase->fill($request->all());
        $pase->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('evaluacions.permissions.pases.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pase = Pase::findOrfail($id);
        $pase->delete();
        $messenge = trans('db_oper_result.delete_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('evaluacions.permissions.pases.index');
    }

    public function send($id)
    {
        $pase = Pase::findOrfail($id);

        $messenge = 'Mensaje enviado exitosamente.';
        Session::flash('operp_ok',$messenge);
        return redirect()->route('evaluacions.permissions.pases.index');
    }

    public function view($id)
    {
        $pase = Pase::findOrfail($id); // email.permissions.pases
        $toDate = Date::now()->format('d F Y');
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad = Autoridad::getTipoAuthority('2');//DIRECTOR
        $pestudio = $pase->pestudio;
        $coordinador = ($pestudio) ? $pestudio->manager : null;
        return view('email.permissions.pases', compact('pase','institucion','autoridad','toDate','coordinador'));
    }
}
