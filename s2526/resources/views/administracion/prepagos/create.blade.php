@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-primary">
                <h4>
                    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
                    Registrar <span class="font-weight-bolder">Notificación de Pago</span>
                </h4>
                {{-- <h4><u title="Listado especial con botones de acción">Listado</u> de los <span class="font-weight-bolder">Movimientos Bancarios CSV</span> registrados</h4> --}}
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.prepagos.form.search.create',['route'=>'administracion.prepagos.create'])

                <div class="border rounded">
                    <p class="card-title text-left alert alert-secondary">
                        Listado de los <span class="font-weight-bolder">Movimientos Bancarios CSV</span> sin notificaciones de pago asociada
                    </p>

                    <div class=" p-2">
                        @include('administracion.prepagos.table.create')

                    </div>

                </div>


            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Registrar Notificación de Pago'; </script> @endsection

