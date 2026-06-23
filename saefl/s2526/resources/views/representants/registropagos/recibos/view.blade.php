@extends('representants.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right"> --}}

                    {{-- @include('representants.inscripciones.menus.book') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                    <i class="{{ $icon_menus['imprimir'] }} fa-1x text-success"></i>
                    Imprimir listado de estudiantes inscritos (PDF)

                </h3>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('representants.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'representants.inscripciones.list.pdf',
                    'method' => 'POST',
                    'class' => '',
                    'role' => 'search',
                    'target' => '_blank',
                ]) !!}

                <div class="row">
                    <div class="col-sm-6">
                        <label for="list_pescolar" class="m-0">Peŕodo Escolar a consultar</label>
                        <div class="input-group mb-3">
                            {!! Form::select('pescolar_id', $list_pescolar, old('list_pescolar'), [
                                'class' => 'form-control',
                                'id' => 'pescolar_id',
                                'placeholder' => 'Seleccione',
                                'required' => 'required',
                            ]) !!}
                            {{-- <div class="input-group-append"> --}}
                            {{-- <button class="btn btn-primary" type="submit">Imprimir</button> --}}
                            {{-- </div> --}}
                        </div>
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

                <hr>

                {{-- @isset($pestudios)
                    <h5>Planes de Estudio</h5>
                    @include('representants.inscripciones.book.pestudios')
                @endisset

                @isset($grados)
                    <h5>Grados</h5>
                    @include('representants.inscripciones.book.grados')
                @endisset

                @isset($tinscripcions)
                    <h5>TIPO DE INSCRIPCIÓN</h5>
                    @include('representants.inscripciones.book.tinscripcions')
                @endisset --}}

                {{-- @include('representants.inscripciones.deck.inscripcion') --}}

                {{-- deck con el los usuarios encontrados --}}
                {{-- @include('representants.inscripciones.deck.inscripcion') --}}

                {{-- @endisset --}}

            </div>

        </div>

    </main>
@endsection

@section('scripts')
    @parent
@endsection
