@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.asignaturas.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Listado de <span class="font-weight-bolder">Asignatura</span></h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')
                @include('administracion.elements.forms.errors')

                @include('administracion.configuraciones.asignaturas.table.index')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Asignatura, listado'; </script> @endsection
