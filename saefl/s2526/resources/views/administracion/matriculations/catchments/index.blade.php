@extends('administracion.layouts.dashboard.app')

@section('title')
    Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }}
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">
        @livewire('administracion.matriculation.catchment-component')
    </main>
@endsection

@section('sweetalert')
    @parent
    <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script>
@endsection
