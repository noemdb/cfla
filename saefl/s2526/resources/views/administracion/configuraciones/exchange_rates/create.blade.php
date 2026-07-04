@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.exchange_rates.menus.create')
                </div>
                {{-- FIN Menu rapido --}}

                <h4>Registrar nueva <span class="font-weight-bolder">Tasa de Cambio</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open(['route' => 'administracion.configuraciones.exchange_rates.store', 'method' => 'POST', 'id'=>'form-exchange_rates-create', 'class'=>'form-signin']) !!}

                    <div class="card bd-callout bd-callout-primary">

                        <h5 class="card-header pb-1 mb-1">
                            <i class="{{ $icon_menus['nuevo'] }} fa-1x text-primary float-right"></i>
                            Datos
                        </h5>

                        <div class="card-body p-2">

                            <div class="row">
                                <div class="col-9">

                                    @include('administracion.configuraciones.exchange_rates.form.fields')

                                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-create">
                                        <i class="far fa-save"></i>
                                        Registrar
                                    </button>
                                </div>
                                <div class="col-3">
                                    @include('administracion.configuraciones.exchange_rates.partials.resume.create')
                                </div>
                            </div>

                        </div>
                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('style')
    @parent
@endsection

@section('title') Registrar Tasa de cambio @endsection
