<div>

    <div class="card-header  alert-dark mt-2">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.bienestar.interview.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>

            <i class="{{ $icon_menus['interviews'] ?? '' }} text-dark" aria-hidden="true"></i>
            <span class=" font-weight-bold">Agenda de entrevistas por profesor</span>

        </h3>

        {{-- @include('livewire.bienestars.bienestar.modals.helps') --}}

    </div>

    <div class="card-body p-1 m-1">

        @if (Session::has('operp_ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! Session::get('operp_ok') !!}
            </div>
        @endif

        <div class="container-fluid  px-0">

            <div class="row">

                <div class="col-{{ $modeIndex ? '12' : '4' }}" style="{{ ( ! $modeIndex ) ? 'opacity: 0.6;' : null}}">
                    <div class="border rounded" style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                        <h5 class="p-2 m-2 font-weight-bold"> Listado de profesores con al menos una incidencia registrada</h5>
                        @include('livewire.bienestar.interview.table.index')
                    </div>
                </div>

                @if ($modeView)
                    <div class="col-8">
                        <div>
                            @include('livewire.bienestar.interview.mode.itinerary')
                        </div>
                    </div>
                @endif
                
                @if ($modeCalendar)
                    <div class="col-8">
                        <div>
                            @include('livewire.bienestar.interview.mode.calendar')
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

