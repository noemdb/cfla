<?php

namespace App\Http\Controllers\Administracion\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\Mbancario;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\NomConceptoPago;
use App\Models\app\Planpago\Prepago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Profesor\Pevaluacion\Edescriptiva;
use App\Models\app\Planpago\ExchangeRate;

class FillPartialController extends Controller
{
    public function ExchangeRateAmmount($date_payment)
    {
        $exchange = ExchangeRate::where('date',$date_payment)->first();
        return ($exchange) ? $exchange->ammount: null;
    }

    public function PrepagoCreateModal($id, Request $request)
    {
        $mbancario = Mbancario::findOrFail($id);
        $list_representant = Representant::list_representant();
        $metodo_pago = MetodoPago::find(3); //Transferencia Electrónica

        if($request->ajax()){
            return view('administracion.prepagos.show.prepago.modal', compact('mbancario','list_representant','metodo_pago'));
            // /home/userpc/code/s2021/resources/views/administracion/prepagos/show//modal.blade.php
        }
    }

    public function listRegistroRagoRepresentant($id, Request $request)
    {
        $representant = Representant::findOrFail($id);
        $registropagos = RegistroPago::where('representant_id',$id)->get();
        if($request->ajax()){
            return view('administracion.registropagos.show.modal.list', compact('representant','registropagos'));
        }
    }

    public function inscripcionsGrupoEstableUpdate($id, Request $request)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $list_grupo_estables = GrupoEstable::select('id', 'name',DB::raw("CONCAT(name,' || ',code) as fullname"))->orderby('name','asc')->pluck('fullname', 'id');

        if($request->ajax()){
            return view('administracion.inscripciones.show.grupo_estable.modal', compact('inscripcion','list_grupo_estables'));
        }
    }
    public function PrepagoAbonoModal($id, Request $request)
    {
        $prepago = Prepago::findOrFail($id);
        $representant = $prepago->representant;
        $estudiant = $representant->estudiants->first();

        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');
        $banco_list         = Banco::banco_list();

        if($request->ajax()){
            return view('administracion.prepagos.show.abono.modal', compact('prepago','representant','estudiant','method_pay_list','banco_list'));
        }
    }
    public function PrepagoPagoModal($id, Request $request)
    {
        $prepago = Prepago::findOrFail($id);
        $representant = $prepago->representant;
        $estudiant = $representant->estudiants->first();

        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');
        $banco_list         = Banco::banco_list();

        if($request->ajax()){
            return view('administracion.prepagos.show.pago.modal', compact('prepago','representant','estudiant','method_pay_list','banco_list'));
        }
    }

    public function creditoafavor($id, Request $request)
    {
        $creditoafavor = CreditoAFavor::withTrashed()->findOrFail($id);

        if($request->ajax()){
            return view('administracion.creditoafavors.show.modal', compact('creditoafavor'));
        }
    }

    public function EdescriptivaDetails($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);

        $list_comment = Edescriptiva::COLUMN_COMMENTS;

        if($request->ajax()){
            return view('administracion.edescriptivas.show.modal.details', compact('estudiant','list_comment'));
        }
    }

    public function EdescriptivaCreate($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);

        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_lapso->put(null,'Final');

        $list_comment = Edescriptiva::COLUMN_COMMENTS;

        if($request->ajax()){
            return view('administracion.edescriptivas.show.modal.create', compact('estudiant','list_lapso','list_comment'));
        }
    }

    public function EstudiantCard($id, Request $request)
    {
        $estudiant = Estudiant::findOrFail($id);

        if($request->ajax()){
            return view('administracion.estudiants.show.modal.card', compact('estudiant'));
        }
    }

    public function RepresentantHistoricoPago($id, Request $request)
    {
        $representant = Representant::findOrFail($id);

        $registro_pago_combinados = RegistroPagoCombinado::where('representant_id',$id)->get();

        if($request->ajax()){
            return view('administracion.representants.show.modal.historico', compact('representant','registro_pago_combinados'));
        }
    }

    public function RegistroPagoModal($id, Request $request)
    {
        $registropago = RegistroPago::findOrFail($id);
        // $registro_pago_combinado = RegistroPagoCombinado::findOrFail($registropago->registro_pago_combinado_id);
        $registro_pago_combinado = $registropago->registro_pago_combinado;
        // $registropagos = (!empty($registro_pago_combinado->registropagos)) ? $registro_pago_combinado->registropagos:collect();
        $id_modal = 'modal_registropago_'.$registropago->id;

        $creditos_aplicados= CreditoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();
        $credito_generado= CreditoAFavor::withTrashed()->where('registro_pago_id', $registropago->id)->first();
        $abonos_aplicados = AbonoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();
        $descuentos_aplicados = DescuentoAplicado::withTrashed()->where('registro_pago_id', $registropago->id)->get();

        if($request->ajax()){
            return view('administracion.registropagos.show.modal', compact('registropago','registro_pago_combinado','id_modal','creditos_aplicados','credito_generado','abonos_aplicados','descuentos_aplicados'));
        }
    }
    public function RegistroPagoCombinadoModal($id, Request $request)
    {
        $registro_pago_combinado = RegistroPagoCombinado::findOrFail($id);
        $id_modal = 'modal_registropago';

        if($request->ajax()){
            return view('administracion.registropagos.show.modal', compact('registro_pago_combinado','id_modal'));
        }
    }

    public function RegistroPagoCombinadoModalResume($id, Request $request)
    {
        $registro_pago_combinado = RegistroPagoCombinado::findOrFail($id);

        if($request->ajax()){
            return view('administracion.registropagos.show.registro_pago_combinados.modal', compact('registro_pago_combinado'));
        }
    }

    public function ShowConceptoPago($id, Request $request)
    {
        $concepto_pago = ConceptoPago::findOrFail($id);
        $modal_id = 'modal_cuentaxpagar_'.$concepto_pago->id;

        if($request->ajax()){
            return view('administracion.configuraciones.concepto_pagos.show.modal', compact('concepto_pagos','modal_id'));
        }
    }
}
