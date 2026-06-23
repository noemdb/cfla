<div>
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <h3>
                <i class="{{ $icon_menus['pagos_adelantados'] }} fa-1x text-success"></i>
                Listado de Vueltos/Devoluciones
                {{-- INI Menu rapido --}}
                    <div class="btn-group float-right">

                         <button class="btn btn-primary btn-sm float-right" wire:click="create()">
                            <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
                        </button>
                        @component('elements.buttons.default')
                            @slot('title', 'Refrescar la página')
                            @slot('class_bt', 'dark')
                            @slot('route', route('administracion.refunds.index'))
                            @slot('icon', 'fas fa-redo')
                        @endcomponent

                    </div>
                {{-- FIN Menu rapido --}}

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

            <div class="container-fluid">

                <div class="row">

                    <div class="col">
                        <div class="border rounded p-2" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                            @include('livewire.administracion.refund.table.index')                            
                        </div>
                    </div>
                    
                    @if ($modeCreate)
                        <div class="col-7">
                            <div class="border rounded shadow-lg" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                                <h4 class="alert alert-secondary ">
                                    Registrar un nuevo Vuelto/Devolución.
                                    <button type="button" class="close" wire:click='closeCreateMode()'>
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </h4>
                                <div class="px-2">
                                    @include('livewire.administracion.refund.form.create')
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if ($modeShow)
                        <div class="col-7">
                            <div class="border rounded shadow-lg" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                                <h4 class="alert alert-secondary ">
                                    Detalles del pago combinado.
                                    <button type="button" class="close" wire:click='closeShowMode()'>
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </h4>
                                @include('administracion.representants.table.list')
                            </div>
                        </div>
                    @endif

                    @if ($modeDetails)
                        <div class="col-7">
                            <div class="border rounded shadow-lg" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                                <h4 class="alert alert-secondary ">
                                    Detalles del Vuelto/Devolución.
                                    <button type="button" class="close" wire:click='closeShowMode()'>
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </h4>
                                @include('livewire.administracion.refund.partials.details')
                                {{-- livewire.administracion.refund.partials.details --}}
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</div>
