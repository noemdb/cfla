@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['inscripciones'] }} fa-1x text-success"></i>
                    Búsqueda de Inscripciones
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        {{-- @include('administracion.inscripciones.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.inscripciones.index',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}
                {{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @isset($inscripcions)
                    <h4>Inscripciones</h4>

                    {{-- @foreach ($inscripcions as $estudiant) --}}

                    @include('administracion.inscripciones.deck.inscripcion')

                    {{-- @endforeach --}}

                    {{-- deck con el los usuarios encontrados --}}
                    {{-- @include('administracion.inscripciones.deck.inscripcion') --}}
                @endisset

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
