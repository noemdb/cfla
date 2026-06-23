@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                {{-- <h4>Sincronizaciones relizadas anteriormente por: <strong> {{ $user->fullname ?? ''}} </strong></h4> --}}
                <h4>Descarga de archivos XLS requeridos para la sincronización de datos al portal web</h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                {{-- @include('administracion.elements.forms.errors') --}}

                {{-- @include('administracion.elements.messeges.oper_ok') --}}

                @admon @include ('administracion.configuraciones.sync_datas.partials.admon') @endadmon

                @control @include ('administracion.configuraciones.sync_datas.partials.control') @endcontrol

                <hr>

                {{-- @include('administracion.configuraciones.sync_datas.table.index') --}}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Sincronizaciones, listado'; </script> @endsection
