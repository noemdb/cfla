@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 bd-callout bd-callout-gray">

            <div class="card-header alert-secondary">
                <h3 class="mb-0 pb-0 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div><i class="{{$icon_menus['debates'] ?? ''}} text-info pr-1" aria-hidden="true"></i><span class="font-weight-bold">Competiciones Académicas</span></div>
                    </div>
                </h3>                
            </div>
            
            <div class="card-body p-1 m-1">
                
                <livewire:profesor.debate.index-component />

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