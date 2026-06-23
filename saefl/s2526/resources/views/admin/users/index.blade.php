@extends('admin.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary">
            <div class="card-header">
                <h3>
                    Listados de Usuarios Registrados<br>
                    <small class="text-default">
                        <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong>
                    </small>
                    
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.users.menus.index')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')             

                {{-- Partial con el listado de los usuarios --}}
                @include('admin.users.table.list')

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