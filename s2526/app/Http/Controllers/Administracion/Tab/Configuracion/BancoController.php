<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Configuracion\CreateBancoRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateBancoRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Institucion\Banco;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Currency;

class BancoController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'is_admon']);
    }

    public function libroAbonos(Request $request)
    {
        $banco_id = $request->banco_id;
        $start = new Carbon();
        $end = new Carbon();

        $finicial = (!empty($request->finicial)) ? $request->finicial : $start->startOfmonth()->format('Y-m-d');
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : $end->endOfmonth()->format('Y-m-d');
        $status_late_payment = (!empty($request->status_late_payment)) ? $request->status_late_payment : 'false';
        $method_pay_id = (!empty($request->method_pay_id)) ? $request->method_pay_id : null;

        $finicial_late = Carbon::createFromFormat('Y-m-d H:i', $finicial . ' 00:00');
        $ffinal_late = Carbon::createFromFormat('Y-m-d H:i', $ffinal . ' 23:59');

        $banco = ($banco_id) ? Banco::findOrFail($banco_id) : Banco::active()->first();

        $institucion = $banco->institucion;

        $ingresos = Ingreso::select('ingresos.*', DB::raw('sum(ingresos.ingreso_ammount) as ingreso_ammount_total'))
            ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')
            ->where('banco_id', $banco->id)
            ->whereNull('ingresos.deleted_at')
            ->whereNull('abonos.deleted_at')
            ->groupBy('ingresos.number_i_pay')
            ->orderBy('ingresos.created_at', 'asc');

        if ($status_late_payment == 'false') {
            $ingresos = $ingresos
                ->where('ingresos.status_late_payment', 'false')
                ->where('ingresos.date_transaction', '>=', $finicial)
                ->where('ingresos.date_transaction', '<=', $ffinal);
        }

        if ($status_late_payment == 'true') {
            $ingresos = $ingresos
                ->where('ingresos.status_late_payment', 'true')
                ->where('ingresos.created_at', '>=', $finicial_late)
                ->where('ingresos.created_at', '<=', $ffinal_late);
        }

        if ($method_pay_id) {
            $ingresos = $ingresos->where('ingresos.method_pay_id', $method_pay_id);
        }

        $ingresos = $ingresos->get();

        $metodos = MetodoPago::all()->map(function ($metodo) use ($banco, $status_late_payment, $finicial, $ffinal, $finicial_late, $ffinal_late, $method_pay_id) {
            $query = Ingreso::where('method_pay_id', $metodo->id)
                ->join('abonos', 'ingresos.id', '=', 'abonos.ingreso_id')

                ->where('ingresos.banco_id', $banco->id)
                ->whereNull('ingresos.deleted_at')
                ->whereNull('abonos.deleted_at');

            if ($status_late_payment === 'false') {
                $query = $query
                    ->where('status_late_payment', 'false')
                    ->where('date_transaction', '>=', $finicial)
                    ->where('date_transaction', '<=', $ffinal);
            } elseif ($status_late_payment === 'true') {
                $query = $query
                    ->where('status_late_payment', 'true')
                    ->where('created_at', '>=', $finicial_late)
                    ->where('created_at', '<=', $ffinal_late);
            }

            if ($method_pay_id) {
                $query = $query->where('ingresos.method_pay_id', $method_pay_id);
            }

            $ingresos = $query->get();

            if ($ingresos->isEmpty()) {
                return null; // ignorar este método si no tiene ingresos válidos
            }

            $metodo->total = $ingresos->sum('ingreso_ammount');
            $metodo->total_exchange_ammount = $ingresos->sum('exchange_ammount');
            $metodo->count = $ingresos->count();

            return $metodo;
        })->filter(); // eliminar los nulls

        $list_banco = Banco::banco_list();
        $list_metodo_pago = MetodoPago::list_metodo_pago();

        return view('administracion.configuraciones.banco.libroAbono', compact('banco', 'institucion', 'metodos', 'ingresos', 'request', 'banco_id', 'method_pay_id', 'finicial', 'ffinal', 'status_late_payment', 'list_banco', 'list_metodo_pago'));
    }

    public function libro(Request $request)
    {
        $banco_id = $request->banco_id;
        $start = new Carbon();
        $end = new Carbon();

        $finicial = (!empty($request->finicial)) ? $request->finicial : $start->startOfmonth()->format('Y-m-d');
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : $end->endOfmonth()->format('Y-m-d');
        $status_late_payment = (!empty($request->status_late_payment)) ? $request->status_late_payment : 'false';
        $method_pay_id = (!empty($request->method_pay_id)) ? $request->method_pay_id : null;

        $finicial_late = Carbon::createFromFormat('Y-m-d H:i', $finicial . ' 00:00');
        $ffinal_late = Carbon::createFromFormat('Y-m-d H:i', $ffinal . ' 23:59');

        $banco = Banco::findOrFail($banco_id);

        $institucion = $banco->institucion;

        $ingresos = Ingreso::select('ingresos.*', DB::raw('sum(ingresos.ingreso_ammount) as ingreso_ammount_total'))
            ->where('banco_id', $banco->id)
            ->wherenull('ingresos.deleted_at')
            ->groupBy('ingresos.number_i_pay') // el groupBy se debe quitar (NMDB 01-10-2025)
            ->orderBy('ingresos.created_at', 'desc');

        if ($status_late_payment == 'false') {
            $ingresos = $ingresos
                //->where('ingresos.status_late_payment', 'false')
                ->where('ingresos.date_transaction', '>=', $finicial)
                ->where('ingresos.date_transaction', '<=', $ffinal)
                ;
        }

        if ($status_late_payment == 'true') {
            $ingresos = $ingresos
                ->where('ingresos.status_late_payment', 'true')
                ->where('created_at', '>=', $finicial_late)
                ->where('created_at', '<=', $ffinal_late);
        }

        if ($method_pay_id) {
            $ingresos = $ingresos->where('ingresos.method_pay_id', $method_pay_id);
        }

        $ingresos = $ingresos->get()->filter(function ($ingreso) {
            return !is_null($ingreso->invoice_number);
        }); 
        //dd($ingresos->get());

        //$ingresos = $ingresos->get();

        $metodos = MetodoPago::all()->map(function ($metodo) use ($banco, $status_late_payment, $finicial, $ffinal, $finicial_late, $ffinal_late, $method_pay_id) {
            $query = Ingreso::where('method_pay_id', $metodo->id)
                ->where('banco_id', $banco->id)
                ->groupBy('number_i_pay') // el groupBy se debe quitar (NMDB 01-10-2025)
                ->orderBy('created_at', 'desc')
                ->whereNull('deleted_at');

            if ($status_late_payment === 'false') {
                $query = $query
                    ->where('status_late_payment', 'false')
                    ->where('date_transaction', '>=', $finicial)
                    ->where('date_transaction', '<=', $ffinal);
            } elseif ($status_late_payment === 'true') {
                $query = $query
                    ->where('status_late_payment', 'true')
                    ->where('created_at', '>=', $finicial_late)
                    ->where('created_at', '<=', $ffinal_late);
            }

            if ($method_pay_id) {
                $query = $query->where('ingresos.method_pay_id', $method_pay_id);
            }

            $ingresos = $query->get()->filter(function ($ingreso) {
                return !is_null($ingreso->invoice_number); // atributo dinámico
            });

            if ($ingresos->isEmpty()) {
                return null; // ignorar este método si no tiene ingresos válidos
            }

            $metodo->total = $ingresos->sum('ingreso_ammount');
            $metodo->total_exchange_ammount = $ingresos->sum('exchange_ammount');
            $metodo->count = $ingresos->count();

            return $metodo;
        })->filter(); // eliminar los nulls // eliminar los nulls

        $list_banco = Banco::banco_list();
        $list_metodo_pago = MetodoPago::list_metodo_pago();

        return view('administracion.configuraciones.banco.libro', compact('banco', 'institucion', 'metodos', 'ingresos', 'request', 'banco_id', 'method_pay_id', 'finicial', 'ffinal', 'status_late_payment', 'list_banco', 'list_metodo_pago'));
    }

    public function banco()
    {
        $bancos =
            Banco::OrderBy('created_at', 'DESC')
            ->where('institucion_id', session('institucion_id'))
            ->orderby('id')
            ->get();

        $list_comment = Banco::COLUMN_COMMENTS;

        $institucion_list = Institucion::pluck('name', 'id');
        $list_currency = Currency::pluck('name', 'id');

        return view('administracion.configuraciones.banco.index', compact('bancos', 'list_comment', 'institucion_list', 'list_currency'));
    }

    public function create()
    {
        $bancos = Banco::OrderBy('created_at', 'DESC')->where('status_active_bank', 'true')->where('institucion_id', session('institucion_id'))->orderby('id')->get()->take(3);
        $list_comment = Banco::COLUMN_COMMENTS;
        $institucion_list = Institucion::pluck('name', 'id');
        $list_currency = Currency::pluck('name', 'id');
        return view('administracion.configuraciones.banco.create', compact('institucion_list', 'list_comment', 'list_currency', 'bancos'));
    }

    public function store(CreateBancoRequest $request)
    {
        $Lapso = Banco::create($request->all());
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok', $messenge);
        Session::flash('class_oper', 'success');
        return redirect()->route('administracion.configuraciones.banco');
    }

    public function edit($id)
    {
        $banco = Banco::findOrFail($id);
        $list_comment = Banco::COLUMN_COMMENTS;
        $institucion_list = Institucion::pluck('name', 'id');
        $list_currency = Currency::pluck('name', 'id');
        return view('administracion.configuraciones.banco.edit', compact('banco', 'institucion_list', 'list_comment', 'list_currency'));
    }

    public function update(UpdatebancoRequest $request, $id)
    {
        $banco = Banco::findOrFail($id);
        $banco->fill($request->all());
        $banco->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok', $messenge);
        Session::flash('class_oper', 'success');
        return redirect()->route('administracion.configuraciones.banco');
    }
}
