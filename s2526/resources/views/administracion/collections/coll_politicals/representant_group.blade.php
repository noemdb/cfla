@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - CRUD @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.crud') </div>
                {{-- FIN Menu rapido --}}

                <h4><u title="Listado especial con botones de acción">Listado</u> de los <span class="font-weight-bolder">Rerpresentates</span></h4>
            </div>

            <div class="card-body">

                <h4>Seleccione criterio de agrupamiento</h4>
                @include('administracion.collections.coll_politicals.form.search.representant_group')

                @include('administracion.collections.coll_politicals.table.representant_group')

            </div>
        </div>
    </main>

@endsection


{{-- @section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Bancos, actualizar datos'; </script>
@endsection --}}
