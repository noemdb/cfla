@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('representants.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4><u title="Listado especial con botones de acción">Listado</u> de las Constancias de Prosecución</h4>
            </div>

            <div class="card-body">
                @include('representants.elements.forms.errors')

                    @include('representants.elements.messeges.oper_ok')

                    @include('representants.preinscripcions.partials.search_prosecucion',['route'=>'representants.preinscripcions.prosecucion'])

                    @include('representants.preinscripcions.table.prosecucion')
            </div>
        </div>
    </main>

@endsection
