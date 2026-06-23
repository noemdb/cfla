@extends('administracion.layouts.dashboard.app')

@section('title') - Listado Notas registradas @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4><u>Listado</u> Notas registradas</h4>
            </div>

            <div class="card-body">
                @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    @include('administracion.boletins.partials.search_crud',['route'=>'administracion.boletins.crud'])

                    @include('administracion.boletins.table.crud')
            </div>
        </div>
    </main>

@endsection
