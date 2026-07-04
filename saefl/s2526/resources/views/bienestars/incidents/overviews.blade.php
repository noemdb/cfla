@extends('bienestars.layouts.dashboard.app')

@section('title') Indicadores - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        {{-- <h3>Registro de incidentes estudiantiles.</h3> --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <livewire:bienestar.overview.index-component />
                </div>
            </div>
        </div>
    </main>

{{--     <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">

                	<div class="card-header  alert-dark mt-2">

			            <h3 class="mb-0 pb-0">

			                <div class="btn-group float-right pt-0 pb-2">
			                    @include('bienestars.incidents.menu.overviews')
			                </div>

			                <i class="{{$icon_menus['overviews'] ?? ''}} text-success" aria-hidden="true"></i>
			                <span class=" font-weight-bold">Reportes semanales de incidencias.</span>
                            <div class="text-muted small">Por docente</div>

			            </h3>

			        </div>

                	<livewire:bienestar.overview.index-component />


                </div>
            </div>
        </div>
    </main> --}}

@endsection
