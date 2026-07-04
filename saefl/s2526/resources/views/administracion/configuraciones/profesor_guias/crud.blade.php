@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.profesor_guias.menus.crud')
                </div>
                <h4><u>Listado</u> de <span class="font-weight-bolder">Profesores Guía</span> registrados</h4>
            </div>

            <div class="card-body">
                @include('administracion.configuraciones.profesor_guias.form.search',['route'=>'administracion.configuraciones.profesor_guias.crud'])
                @include('administracion.configuraciones.profesor_guias.table.crud')
            </div>
        </div>
    </main>

@endsection
