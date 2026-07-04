@extends('movile.android.layouts.app')

@section('content')

    <div class="content w-100 py-2 px-1 h-100">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['poll'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Herramienta de consulta</div>
            </div>
        </div>

        @auth
            <div class="p-1">
                @include('movile.android.module.poll.main')
            </div>
        @else
            <a name="" id="" class="btn btn-dark btn-sm" href="{{route('movile.android.welcome')}}" role="button">
                <div> Inicia tu sesión </div>
            </a>
        @endauth

        @include('movile.android.help.poll.info')

    </div>

@endsection

@section('stylesheets')
	@parent
	{{-- <link href="{{ asset('vendor/stepper/css/all.css') }}" rel="stylesheet"> --}}
	{{-- <link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet"> --}}
@endsection
