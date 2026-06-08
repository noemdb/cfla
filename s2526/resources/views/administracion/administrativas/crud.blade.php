@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    {{-- @include('administracion.administrativas.modal.pdf') --}}
                    {{-- @include('administracion.administrativas.modal.xls') --}}
                    @include('administracion.administrativas.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de Inscripciones Administrativas
                </h4>
            </div>

            <div class="card-body">

                <div class="card-header p-0 m-0 mb-2">
                    @include('administracion.administrativas.form.search',[
                        'route'=>'administracion.administrativas.crud',
                        // 'required_seccion'=>true
                        ])
                </div>

                @include('administracion.administrativas.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript"> document.title = 'SAEFL - Listado de Inscripciones Administrativas'; </script>
@endsection
