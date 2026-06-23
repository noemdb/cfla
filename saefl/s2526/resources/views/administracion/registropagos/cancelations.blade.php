@extends('administracion.layouts.dashboard.app')

@section('title') Gestión de Anulación de Pagos - {{ Auth::user()->rol ?? '' }} @endsection

@section('stylesheet')
    <style>
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; }
        .table-info { background-color: #d1ecf1; }
    </style>
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">
        @livewire('administracion.registro-pago.cancelation-component')
    </main>
@endsection

@section('scripts')
    @parent
    {{-- SweetAlert2 for notifications --}}
    <script src="{{ asset('vendor/sweetalert/11.4.8/js/sweetalert2.all.min.js') }}"></script>
@endsection
