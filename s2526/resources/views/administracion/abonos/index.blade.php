@extends('administracion.layouts.dashboard.app')

@section('title')
    - Abonos - Representantes
@endsection

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['pagos_adelantados'] }} fa-1x text-success"></i>
                    Registro de abonos
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- <a href="{{ route('administracion.abonos.list.abono.dw.excel') }}" class="btn btn-success float-right" target="_sefl" title="Listado de abonos disponibles">
                            <i class="fas fa-file-excel text-light" aria-hidden="true"></i>
                        </a> --}}

                        {{-- @include('administracion.abonos.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open(['route' => 'administracion.abonos.index', 'method' => 'GET', 'class' => '', 'role' => 'search']) !!}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @if (!empty($estudiants))
                    @include('administracion.abonos.deck.abonos')
                @endif

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
