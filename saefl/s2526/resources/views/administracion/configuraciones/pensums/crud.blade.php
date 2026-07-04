@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.pensums.menus.crud')
                </div>
                <h4><u title="Listado especial con botones de acción">Listado</u> de los <span class="font-weight-bolder">Pensums</span> registrados</h4>
            </div>

            <div class="card-body">
                @include('administracion.configuraciones.pensums.table.crud')
            </div>

        </div>
    </main>

@endsection
