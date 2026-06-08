@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Conceptos de Cobro, listado
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.cuentaxpagars.menus.index')
                </div>
                {{-- FIN Menu rapido --}}

                <h4><span class="font-weight-bolder">Conceptos de cobros</span> registrados</h4>

            </div>

            <div class="card-body p-1">

                @livewire('administracion.cuentaxpagar.asistente.index-component')


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
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit(e.detail.method, e.detail.id);
                    }
                });
        });
    </script>
@endsection
