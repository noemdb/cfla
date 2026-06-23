@extends('administracion.layouts.dashboard.app')

@section('title') Generación de Recibos de Pagos Rápidos @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                {{-- <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.index') </div> --}}
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Listado de recibo de pago rápido registrados</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.receibts.recibos.table.crud')

            </div>
        </div>
    </main>

@endsection


