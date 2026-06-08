@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h4>
                    <div class="float-right pt-0">
                        <button title="Registrar nombre para las cuentas de cobro" type="button" class="btn btn-primary " data-toggle="modal" data-target="#CreateNomConceptModal" data-whatever="@mdo">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                        @include('administracion.configuraciones.concepto_pagos.create.modal.nom_concept')
                        {{-- @include('administracion.configuraciones.menus.index') --}}
                    </div>
                    Concepto: <b>{{$cuentaxpagar->name}}</b>
                    @if ($cuentaxpagar->type == 'INDIVIDUAL')
                    <small class="text-secondary small">
                        {{$cuentaxpagar->estudiant->representant->name ?? ''}} ({{$cuentaxpagar->estudiant->representant->representant_ci ?? ''}})<br>
                        <span class="small">{{$cuentaxpagar->estudiant->fullname ?? ''}} ({{$cuentaxpagar->estudiant->ci_estudiant ?? ''}})</span>
                    </small>
                    @endif
                    <!-- TODO: This is for server side, there is another version for browser defaults -->

                    {{-- INI Menu rapido --}}
                    
                    {{-- FIN Menu rapido --}}

                </h4>
                <span class="text-muted small">Asignación Cuentas de Cobro</span>
            </div>

            <div class="card-body">
                

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')
                
                {!! Form::open(['route' => 'administracion.configuraciones.concepto_pagos.store', 'method' => 'POST', 'id'=>'form-concepto_pagos-create', 'class'=>'form-signin']) !!}
                    
                    {{ Form::hidden('cuentaxpagar_id', $cuentaxpagar->id) }}                            
                
                    <div class="card bd-callout bd-callout-primary">
                        <h5 class="card-header pb-0 mb-0">
                            <span class="font-weight-bold text-primary">Datos de la cuenta de cobro</span>
                        </h5>
                        <div class="card-body p-1"> 
                            <div class="card-body">
                                <div class="container pl-0 pr-0">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            @include('administracion.configuraciones.concepto_pagos.form.field')
                                            <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-cuentaxpagar-{{$cuentaxpagar->id ?? ''}}">
                                                <i class="far fa-save"></i>
                                                Registrar
                                            </button>
                                        </div>
                                        <div class="col-sm-3 pl-0 pr-0">                                        
                                            @include('administracion.configuraciones.cuentaxpagars.partial.concepto',$cuentaxpagar)
                                        </div>                                    
                                    </div>                                
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

@section('scripts')
    @parent

@endsection
