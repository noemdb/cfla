@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @php
                            $lapso_id = $evaluacion->pevaluacion->lapso_id;
                            $pensum_id = $evaluacion->pevaluacion->pensum->id;
                            $grado_id = $evaluacion->pevaluacion->pensum->grado_id;
                            $route_pe = route('administracion.pevaluacions.create',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum_id,'lapso_id'=>$lapso_id]);
                        @endphp
                        @include('administracion.pevaluacions.evaluacions.menus.edit')    
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar Evaluación

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')
                    
                    {!! Form::model($evaluacion,['route' => ['administracion.evaluacions.update', $evaluacion->id], 'method' => 'PUT', 'id'=>'form-update-boletin_'.$evaluacion->id, 'role'=>'form']) !!}
                    
                        <div class="card bd-callout bd-callout-primary">

                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos                            
                            </h4>

                            <div class="card-body">                                                               
                                <div class="row">
                                    @if (!empty($pevaluacion->id))
                                        <div class="col-sm-3">
                                            <h5 class="card-title">Resumen del PE</h5>
                                            <div class="dropdown-divider mb-0"></div>
                                            @include('administracion.pevaluacions.partials.resumen') 
                                        </div>    
                                    @endif                                   
                                    <div class="col-sm-6">
                                        @include('administracion.pevaluacions.evaluacions.form.fields')                                
                                        <button type="submit" class="btn-evaluacion-edit btn btn-primary btn-block" value="Actualizar" data-id="edit" id="btn-edit-evaluacion-{{$evaluacion->id ?? ''}}">
                                            <i class="far fa-save"></i>
                                            Actualizar
                                        </button>
                                    </div>
                                    <div class="col-sm-3">
                                        <h6 class="card-title">Lista de las evaluaciones del Plan asociado a ésta evaluación</h6>
                                        <div class="dropdown-divider mb-0"></div>
                                        @php $evaluacions = (!empty($evaluacion->pevaluacion->evaluacions->first())) ? $evaluacion->pevaluacion->evaluacions : null; @endphp                                      
                                        @includewhen(($evaluacions),'administracion.pevaluacions.partials.evaluacion')
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
