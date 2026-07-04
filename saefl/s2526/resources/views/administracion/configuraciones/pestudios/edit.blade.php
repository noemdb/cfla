@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h4>Actualizar <span class="font-weight-bolder">Plan de Estudio</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::model($pestudio,['route' => ['administracion.configuraciones.pestudios.update', $pestudio->id], 'method' => 'PUT', 'id'=>'form-update-banco_'.$pestudio->id, 'role'=>'form']) !!}

                    <div class="card bd-callout bd-callout-{{$pestudio->status_active=='true'  ? 'primary':'danger'}}">
                        <h5 class="card-header">
                            Datos
                        </h5>
                        <div class="card-body">
                            @include('administracion.configuraciones.pestudios.form.fields',$pestudio)
                        </div>
                    </div>

                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-pestudio-{{$pestudio->id}}">
                        <i class="far fa-save"></i>
                        Actualizar
                    </button>

                {!! Form::close() !!}
            </div>

        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Planes de Estudio, Editar'; </script> @endsection




