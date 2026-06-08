@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">
                <h3>
                    Crear nuevo boletin<br>
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
                    
                    {!! Form::open(['route' => 'administracion.boletins.store', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}
                        
                        {{-- {{ Form::hidden('pescolar_id', Session::get('pescolar_id')) }} --}}
                    
                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                Datos del boletin 
                            </h4>

                            <div class="card-body">                               
                                
                                <div class="row">
                                    <div class="col-3">
                                        {{-- @include('administracion.boletins.deck.card.benefico') --}}
                                    </div>
                                    <div class="col-9">
                                        @include('administracion.boletins.form.fields')  
                                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion">
                                            <i class="far fa-save"></i>
                                            Registrar
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

@section('scripts')
    @parent

@endsection





@section('scripts')
    @parent

@endsection
