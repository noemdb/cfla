@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card bd-callout bd-callout-{{$grado->color ?? 'default'}} mt-2 border">
            <div class="card-header alert-secondary">
                <div class="btn-group float-right">
                    @include('administracion.configuraciones.profesor_guias.menus.create')
                </div>
                <h4> Designación del <span class="font-weight-bolder">Profesores Guía</span></h4>
            </div>

            <div class="card-body p-2 border">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                <div class="card-header alert-secondary py-1">
                    <h5>Datos</h5>
                </div>

                <div class="border rounded-bottom p-2 m-2">
                    <div class="container">
                        <div class="row font-weight-bold text-secondary">
                            <div class="col-8">
                                {!! Form::open(['route' => 'administracion.configuraciones.profesor_guias.store', 'method' => 'POST', 'id'=>'form-pevaluacions-create', 'class'=>'form-signin']) !!}
                                    @include('administracion.configuraciones.profesor_guias.form.fields')
                                    <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-pevaluacions">
                                        <i class="far fa-save"></i> Designar
                                    </button>
                                {!! Form::close() !!}                                
                            </div>
                            <div class="col-4">
                                @include('administracion.configuraciones.profesor_guias.partials.resumen.create')
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
