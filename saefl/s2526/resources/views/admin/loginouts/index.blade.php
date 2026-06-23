@extends('admin.layouts.dashboard.app')

@section('main')

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary">
            <div class="card-header">
                <h3>
                    Listados de LogInOuts Registrados<br>
                    <small class="text-default">
                        <strong><span id="loginout_counter">{{$loginouts->count()}}</span> LogInOuts</strong>
                    </small>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.loginouts.menus.index')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body p-1">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')

                {{-- Partial con el listado de los usuarios --}}
                @include('admin.loginouts.table.list')

            </div>
        </div>
    </main>

    {!! Form::open(['route' => ['loginouts.destroy',':LOGINOUT_ID'], 'method' => 'DELETE', 'id'=>'form-delete', 'loginout'=>'form']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')
    @parent

    {{-- INI script ajax json models --}}
    {{-- <script src="{{ asset("js/models/loginouts/delete.js") }}"></script> --}}
    {{-- FIN script ajax json models --}}

@endsection