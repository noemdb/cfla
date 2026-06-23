@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-dark">

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['chartbar'] ?? ''}} text-info" aria-hidden="true"></i>
                    Indicadores sobre el uso del <span class=" font-weight-bold">SAEFL</span>
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                @include('directors.audits.charts.main')

            </div>
        </div>
    </main>

@endsection

