@extends('admin.layouts.dashboard.app')

@section('main')

    <main taske="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary">
            <div class="card-header">
                <h3>
                    Listados de Settings Registrados<br>
                    <small class="text-default">
                        <strong><span id="setting_counter">{{$settings->count()}}</span> Settings</strong>
                    </small>

                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">

                        @include('admin.settings.menus.index')

                    </div>
                    {{-- FIN Menu rapido --}}

                </h3>
            </div>

            <div class="card-body p-1">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')

                {{-- Partial con el listado de los usuarios --}}
                @include('admin.settings.table.list')

            </div>
        </div>
    </main>

    {!! Form::open(['route' => ['settings.destroy',':SETTING_ID'], 'method' => 'DELETE', 'id'=>'form-delete', 'setting'=>'form']) !!}
    {!! Form::close() !!}

@endsection

@section('scripts')
    @parent

    {{-- INI script ajax json models --}}
    {{-- <script src="{{ asset("js/models/settings/delete.js") }}"></script> --}}
    {{-- FIN script ajax json models --}}

@endsection