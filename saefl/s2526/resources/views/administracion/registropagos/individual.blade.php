@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    <i class="{{ $icon_menus['estudiante'] }} fa-1x text-success"></i>
                    Registro de Pagos
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
                    'route' => 'administracion.registropagos.individual',
                    'method' => 'GET',
                    'class' => '',
                    'id' => 'form-inscripcion-search',
                ]) !!}
                {{-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name"> --}}
                {{-- <label for="representant" class="m-0">Representante</label> --}}
                <div class="input-group">
                    {!! Form::text('search', $search, [
                        'class' => 'form-control',
                        'placeholder' => 'Buscar nombre o cédula del representante',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-info my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @isset($estudiants)
                    @include('administracion.registropagos.deck.estudiants')
                @endisset
                {{-- @isset($representants)                
                    @include('administracion.registropagos.deck.representant')                    
                @endisset --}}

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
