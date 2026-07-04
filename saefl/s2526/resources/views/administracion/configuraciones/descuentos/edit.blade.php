@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.configuraciones.descuentos.menus.edit')    
                    </div>
                    {{-- FIN Menu rapido --}}

                    Actualizar Asignatura

                </h3>
            </div>

            <div class="card-body">

                    @include('administracion.elements.forms.errors')

                    @include('administracion.elements.messeges.oper_ok')
                    
                    {!! Form::model($descuento,['route' => ['administracion.configuraciones.descuentos.update', $descuento->id], 'method' => 'PUT', 'id'=>'form-update-asignatura_'.$descuento->id, 'role'=>'form']) !!}
                    
                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Datos de la Asignatura                            
                            </h4>

                            <div class="card-body">                               
                                
                                <div class="row">
                                    <div class="col-3">
                                        {{-- @include('administracion.configuraciones.descuentos.deck.card.estudiant_simple') --}}
                                    </div>
                                    <div class="col-9">
                                        @include('administracion.configuraciones.descuentos.form.fields')                                
                                        <button type="submit" class="btn-descuento-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-descuento-{{$descuento->id ?? ''}}">
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

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Descuentos, actualizar'; </script> @endsection
