@extends('plannings.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card m-1 p-1">
            <div class="card card-success mb-2 bd-callout bd-callout-info">
                <div class="card-header alert-secondary">
                    <h4 class="mb-0 pb-0 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div class="text-dark">
                                <i class="{{$icon_menus['pevaluacion'] ?? ''}} pr-1" aria-hidden="true"></i>
                                <span class="font-weight-bold">Asignación de <strong>Carga Académica</strong>   </span>
                            </div>
                        </div>

                        <div class="text-right">    
                        <span class="text-muted small font-weight-bold">Planes Educativos: </span>
                        @foreach ($pestudios as $pestudio)
                            <span class="text-muted pl-2 small">{{$pestudio->name ?? null}}</span>  @if (! $loop->last) || @endif
                        @endforeach
                        </div>
                    </h4>                 
                </div>
            </div>
            <div class="card-body m-1 p-1">
                <livewire:planning.pevaluacion.index-component />
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