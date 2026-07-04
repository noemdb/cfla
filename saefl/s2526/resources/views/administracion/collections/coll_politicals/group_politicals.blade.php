@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - CRUD @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.crud') </div>
                {{-- FIN Menu rapido --}}

                <h4><u title="Listado especial con botones de acción">Listado</u> de los <i>Rerpresentates</i> por <strong>Política de Cobro</strong></h4>
            </div>

            <div class="card-body">

                <h5>Seleccione <strong>Política de Cobro</strong></h5>
                @include('administracion.collections.coll_politicals.form.search.group_politicals')

                @include('administracion.collections.coll_politicals.table.group_politicals')

            </div>
        </div>
    </main>

@endsection


{{-- @section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Bancos, actualizar datos'; </script>
@endsection --}}
