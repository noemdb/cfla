@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.registro_titulos.menus.crud')
                </div>
                <h4> <u title="Listado especial con botenes de acciòn">Listado</u> de las <span class="font-weight-bold">Promociones</span> registradas</h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                {{-- @include('administracion.registro_titulos.form.search',['route'=>'administracion.registro_titulos.crud']) --}}

                @include('administracion.registro_titulos.table.crud')

            </div>
        </div>
    </main>

@endsection
