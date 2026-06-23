@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @includeWhen(Auth::user()->isAdmin(),'administracion.autoresponders.bmains.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Listado <span class="font-weight-bolder">de Autorespondedores</span> registrados</h4>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                {{-- @include('administracion.elements.forms.errors') --}}
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.autoresponders.bmains.table.index')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Autorrespondedor'; </script> @endsection



