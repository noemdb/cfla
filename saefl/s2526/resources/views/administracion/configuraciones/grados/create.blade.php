@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h4>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.configuraciones.peducativos.menus.create')
                    </div>
                    {{-- FIN Menu rapido --}}

                    Registrar <span class="font-weight-bolder">Programa Educativo</span>
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">

                    <h5 class="card-header">Datos</h5>

                    <div class="p-2 m-2">

                        <div class="row">

                            <div class="col-8">

                                {!! Form::open([
                                    'route' => 'administracion.configuraciones.peducativos.store',
                                    'method' => 'POST',
                                    'id' => 'form-peducativos-create',
                                    'class' => 'form-signin',
                                ]) !!}

                                {!! Form::hidden('pescolar_id', Session::get('pescolar_id')) !!}
                                {{-- {{ Form::hidden('institucion_id', $banco->institucion_id) }} --}}

                                @include('administracion.configuraciones.peducativos.form.fields')

                                {!! Form::submit('Registrar', [
                                    'class' => 'btn-grupo_estable-create btn btn-primary btn-block',
                                    'placeholder' => 'Seleccione',
                                    'id' => 'create',
                                ]) !!}

                                {!! Form::close() !!}

                            </div>

                            <div class="col-4">

                            </div>

                        </div>

                    </div>

                </div>
            </div>



        </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
@endsection
