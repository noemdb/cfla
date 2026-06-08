@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0 pb-0 border-bottom">
                            <i class="{{$icon_menus['competitions'] ?? ''}} text-info pr-1" aria-hidden="true"></i>
                            <u title="Listado especial con botones de acción">Listado</u> de las <strong>Áreas de Formación</strong> asignadas
                            </h3> 
                        </div>
                        <div>[Prof: {{ Auth::user()->profesor->fullname}}]</div>
                    </div>
                               
            </div>
            
            <div class="card-body p-1 m-1">
                
                <livewire:profesor.competition.index-component />

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