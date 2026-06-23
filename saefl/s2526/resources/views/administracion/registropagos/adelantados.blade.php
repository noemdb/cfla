@extends('administracion.layouts.dashboard.app')

@section('title') Listado de representantes con Pagos Adelantados @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.registropagos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}

                <h4>
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de <span class="font-weight-bolder">Representantes con Pagos Adelantados</span>
                    @if ($next_month) <span>para el mes de</span> <span class=" font-weight-bold text-capitalize">{{ $next_month->format('F')}}</span> @endif
                </h4>

            </div>

            <div class="card-body p-2">

                <div class="card-header p-0 m-0 mb-2">
                    {!! Form::open(['route'=>'administracion.registropagos.adelantados','method'=>'GET','class'=>'pb-1','id'=>'form_search','role'=>'search']) !!}
                        @include('administracion.registropagos.form.adelantados.search')
                    {!! Form::close() !!}
                </div>

                <div class=" border rounded p-1">

                    @if ($adelantados->isNotEmpty())
                        <table class="table text-left table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th class=" text-right">Total General Ingresado</th>
                                    <th> $ {{ f_float($adelantados->sum('total_exchange_ammoun_ingreso')) }}</th>
                                    <th class=" text-right">Total General Pagado</th>
                                    <th> $ {{ f_float($adelantados->sum('total_exchange_ammount_concepto_pagos')) }}</th>
                                    <th class=" text-right">Total Monto Pago Adelantado: </th>
                                    <th> $ {{ f_float($adelantados->sum('texchange_ammoun_advanced')) }}</th>
                                </tr>
                            </thead>
                        </table>
                    @endif

                    @include('administracion.registropagos.table.adelantados.crud')
                    
                </div>

            </div>

        </div>

    </main>

@endsection
