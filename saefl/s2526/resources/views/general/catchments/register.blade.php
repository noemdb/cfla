@extends('general.layaout.main')

@section('title') Censo Escolar Período Escolar 2024 2025 @endsection

@section('main')

    <main role="main">

        @if ($token)

            @if ($catchment->status_active)
                <livewire:general.catchment.register-component :token="$token"/>
            @else               
                
                <div class="px-4 py-5 my-5 text-center">
                    <h1 class="display-5 fw-bold">SAEFL</h1>
                    <div class="col-lg-6 mx-auto">
                    <p class="lead mb-4">
                        <strong>La manifestación de interes a participar en éste proceso de matriculación escolar asociada a este enlace ya ha sido completada.</strong>
                    </p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-dark btn-sm " href="{{env('APP_URL_PORTAL') ?? null}}" role="button">Ir a la página principal</a>
                    </div>
                    </div>
                </div>

            @endif            
        @else

            <div class="px-4 py-5 my-5 text-center">
                <h1 class="display-5 fw-bold">SAEFL</h1>
                <div class="col-lg-6 mx-auto">
                <p class="lead mb-4">
                    <strong>No hemos encontrado una manifestación de interes a participar en éste proceso de matriculación escolar asociada a este enlace.</strong>
                </p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a class="btn btn-dark btn-sm " href="{{env('APP_URL_PORTAL') ?? null}}" role="button">Ir a la página principal</a>
                </div>
                </div>
            </div>

        @endif       

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
