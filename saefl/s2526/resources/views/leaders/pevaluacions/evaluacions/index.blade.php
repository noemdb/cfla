@extends('leaders.layouts.dashboard.app')

@section('main')

    <main role="main">

        <h4 class="alert alert-secondary">
            Listado de evaluaciones registradas que pertenecen a las Áreas de Conocimiento <br>
            @foreach ($area_conocimientos as $item)
                <small class="text-uppercase font-weight-bold text-muted" style="font-size: 1rem"> {{$loop->iteration }} {{$item->name ?? null}} @if (! $loop->last ) || @endif</small> 
            @endforeach          
        </h4>

        
        {{-- @include('leaders.profesors.table.index') --}}

        <livewire:leader.evaluacion-component />  
        
        
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