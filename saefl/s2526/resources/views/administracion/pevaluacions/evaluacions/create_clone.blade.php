@extends('administracion.layouts.dashboard.app')

@section('main')

<main role="main" id="main" class="col-md-10 ml-sm-auto col-lg-10">
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <div class="btn-group float-right pt-2">
                {{-- @include('administracion.configuraciones.menus.index') --}}
            </div>
            <h4> Duplicar Evaluaciones a otra sección</h4>
        </div>

        <div class="card-body pt-2">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!!Form::open(['route'=>'administracion.evaluacions.store_clone','method'=>'POST','id'=>'form-pevaluacions-create','class'=>'form-signin'])!!}

            <div class="row">
                <div class="col-sm-3">
                    <h5 class="card-title">Resumen del PE</h5>
                    <div class="dropdown-divider mb-0"></div>
                    @include('administracion.pevaluacions.partials.resumen') 
                </div>
                <div class="col-6 h-100">
                    <div
                        class="card p-0">
                        <div class="card-header alert-secondary pb-0 mb-0" >
                        <h6>
                            Datos
                        </h6>
                        </div>
                        <div class="card-body p-3">
                            @include('administracion.pevaluacions.evaluacions.form.clone') 
                            <div class="align-bottom">
                                <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block mt-1" value="Registrar" data-id="create" id="btn-create-pevaluacions-{{$pevaluacion->id ?? ''}}">
                                    <i class="{{ $icon_menus['clone'] ?? ''}} fa-1x"></i>
                                    Duplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <h6 class="card-title">Lista de las evaluaciones del Plan asociado a ésta evaluación</h6>
                    <div class="dropdown-divider mb-0"></div>
                    @php $evaluacions = (!empty($evaluacion->pevaluacion->evaluacions->first())) ? $evaluacion->pevaluacion->evaluacions : null; @endphp                                      
                    @includewhen(($evaluacions),'administracion.pevaluacions.partials.evaluacion')
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