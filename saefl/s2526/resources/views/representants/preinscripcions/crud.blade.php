@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    @include('representants.preinscripcions.menus.crud')
                </div>
                <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class=" font-weight-bold"> Preinscripciones</span> Académicas registradas</h4>
            </div>

            <div class="card-body">

                {{-- @include('representants.elements.forms.errors') --}}

                @include('representants.elements.messeges.oper_ok')

                {{-- @include('representants.preinscripcions.partials.search',['route'=>'representants.preinscripcions.crud']) --}}

                @include('representants.preinscripcions.table.crud')

            </div>
        </div>
    </main>

@endsection
