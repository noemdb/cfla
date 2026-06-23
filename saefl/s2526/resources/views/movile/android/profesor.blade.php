@extends('movile.android.layouts.app')
@section('content')

    <div class="content w-100 py-2 px-0">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['profesor'] ?? '' }} fa-3x img-thumbnail py-2"></i>
                <div class="small text-muted fw-bold">Profesor</div>
            </div>
        </div>

        @auth
            @if (Auth::user()->IsProfesor())

                @include('movile.android.module.profesor.main')

                @include('movile.android.help.profesor.info')

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


@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                console.log('event');
                if (result.value) {
                    window.livewire.emit('remove',e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question',function(e){
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                toast: e.detail.toast,
                position: e.detail.position,
            })
            .then((result) => {                
                if (result.value) {
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection