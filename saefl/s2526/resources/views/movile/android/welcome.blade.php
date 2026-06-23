@extends('movile.android.layouts.app')

@section('content')

    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2">

            <div>

                <div class="display-1 fw-bold mb-2">

                    <img class="bi pe-none" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">

                </div>

                @auth
                    <div class=" display-3 fw-bolder">Bienvenido</div>
                @else
                    @include('movile.android.login')
                @endauth

            </div>

        </div>

        <div class=" mt-4">

            @include('movile.android.text.main')

        </div>

    </div>

@endsection

