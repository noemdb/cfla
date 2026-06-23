@extends('evaluacions.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-1 p-1">
            <div class="card card-success mb-2 bd-callout bd-callout-primary">
                <div class="card-header alert-secondary">
                    <h4 class="mb-0 pb-0 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="{{$icon_menus['profesors'] ?? ''}} text-info pr-1" aria-hidden="true"></i>
                                <span class="font-weight-bold">Listado de profesores activos que pertenecen a los planes estudio </span>
                            </div>
                        </div>
    
                        @foreach ($peducativos as $peducativo)
                            <span class="text-muted pl-2 small">{{$peducativo->name ?? null}}</span>  @if (! $loop->last) || @endif
                        @endforeach
                    </h4>                 
                </div>
            </div>
            <div class="card-body m-1 p-1">
                @include('evaluacions.profesors.table.index')
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
                console.log('event');
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
                showCancelButton: true,
                toast: e.detail.toast,
                position: e.detail.position,
            })
            .then((result) => {                
                if (result.value) {
                    window.livewire.emit(e.detail.method,e.detail.id);
                }
            });
        });

    </script>
@endsection