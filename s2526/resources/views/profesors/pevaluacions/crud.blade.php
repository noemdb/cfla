@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.pevaluacions.menus.index')
                </div>
                <h3 class="pb-0 mb-0">
                    <i class="{{$icon_menus['pevaluacion'] ?? ''}} text-primary" aria-hidden="true"></i>
                    <u class="text-dark">Carga Académica</u> Planes de Evaluación Asignados
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span>
            </div>

            <div class="card-body p-1 m-1">

                @include('profesors.pevaluacions.partials.search',['route'=>'profesors.pevaluacions.crud'])

                <h6 class="pb-2 font-weight-bold text-muted"><u title="Listado especial con botones de acción">Listado</u> de los Planes de Evaluación Asignados</h6>

                @include('profesors.pevaluacions.table.crud')

            </div>
        </div>
    </main>

@endsection
