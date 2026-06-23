@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-info">
                {{-- <div class="btn-group float-right pt-2">
                    @include('administracion.pevaluacions.evaluacions.menus.index')
                </div> --}}

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['carga'] ?? ''}} text-info" aria-hidden="true"></i>
                    Asignación de Evaluaciones
                </h3>

            </div>

            <div class="card-body p-1 m-1">

                <h5 class="card-title">Buscar Plan de Evaluación</h5>

                @include('administracion.pevaluacions.evaluacions.partials.search',['route'=>'administracion.evaluacions.index'])

                {{-- @includewhen($pevaluacions->IsNotEmpty(),'administracion.pevaluacions.evaluacions.table.index') --}}
                @include('administracion.pevaluacions.evaluacions.table.index')

            </div>
        </div>
    </main>

@endsection



