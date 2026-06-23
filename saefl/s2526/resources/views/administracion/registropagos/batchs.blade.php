@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos de la Inscripción<br>
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
                    
                    {!! Form::open(['route' => 'administracion.registropagos.create', 'method' => 'POST', 'id'=>'form-inscripcion-create', 'class'=>'form-signin']) !!}
                    
                        <div class="card bd-callout bd-callout-primary">
                            <h4 class="card-header">
                                Datos
                            </h4>
                            <div class="card-body">
                                @include('administracion.registropagos.form.fields')
                            </div>
                        </div>
                    
                        <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block" value="Registrar" data-id="create" id="btn-create-inscripcion-{{$inscripcion->id ?? ''}}">
                            <i class="far fa-save"></i>
                            Registrar
                        </button>
                    
                    {!! Form::close() !!}

            </div>
        </div>
    </main>

    {!! Form::open(['route' => ['users.destroy',':USER_ID'], 'method' => 'DELETE', 'id'=>'form-delete', 'role'=>'form']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')
    @parent

    {{-- INI script ajax json models --}}
    <script src="{{ asset("js/models/users/delete.js") }}"></script>
    {{-- FIN script ajax json models --}}

@endsection





@section('scripts')
    @parent

    <script type="text/javascript">

        $(document).ready(function() {
            $('.crt_checkboxes').click(function (e) {
                //alert('123');
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');  console.log(name);
                var checked = $(this).prop('checked'); console.log(checked);
                var input = '[name='+name+']';
                $(input).val(checked); console.log($(input).val());
            });
        });

    </script>

@endsection
