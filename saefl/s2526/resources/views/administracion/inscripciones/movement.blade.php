@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4><u title="Listado especial con botones de acción">Listado</u> de Inscripciones Académicas - Movimientos y actualizaciones</h4>
            </div>

            <div class="card-body">

                @include('administracion.inscripciones.partials.movement',['route'=>'administracion.inscripciones.movement'])

                @include('administracion.inscripciones.table.movement')

            </div>
            
        </div>
    </main>

@endsection