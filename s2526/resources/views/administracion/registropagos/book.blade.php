@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right"> --}}

                    {{-- @include('administracion.registropagos.menus.book') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                    <i class="{{ $icon_menus['libro'] }} fa-1x text-success"></i>
                    Libro de Registrar Pagos

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.registropagos.book',
                    'method' => 'GET',
                    'class' => '',
                    'role' => 'search',
                ]) !!}

                <label for="list_pescolar" class="m-0">Peŕodo Escolar a consultar</label>
                <div class="input-group mb-3">
                    {!! Form::select('pescolar_id', $list_pescolar, $pescolar_id, [
                        'class' => 'form-control',
                        'id' => 'pescolar_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Mostrar</button>
                    </div>
                </div>

                {!! Form::close() !!}

                @isset($pestudios)
                    @include('administracion.registropagos.book.tab.pestudios')
                @endisset

                @isset($grados)
                    {{-- <hr> --}}
                    {{-- <h5 class="pt-1">Grados</h5> --}}
                    @include('administracion.registropagos.book.tab.grados')
                @endisset

                @isset($tinscripcions)
                    <hr>
                    <h5 class="pt-1">Tipos de Inscripción</h5>
                    @include('administracion.registropagos.book.table.tinscripcions')
                @endisset

                {{-- @include('administracion.registropagos.deck.inscripcion') --}}

                {{-- deck con el los usuarios encontrados --}}
                {{-- @include('administracion.registropagos.deck.inscripcion') --}}

                {{-- @endisset --}}

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
    {{-- <script src="{{ asset("js/Chart.js") }}"></script> --}}
    <script src="{{ asset('js/Chart.bundle.js') }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset('js/ChartFunction.js') }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI Evento clic para generar los Chart por rango --}}
    <script src="{{ asset('js/ChartEvent.js') }}"></script>{{-- Funciones para generar los Chart --}}
    {{-- FIN Evento clic para generar los Chart por rango --}}
@endsection
