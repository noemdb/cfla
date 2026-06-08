@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos del pensum<br>
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
                    
                    {!! Form::model($pensum,['route' => ['administracion.configuraciones.pensums.update', $pensum->id], 'method' => 'PUT', 'id'=>'form-update-pensum_'.$pensum->id, 'role'=>'form']) !!}
                        
                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                Actualizar Pensum                              
                            </h4>

                            <div class="card-body">                               
                                
                                @include('administracion.configuraciones.pensums.form.fields')
                                
                            </div>
                            
                        </div>
                    
                        <button type="submit" class="btn-pensum-create btn btn-primary btn-block" value="Actualizar" data-id="create" id="btn-create-inscripcion-{{$pensum->id ?? ''}}">
                            <i class="far fa-save"></i>
                            Actualizar
                        </button>
                    
                    {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

