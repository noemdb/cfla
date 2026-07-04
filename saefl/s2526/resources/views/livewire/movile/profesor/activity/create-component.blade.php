<div>    

    <div wire:init class="px-1 mx-1 pt-2 fade-in">

        @if ($lapso->status_preclosing)

            @include('livewire.movile.profesor.activity.form.fields')            
        
            <div class="input-group mt-2">

                {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'saveActivity()']) !!}

            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small text-start p-1 mt-2 mb-1" role="alert">
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    <ul class="mb-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
        @else
            <div class="alert alert-warning">
                No se pueden crear actividades, fecha de precierre ({{$lapso->full_date_preclosing}}) vencida.
            </div>
        @endif

        <!-- Spinner de carga -->
        <div wire:loading class="position-fixed bottom-0 end-0 p-3 rounded-4 bg-white shadow-sm p-2 m-2">
            <div class="spinner-border text-success spinner-border-sm" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

    </div>

    @section('livewires')
    @parent

    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.hook('element.initialized', (el) => {
                el.classList.add('fade-in');
            });
        
            Livewire.hook('element.removed', (el) => {
                el.classList.add('fade-out');
                setTimeout(() => {
                    el.remove();
                }, 500); // Ajusta el tiempo según la duración de la transición
            });
        });
    </script>
    @endsection


    

</div>
