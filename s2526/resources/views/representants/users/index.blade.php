@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-12 p-1">
                    <h3>
                        <i class="{{ $icon_menus['user'] }} fa-1x text-dark"></i>
                        Actualiza los datos de usuario.
                    </h3>

                    <div>Próximamente..</div>

                    {{-- <livewire:representant.users.index-component /> --}}
                </div>

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
