@extends('profesors.layouts.dashboard.app')

@section('main')

<main role="main" id="main">
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <div class="btn-group float-right pt-2">
                @include('profesors.evaluacions.menus.create')
            </div>
            <h4 class="pb-0 mb-0">Duplicar Evaluaciones a otra sección</h4>
            <span class="text-muted small text-capitalize font-light">
                {{ Auth::user()->profesor->fullname}}
            </span>
        </div>

        <div class="card-body pt-2">

            @include('profesors.elements.forms.errors')

            @include('profesors.elements.messeges.oper_ok')

            <div class="row">
                <div class="col-sm-3">
                    <h3 class="card-title">Resumen del PE</h3>
                    <div class="dropdown-divider mb-0"></div>
                    @include('profesors.pevaluacions.partials.resumen')
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
                            {!!Form::open(['route'=>'profesors.evaluacions.store_clone','method'=>'POST','id'=>'form-pevaluacions-create','class'=>'form-signin'])!!}
                            @include('profesors.evaluacions.form.clone')
                            <div class="align-bottom">
                                <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block mt-1" value="Registrar" data-id="create" id="btn-create-pevaluacions-{{$pevaluacion->id ?? ''}}">
                                    <i class="{{ $icon_menus['clone'] ?? ''}} fa-1x"></i>
                                    Exportar
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                @if (!empty($pevaluacion->evaluacions->first()))
                    <div class="col-sm-3">
                        <div class="h-100 rounded p-1 m-1 alert-{{ ($pevaluacion->status_eva_complete) ? 'success':'warning' }}">
                            <h5 class="card-title">Lista de Evaluaciones registradas</h3>
                            <div class="dropdown-divider mb-0"></div>
                            @php $evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions:null; @endphp
                            @includewhen(($evaluacions),'profesors.pevaluacions.partials.evaluacion')
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent

@endsection
