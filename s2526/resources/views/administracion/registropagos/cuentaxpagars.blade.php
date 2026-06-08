@extends('administracion.layouts.dashboard.app')

@section('title')
    - Conceptos Pendientes Representante {{ Carbon\Carbon::now()->format('Y-m-d h:m') }} - Total:
    {{ $representants->count() ?? '' }}
@endsection

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
                    <span title="Listado especial con botones de acción"><u>Listado</u></span> de <span
                        class="font-weight-bolder">Representantes con conceptos de cobro pendiente</span>
                </h4>
            </div>

            <div class="card-body p-2">

                <div class="card-header p-0 m-0 mb-2">
                    {!! Form::open([
                        'route' => 'administracion.registropagos.cuentaxpagars',
                        'method' => 'GET',
                        'class' => 'pb-1',
                        'id' => 'form_search',
                        'role' => 'search',
                    ]) !!}

                    @include('administracion.registropagos.form.search.cuentaxpagars')

                    {!! Form::close() !!}
                    <span class="small text-muted float-right">Decimales separados por punto</span>
                </div>

                <br>

                @include('administracion.registropagos.table.cuentaxpagars')

            </div>
        </div>
    </main>
@endsection
