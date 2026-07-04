@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @php
                        $lapso_id = $evaluacion->pevaluacion->lapso_id;
                        $pensum_id = $evaluacion->pevaluacion->pensum->id;
                        $grado_id = $evaluacion->pevaluacion->pensum->grado_id;
                        // $route_pe = route('profesors.pevaluacions.create',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum_id,'lapso_id'=>$lapso_id]);
                    @endphp
                    @include('profesors.evaluacions.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}
                <h3>Actualizar Evaluación</h3>

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
                                    @if (!empty($pevaluacion->id))
                                        <div class="col-sm-3">
                                            <h5 class="card-title">Resumen del PE</h3>
                                            <div class="dropdown-divider mb-0"></div>
                                            @include('profesors.pevaluacions.partials.resumen')
                                        </div>
                                    @endif
                                    <div class="col-sm-6">
                                        {!! Form::model($evaluacion,['route' => ['profesors.evaluacions.update', $evaluacion->id], 'method' => 'PUT', 'id'=>'form-update', 'role'=>'form']) !!}
                                        @include('profesors.evaluacions.form.fields')
                                        <button type="submit" class="btn-evaluacion-edit {{$disabled ?? ''}} btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-evaluacion-{{$evaluacion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                        {!! Form::close() !!}

                                        <hr>

                                        {{-- <livewire:profesor.pevaluacion.index-component /> --}}

                                    </div>
                                    <div class="col-sm-3">
                                        <h6 class="card-title">Lista de las evaluaciones del Plan asociado a ésta evaluación</h6>
                                        <div class="dropdown-divider mb-0"></div>
                                        @php $evaluacions = (!empty($evaluacion->pevaluacion->evaluacions->first())) ? $evaluacion->pevaluacion->evaluacions : null; @endphp
                                        @includewhen(($evaluacions),'profesors.pevaluacions.partials.evaluacion')
                                    </div>
                                </div>                                

                            </div>
                        </div>
            </div>
        </div>

        

    </main>

@endsection

@php $disabled  = ($evaluacion->boletins->isNotEmpty()) ? true: false ; @endphp
@php $seccion_id  = $pevaluacion->seccion_id ; @endphp
@php $disabled  = ($seccion_id <> 9) ? $disabled : null ; @endphp
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            if ({{ ($disabled) ? 1:0}}) {
                $('#form-update').find('input, textarea, button, select').attr('disabled','disabled');
                // $('.next-form').removeAttr('disabled','disabled').attr('enabled','enabled');
                // $('.previous-form').removeAttr('disabled','disabled').attr('enabled','enabled');
                // $('#btn-create-registropago').attr('disabled','disabled');
            }
        });
    </script>

@endsection
