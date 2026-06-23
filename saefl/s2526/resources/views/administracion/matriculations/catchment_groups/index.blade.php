@extends('administracion.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.matriculations.catchment_groups.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Listado</u> de los <span class="font-weight-bolder">Grupos de Captación</span> registrados</h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.matriculations.catchment_groups.table.index')

            </div>
        </div>
    </main>
    

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection