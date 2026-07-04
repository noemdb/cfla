@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-1 p-1">

            <div class="alert alert-secondary mb-1 pb-1">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('evaluacions.pases.menus.index') --}}
                </div>
                <h3>
                    Evaluaciones registradas por los docentes                 
                </h3>
                <div>
                    Planes de estudio:
                    @foreach ($pestudios as $pestudio)
                        <span class="text-muted pl-2">{{$pestudio->name ?? null}}</span> ||
                    @endforeach
                </div>
            </div>

            <div class="card-body m-1 p-1">

                <livewire:evaluacion.execution.index-component />

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