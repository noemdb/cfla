@extends('profesors.layouts.dashboard.app')

@section('title') SAEFL - Profesor - Profesor Guía @endsection

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h3 class="pb-0 mb-0">
                    <i class="{{$icon_menus['profesor_guias'] ?? ''}} text-primary" aria-hidden="true"></i>
                    <u class="text-dark">Listado Especial</u> 
                    <ul class="small">
                        <li class="small">Formato sabana con notas registradas de los grados.</li>
                        <li class="small">Informe de Resultados Diagnóstico Académico.</li>
                    </ul>
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}} [{{ Auth::user()->profesor->id ?? '' }}]
                </span>
            </div>

            <div class="card-body">
                @include('profesors.profesor_guias.table.index')
            </div>

        </div>
    </main>

@endsection





