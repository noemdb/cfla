<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// use App\User;
// use App\Models\sys\Profile;
// use App\Models\sys\Rol;

// use App\Models\sys\Task;
// use App\Models\sys\Alert;
// use App\Models\sys\Messege;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use CreateGradosTable;

use App\Http\Controllers\Admin\Fix\DB\HomeController as FixDBControlller;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Pescolar;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Jenssegers\Date\Date;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users()
    {
        return view('administracion.users.index');
    }

    // public function dashboard()
    // {
    //     $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
    //     $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
    //     $indicadores = Pestudio::getIndicadores(); //dd($indicadores);
    //     $now = Carbon::now()->format('Y-m-d');
    //     $plan_beneficos = PlanBenefico::where('created_at','<=',$now)->where('ffinal','>=',$now)->get();
    //     $retiros = Retiro::getEstudiants();
    //     $profesors = Profesor::asignado('true')->get(); //dd($profesors);
    //     $pescolar = Pescolar::OrderBy('created_at','DESC')->first();
    //     $date_start = Date::createFromFormat('Y-m-d',$pescolar->finicial);
    //     $date_end = Date::createFromFormat('Y-m-d',$pescolar->ffinal);

    //     $compact = [
    //         'estudiants',
    //         'pestudios',
    //         'plan_beneficos',
    //         'profesors',
    //         'retiros',
    //         'indicadores',
    //         'pescolar',
    //         'date_start',
    //         'date_end',
    //     ];
    //     return view('administracion.dashboard',compact($compact));
    // }

    public function dashboard()
    {
        return view('administracion.dashboard');
    }


    public function home()
    {
        return view('administracion.home');
    }

    public function home_full()
    {
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $grados = Grado::Orderby('id','asc')->where('status_active','true')->get();
        $cuentaxpagars = Cuentaxpagar::select('cuentaxpagars.*')
            ->join('concepto_pagos', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.type','GENERAL')
            // ->OrderBy('cuentaxpagars.id','asc')
            ->groupby('cuentaxpagars.id')
            ->get();
        $d = collect([]);

        $last_month_first = new Carbon('first day of last month');
        $last_month_last = new Carbon('last day of last month');
        $current_first = new Carbon('first day of this month');
        $current_last = new Carbon('last day of this month');
        $next_month_first = new Carbon('first day of next month');
        $next_month_last = new Carbon('last day of next month');
        $estudiants = Estudiant::select('estudiants.*')->active('true')->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id');
        $grado = new Grado;
        $pestudios = Pestudio::active('true')->get();
        $plan_beneficos = PlanBenefico::all();

        $total_pago_last = RegistroPago::select('registro_pagos.*')->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$last_month_first)->where('cuentaxpagars.date_expiration','<=',$last_month_last)->sum('pagos.pagos_ammount');
        $registro_pago_last = RegistroPago::select('registro_pagos.*')->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$last_month_first)->where('cuentaxpagars.date_expiration','<=',$last_month_last)->groupby('registro_pagos.estudiant_id')->get();
        $total_meta_last = 0;
        $name_cta_last = null;
        $count_deuda_last = 0;
        $deudores_last = 0;
        $deudores_i_last = [];
        $deudores_grado_last = [];
        $cuentaxpagars_last = Cuentaxpagar::getCuentasActivas($last_month_first,$last_month_last);
        foreach ($cuentaxpagars_last as $cuentaxpagar) {
            $total_meta_last = $total_meta_last + $estudiants->get()->count() * $cuentaxpagar->total_conceptos;
            $name_cta_last = $cuentaxpagar->name;
            $deudores_last = $deudores_last + $cuentaxpagar->deudores_g->count();
            $deudores_grado_last = $cuentaxpagar->deudores_g_by_grado;
        }
        $deudores_i_last = Cuentaxpagar::getDeudoresI($last_month_first,$last_month_last);
        $deudores_last = $deudores_last + count($deudores_i_last);
        $total_meta_last = $total_meta_last + 800000 * $deudores_i_last->count();

        $total_pago_current = RegistroPago::select('registro_pagos.*') ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id') ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$current_first)->where('cuentaxpagars.date_expiration','<=',$current_last)->sum('pagos.pagos_ammount');
        $registro_pago_currtent = RegistroPago::select('registro_pagos.*') ->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id') ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$current_first)->where('cuentaxpagars.date_expiration','<=',$current_last)->groupby('registro_pagos.estudiant_id')->get();
        $total_meta_current = 0;
        $cuentaxpagars_current = Cuentaxpagar::getCuentasActivas($current_first,$current_last);
        $name_cta_current = null;
        $deudores_current = 0;
        $deudores_i_current = [];
        $deudores_grado_current = [];
        foreach ($cuentaxpagars_current as $cuentaxpagar) {
            $total_meta_current = $total_meta_current + $estudiants->get()->count() * $cuentaxpagar->total_conceptos;
            $name_cta_current = $cuentaxpagar->name;
            $deudores_current = $deudores_current + $cuentaxpagar->deudores_g->count();
            $deudores_grado_current = $cuentaxpagar->deudores_g_by_grado;
        }
        $deudores_i_current = Cuentaxpagar::getDeudoresI($current_first,$current_last);
        $deudores_current = $deudores_current + count($deudores_i_current);

        $total_pago_next = RegistroPago::select('registro_pagos.*')->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$next_month_first)->where('cuentaxpagars.date_expiration','<=',$next_month_last)->sum('pagos.pagos_ammount');
        $registro_pago_next = RegistroPago::select('registro_pagos.*')->join('pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')->where('cuentaxpagars.date_expiration','>=',$next_month_first)->where('cuentaxpagars.date_expiration','<=',$next_month_last)->groupby('registro_pagos.estudiant_id')->get();
        $total_meta_next = 0;
        $name_cta_next = null;
        $deudores_next = 0;
        $deudores_i_next = [];
        $deudores_grado_next = [];
        $cuentaxpagars_next = Cuentaxpagar::getCuentasActivas($next_month_first,$next_month_last);
        foreach ($cuentaxpagars_next as $cuentaxpagar) {
            $total_meta_next = $total_meta_next + $estudiants->get()->count() * $cuentaxpagar->total_conceptos;
            $name_cta_next = $cuentaxpagar->name;
            $deudores_next = $deudores_next + $cuentaxpagar->deudores_g->count();
            $deudores_grado_next = $cuentaxpagar->deudores_g_by_grado;
        }
        // dd($cuentaxpagars_next,$deudores_next);
        $deudores_i_next = Cuentaxpagar::getDeudoresI($next_month_first,$next_month_last);
        $deudores_next = ($cuentaxpagars_next->count()>0) ? $deudores_next + count($deudores_i_next) / $cuentaxpagars_next->count():null;

        // dd($cuentaxpagars_last,$deudores_last);

        // dd($cuentaxpagars_current,$deudores_current);

        // dd($cuentaxpagars_next,$deudores_next);

        // dd($cuentaxpagars_current);

        // dd($deudores_last,$deudores_current,$deudores_next,$deudores_i_next);

        // dd($deudores_grado_last,$deudores_grado_current,$deudores_grado_next);


        return view('administracion.home',compact('cuentaxpagars','plan_beneficos','pestudios','grado','deudores_grado_next','deudores_grado_current','deudores_grado_last','deudores_last','deudores_current','deudores_next','pestudios','grados','estudiants','registro_pago_next','registro_pago_currtent','registro_pago_last','name_cta_current','cuentaxpagars_next','cuentaxpagars_current','cuentaxpagars_last','name_cta_last','total_pago_last','total_meta_last','total_pago_current','total_meta_current','name_cta_next','total_pago_next','total_meta_next'));
    }
}
