@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos de la Inscripción<br>
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

                @if ($estudiant->status_blacklist=="true")
                    <div class="alert alert-danger">
                        Este estudiante incumplió con el compromiso de pago en las fechas correspondientes.
                    </div>
                @endif

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::open(['route' => 'administracion.inscripciones.store', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}

                    {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }}
                    {{ Form::hidden('estudiant_id', $estudiant->id) }}
                    {{-- {{ Form::hidden('ci_estudiant', $estudiant->ci_estudiant) }} --}}
                    {{-- {{ Form::hidden('search', $search) }} --}}

                    <div class="card bd-callout bd-callout-primary">
                        <h4 class="card-header">
                            Datos para el registro de la nueva inscripción
                        </h4>

                        {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small> --}}

                        <div class="card-body">

                            <fieldset {{ ($estudiant->status_blacklist=="true") ? 'disabled="disabled"':null}} >

                                <div class="row">
                                    <div class="col-3">
                                        @include('administracion.inscripciones.deck.card.estudiant_simple')
                                    </div>
                                    <div class="col-9">
                                        @include('administracion.inscripciones.form.fields')
                                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Registrar
                                        </button>
                                    </div>
                                </div>

                            </fieldset>
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
