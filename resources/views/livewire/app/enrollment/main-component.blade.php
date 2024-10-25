<div>
    
    @include('livewire.app.enrollment.start')
    @if ($modalSearch) @include('livewire.app.enrollment.search') @endif

    @section('scriptsLivewire')
        <script>
            document.addEventListener('livewire:init', function () {
                Livewire.on('redireccionar', url => {
                    console.log('123');
                    window.open(url, '_blank'); // Abre la URL en una nueva pestaña
                });
            });
        </script>
    @endsection
    
</div>


