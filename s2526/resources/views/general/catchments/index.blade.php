@extends('general.layaout.main')

@section('title') U.E. Colegio Fray Luis Amigó @endsection

@section('main')

    <main role="main">

        <div style="opacity: 1 !important">
            <livewire:general.catchment.index-component/>
            
        </div> 

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection




