@extends('administracion.layouts.dashboard.app')

@section('main')

<main role="main" id="main">
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <div class="btn-group float-right pt-2">
                {{-- @include('administracion.configuraciones.menus.index') --}}
            </div>
            <h3>Duplicar Plan de Evaluación a otra sección</h3>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!!Form::open(['route'=>'administracion.pevaluacions.store_clone','method'=>'POST','id'=>'form-pevaluacions-create','class'=>'form-signin'])!!}

            <div class="row">
                <div class="col-2 p-0">
                    <h5 class="card-title">Resumen</h3>
                    <div class="dropdown-divider mb-0"></div>
                    @include('administracion.pevaluacions.partials.resumen')
                </div>
                <div class="col-10 h-100">
                    <div
                        class="card p-0">
                        <div class="card-header alert-secondary pb-0 mb-0" >
                        <h6>
                            Datos
                        </h6>
                        </div>
                        <div class="card-body p-3">
                            @include('administracion.pevaluacions.form.clone')
                            <div class="align-bottom">
                                <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block mt-1"
                                    value="Registrar" data-id="create" id="btn-create-pevaluacions-{{$pevaluacion->id ?? ''}}">
                                    <i class="{{ $icon_menus['clone'] ?? ''}} fa-1x"></i>
                                    Duplicar
                                </button>
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

@section('scripts')
@parent

@endsection
