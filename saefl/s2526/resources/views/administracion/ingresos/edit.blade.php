@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
        <div class="card-header {{ (!empty($ingreso->deleted_at) ? 'alert-danger':'alert-success')}}">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.ingresos.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}
                <h3>
                    Actualizar Ingreso
                </h3>
            </div>

            <div class="card-body ">
                {{-- @include('administracion.elements.forms.errors') 
                @include('administracion.elements.messeges.oper_ok') --}}
                
                {!! Form::model($ingreso,['route' => ['administracion.ingresos.update', $ingreso->id], 'method' => 'PUT', 'id'=>'form-update-ingreso_'.$ingreso->id, 'role'=>'form']) !!}
                    
                    {{-- {{ Form::hidden('search', $search) }} --}}
                
                    <div class="card bd-callout bd-callout-primary ">
                        {{-- <h4 class="card-header"> --}}
                            {{-- <i class="{{ $icon_menus['editar'] }} fa-1x"></i> --}}
                            {{-- Actualizar Ingreso                               --}}
                        {{-- </h4> --}}

                        <div class="card-body">                             
                            <div class="row">
                                <div class="col-3">
                                    
                                    {{-- @include('administracion.ingresos.show') --}}
                                    @include('administracion.elements.forms.errors') 
                                    @include('administracion.elements.messeges.oper_ok')
                                    {{$ingreso}}
                                </div>
                                <div class="col-9">
                                    @include('administracion.ingresos.form.fields') 
                                    <button type="submit" class="btn-ingreso-update btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-ingreso-{{$ingreso->id ?? ''}}">
                                        <i class="far fa-save"></i>
                                        Actualizar
                                    </button>                                                               
                                </div>
                            </div>                                
                        </div>
                    </div>                    
                {!! Form::close() !!}                
            </div>
        </div>
    </main>

@endsection
