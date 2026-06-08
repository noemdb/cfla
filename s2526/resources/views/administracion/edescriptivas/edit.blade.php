@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header">
                <h3>
                    Datos del Banco<br>
                    <small class="text-default">
                        {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                    </small>

                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}

                        {{-- @include('administracion.configuraciones.menus.index') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                {!! Form::model($banco,['route' => ['administracion.configuraciones.bancoupdate', $banco->id], 'method' => 'PUT', 'id'=>'form-update-banco_'.$banco->id, 'role'=>'form']) !!}

                    <div class="card bd-callout bd-callout-{{$banco->status_active_bank=='true'  ? 'primary':'danger'}}">
                        <h4 class="card-header">
                            Generales
                        </h4>
                        <div class="card-body">
                            @include('administracion.configuraciones.banco.form.field',$banco)
                        </div>
                    </div>

                    <button type="submit" class="btn-user-update btn btn-primary btn-block" value="update" data-id="update" id="btn-update-banco-{{$banco->id}}">
                        <i class="far fa-save"></i>
                        Actualizar
                    </button>

                {!! Form::close() !!}
            </div>

        </div>
    </main>

    {{-- {!! Form::open(['route' => ['users.destroy',':USER_ID'], 'method' => 'DELETE', 'id'=>'form-delete', 'role'=>'form']) !!}
    {!! Form::close() !!} --}}

@endsection

@section('scripts')
    @parent

    {{-- INI script ajax json models --}}
    {{-- <script src="{{ asset("js/models/users/delete.js") }}"></script> --}}
    {{-- FIN script ajax json models --}}

@endsection

@section('style')
    @parent
@endsection




