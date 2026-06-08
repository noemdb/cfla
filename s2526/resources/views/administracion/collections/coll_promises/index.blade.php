@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza Niveles - Inicio @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_promises.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Niveles</span></h3>
            </div>

            <div class="card-body">

                {{-- @include('administracion.configuraciones.banco.edit') --}}

            </div>
        </div>
    </main>

@endsection


{{-- @section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Bancos, actualizar datos'; </script>
@endsection --}}
