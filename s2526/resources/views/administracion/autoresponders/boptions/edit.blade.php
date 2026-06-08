@extends('administracion.layouts.dashboard.app')

@section('title') Autorespondedor - Mensajería Instantanea @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.autoresponders.boptions.menus.edit') </div>
                {{-- FIN Menu rapido --}}

                <h3>Actualizar <span class="font-weight-bolder">Opciones</span></h3>
            </div>

            <div class="card-body">

                {{-- <div class="card-body"> --}}

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::model($boption,['route' => ['administracion.autoresponders.boptions.update', $boption->id], 'method' => 'PUT', 'id'=>'form-update-collPolitical'.$boption->id, 'role'=>'form']) !!}

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">

                                @include('administracion.autoresponders.boptions.form.fields')

                                <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update">
                                    <i class="far fa-save"></i>
                                    Actualizar
                                </button>
                            </div>
                            <div class="col-3">
                                {{-- @include('administracion.autoresponders.boptions.partials.resumen.edit') --}}
                            </div>
                        </div>
                    </div>

                {!! Form::close() !!}

                {{-- </div> --}}

            </div>
        </div>
    </main>

@endsection
