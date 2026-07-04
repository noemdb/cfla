@extends('administracion.layouts.dashboard.app')

@section('title') Autorespondedor - Mensajería Instantanea @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.autoresponders.bmains.menus.edit') </div>
                {{-- FIN Menu rapido --}}

                <h3>Actualizar <span class="font-weight-bolder">Autorespondedor</span></h3>
            </div>

            <div class="card-body">

                <div class="card-body">

                    @include('administracion.elements.forms.errors')
                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($bmain,['route' => ['administracion.autoresponders.bmains.update', $bmain->id], 'method' => 'PUT', 'id'=>'form-update-collPolitical'.$bmain->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">

                            <h5 class="card-header pb-1 mb-1">
                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x text-dark float-right"></i>
                                Datos
                            </h5>

                            <div class="card-body p-2">

                                <div class="row">
                                    <div class="col">

                                        @include('administracion.autoresponders.bmains.form.fields')

                                        <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        {{-- @include('administracion.autoresponders.bmains.partials.resumen.edit') --}}
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
