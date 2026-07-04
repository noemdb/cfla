@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.concepto_pagos.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Registrar <span class="font-weight-bolder">Cuenta de Cobro</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">Datos</h5>

                    <div class="card-body">

                        <div class="container">
                            <div class="row">
                                <div class="col-8">
                                    {!! Form::open(['route' => 'administracion.configuraciones.concepto_pagos.store', 'method' => 'POST', 'id'=>'form-concepto_pagos-create', 'class'=>'form-signin']) !!}

                                        @include('administracion.configuraciones.concepto_pagos.form.field')

                                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Registrar
                                        </button>

                                    {!! Form::close() !!}

                                </div>
                                <div class="col-4">
                                    @include('administracion.configuraciones.concepto_pagos.partials.resume.create')
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Conceptos de Cobro, crear'; </script> @endsection
