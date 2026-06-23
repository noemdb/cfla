@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    @include('administracion.boletin_revisions.menus.crud')
                </div>
                <h4><u title="Listado escial con botones de acción">Listado</u> de la <span class=" font-weight-bold"> Revisión de Notas</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.boletin_revisions.partials.search_crud',['route'=>'administracion.boletin_revisions.crud'])

                @include('administracion.boletin_revisions.table.crud')


            </div>
        </div>
    </main>

@endsection
