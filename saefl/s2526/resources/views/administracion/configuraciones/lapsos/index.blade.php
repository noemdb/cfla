@extends('administracion.layouts.dashboard.app')

@section('title') Lapsos, listado @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.lapsos.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Lapsos</span> registrados</h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.lapsos.table.index')

            </div>
        </div>
    </main>

@endsection
