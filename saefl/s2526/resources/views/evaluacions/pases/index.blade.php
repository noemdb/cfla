@extends('evaluacions.layouts.dashboard.app')

@section('main')
    <main role="main">

        <div class="card m-1 p-1">

            {{--
            <div class="alert alert-danger mb-1 pb-1">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('evaluacions.pases.menus.index')
                </div>
                <h3>Gestión de Pases Escolares. <small class="text-muted small d-block">Este módulo esta en etapa de validación y aprobación.</small></h3>

                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2">{{$pestudio->name ?? null}}</span> ||
                    @endforeach
                </div>
            </div>
            --}}

            <div class="card-body m-1 p-1">

                {{-- @include('administracion.elements.messeges.oper_ok')  --}}

                {{-- @includeif('evaluacions.pases.tables.index') --}}

                @livewire('evaluacion.pase.index-component')

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
