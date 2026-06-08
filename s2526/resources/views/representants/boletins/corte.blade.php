@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">

                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('representants.boletins.menus.crud') --}}
                </div>

                <h4><b class="text-dark">Informe de Corte de Notas</b> </h4>
                <small class="text-muted small float-right"> Los archivos generados en ésta sección no son válidos sin firma y sello de la institución</small>

            </div>

            <div class="card-body bd-callout bd-callout-secondary p-2 m-2">

                @if ($pescolar->status_begin)
                    @include('representants.boletins.table.corte')
                    @include('representants.boletins.help.corte.main')
                    @include('representants.boletins.modals.updateCuteNote')
                @else
                    <div class="alert alert-warning font-weight-bold">Aún no ha iniciado formalmente el perìodo escolar 2023 2024.</div>
                @endif                

            </div>
        </div>
        
    </main>

@endsection
