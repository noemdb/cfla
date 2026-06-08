@extends('administracion.layouts.dashboard.app')

@section('title') Posiciones de los estudiantes según su promedio académico por lapso @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.index')
                </div>

                <h4><b class="text-dark">Posiciones de los estudiantes según su promedio académico</b> por lapso</h4>

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                {{-- @include('administracion.elements.forms.errors') --}}

                {{-- @include('administracion.elements.messeges.oper_ok') --}}

                @include('administracion.boletins.form.search.positions',[
                    'route'=>'administracion.boletins.positions',
                    'btn_toprint_lote'=>'true'])

                @include('administracion.boletins.table.positions')

            </div>
        </div>
    </main>

@endsection
