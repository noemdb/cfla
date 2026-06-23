@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4><u title="Listado especial con botones de acción">Listado</u> de las Constancias de Prosecución</h4>
            </div>

            <div class="card-body">
                @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    @include('administracion.inscripciones.partials.search_prosecucion',['route'=>'administracion.inscripciones.prosecucion'])

                    @include('administracion.inscripciones.table.prosecucion')
            </div>
        </div>
    </main>

@endsection
