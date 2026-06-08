@extends('profesors.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.incidents.menus.index')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{ $icon_menus['census'] ?? '' }} text-secondary" aria-hidden="true"></i>
                    Gestionamiento de las Incidencias Estudiantiles.
                </h3>

                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname }}
                </span>

            </div>

            <div class="card-body p-1 m-1">

                @if ($profesor->is_profesor_guia)
                    <livewire:profesor.incident.index-component />
                @else
                    <div class="alert alert-warning" role="alert">
                        <h4>Pronto estará 100% operativo el módulo <strong>Gestión de Incidencias.</strong></h4>
                        {{-- <strong>Pronto estará 100% operativo.</strong> --}}
                    </div>
                @endif

            </div>

        </div>

    </main>
@endsection

@section('sweetalert')  @parent  <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
