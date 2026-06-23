@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                <div class="btn-group float-right">
                    {{-- @include('administracion.materia_pendientes.menus.index') --}}
                </div>

                <h3>
                    <i class="{{ $icon_menus['materia_pendientes'] }} fa-1x text-success"></i>
                    Materia Pendientes - Diferido
                </h3>

            </div>

            <div class="card-body">

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.materia_pendientes.index',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}
                <div class="input-group">
                    {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Cédula']) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @isset($estudiants)
                    <h4>Estudiantes</h4>

                    {{-- @foreach ($inscripcions as $estudiant) --}}

                    {{-- @include('administracion.materia_pendientes.deck.inscripcion') --}}

                    {{-- @endforeach --}}

                    {{-- deck con el los usuarios encontrados --}}
                    {{-- @include('administracion.materia_pendientes.deck.inscripcion') --}}
                @endisset

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
