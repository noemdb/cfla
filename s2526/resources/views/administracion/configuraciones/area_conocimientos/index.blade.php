@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.area_conocimientos.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Áreas de Conocimiento</span> registradas</h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-area-tab" data-toggle="tab" href="#nav-area" role="tab" aria-controls="nav-area" aria-selected="true">Áreas de Conocimiento</a>
                        <a title="Asignaturas abscritas a las Áreas Conocimiento" class="nav-item nav-link" id="nav-campo-tab" data-toggle="tab" href="#nav-campo" role="tab" aria-controls="nav-campo" aria-selected="false">Asignaturas abscritas</a>
                    </div>
                </nav>
                <div class="tab-content border border-top-0" id="nav-tabContent">
                    <div class="tab-pane fade show active p-2" id="nav-area" role="tabpanel" aria-labelledby="nav-area-tab">
                        @include('administracion.configuraciones.area_conocimientos.table.index')
                    </div>
                    <div class="tab-pane fade p-2" id="nav-campo" role="tabpanel" aria-labelledby="nav-campo-tab">
                        @includeif('administracion.configuraciones.area_conocimientos.table.campo')
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Área de Conocimiento, listado'; </script> @endsection
