@extends('administracion.layouts.dashboard.app')

@section('title') - Saldos Estudiantes {{ Carbon\Carbon::now()->format('Y-m-d h:m') }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">

                {{-- <div class="btn-group float-right p-1">
                    <a href="{{ route('administracion.estudiants.list.saldos.dw.excel') }}" class="btn btn-light float-right" target="_blank">
                        <i class="fas fa-file-excel text-success" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="btn btn-light float-right" target="_blank">
                        <i class="fa fa-file-pdf  text-danger" aria-hidden="true"></i>
                    </a>
                </div> --}}

                <h4>
                    {{-- Asignar plan de pago a los Estudiantes para el período escolar actual<br> --}}
                     <span title="Listado especial con botones de acción"><u>Listado</u></span> de de saldos por estudiante
                    {{-- <small class="small text-dark float-right">
                        <strong><span id="user_estudiant">{{$estudiants->count()}}</span> Estudiantes</strong>
                    </small> --}}

                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}

                        {{-- @include('admin.users.menus.index') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                </h4>
            </div>

            <div class="card-body p-1 m-1">

                @include('administracion.estudiants.form.search',[
                    'route'=>'administracion.estudiants.saldos',
                    'btn_xls'=>'true'
                    ])

                {{-- <table class="table text-left table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Total General</th>
                            <th colspan="2">Bs. {{(!empty($deuda_total)) ? f_float($deuda_total):''}}</th>
                        </tr>
                    </thead>
                </table> --}}

                <table class="table text-left table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Total General: Deuda + R. Morosidad</th>
                            {{-- <th colspan="2">Bs. {{ f_float($deuda_total_bs) }}</th> --}}
                            <th colspan="2">$ {{ f_float($deuda_total_ex) }}</th>
                        </tr>
                    </thead>
                </table>

                @include('administracion.estudiants.table.saldos')

            </div>
        </div>
    </main>

@endsection
