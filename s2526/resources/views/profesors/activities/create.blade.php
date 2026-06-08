@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card bd-callout bd-callout-primary mt-2">
            <div class="card-header alert-primary">
                <h3>
                    <div class="d-flex justify-content-between">
                        <div><i class="{{$icon_menus['activities'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Módulo Plan de Actividades</span></div>
                        <div><span class="text-muted font-weight-bold" style="font-size: 1rem;opacity: 0.5;">Diseñado por: Prof. Carmin Cortez</span></div>
                    </div>
                </h3>
            </div>

            <div class="card-body p-2 m-2">

                <div class="container-fluid">
                    <div class="row">

                        <div class="col-sm-2 px-1">
                            <h5 class="card-title">Resumen</h3>
                            <div class="dropdown-divider mb-0"></div>
                            @include('profesors.activities.partials.resumen')
                        </div>

                        <div class="col-sm-10">

                            <livewire:profesor.activity.index-component :id="$pevaluacion->id"/>

                        </div>

                    </div>
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