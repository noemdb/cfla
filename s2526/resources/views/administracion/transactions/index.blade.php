@extends('administracion.layouts.dashboard.app')

@section('title') - Transacciones Botón de Pago @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
                    <u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Transacciones del Botón de Pago</span> registradas.
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.transactions.table.index')

            </div>
        </div>
    </main>

@endsection

