@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
                    <u title="Listado especial con botones de acción">Listado</u> de los <span class="font-weight-bolder">Reportes de Pago</span> registrados por Representantes
                </h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.payments.form.search.crud',['route'=>'administracion.payments.crud'])

                @include('administracion.payments.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Reportes de Pago, listado'; </script> @endsection
