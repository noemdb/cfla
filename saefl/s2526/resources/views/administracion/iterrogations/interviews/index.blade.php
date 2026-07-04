@extends('administracion.layouts.dashboard.app')

@section('title') - Entrevistas Interactivas @endsection

@section('main')

<main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">

                    {{-- @include('administracion.representants.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}

                <h3>
                    <i class="{{ $icon_menus['crud'] }} fa-1x text-dark"></i>
                    Listado de repuestas de las entrevistas interactivas
                </h3>
            </div>

            <div class="card-body">

                @include('administracion.iterrogations.interviews.table.index')

            </div>
        </div>
    </main>

@endsection
