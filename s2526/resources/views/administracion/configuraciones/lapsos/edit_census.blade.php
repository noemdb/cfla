@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.lapsos.menus.census_edit')
                    {{-- /home/nuser/code/s2223/resources/views/administracion/configuraciones/lapsos/menus/census.blade.php --}}
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Actualizar <span class="font-weight-bolder">Registro de participante del Censo Escolar</span> </h4>
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
                            <div class="col">

                                {!! Form::model($census, [
                                    'route' => ['administracion.configuraciones.lapsos.census.update', $census->id],
                                    'method' => 'PUT',
                                    'id' => 'form-update-census_' . $census->id,
                                    'role' => 'form',
                                ]) !!}

                                @include('administracion.configuraciones.lapsos.form.census.fields')

                                {!! Form::submit('Actualizar', [
                                    'class' => 'btn-grupo_estable-create btn btn-primary btn-block',
                                    'placeholder' => 'Seleccione',
                                    'id' => 'create',
                                ]) !!}

                                {!! Form::close() !!}
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
    <script type="text/javascript">
        document.title = 'SAEFL - Censo, editar';
    </script>
@endsection
