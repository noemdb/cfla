@extends('bienestars.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')
    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">

                    @if (session('operp_ok'))
                        <div class="alert alert-success">{{ session('operp_ok') }}</div>
                    @endif
                    @if (session('operp_error'))
                        <div class="alert alert-danger">{{ session('operp_error') }}</div>
                    @endif

                    @livewire('bienestar.matriculation.interview-component')
                </div>
            </div>
        </div>
    </main>
@endsection

@section('sweetalert')
    @parent
    <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script>
@endsection
