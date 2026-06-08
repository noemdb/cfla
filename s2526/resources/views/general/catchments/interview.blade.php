@extends('general.layaout.main')

@section('title') Entrevista para el Censo Escolar Período Escolar 2024 2025 @endsection

@section('main')

<main role="main">

    <div class="container-fluid alert">
        <div class="row">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2 px-1">
            {{-- <div class="col-sm-12 col-md-12 col-lg-10 offset-lg-1 col-xl-10  offset-xl-1"> --}}
                <div class="d-flex justify-content-center bd-highlight mb-3">
                    <div class="p-2 bd-highlight">

                        <div class="form-signin">

                            <h2 class="text-center text-success fw-bold">{{$institucion->name}}</h2>
                            <h3 class="text-success text-center">Entrevista, Censo Escolar<br>2024 2025</h3>
                            <div class="text-center">
                                <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                            </div>

                            <livewire:general.catchment.interview-component />

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection