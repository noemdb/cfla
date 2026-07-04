@extends('movile.android.layouts.app')
@section('content')

    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['representante'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Representes</div>
            </div>
        </div>

        @auth

            @if (Auth::user()->IsRepresentant())

                @include('movile.android.module.representant.main')

                @include('movile.android.help.representant.info')

            @else
                <div class="content pt-4 mt-4">
                    <div>Contenido no disponible</div>
                </div>
            @endif

        @else

            <a name="" id="" class="btn btn-dark btn-sm" href="{{route('movile.android.welcome')}}" role="button">
                <div> Inicia tu sesión </div>
            </a>

        @endauth

    </div>

@endsection

@section('stylesheets')
	@parent
	{{-- <link href="{{ asset('vendor/stepper/css/all.css') }}" rel="stylesheet"> --}}
	{{-- <link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet"> --}}
@endsection
