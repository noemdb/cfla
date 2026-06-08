@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.evaluacions.menus.index')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['evaluacion'] ?? ''}} text-info" aria-hidden="true"></i>
                    Registro de Evaluaciones
                    {{-- <span class="text-muted" style="opacity: 0.5;">/Indicadores/Logros</span> --}}
                </h3>
                <span class="text-muted small text-capitalize font-light">
                        {{ Auth::user()->profesor->fullname}}
                </span>
            </div>

            <div class="card-body p-1 m-1">

                @include('profesors.evaluacions.partials.search',['route'=>'profesors.evaluacions.index'])

                <h6 class="pb-2 font-weight-bold text-muted"><u title="Listado especial con botones de acción">Listado</u> de Asignaturas</h6>

                @include('profesors.evaluacions.table.index')

            </div>
        </div>
    </main>

@endsection

