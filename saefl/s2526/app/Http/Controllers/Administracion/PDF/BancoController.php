<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Institucion\Banco;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\MetodoPago;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class BancoController extends Controller
{
    public function libro_facturacion(Request $request)
    {
        $orientacion = 'portrait';
        $paper  = 'lettet';
        $status_pdf = true;

        $id = $request->banco_id;

        $method_pay_id = (!empty($request->method_pay_id)) ? $request->method_pay_id : null; //dd($method_pay_id);

        $finicial = (!empty($request->finicial)) ? $request->finicial : '2019-01-01';

        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : '2019-12-31';

        $finicial_late = Carbon::createFromFormat('Y-m-d H:i', $finicial . ' 00:00');
        $ffinal_late = Carbon::createFromFormat('Y-m-d H:i', $ffinal . ' 23:59');

        $status_late_payment = (!empty($request->status_late_payment)) ? $request->status_late_payment : null;

        $banco = Banco::findOrFail($id);

        $institucion = $banco->institucion;

        $ingresos = Ingreso::select('ingresos.*', DB::raw('sum(ingresos.ingreso_ammount) as ingreso_ammount_total'))
            ->where('banco_id', $banco->id)
            ->wherenull('ingresos.deleted_at')
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

        $ingresos = $ingresos->get()->filter(function ($ingreso) {
            return !is_null($ingreso->invoice_number);
        });

        $metodos = MetodoPago::all()->map(function ($metodo) use ($banco, $status_late_payment, $finicial, $ffinal, $finicial_late, $ffinal_late, $method_pay_id) {
            $query = Ingreso::where('method_pay_id', $metodo->id)
                ->where('banco_id', $banco->id)
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
        })->filter(); // eliminar los nulls

        $view =  View::make('administracion.configuraciones.banco.libro.pdf', compact('ingresos', 'metodos', 'banco', 'institucion', 'finicial', 'ffinal', 'status_late_payment'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('libros_farturacion');
        // return $view;

        $name_file = 'libros_farturacion';

        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }

    public function libro_facturacion_no_asociados(Request $request)
    {
        $orientacion = 'portrait';
        $paper  = 'lettet';
        $status_pdf = true;

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

        $view =  View::make('administracion.configuraciones.banco.libro.abonoPdf', compact('ingresos', 'metodos', 'banco', 'institucion', 'finicial', 'ffinal', 'status_late_payment'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'libros_farturacion';

        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }
}
