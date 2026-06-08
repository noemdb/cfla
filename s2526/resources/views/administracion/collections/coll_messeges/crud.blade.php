@extends('administracion.layouts.dashboard.app')

@section('title') Niveles de Cobranza - CRUD @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_messeges.menus.crud') </div>
                {{-- FIN Menu rapido --}}

                <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">Actividades de Cobranza</span> registradas</h4>
            </div>

            <div class="card-body">

                @include('administracion.collections.coll_messeges.table.crud')

            </div>
        </div>
    </main>

@endsection


{{-- @section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Bancos, actualizar datos'; </script>
@endsection --}}
