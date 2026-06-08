@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('representants.prepagos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Notificaciones de Pago</span> registradas</h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('representants.elements.forms.errors')
                @include('representants.elements.messeges.oper_ok')

                {{-- @include('representants.prepagos.form.search',['route'=>'representants.prepagos.crud']) --}}

                @include('representants.prepagos.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Notificaciones de Pago, listado'; </script> @endsection
