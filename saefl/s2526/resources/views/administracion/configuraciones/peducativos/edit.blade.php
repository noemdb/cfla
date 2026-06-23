@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.peducativos.menus.edit')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Actualizar <span class="font-weight-bolder">Programa Educativo</span></h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')               

                <div class="card bd-callout bd-callout-warning">
                    
                    <h5 class="card-header">Datos</h5>

                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-8">
                                    {!! Form::model($peducativo,['route' => ['administracion.configuraciones.peducativos.update', $peducativo->id], 'method' => 'PUT', 'id'=>'form-update-peducativos_'.$peducativo->id, 'role'=>'form']) !!}
                                    @include('administracion.configuraciones.peducativos.form.fields',$peducativo)
                                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-peducativo-{{$peducativo->id}}">
                                        <i class="far fa-save"></i>
                                        Actualizar
                                    </button> 
                                    {!! Form::close() !!}                                     
                                </div> 
                                <div class="col-4">
                                </div>                                   
                            </div>
                        </div>                            
                    </div>
                </div> 

            </div>

        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Programas Educativos, Editar'; </script> @endsection





