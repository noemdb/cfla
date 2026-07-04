@extends('representants.layouts.dashboard.app')

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

                        {{-- @include('representants.configuraciones.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                @include('representants.elements.forms.errors')

                @include('representants.elements.messeges.oper_ok')

                {!! Form::model($inscripcion,['route' => ['representants.preinscripcions.update', $inscripcion->id], 'method' => 'PUT', 'id'=>'form-update-inscripcion_'.$inscripcion->id, 'role'=>'form']) !!}

                    {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }}
                    {{ Form::hidden('estudiant_id', $inscripcion->estudiant_id) }}
                    {{-- {{ Form::hidden('ci_inscripcion', $inscripcion->ci_inscripcion) }} --}}
                    {{-- {{ Form::hidden('search', $search) }} --}}

                    <div class="card bd-callout bd-callout-primary">
                        <h4 class="card-header">
                            <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            Actualizar Inscripción
                        </h4>

                        {{-- <small class="font-weight-bold text-mute">Período Escolar: {{ Session::get('pescolar_name') }}</small> --}}

                        <div class="card-body">

                            <div class="row">
                                <div class="col-3">
                                    @include('representants.preinscripcions.deck.card.estudiant_simple')
                                </div>
                                <div class="col-9">
                                    @include('representants.preinscripcions.form.fields')
                                    <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
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

@section('scripts')
    @parent

@endsection
