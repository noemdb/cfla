@extends('representants.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['ayudas'] ?? '' }} fa-1x text-success"></i>
                    {{-- <i class="fa fa-question" aria-hidden="true"></i> --}}
                    Búsqueda de Tutoriales
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- @include('representants.ayudas.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('representants.elements.messeges.oper_ok')

                {!! Form::open(['route' => 'representants.ayudas.index', 'method' => 'GET', 'class' => '', 'role' => 'search']) !!}
                {{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Ingresar palabra']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @include('representants.ayudas.table.index')

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
