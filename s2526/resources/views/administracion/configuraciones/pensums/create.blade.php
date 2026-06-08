@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card mt-2 bd-callout bd-callout-{{$grado->color ?? 'default'}}">
            <div class="card-header alert-secandary ">
                <h6>
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-0 pb-2">
                        @include('administracion.configuraciones.pensums.menus.create')
                    </div>
                    {{-- FIN Menu rapido --}}
                    Datos de la Asignatura a agregar para el Pensum de <b>{{$pestudio->fullname ?? ''}}</b><br>
                    <small class="small text-muted">
                        {{$grado->name ?? ''}}
                    </small>
                </h6>
            </div>

            <div class="card-body p-1">

                @include('administracion.elements.forms.errors')
                
                {!! Form::open(['route' => 'administracion.configuraciones.pensums.store', 'method' => 'POST', 'id'=>'form-pensums-create', 'class'=>'form-signin']) !!}
                    
                    {{ Form::hidden('pestudio_id', $pestudio->id) }}                            
                    {{ Form::hidden('grado_id', $grado->id) }}                            
                
                    {{-- <div class="card bd-callout bd-callout-primary"> --}}
                        {{-- <h5 class="card-header pb-0 mb-0"><span class="font-weight-bold text-primary">{{$pestudio->name}}</span></h5> --}}
                        <div class="card-body p-1"> 
                            <div class="card-body p-1">
                                <div class="container pl-0 pr-0">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            @include('administracion.configuraciones.pensums.form.fields')
                                            <button type="submit" class="btn-user-update btn-sm btn-primary btn-block" value="update" data-id="update" id="btn-update-pensums-{{$pestudio->id ?? ''}}">
                                                <i class="far fa-save"></i>
                                                Asignar
                                            </button>
                                        </div>
                                        <div class="col-sm-3 pl-0 pr-0 small">
                                            @php $pensums = (!empty($grado->pensums)) ? $grado->pensums:null; @endphp                                      
                                            @includewhen((count($pensums)>0),'administracion.configuraciones.pensums.partial.resumen')
                                        </div>                                    
                                    </div>                                
                                </div>
                            </div>                                
                        </div>
                    {{-- </div>    --}}

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
