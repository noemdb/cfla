@extends('bienestars.layouts.dashboard.app')

@section('title') Indicadores - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">

                	<div class="card-header  alert-dark mt-2">

			            <h3 class="mb-0 pb-0">

			                <div class="btn-group float-right pt-0 pb-2">
			                    @include('bienestars.incidents.menu.summaries')
			                </div>

			                <i class="{{$icon_menus['chartarea'] ?? ''}} text-info" aria-hidden="true"></i>
			                <span class=" font-weight-bold">Indicadores.</span>
                            <div class="text-muted small">Porcetajes presentados son demotrativos</div>

			            </h3>

			        </div>

                	@include('bienestars.incidents.partials.details.general')

                	{{-- <hr> --}}

                	{{-- @include('bienestars.incidents.partials.summaries.patologies') --}}


                </div>
            </div>
        </div>
    </main>

@endsection
