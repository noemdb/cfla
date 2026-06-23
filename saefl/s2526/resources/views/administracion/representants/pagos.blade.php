@extends('administracion.layouts.dashboard.app')

@section('title')
    @parent
    - Saldos Representante {{ Carbon\Carbon::now()->format('Y-m-d h:m') }}
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">

                <div class="btn-group float-right">
                    @include('administracion.representants.menus.index')
                </div>

                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> del total de pagos por
                    representante.
                </h4>
            </div>

            <div class="card-body p-1 m-1">

                @include('administracion.representants.form.pagos', [
                    'route' => 'administracion.representants.pagos',
                ])

                @include('administracion.representants.table.pagos')

            </div>

        </div>

    </main>
@endsection
