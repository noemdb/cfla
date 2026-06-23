@extends('general.layaout.main')

@section('title') U.E. Colegio Fray Luis Amigó - Entrevistas @endsection

@section('main')

    <main role="main">

        <div style="opacity: 1 !important">
            <livewire:general.interview.index-component/> 
        </div>              

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection


