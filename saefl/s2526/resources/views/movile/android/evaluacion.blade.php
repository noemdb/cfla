@extends('movile.android.layouts.app')
@section('content')

    <div class="content w-100 py-2 px-1">

        <div class=" d-flex d-flex justify-content-center px-2 py-2">
            <div>
                <i class="{{ $icon_menus['profesor'] ?? '' }} fa-3x img-thumbnail p-2"></i>
                <div class="small text-muted">Coord. de Evaluación</div>
            </div>
        </div>

        @auth
            @if (Auth::user()->IsEvaluacion())

                <livewire:movile.evaluacion.index-component />

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

@section('sweetalert')
@parent
<script>
    // Listener para eventos SweetAlert de Livewire
    window.addEventListener('swal', function(e) {
        Swal.fire({
            title: e.detail.title,
            text: e.detail.text,
            icon: e.detail.icon,
            timer: e.detail.timer,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });

    // También mantener el listener original para compatibilidad
    window.addEventListener('swal:confirm', function(e) {
        Swal.fire({
            title: e.detail.message,
            text: e.detail.text,
            icon: e.detail.type,
            buttons: true,
            dangerMode: true,
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                window.livewire.emit('remove', e.detail.id);
            }
        });
    });

    window.addEventListener('swal:question', function(e) {
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
                window.livewire.emit(e.detail.method, e.detail.id);
            }
        });
    });
</script>
@endsection
