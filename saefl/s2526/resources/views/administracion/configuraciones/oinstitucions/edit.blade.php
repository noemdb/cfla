@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.configuraciones.oinstitucions.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Actualizar otras <span class="font-weight-bolder">Instituciones</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card bd-callout bd-callout-primary">
                    <h5 class="card-header">
                        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                        Datos
                    </h5>

                    <div class="card-body m-1 p-1">
                        <div class="container">
                            <div class="row">
                                <div class="col-8">

                                    {!! Form::model($oinstitucion, [
                                        'route' => ['administracion.configuraciones.oinstitucions.update', $oinstitucion->id],
                                        'method' => 'PUT',
                                        'id' => 'form-update-oinstitucionse_' . $oinstitucion->id,
                                        'role' => 'form',
                                    ]) !!}
                                    @include('administracion.configuraciones.oinstitucions.form.fields')
                                    {!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}
                                    {!! Form::close() !!}

                                </div>
                                <div class="col-4">
                                    @include('administracion.configuraciones.oinstitucions.partials.resumen.edit')
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
    <script type="text/javascript">
        document.title = 'SAEFL - Instituciones, editar';
    </script>
@endsection
