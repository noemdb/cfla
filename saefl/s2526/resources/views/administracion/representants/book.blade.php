@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right"> --}}

                    {{-- @include('administracion.inscripciones.menus.book') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                    <i class="{{ $icon_menus['libro'] }} fa-1x text-success"></i>
                    Libro de Inscripciones

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.inscripciones.list.pdf',
                    'method' => 'POST',
                    'class' => '',
                    'role' => 'search',
                    'target' => '_blank',
                ]) !!}

                <div class="row">
                    <div class="col-sm-6">
                        <label for="grado_id" class="m-0">Grados o niveles</label>
                        <div class="input-group mb-3">
                            {!! Form::select('grado_id', $list_grados, old('grado_id'), [
                                'class' => 'form-control',
                                'id' => 'grado_id',
                                'placeholder' => 'Seleccione',
                                'required' => 'required',
                            ]) !!}
                        </div>
                        {{-- <label for="list_pescolar" class="m-0">Peŕodo Escolar a consultar</label>
                            <div class="input-group mb-3">                            
                                {!! Form::select('pescolar_id',$list_pescolar,old('list_pescolar'),['class' => 'form-control','id'=>'pescolar_id','placeholder' => 'Seleccione','required'=>'required']) !!}
                            </div>                         --}}
                    </div>
                    <div class="col-sm-6">
                        <label for="order" class="m-0">Ordenado por</label>
                        {!! Form::select(
                            'order',
                            ['ci_estudiant' => 'Identificador', 'lastname' => 'Apellidos y nombres'],
                            old('order'),
                            ['class' => 'form-control', 'id' => 'order_list', 'placeholder' => 'Seleccione', 'required' => 'required'],
                        ) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="orientacion" class="m-0">Orientación</label>
                        {!! Form::select('orientacion', ['portrait' => 'Vertical', 'landscape' => 'Horizontal'], old('orientacion'), [
                            'class' => 'form-control',
                            'id' => 'order_list',
                            'placeholder' => 'Seleccione',
                            'required' => 'required',
                        ]) !!}
                    </div>
                    <div class="col-sm-6">
                        <label for="paper" class="m-0">Tamaño del papel</label>
                        {!! Form::select('paper', ['lettet' => 'Carta', 'A4' => 'Oficio'], old('paper'), [
                            'class' => 'form-control',
                            'id' => 'order_list',
                            'placeholder' => 'Seleccione',
                            'required' => 'required',
                        ]) !!}
                    </div>
                </div>

                <button class="btn btn-primary btn-block pt-2 mt-2" type="submit">Imprimir</button>




                {{-- <button class="btn btn-primary btn-block" type="submit">Mostrar</button> --}}

                {!! Form::close() !!}

            </div>

        </div>

    </main>{!! Form::open([
        'route' => 'administracion.inscripciones.book',
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

    @if ($std_ciaca_siadm->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <span>{{ $std_ciaca_siadm->count() ?? '0' }} estudiante(s) con inscripción académica y sin inscripción
                administrativa</span>
        </div>
    @endif

    @isset($pestudios)
        @include('administracion.inscripciones.book.tab.pestudios')
    @endisset

    @isset($grados)
        {{-- <hr> --}}
        {{-- <h5 class="pt-1">Grados</h5> --}}
        @include('administracion.inscripciones.book.tab.grados')
    @endisset

    @isset($tinscripcions)
        <hr>
        <h5 class="pt-1">Tipos de Inscripción</h5>
        @include('administracion.inscripciones.book.table.tinscripcions')
    @endisset

    {{-- @include('administracion.inscripciones.deck.inscripcion') --}}

    {{-- deck con el los usuarios encontrados --}}
    {{-- @include('administracion.inscripciones.deck.inscripcion') --}}

    {{-- @endisset --}}
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
