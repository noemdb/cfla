@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['pollmain'] ?? ''}} text-info" aria-hidden="true"></i>
                    Procesos de Consultas.
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                {{-- @include('directors.financials.charts.main') --}}

            </div>
        </div>
    </main>

@endsection

