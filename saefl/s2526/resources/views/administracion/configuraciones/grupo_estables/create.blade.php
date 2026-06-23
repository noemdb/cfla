@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.configuraciones.grupo_estables.menus.create')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Registrar un nuevo <span class="font-weight-bold">Grupos Estables</span>
                    {{-- <br> --}}
                    {{-- <small class="text-default"> --}}
                        {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                    {{-- </small> --}}
                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::open(['route' => 'administracion.configuraciones.grupo_estables.store', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}

                        {{-- {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }} --}}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                Datos del Grupo Estable
                            </h4>

                            <div class="p-2 m-2">
                                @include('administracion.configuraciones.grupo_estables.form.fields')
                                <button type="submit" class="btn-grupo_estable-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-grupo_estables-{{$grupo_estable->id ?? ''}}">
                                    <i class="far fa-save"></i>
                                    Registrar
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
