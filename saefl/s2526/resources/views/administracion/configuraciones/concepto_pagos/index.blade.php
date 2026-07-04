@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Cuentas de Cobro, listado
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.concepto_pagos.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                <h4> <u title="Listado especial con botones de acción">Listado</u> de las <span
                        class="font-weight-bolder">Cuentas de Cobros</span> registradas</h4>
            </div>

            <div class="card-body p-1">

                @livewire('administracion.configuraciones.concepto-pago-crud')

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
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
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
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });
    </script>
@endsection
