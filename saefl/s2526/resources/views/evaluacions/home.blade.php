@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-0">

            <div class="alert alert-secondary mb-1 pb-1">
                <h3>Coordinación de Evaluación.</h3>
                
                @foreach ($pestudios as $pestudio)
                    <div class="text-muted pl-2">{{$pestudio->name ?? null}}</div>
                @endforeach
            </div>

            <div class="card-body m-1 p-1">
                
                @includeif('evaluacions.home.indicators')

            </div>

        </div>

    </main>

@endsection


@section('chartjs')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>
    <script src="{{ asset("js/ChartEvent.js") }}"></script>
@endsection
