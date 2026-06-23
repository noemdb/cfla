@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-danger">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('administracion.inscripciones.menus.retiro')

                </div>
                {{-- FIN Menu rapido --}}

                <h4>
                    <i class="{{ $icon_menus['crud'] }} fa-1x"></i>
                    <span class="font-weight-bolder">Estudiantes</span> no matrículados.
                </h4>
            </div>

            <div class="card-body">

                <div class="card-header p-0 m-0 mb-2">

                    @include('administracion.inscripciones.form.search.unregistered', [
                        'route' => 'administracion.inscripciones.unregistered',
                    ])

                </div>

                <div class=" border rounded p-1">
                    @include('administracion.inscripciones.table.unregistered')
                </div>

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
@endsection
