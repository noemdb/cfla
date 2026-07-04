@extends('administracion.layouts.dashboard.app')

@section('title') SAEFL - Timeline de Pagos @endsection

@section('main')
<main role="main" class="col-md-10 ml-sm-auto col-lg-10">
    <div class="card card-primary mt-2">
        <div class="card-header alert-secondary">
            <div class="btn-group float-right">
                @include('administracion.representants.menus.index')
            </div>
            <h4 class="pb-0 mb-0">
                <i class="{{ $icon_menus['representante'] ?? 'fas fa-users' }} fa-1x"></i>
                Timeline de Pagos
            </h4>
            <span class="small text-muted">
                Visualización cronológica de los pagos de representantes
            </span>
        </div>

        <div class="card-body">
            @livewire('administracion.representant.timeline-component')
        </div>
    </div>
</main>
@endsection


