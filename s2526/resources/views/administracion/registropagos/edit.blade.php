@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        @php
            $total_pagado = $cuentaxpagar->TotalMontoConceptosPagados($estudiant->id);
            $monto_descuento = $estudiant->descuento_ammount($cuentaxpagar->id);
            $descuento = 1 - ($monto_descuento / 100);
            $total_concepto = ($monto_descuento>0) ? ( $cuentaxpagar->SumaConceptos() * (1 - ($monto_descuento / 100))): $cuentaxpagar->SumaConceptos();
            $total_concepto_descuento = $cuentaxpagar->SumaConceptosDescuentos($estudiant->id);
            $total_a_pagar = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id);
        @endphp

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos de la Registro de pagos<br>
                    <small class="text-default">
                        {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                    </small>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        {{-- @include('administracion.configuraciones.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')
                    @php
                        $form = 'form-update-registropago_'.$registropago->id;
                    @endphp

                    {{-- {!! Form::model($ingreso,['route' => ['administracion.registropagos.update', $ingreso->id], 'method' => 'PUT', 'id'=>$form, 'role'=>'form']) !!} --}}
                    {!! Form::model($ingreso,['route' => ['administracion.registropagos.update', $registropago->id], 'method' => 'PUT', 'id'=>$form, 'role'=>'form']) !!}
                    {{-- {!! Form::model($estudiant,['route' => ['administracion.estudiants.update', $estudiant->id], 'method' => 'PUT', 'id'=>'form-update-estudiant_'.$estudiant->id, 'role'=>'form']) !!} --}}

                        <div class="card bd-callout bd-callout-primary">

                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Actualizar Registro de pago
                            </h4>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                        @include('administracion.registropagos.deck.card.estudiant_simple')
                                    </div>
                                    <div class="col-7">
                                        {{-- @include('administracion.registropagos.form.fields.transaccion')   --}}
                                        @include('administracion.registropagos.partial.navtabs')
                                        {{-- @include('administracion.estudiants.form.fields') --}}

                                        @php
                                            $btn_update = 'btn-update-inscripcion-'.$registropago->id
                                        @endphp
                                        <button type="submit" class="btn-registropago-edit btn btn-primary btn-block" value="Actualizar" data-id="create" id="{{$btn_update ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>
                                    <div class="col-2 text-right small pl-0">
                                        @include('administracion.registropagos.partial.resumen')
                                    </div>
                                </div>

                            </div>
                        </div>


                    {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
    $(document).ready(function() {
        $('#{{$form ?? ''}}').find('textarea, button, select').attr('disabled','disabled');
        $('#method_pay_id').removeAttr('disabled','disabled').attr('enabled','enabled');
        $('#date_transaction').attr('disabled','disabled');
        $('#ingreso_ammount').attr('disabled','disabled');
        $('#banco_id').removeAttr('disabled','disabled').attr('enabled','enabled');
        $('#number_i_pay').removeAttr('disabled','disabled').attr('enabled','enabled');
        $('#ingreso_observations').removeAttr('disabled','disabled').attr('enabled','enabled');
        $('#{{$btn_update ?? ''}}').removeAttr('disabled','disabled').attr('enabled','enabled');
    });
</script>
@endsection
