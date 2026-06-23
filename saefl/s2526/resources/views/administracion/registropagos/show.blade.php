@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    <i class="{{ $icon_menus['registropagos'] }} fa-1x text-success"></i>
                    Detalles del pago registrado de los conceptos de cobro <span class=" text-dark">{{$registropago->cuentaxpagar->name ?? ''}}</span>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- @include('administracion.registropagos.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h4>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- {{$registropago}} --}}
                @isset($registropago)
                    @include('administracion.registropagos.table.list')
                    {{-- @include('administracion.registropagos.show.tabs') --}}
                @endisset

                {{-- @isset (!$registropago)
                    <span class="small">No se encontraron registros de pagos</span>
                @endisset --}}

            </div>

        </div>

    </main>

@endsection

@section('scripts')
    @parent
@endsection
