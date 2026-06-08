<?php

namespace App\Http\Controllers\Administracion\Tab\Collection;

use App\Http\Controllers\Administracion\Email\CollectionController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Collection\CreateAsistentRequest;
use App\Http\Requests\Administracion\Collection\CreateCollPoliticallRequest;
use App\Models\app\Cobranzas\CollMessege;
use App\Models\app\Cobranzas\CollNivel;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class CollPoliticalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function coll_calendars(Request $request)
    {
        return view('administracion.collections.coll_calendars.index');
    }

    public function EmailForQueuingAnuality($id)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $jobSend = new CollectionController();
    }

    public function EmailForQueuing($id)
    {
        $coll_political = CollPolitical::findOrFail($id); //dd($coll_political);
        if ($coll_political->finicial >= Carbon::now()) {
            $jobSend = new CollectionController(); //dd($jobSend);
            $jobSend->bacthCollectionSend($coll_political->id,true);
            $messenge = "Buen trabajo! la cola de taréas fue cargada";
        } else {
            $messenge = "No se registraron taréas";
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_politicals.index',$id);
    }

    public function groupPoliticals(Request $request)
    {
        $coll_political_id = (!empty($request->coll_political_id)) ? $request->coll_political_id : null ;
        $coll_political = ($coll_political_id) ? CollPolitical::find($coll_political_id) : null;
        $representants = ($coll_political) ? $coll_political->representants : collect();
        $list_coll_politicals = CollPolitical::list_coll_politicals();
        $list_comment = Representant::COLUMN_COMMENTS;
        $compact = ['coll_political_id','coll_political','representants','list_coll_politicals','list_comment'];
        return view('administracion.collections.coll_politicals.group_politicals',compact($compact));
    }

    public function representantGroup(Request $request)
    {
        $canon = (!empty($request->canon)) ? $request->canon : null ;
        $representants = CollPolitical::getRepresentants($canon);
        $list_coll_politicals_canon = CollPolitical::list_coll_politicals_canon();
        $list_comment = Representant::COLUMN_COMMENTS;
        return view('administracion.collections.coll_politicals.representant_group',compact('representants','list_coll_politicals_canon','list_comment','canon'));
    }

    public function asistent()
    {
        $coll_politicals = CollPolitical::all()->sortByDesc('created_at');
        $list_comment_arr['coll_politicals'] = CollPolitical::COLUMN_COMMENTS;
        $list_comment_arr['coll_nivels'] = CollNivel::COLUMN_COMMENTS;
        $list_comment_arr['coll_messeges'] = CollMessege::COLUMN_COMMENTS;

        $pescolar_list = Pescolar::pescolar_list();
        $list_coll_politicals_canon = CollPolitical::list_coll_politicals_canon();

        $compact = [
            'coll_politicals',
            'pescolar_list','list_coll_politicals_canon',
            'list_comment_arr',
        ];
        return view('administracion.collections.coll_politicals.asistent',compact($compact));
    }

    public function fullStore(CreateAsistentRequest $request)
    {
        // dd($request->all());
        $arr = [
            'pescolar_id' => $request['pescolar_id'],
            'name' => $request['name'],
            'code' => $request['code'],
            'description' => $request['description'],
            'finicial' => $request['finicial'],
            'ffinal' => $request['ffinal'],
            'status' => 'true',
            'canon' => $request['canon'],
            'status_debts' => $request['status_debts'],
            'numbers_bills' => $request['numbers_bills'],
            'status_approved' => 'true',
        ];

        $coll_political = CollPolitical::create($arr);

        //'coll_political_id','name','order','weighing','description','status'
        $arr = [
            'coll_political_id' => $coll_political->id,
            'name' => $request['name'],
            'order' => 1,
            'weighing' => 1,
            'description' => $request['description'],
            'status' => 'true',
        ];
        $coll_nivel = CollNivel::create($arr);

        //'user_id','coll_nivel_id','subject','title','subtitle','greeting','consider','sentence','waiting','footer','status'
        $arr = [
            'user_id' => Auth::user()->id,
            'coll_nivel_id' => $coll_nivel->id,
            'subject' => $request['subject'],
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'greeting' => $request['greeting'],
            'consider' => $request['consider'],
            'sentence' => $request['sentence'],
            'waiting' => $request['waiting'],
            'footer' => $request['footer'],
            'status' => 'true',
        ];

        $coll_messege = CollMessege::create($arr);

        // $jobSend = new CollectionController();

        // $jobSend->bacthCollectionSend($coll_political->id);

        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.collections.coll_politicals.asistent');
    }

    public function preview(Request $request)
    {
        $arr = [
            'user_id' => Auth::user()->id,
            'subject' => $request['subject'],
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'greeting' => $request['greeting'],
            'consider' => $request['consider'],
            'sentence' => $request['sentence'],
            'waiting' => $request['waiting'],
            'footer' => $request['footer'],
            'status' => 'true',
        ];
        $representant = Representant::inRandomOrder()->first(); //dd($representants);
        $coll_message = new CollMessege();
        $coll_message->fill($arr);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $compact = ['representant','coll_message','institucion','autoridad1','autoridad2','toDate','lastDate'];
        return view('email.collections.messege',compact($compact));

    }

    public function index()
    {
        $coll_politicals = CollPolitical::all()->sortByDesc('created_at')->sortByDesc('status');
        $list_comment = CollPolitical::COLUMN_COMMENTS;

        $fecha = Carbon::now();
        $list_users = DB::table('users')
        ->select('users.id', 'users.username','rols.area','rols.rol', DB::raw('concat(profiles.firstname, " ",profiles.lastname ) as proFullname'))
        ->join('profiles', 'users.id', '=', 'profiles.user_id')
        ->join('rols', 'users.id', '=', 'rols.user_id')
        ->Where('rols.ffinal','>=',$fecha)
        ->Where('rols.finicial','<=',$fecha)
        ->whereIn('rols.area', ['ADMINISTRACION','AUTORIDAD','DIRECTOR','ADMINISTRACION','CONTROL ESTUDIO'])
        ->whereIn('rols.rol', ['DIRECTOR','COORDINADOR','ASISTENTE'])
        ->get();
        return view('administracion.collections.coll_politicals.index',compact('coll_politicals','list_comment','list_users'));
    }

    public function crud()
    {
        $coll_politicals = CollPolitical::all();
        $list_comment = CollPolitical::COLUMN_COMMENTS;
        return view('administracion.collections.coll_politicals.crud',compact('coll_politicals','list_comment'));
    }

    public function create()
    {
        $coll_politicals = CollPolitical::all()->take(4);
        $list_comment = CollPolitical::COLUMN_COMMENTS;
        $pescolar_list = Pescolar::pescolar_list();
        $list_coll_politicals_canon = CollPolitical::list_coll_politicals_canon();
        return view('administracion.collections.coll_politicals.create',compact('coll_politicals','pescolar_list','list_coll_politicals_canon','list_comment'));
    }

    public function store(CreateCollPoliticallRequest $request)
    {
        $coll_political = CollPolitical::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.collections.coll_politicals.create');
    }

    public function edit($id)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_politicals = CollPolitical::all()->take(4);
        $list_comment = CollPolitical::COLUMN_COMMENTS;
        $pescolar_list = Pescolar::pescolar_list();
        $list_coll_politicals_canon = CollPolitical::list_coll_politicals_canon();
        return view('administracion.collections.coll_politicals.edit',compact('coll_political','pescolar_list','list_coll_politicals_canon','coll_politicals','list_comment'));
    }
    public function update(Request $request, $id)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_political->fill($request->all());
        $coll_political->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_politicals.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_political->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.collections.coll_politicals.crud');
    }

    public function previewMsnId($id)
    {
        $coll_message = CollMessege::findOrFail($id);
        $representant = Representant::inRandomOrder()->first(); //dd($representants);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR
        $toDate = Date::now()->format('d F Y');
        $lastDate = Date::now()->lastOfMonth()->format('d F Y');

        $compact = ['coll_message','representant','institucion','autoridad1','autoridad2','toDate','lastDate'];
        return view('email.collections.messege',compact($compact));

    }
}
