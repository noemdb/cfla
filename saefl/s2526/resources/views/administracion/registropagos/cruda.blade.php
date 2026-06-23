@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">
                <h4>
                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de Pagos Registrados

                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}
                        {{-- @include('admin.users.menus.index') --}}
                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                </h4>
            </div>

            <div class="card-body">

                <table class="table text-left table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Total General</th>
                            {{-- <th colspan="2">Bs. {{(!empty($pago_total)) ? f_float($pago_total):''}}</th> --}}
                            {{-- <th colspan="2">Bs. {{f_float($ingresos->sum('ingreso_ammount'))}}</th> --}}
                        </tr>
                    </thead>
                </table>

                {{-- @include('administracion.registropagos.table.crud') --}}
                @include('administracion.registropagos.table.cruda')

            </div>
        </div>
    </main>

@endsection


@section('scripts') <script type="text/javascript"> document.title = 'SAEFL - REGISTRO DE PAGOS'; </script> @endsection
