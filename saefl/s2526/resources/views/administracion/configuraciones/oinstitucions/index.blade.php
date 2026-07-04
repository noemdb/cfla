@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-secondary">
                <h4>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-0 pb-2">
                        @include('administracion.configuraciones.oinstitucions.menus.index')
                    </div>
                    {{-- FIN Menu rapido --}}

                    <u title="Listado especial con botenes de acciòn">Listado</u> de las <span class="font-weight-bolder">Instituciones</span> registradas

                </h4>
            </div>

            <div class="card-body">
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.oinstitucions.table.index')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Instituciones, listado'; </script> @endsection