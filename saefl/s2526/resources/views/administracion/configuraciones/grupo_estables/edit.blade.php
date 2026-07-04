@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.configuraciones.grupo_estables.menus.edit')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar <span class="font-weight-bold">Grupos Estables</span>

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($grupo_estable,['route' => ['administracion.configuraciones.grupo_estables.update', $grupo_estable->id], 'method' => 'PUT', 'id'=>'form-update-grupo_estable_'.$grupo_estable->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos
                            </h4>

                            <div class="card-body">

                                <div class="p-2 m-2">
                                    @include('administracion.configuraciones.grupo_estables.form.fields')
                                    <button type="submit" class="btn-grupo_estable-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-grupo_estable-{{$grupo_estable->id ?? ''}}">
                                        <i class="far fa-save"></i>
                                        Actualizar
                                    </button>
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
