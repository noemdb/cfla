@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - Registrar @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_promises.menus.create') </div>
                {{-- FIN Menu rapido --}}

                <h3>Registrar una nueva <span class="font-weight-bolder">Promesa de Pago</span></h3>
            </div>

            <div class="card-body">

                <div class="card-body">

                    @include('administracion.elements.forms.errors')
                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::open(['route' => 'administracion.collections.coll_promises.store', 'method' => 'POST', 'id'=>'form-collPolitical-create', 'class'=>'form-signin']) !!}

                        <div class="card bd-callout bd-callout-primary">

                            <h5 class="card-header pb-1 mb-1">
                                <i class="{{ $icon_menus['nuevo'] }} fa-1x text-primary float-right"></i>
                                Datos
                            </h5>

                            <div class="card-body p-2">

                                <div class="row">
                                    <div class="col">

                                        @include('administracion.collections.coll_promises.form.fields')

                                        <button type="submit" class="btn-user-create btn btn-primary btn-block" value="create" data-id="create" id="btn-create">
                                            <i class="far fa-save"></i>
                                            Registrar
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        @include('administracion.collections.coll_promises.partials.resumen.create')
                                    </div>
                                </div>

                            </div>
                        </div>

                    {!! Form::close() !!}

                </div>

            </div>
        </div>
    </main>

@endsection
