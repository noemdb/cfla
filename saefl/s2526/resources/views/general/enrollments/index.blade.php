@extends('general.layaout.main')

@section('title') Proceso de Renovación de Matrícula @endsection

@section('main')

    <main role="main">
        
        @include('general.enrollments.partials.main')

        {{-- <livewire:general.enrollment.index-component :token="$token"/> --}}

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
