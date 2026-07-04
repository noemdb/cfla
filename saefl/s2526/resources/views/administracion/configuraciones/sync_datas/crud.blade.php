@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.descuentos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de los <span class=" font-weight-bolder">Descuentos registrados</span> registrados
                </h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.descuentos.table.crud')

            </div>
        </div>
    </main>

@endsection
