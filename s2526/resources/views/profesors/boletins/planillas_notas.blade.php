@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.boletins.menus.index')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['notas'] ?? ''}} text-secondary" aria-hidden="true"></i>
                    Planilla de <span class="font-weight-bold">Registro de Notas</span> por lapsos y definitiva
                </h3>

                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span>

            </div>

            <div class="card-body p-1 m-1">

                <h6 class="pb-2"><u>Listado</u> de Asignaturas</h6>

                {{-- @include('profesors.boletins.partials.nav_tabs.pensums') --}}

                @include('profesors.boletins.table.planillas_notas')

            </div>

        </div>

    </main>

@endsection

