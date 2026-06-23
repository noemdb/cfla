<div>

    <div class="card-header  alert-dark mt-2">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.bienestar.incident-agreement.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2" style="">
                    <div class="clearfix">
                        <div class="spinner-border float-right spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </small>
            </div>

            <i class="{{$icon_menus['incident_agreements'] ?? ''}} text-dark" aria-hidden="true"></i>
            <span class=" font-weight-bold">Acuerdos establecidos asociados a las incidencias registradas.</span>

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

                <div class="col-{{ $modeIndex ? '12' : '4' }}" style="{{ ( ! $modeIndex ) ? 'opacity: 0.6;' : null}}">
                    <div class="border rounded" style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                        <h5 class="p-2 m-2 font-weight-bold">Listado de estudiantes con incidencias registradas</h5>
                        @include('livewire.bienestar.incident-agreement.table.index')
                    </div>
                </div>

                @if ($modeCreate)
                    <div class="col-8" >
                        <div>
                            @include('livewire.bienestar.incident-agreement.form.create')
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-8" >
                        <div>
                            @include('livewire.bienestar.incident-agreement.form.edit')
                        </div>
                    </div>
                @endif

                @if ($modeView)
                    <div class="col-8" >
                        <div>
                            @include('livewire.bienestar.incident-agreement.mode.viewMail')
                        </div>
                    </div>
                @endif

                @if ($modeClose)
                    <div class="col-8" >
                        <div>
                            @include('livewire.bienestar.incident-agreement.form.close')
                        </div>
                    </div>
                @endif


            </div>

        </div>

    </div>

</div>
