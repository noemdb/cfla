@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['administracion'] ?? ''}} text-info" aria-hidden="true"></i>
                    Indicadores Administrativos
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                @include('academicos.financials.charts.main')

            </div>
        </div>
    </main>

@endsection

