@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('representants.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4>
                    <u title="Listado especial con botones de acción">Listado</u> de <span class=" font-weight-bold ">Inscripciones</span> formalizadas
                </h4>
                <small class=" font-weight-bold text-muted float-right">
                    Las contacias generadas en éste listado especial, no son válidas sin firma y sello de la institución
                </small>
            </div>

            <div class="card-body">

                @include('representants.inscripcions.table.crud')

            </div>

        </div>
    </main>

@endsection
