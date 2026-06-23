@extends('administracion.layouts.dashboard.app')

@section('title') - Registrar: Histórico de Notas @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.historico_notas.menus.crud')
                </div>
                <h4> Carga/Actualización para las <span class="font-weight-bold">Notas Certificadas</span> del período escolar actual</h4>
                <span class="small text-muted">Procedimiento habilitado al finalizar el último lapso</span>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.historico_notas.partials.search',['route'=>'administracion.historico_notas.carga'])

                @include('administracion.historico_notas.table.carga')

                <fieldset {{ ($grado_id && $seccion_id) ? null : 'disabled=disabled' }} >

                    {!! Form::open(['route' => 'administracion.historico_notas.store.carga', 'method' => 'POST', 'id'=>'form-cargar-actualizar', 'class'=>'form-signin']) !!}

                        {{ Form::hidden('grado_id', $grado_id) }}
                        {{ Form::hidden('seccion_id', $seccion_id) }}

                        <button type="submit" class="btn-create btn btn-primary btn-block" value="Registrar">
                            <i class="far fa-save"></i>
                            Cargar/Actualizar
                        </button>

                    {!! Form::close() !!}

                </fieldset>

            </div>
        </div>
    </main>

@endsection
