@extends('plannings.layouts.home.app')

@section('main')

    <main role="main" class="">

        <div class="card m-0">

            <div class="alert alert-secondary mb-1 pb-1">
                <h3>Módulo de Planificación.</h3>
                
                @foreach ($pestudios as $pestudio)
                    <div class="text-muted pl-2">{{$pestudio->name ?? null}}</div>
                @endforeach

            </div>

            <div class="card-body m-1 p-1">
                
                @includeif('plannings.partials.index')

            </div>

        </div>

    </main>

@endsection

@section('scripts')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
@endsection

