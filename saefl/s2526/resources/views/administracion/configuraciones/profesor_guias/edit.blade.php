@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">

                <div class="btn-group float-right pt-2">
                    @include('administracion.profesor_guias.menus.edit')
                </div>

                <h4>Actualizar Plan de Evaluación</h4>

            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')

                    {!! Form::model($profesor_guia,['route' => ['administracion.profesor_guias.update', $profesor_guia->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$profesor_guia->id, 'role'=>'form']) !!}

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos
                            </h4>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        @include('administracion.profesor_guias.form.fields')
                                        <button type="submit" class="btn-pevaluacion-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-pevaluacion-{{$profesor_guia->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>
                                    <div class="col-3">
                                        <h5 class="card-title">Lista de Evaluaciones registradas</h5>
                                        <div class="dropdown-divider mb-0"></div>
                                        @php $evaluacions = (!empty($profesor_guia->evaluacions)) ? $profesor_guia->evaluacions:null; @endphp
                                        @includewhen(($evaluacions),'administracion.profesor_guias.partials.evaluacion')
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
