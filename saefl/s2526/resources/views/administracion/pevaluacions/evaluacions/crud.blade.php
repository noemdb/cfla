@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    @include('administracion.pevaluacions.evaluacions.menus.crud')
                </div>
                <h4>
                    <i class="{{$icon_menus['crud'] ?? ''}} text-dark" aria-hidden="true"></i>
                    <u>Listado</u> de Evaluaciones registradas
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.pevaluacions.evaluacions.partials.search',['route'=>'administracion.evaluacions.crud'])

                @include('administracion.pevaluacions.evaluacions.table.crud')

            </div>
        </div>
    </main>

@endsection
