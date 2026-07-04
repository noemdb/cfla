@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-0 pb-2">
                        @include('administracion.configuraciones.grupo_estables.menus.index')
                    </div>
                    {{-- FIN Menu rapido --}}

                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                    <u title="Listado especial con botenes de acciòn">Listado</u> de los <span class="font-weight-bold">Grupos Estables</span> registrados

                </h4>
            </div>

            <div class="card-body">
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.grupo_estables.table.index')
                {{-- @include('administracion.configuraciones.grupo_estables.arcadat') --}}

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
@endsection
