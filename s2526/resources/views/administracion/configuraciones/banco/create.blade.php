@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.banco.menus.create')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Registrar nuevo <span class="font-weight-bolder">Banco</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">
                        <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
                        Datos
                    </h5>

                    <div class="card-body m-1 p-1">

                        {{-- <div class="container m-1 p-1"> --}}

                        <div class="row">

                            <div class="col-10">
                                {!! Form::open([
                                    'route' => 'administracion.configuraciones.banco.store',
                                    'method' => 'POST',
                                    'id' => 'form-inscripcion-create',
                                    'class' => 'form-signin',
                                ]) !!}
                                @include('administracion.configuraciones.banco.form.fields')
                                {!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}
                                {!! Form::close() !!}
                            </div>

                            <div class="col-2">
                                @include('administracion.configuraciones.banco.partials.resumen.create')
                            </div>

                        </div>

                        {{-- </div> --}}

                    </div>
                </div>

            </div>

        </div>
    </main>
@endsection

@section('title')
    Registrar Banco
@endsection
