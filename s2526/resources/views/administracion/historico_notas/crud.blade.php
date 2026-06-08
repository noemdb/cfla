@extends('administracion.layouts.dashboard.app')

@section('title') - Listado: Histórico de Notas @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.historico_notas.menus.crud')
                </div>
                <h4> <u title="Listado especial con botenes de acciòn">Listado</u> de estudiantes con formato para la <span class="font-weight-bold">Certificación de Calificaciones</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.historico_notas.form.search_crud',['route'=>'administracion.historico_notas.crud'])

                <hr>

                @include('administracion.historico_notas.table.crud')

            </div>
        </div>
    </main>

@endsection
