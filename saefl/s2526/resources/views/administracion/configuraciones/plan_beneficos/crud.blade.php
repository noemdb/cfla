@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.plan_beneficos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}

                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de los <span class=" font-weight-bolder">Planes Benéficos</span> asignados
                </h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.plan_beneficos.table.crud')

            </div>
        </div>
    </main>

@endsection


@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Plan Benéfico, Listado'; </script> @endsection
