@extends('administracion.layouts.home.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10" style="box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);">

            <div class="vertical-center text-center">
                <div class="container">
                    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/192/1.png') }}" alt="" width="192" height="192">
                </div>
            </div>

        {{-- <div class="h-100 w-100 align-middle text-center">

        </div> --}}

    </main>

@endsection

@section('stylesheet')
    @parent
    {{-- <link href="{{ asset('css/login.css') }}" rel="stylesheet"> --}}
    <style>
        .vertical-center {
            min-height: 100%;  /* Fallback for browsers do NOT support vh unit */
            /*min-height: 100vh; /* These two lines are counted as one :-)       */

            display: flex;
            align-items: center;
        }
    </style>
@endsection
