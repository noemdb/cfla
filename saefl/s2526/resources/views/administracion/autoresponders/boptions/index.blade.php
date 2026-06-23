@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @includeWhen(Auth::user()->isAdmin(),'administracion.autoresponders.boptions.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Listado <span class="font-weight-bolder">de Opciones de los autorrespondedores</span> registrados</h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.autoresponders.boptions.table.index')
                {{-- views/administracion/autoresponder/table/index.blade.php --}}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Autorrespondedor'; </script> @endsection



