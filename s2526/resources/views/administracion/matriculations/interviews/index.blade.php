@extends('administracion.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        @if (session('operp_ok'))
            <div class="alert alert-success">{{ session('operp_ok') }}</div>
        @endif
        @if (session('operp_error'))
            <div class="alert alert-danger">{{ session('operp_error') }}</div>
        @endif

        @livewire('administracion.matriculation.interview-component')

    </main>
@endsection

@section('sweetalert')
    @parent
    <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script>
@endsection