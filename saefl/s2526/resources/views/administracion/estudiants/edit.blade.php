@extends('administracion.layouts.dashboard.app')

@section('title') - Editar Estudiante @endsection

@section('main')

<main role="main" class="col-md-10 ml-sm-auto col-lg-10">

    <div class="card card-primary mt-2">
        <div class="card-header">
            <h3>


                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">

                    {!! Form::open(['route' => ['administracion.estudiants.destroy',$estudiant->id], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
                        <button type="submit" class="btn-destroy btn btn-danger bnt-sm" title="eliminar estudiante">
                            <i class="fas fa-trash"></i>
                        </button>
                    {!! Form::close() !!}

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                </div>
                {{-- FIN Menu rapido --}}
                Datos del Estudiante<br>
                <small class="text-default">
                    {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                </small>

            </h3>
        </div>

        <div class="card-body">

            @include('administracion.elements.forms.errors')

            @include('administracion.elements.messeges.oper_ok')

            {!! Form::model($estudiant,['route' => ['administracion.estudiants.update', $estudiant->id], 'method' => 'PUT', 'id'=>'form-update-estudiant_'.$estudiant->id, 'role'=>'form']) !!}

            {{-- {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }} --}}
            {{ Form::hidden('id', $estudiant->id) }}
            {{-- {{ Form::hidden('ci_estudiant', $estudiant->ci_estudiant) }} --}}
            {{-- {{ Form::hidden('search', $search) }} --}}

            <div class="card bd-callout bd-callout-primary">
                <h4 class="card-header">
                    <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                    Actualizar Estudiante
                </h4>

                {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small> --}}

                <div class="card-body">

                    <div class="row">
                        <div class="col-3">
                            @include('administracion.estudiants.deck.card.estudiant_simple')
                        </div>
                        <div class="col-9">
                            @include('administracion.estudiants.form.fields')
                            <button type="submit" class="btn-estudiant-create btn btn-primary btn-block" value="Actualizar"
                                data-id="create" id="btn-create-estudiant-{{$estudiant->id ?? ''}}">
                                <i class="far fa-save"></i>
                                Actualizar
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


