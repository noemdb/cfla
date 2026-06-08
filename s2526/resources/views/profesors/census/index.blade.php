@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-2">
                    @include('profesors.census.menus.index')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['census'] ?? ''}} text-secondary" aria-hidden="true"></i>
                    Censo escolar
                </h3>

                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span>

            </div>

            <div class="card-body p-1 m-1">

                @if (Auth::user()->profesor->status_census_taker)
                    <livewire:profesor.census.index-component />
                @else
                    <div class="alert alert-warning" role="alert">
                        <strong>No estas asignado a registrar participantes en los Censos Escolares.</strong>
                    </div>
                @endif

                

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
