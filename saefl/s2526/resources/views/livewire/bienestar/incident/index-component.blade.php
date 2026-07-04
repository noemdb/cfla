<div>

    <div class="card-header  alert-dark mt-2">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.bienestar.incident.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>

            <i class="{{ $icon_menus['incidents'] ?? '' }} text-dark" aria-hidden="true"></i>
            <span class=" font-weight-bold">Incidencias Estudiantiles</span>

        </h3>

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

                <div class="col-{{ $modeIndex ? '12' : '3' }}" style="{{ !$modeIndex ? 'opacity: 0.3;' : null }}">
                    <div class="border rounded">
                        @include('livewire.bienestar.incident.table.index')
                    </div>
                </div>

                @if ($modeCreate)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.form.create')
                        </div>
                    </div>
                @endif

                @if ($modeShow)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.show.index')
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.form.edit')
                        </div>
                    </div>
                @endif

                @if ($modeView)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.mode.viewMail')
                        </div>
                    </div>
                @endif
                @if ($modeTline)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.mode.modeTline')
                        </div>
                    </div>
                @endif

                @if ($modeClose)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident-agreement.form.close')
                        </div>
                    </div>
                @endif

                @if ($modeFilter)
                    <div class="col-9">
                        <div style="background-color:white">
                            @include('livewire.bienestar.incident.mode.filter')
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

