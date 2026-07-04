@extends('profesors.layouts.dashboard.app')

@section('title') SAEFL - Profesor - Acción Social - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h3 class="pb-0 mb-0">
                    <i class="{{$icon_menus['social_actions'] ?? ''}} text-primary" aria-hidden="true"></i>
                    <u class="text-dark">Acción Social.</u> Servicios Ejecutados Acción Comunitaria.
                </h3>
                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}} [{{ Auth::user()->profesor->id ?? '' }}]
                </span>
            </div>

            <div class="card-body">

                <livewire:profesor.social-actions.index-component />

            </div>
        </div>
    </main>

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
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
            })
            .then((result) => {
                if (result.value) {                    
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection



