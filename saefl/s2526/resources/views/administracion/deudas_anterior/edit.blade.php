@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos del concepto {{$cuentaxpagar->name ?? ''}}<br>
                    <small class="text-default">
                        {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                    </small>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        {{-- @include('administracion.configuraciones.menus.index') --}}

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')
                    @php
                        $form = 'form-update-cuentaxpagar_'.$cuentaxpagar->id;
                    @endphp
                    
                    @foreach ($conceptopagos as $conceptopago)
                    
                        {!! Form::model($conceptopago,['route' => ['administracion.deudas_anterior.update', $conceptopago->id], 'method' => 'PUT', 'id'=>$form, 'role'=>'form']) !!}

                            <div class="card bd-callout bd-callout-primary">

                                <h4 class="card-header">
                                    <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                    Actualizar Concepto                             
                                </h4>

                                <div class="card-body">                                
                                    <div class="row">
                                        <div class="col-3">
                                            {{-- @include('administracion.cuentaxpagars.deck.card.estudiant_simple') --}}
                                        </div>
                                        <div class="col-7">
                                            {{-- @include('administracion.cuentaxpagars.form.fields.transaccion')   --}}
                                            {{-- @include('administracion.cuentaxpagars.partial.navtabs') --}}
                                            @include('administracion.deudas_anterior.form.fields')

                                            @php
                                                $btn_update = 'btn-update-'.$conceptopago->id
                                            @endphp
                                            <button type="submit" class="btn-cuentaxpagar-edit btn btn-primary btn-block" value="Actualizar" data-id="create" id="{{$btn_update ?? ''}}">
                                                <i class="far fa-save"></i>
                                                Actualizar
                                            </button>
                                        </div>
                                        <div class="col-2 text-right small pl-0">
                                            {{-- @include('administracion.conceptopago.partial.resumen') --}}
                                        </div>
                                    </div>
                                                                
                                </div>
                            </div>                    
                            
                        
                        {!! Form::close() !!}

                    @endforeach

            </div>
        </div>
    </main>

@endsection

@section('scripts')
@parent

@endsection
