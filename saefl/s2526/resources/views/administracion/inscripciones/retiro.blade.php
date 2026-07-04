@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-danger">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">

                    @include('administracion.inscripciones.menus.retiro')

                </div>
                {{-- FIN Menu rapido --}}

                <h4>
                    <i class="{{ $icon_menus['retiro'] }} fa-1x"></i>
                    Retiro académico de <span class="font-weight-bolder">Estudiantes</span>
                </h4>
            </div>

            <div class="card-body">

                {{-- 
                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                <div class="card-header p-0 m-0 mb-2">
                    @include('administracion.inscripciones.form.search')
                </div>

                <div class=" border rounded p-1">
                    @include('administracion.inscripciones.table.retiro')
                </div> --}}

                @livewire('administracion.administrativa.asistente.retiro-component')

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
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });
    </script>
@endsection
