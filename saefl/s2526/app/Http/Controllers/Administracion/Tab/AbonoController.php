<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Estudiant\CreateAbonoRequest;
use App\Http\Requests\Administracion\Estudiant\UpdateAbonoRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\Currency;

class AbonoController extends Controller
{
    public $currency_primary, $currency_secondary;
    public function __construct()
    {
        $this->middleware(['auth', 'is_admon']);
        //$this->middleware('is_admin')->only(['edit','destroy']);
        $this->currency_primary = Currency::where('status_primary', true)->orderBy('created_at', 'asc')->first();
        $this->currency_secondary = Currency::where('status_secondary', true)->orderBy('created_at', 'asc')->first();
    }

    public function crud(Request $request)
    {
        $abonos = collect();
        $total_abono = null;

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null;
        $banco_id           = (!empty($request->banco_id)) ? $request->banco_id : null;
        $ci_representant                 = (!empty($request->ci_representant)) ? $request->ci_representant : null;
        $number_i_pay       = (!empty($request->number_i_pay)) ? $request->number_i_pay : null;
        $state              = (!empty($request->state)) ? $request->state : null;
        $status_matriculations = (!empty($request->status_matriculations)) ? $request->status_matriculations : null;

        if (count($request->all()) > 0) {

            $abonos = Abono::OrderBy('abonos.created_at')
                ->withTrashed()
                ->select('abonos.*', 'ingresos.ingreso_ammount', 'ingresos.exchange_ammount')
                ->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
                ->join('representants', 'representants.id', '=', 'ingresos.representant_id')
                ->leftjoin('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
                ->leftjoin('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')

                ->whereNull('ingresos.deleted_at')
                ->whereNull('abono_aplicados.deleted_at')
                ->whereNull('registro_pagos.deleted_at')

                ->groupby('abonos.id');

            $abonos = (isset($finicial)) ? $abonos->wheredate('ingresos.date_transaction', '>=', $finicial) : $abonos;
            $abonos = (isset($ffinal)) ? $abonos->wheredate('ingresos.date_transaction', '<=', $ffinal) : $abonos;
            $abonos = (isset($banco_id)) ? $abonos->where('ingresos.banco_id', $banco_id) : $abonos;

            if ($ci_representant) {
                $abonos = $abonos->Where('representants.ci_representant', 'like', "%" . $ci_representant . "%");
            }

            if ($state == "NO APLICADO") {
                $abonos = $abonos->whereNull('abonos.deleted_at');
            }
            if ($state == "APLICADO") {
                $abonos = $abonos->whereNotNull('abonos.deleted_at');
            }

            if (isset($status_matriculations)) {
                $abonos = $abonos->where('abonos.status_matriculations', true);
            }

            $abonos = (isset($number_i_pay)) ? $abonos->where('ingresos.number_i_pay', 'like', "%" . $number_i_pay . "%") : $abonos;

            $abonos = $abonos->get();

            $total_abono = $abonos->sum('ingreso_ammount');
        }

        $currency_primary = $this->currency_primary;
        $currency_secondary = $this->currency_secondary;

        $list_banco = Banco::list_public_bancos();

        return view('administracion.abonos.crud', compact('abonos', 'currency_primary', 'currency_secondary', 'request', 'finicial', 'ffinal', 'banco_id', 'ci_representant', 'number_i_pay', 'state', 'status_matriculations', 'list_banco'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->get('search'))) {
            $search = $request->get('search');
            $arr_get = ['search' => $search];
            $estudiants = Estudiant::name($arr_get)->active('true')->OrderBy('created_at', 'desc')->get();

            return view('administracion.abonos.index', compact('estudiants', 'search'));
        } else {
            $search = '';
            return view('administracion.abonos.index', compact('search'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $estudiant = Estudiant::findOrFail($id);
        $banco_list         = Banco::banco_list();
        $method_pay_list    = MetodoPago::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_divisas = ['1' => '1', '2' => '2', '5' => '5', '10' => '10', '20' => '20', '50' => '50', '100' => '100'];
        return view('administracion.abonos.create', compact('estudiant', 'method_pay_list', 'banco_list', 'list_divisas'));
    }

    public function store(CreateAbonoRequest $request)
    {
        // dd($request->all());
        $id = $request->estudiant_id;
        $estudiant = Estudiant::findOrFail($id);
        $representant = $estudiant->representant;
        $search = $estudiant->ci_estudiant;

        $date_payment = $request->date_payment;

        $exchange_rate_current = ExchangeRate::whereDate('date', $date_payment)->first();
        $exchange_id = ($exchange_rate_current) ? $exchange_rate_current->id : null;
        $exchange_ammount = ($exchange_rate_current) ? $request->ingreso_ammount / $exchange_rate_current->ammount : null;

        $ingreso = Ingreso::create([
            'estudiant_id' => $estudiant->id,
            'representant_id' => $representant->id,
            'method_pay_id' => $request->method_pay_id,
            'banco_id' => $request->banco_id,
            'number_i_pay' => $request->number_i_pay,
            'date_transaction' => $request->date_transaction,
            'date_payment' => $request->date_payment,
            'ingreso_ammount' => $request->ingreso_ammount,
            'exchange_rate_id' => $exchange_rate_current?->id,
            'exchange_ammount' => $exchange_ammount,
            'status_late_payment' => $request->status_late_payment,
            'exchange_ammount_late_payment' => $request->exchange_ammount_late_payment,
            'ingreso_observations' => $request->ingreso_observations,
            'status_late_payment' => $request->status_late_payment,
            'exchange_ammount_late_payment' => $request->exchange_ammount_late_payment,
            'person_bill_ci' => $representant->ci_representant,
            'person_bill_name' => $representant->name,
        ]);

        $abono = Abono::create([
            'representant_id' => $estudiant->representant->id,
            'estudiant_id' => $estudiant->id,
            'ingreso_id' => $ingreso->id,
            'abono_description' => $request->abono_description,
            'status_matriculations' => $request->status_matriculations,
        ]);

        $messenge = trans('db_oper_result.oper_ok');
        $operation = 'create';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        Session::flash('operp_ok', 'Registros guardado exitosamente');
        return redirect()->route('administracion.abonos.create', compact('id', 'estudiant', 'search'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\app\Estudiante\Retiro  $retiro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $abono = Abono::findOrFail($id);
        $ingreso = $abono->ingreso;

        $abono->delete();
        $ingreso->delete();
        $ingreso->forceDelete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }
        Session::flash('operp_ok', $messenge);
        return view('administracion.abonos.crud');
    }

    public function edit($id)
    {
        $abono = Abono::with('ingreso', 'estudiant', 'representant')->findOrFail($id);
        $estudiant = $abono->estudiant;

        return view('administracion.abonos.edit', compact('abono', 'estudiant'));
    }

    public function update(UpdateAbonoRequest $request, $id)
    {
        $abono = Abono::findOrFail($id);

        $abono->update([
            'abono_description' => $request->abono_description,
            'status_matriculations' => $request->status_matriculations,
        ]);

        $messenge = trans('db_oper_result.oper_ok');
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => 'update',
            ]);
        }

        Session::flash('operp_ok', 'Abono actualizado correctamente.');
        return redirect()->route('administracion.abonos.crud');
    }
}
