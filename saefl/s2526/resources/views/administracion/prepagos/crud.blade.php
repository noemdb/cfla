@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
                    <u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Notificaciones de Pago</span> registradas
                </h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.prepagos.form.search.crud',['route'=>'administracion.prepagos.crud'])

                @include('administracion.prepagos.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Notificaciones de Pago, listado'; </script> @endsection
