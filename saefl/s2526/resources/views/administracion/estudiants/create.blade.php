@extends('administracion.layouts.dashboard.app')

@section('title') - Crear Estudiante @endsection

@section('main')

<main role="main" class="col-md-10 ml-sm-auto col-lg-10">

    <div class="card card-primary mt-2">
        <div class="card-header">
            <h3>
                Datos del Estudiante<br>
                <small class="text-default">
                    {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                </small>

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}

            </h3>
        </div>

        <div class="card-body">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!! Form::open(['route' => 'administracion.estudiants.store', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}
            {{ Form::hidden('planpago_id', '1') }}
            {{ Form::hidden('grado_inicial_id', '1') }}
            {{ Form::hidden('seccion_inicial', '1') }}

            <div class="card bd-callout bd-callout-primary">
                <h4 class="card-header">
                    Nuevo Estudiante
                </h4>

                {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small> --}}

                <div class="card-body">

                    <div class="row">
{{--
                        <div class="col-3">
                           @include('administracion.estudiants.deck.card.estudiant_simple')

                        </div>
--}}
                        <div class="col-12">
                            @include('administracion.estudiants.form.fields')
                            <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar"
                                data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                                <i class="far fa-save"></i>
                                Registrar
                            </button>
                        </div>
                    </div>

                </div>
            </div>



            {!! Form::close() !!}

        </div>
    </div>
</main>

@endsection

@section('scripts')
@parent

@endsection





@section('scripts')
@parent

@endsection
