@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary">
            <div class="card-header">
                <h3>

                    Búsqueda de Pagos Adelantados<br>
                    <small class="text-default">
                        {{-- <strong><span id="user_counter">{{$users->count()}}</span> Usuarios</strong> --}}
                    </small>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                        @include('administracion.padres.menus.index')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>

            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('administracion.elements.messeges.oper_ok')

                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Pagos Adelantados" aria-label="Pagos Adelantados" aria-describedby="button-addon2">
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-success" type="button" id="button-addon2">Buscar</button>
                    </div>
                </div>

                {{-- Partial con el listado de los usuarios --}}
                {{-- @include('admin.users.table.list') --}}

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
