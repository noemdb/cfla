@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['registropagos'] }} fa-1x text-success"></i>
                    Búsqueda de Pagos Registrados
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- @include('administracion.registropagos.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.registropagos.index',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}
                {{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                        <a title="Todos los registros" class="btn btn-dark"
                            href="{{ route('administracion.registropagos.index', ['search' => '&ALL']) }}" role="button">
                            {{-- <i class="{{ $icon_menus['buscar'] }} fa-1x"></i> --}}
                            Todos
                            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
                        </a>
                    </div>
                </div>
                {!! Form::close() !!}

                {{-- {{$registropagos}} --}}

                @if (isset($registropagos))
                    @include('administracion.registropagos.deck.registropagos')
                @else
                    <span class="small">No se encontraron registros de pagos</span>
                    <small class="font-weight-bold pb-1">
                        Criterio de Búsqueda: <span class="font-italic">{{ $search ?? '' }}</span>
                    </small>
                @endif

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
