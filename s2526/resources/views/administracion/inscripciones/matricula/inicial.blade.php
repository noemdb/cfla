@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right"> --}}

                    {{-- @include('administracion.inscripciones.menus.book') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                    <i class="{{ $icon_menus['imprimir'] }} fa-1x text-dark"></i>
                    Imprimir Matrícula Inicial

                </h4>
                {{-- <small class="font-weight-bold text-mute">PE: {{ Session::get('pescolar_name') }}</small> --}}
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open([
                    'route' => 'administracion.inscripciones.matricula.pdf.inicial',
                    'method' => 'POST',
                    'class' => '',
                    'role' => 'search',
                    'target' => '_blank',
                ]) !!}

                <div class="row">
                    {{-- <div class="col-sm-6">
                            <label for="pestudio_id" class="m-0">Plan de Estudio</label>
                            <div class="input-group mb-3">                            
                                {!! Form::select('pestudio_id',$list_Pestudio,old('list_pescolar'),['class' => 'form-control','id'=>'pestudio_id','placeholder' => 'Seleccione','required'=>'required']) !!}
                            </div>                        
                        </div> --}}
                    <div class="col-sm-6">
                        <label for="grado_id" class="m-0">Grados</label>
                        <div class="input-group mb-3">
                            {!! Form::select('grado_id', $list_grados, old('grado_id'), [
                                'class' => 'form-control',
                                'id' => 'grado_id',
                                'placeholder' => 'Seleccione',
                                'required' => 'required',
                            ]) !!}
                        </div>
                    </div>
                    {{-- <div class="col-sm-6">
                            <label for="list_pescolar" class="m-0">Peŕodo Escolar</label>
                            <div class="input-group mb-3">                            
                                {!! Form::select('pescolar_id',$list_pescolar,old('list_pescolar'),['class' => 'form-control','id'=>'pescolar_id','placeholder' => 'Seleccione','required'=>'required']) !!}
                            </div>                        
                        </div> --}}
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

    </main>
@endsection

@section('scripts')
    @parent
@endsection
