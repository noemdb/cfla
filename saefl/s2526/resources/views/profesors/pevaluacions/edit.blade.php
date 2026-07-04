@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">

                <div class="btn-group float-right pt-2">
                    @include('profesors.pevaluacions.menus.edit')
                </div>

                <h3>Actualizar Plan de Evaluación</h3>

            </div>

            <div class="card-body">

                    @include('profesors.elements.forms.errors')

                    @include('profesors.elements.messeges.oper_ok')

                    

                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos
                            </h4>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="card-title">Resumen</h6>
                                        <div class="dropdown-divider mb-0"></div>
                                        @include('profesors.pevaluacions.partials.resumen')
                                    </div>
                                    <div class="col-6">
                                        {!! Form::model($pevaluacion,['route' => ['profesors.pevaluacions.update', $pevaluacion->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$pevaluacion->id, 'role'=>'form']) !!}
                                        @include('profesors.pevaluacions.form.fields')
                                        <button type="submit" class="btn-pevaluacion-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-pevaluacion-{{$pevaluacion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                        {!! Form::close() !!}
                                    </div>
                                    <div class="col-3">
                                        <h5 class="card-title">Lista de Evaluaciones registradas</h3>
                                        <div class="dropdown-divider mb-0"></div>
                                        @php $evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions:null; @endphp
                                        @includewhen(($evaluacions),'profesors.pevaluacions.partials.evaluacion')
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
