@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Búsqueda Estudiante
@endsection

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    <i class="{{ $icon_menus['estudiante'] }} fa-1x text-primary"></i>
                    Búsqueda de Estudiantes activos
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- @include('administracion.estudiants.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.estudiants.index',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}
                {{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                        <button class="btn btn-dark my-2 my-sm-0" type="reset"><i class="fas fa-redo"
                                aria-hidden="true"></i></button>

                        {{-- <a title="Todos los registros" class="btn btn-dark" href="{{ route('administracion.estudiants.index',['search'=>'&ALL']) }}" role="button">
                                Todos
                            </a> --}}
                    </div>
                </div>
                {!! Form::close() !!}

                @isset($estudiants)
                    {{-- deck con el los usuarios encontrados --}}
                    @include('administracion.estudiants.deck.estudiant')
                @endisset

            </div>

        </div>

    </main>
@endsection
