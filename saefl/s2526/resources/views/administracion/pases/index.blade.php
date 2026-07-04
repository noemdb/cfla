@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card m-0 p-0">

            <div class="card-body m-0 p-0">

                @livewire('administracion.pase.index-component')

            </div>

        </div>

    </main>

@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {
            Swal.fire({
                    title: e.detail.message,
                    text: e.detail.text,
                    icon: e.detail.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('remove', e.detail.id);
                    }
                });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                    title: e.detail.message,
                    html: e.detail.text,
                    icon: e.detail.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('sendNotification', e.detail.id);
                    }
                });
        });

        window.addEventListener('swal:question-status', function(e) {
            Swal.fire({
                    title: e.detail.message,
                    html: e.detail.text,
                    icon: e.detail.type,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('changeStatusDirect', e.detail.id, e.detail.newStatus);
                    }
                });
        });
    </script>
@endsection
