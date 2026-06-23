@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.configuraciones.pestudios.menus.create')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Registrar <span class="font-weight-bolder">Planes de Estudio</span>
                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::open(['route' => 'administracion.configuraciones.pestudios.store', 'method' => 'POST', 'id'=>'form-pestudios-create', 'class'=>'form-signin']) !!}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                Datos
                            </h4>

                            <div class="p-2 m-2">
                                @include('administracion.configuraciones.pestudios.form.fields')
                                <button type="submit" class="btn-grupo_estable-create btn btn-primary btn-block" value="Registrar" data-id="create">
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
