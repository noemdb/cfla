@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary pb-0 mb-0 ">
                <h3>
                    Actualizar la Cuenta de Cobro {{$conceptopago->name ?? ''}}
                </h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::model($conceptopago,['route' => ['administracion.configuraciones.concepto_pagos.update', $conceptopago->id], 'method' => 'PUT', 'id'=>'form-update-cuentaxpagar_'.$conceptopago->id, 'role'=>'form']) !!}

                    <div class="card bd-callout bd-callout-{{$conceptopago->status_active=='true'  ? 'primary':'danger'}}">
                        <h5 class="card-header">
                            Datos
                        </h5>
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="p-1">
                                            {{$conceptopago->status_discount ?? ''}}
                                            @include('administracion.configuraciones.concepto_pagos.form.field')

                                            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-conceptopago-{{$conceptopago->id ?? ''}}">
                                                <i class="far fa-save"></i>
                                                Actualizar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        @include('administracion.configuraciones.concepto_pagos.partials.resume.edit')
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                {!! Form::close() !!}
            </div>

        </div>
    </main>


@endsection




