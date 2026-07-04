@extends('general.layaout.main')

@section('title') U.E. Colegio Fray Luis Amigó @endsection

@section('main')

    <main role="main">

        <div class="d-flex flex-column align-items-center bg-secondary-subtle">
            <div class="pr-2 mr-2 text-center">
                <div class="text-success text-uppercase fw-bolder">C.E. Colegio Fray Luis Amigó</div>
                <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="96" height="96">
            </div>
            <div class="flex-grow-1 text-center text-success">
                <h2 id="competicion-title">Instrumento de Diágnostico</h2>
            </div>
        </div>

        <livewire:general.instrument.diagnostig-component /> 

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection


