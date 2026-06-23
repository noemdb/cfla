@extends('movile.android.layouts.app')

@section('content')

    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2">

            <div>

                <div class="display-1 fw-bold mb-2">
                    <div class="p-2" style="color:#004000">SAEFL</div>
                </div>

                @auth
                    <div class=" display-3 fw-bolder">Bienvenido</div>
                @else

                    @include('movile.android.partials.auth.reset')
                @endauth

            </div>

        </div>

        <div class=" mt-4">

            @include('movile.android.text.main')

        </div>

    </div>

@endsection
