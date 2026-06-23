@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.profesors.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}

                <h4>Actualizar <span class="font-weight-bolder">Profesor</span></h4>

            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">
                        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                        Datos
                    </h5>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-9">
                                {!! Form::model($profesor, [
                                    'route' => ['administracion.configuraciones.profesors.update', $profesor->id],
                                    'method' => 'PUT',
                                    'id' => 'form-update-profesor_' . $profesor->id,
                                    'role' => 'form',
                                ]) !!}
                                @include('administracion.configuraciones.profesors.form.fields')
                                {!! Form::submit('Actualizar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'update']) !!}
                                {!! Form::close() !!}
                            </div>
                            <div class="col-3">
                                @include('administracion.configuraciones.profesors.partials.resumen.edit')
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection

@section('title')
    Profesores, Editar
@endsection
