@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                <h4>

                    {{-- <a href="{{ route('administracion.representants.libro.pdf') }}" class="btn btn-dark float-right" target="_blank">
                        <i class="fa fa-print" aria-hidden="true"></i>
                    </a> --}}

                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> Deuda años anteriores por estudiante.

                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}

                        {{-- @include('admin.users.menus.index') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                </h4>
            </div>

            <div class="card-body">



                @include('administracion.deudas_anterior.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent

@endsection
