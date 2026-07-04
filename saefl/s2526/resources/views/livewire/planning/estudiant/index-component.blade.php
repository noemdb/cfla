<div>

    <div class="container-fluid  px-0">

        <div class="row">

            <div class="col-{{ $modeIndex ? '12' : '4' }}" style="{{ ( ! $modeIndex ) ? 'opacity: 0.6;' : null}}">
                <div class="border rounded" style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                    <h5 class="p-2 m-2 font-weight-bold">
                        Listado de estudiantes con inscripción formalizada 
                        <div class="float-right ">
                            <small wire:loading.delay.shortest class="text-muted small px-2">
                                Procesando...
                            </small>
                        </div>
                    </h5>
                    @include('livewire.evaluacion.estudiant.table.index')
                </div>
            </div>

            @if ($modeSumamaries)
                <div class="col-8" >
                    <div class="shadow">
                        @include('livewire.evaluacion.estudiant.partials.sumamaries')
                    </div>
                </div>
            @endif

        </div>

    </div>


</div>
