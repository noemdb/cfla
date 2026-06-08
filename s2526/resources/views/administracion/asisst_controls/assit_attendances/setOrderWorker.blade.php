@extends('administracion.layouts.dashboard.app')

@section('title') Control de Asistencia - Establecimiento de Horarios @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.asisst_controls.assit_attendances.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3> Gestionamiento de información del <span class="font-weight-bolder">Personal</span> para el Formato de Asistencia</h3>
            </div>

            <div class="card-body">

                {{-- <livewire:administracion.assist-control.assit-attendance.worker-component /> --}}

                <livewire:administracion.users.index-component />

            </div>

        </div>
    </main>

@endsection


@section('modals') {{-- modals --}} @endsection 

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