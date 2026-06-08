@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.crud')
                </div>
                <h4><u>Listado</u> de <b>Puntos de Ajustes</b> asignados</h4>
            </div>

            <div class="card-body">
                @include('administracion.boletins.partials.search_ajuste_crud',['route'=>'administracion.boletins.crud_ajuste'])
                @include('administracion.boletins.table.crud_ajuste')
            </div>
        </div>
    </main>

@endsection

