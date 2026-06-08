@extends('administracion.layouts.dashboard.app')

@section('title')
    - Saldos Representante {{ Carbon\Carbon::now()->format('Y-m-d h:m') }}
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-info">

                <div class="btn-group float-right">
                    @include('administracion.representants.menus.index')
                </div>

                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de Representantes deudores por
                    Cuota
                </h4>
            </div>

            <div class="card-body p-1 m-1">

                @include('administracion.representants.form.saldosDate', [
                    'route' => 'administracion.representants.saldosDate',
                ])

                @if ($deuda_total_ex_date)
                    @php
                        $goal = $representants->count();
                        $real = count($deuda_ex_arr);
                        $morosidad = (100 * $real) / $goal;
                    @endphp
                    <div class=" bg-light pb-1 font-weight-bold d-block alert-dark">
                        <div class="float-right d-block">
                            Total General: <span>$ {{ f_float($deuda_total_ex_date) ?? '' }}</span> ||
                            Representantes deudores: <span>{{ f_float($morosidad) ?? '' }}%</span>
                            {{-- Nº <span>{{ count($deuda_ex_arr) ?? ''}}</span> --}}
                        </div>
                    </div>
                @endif

                <br>

                @include('administracion.representants.table.saldosDate')

            </div>
        </div>
    </main>
@endsection
