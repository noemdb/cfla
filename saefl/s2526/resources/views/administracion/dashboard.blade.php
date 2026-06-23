@extends('administracion.layouts.home.app')

@section('title') - Indicadores - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        @livewire('administracion.home.dashboard')

    </main>

@endsection

@section('footer') @include('administracion.layouts.footer.dashboard') @endsection

@section('scripts')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>
    <script src="{{ asset("js/ChartEvent.js") }}"></script>
@endsection
