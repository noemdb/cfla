<div>

    <div class="card-header  alert-dark mt-2">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.administracion.mailer.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>

            <i class="{{$icon_menus['mail'] ?? ''}} text-info" aria-hidden="true"></i>
            <span class=" font-weight-bold">Envío de correos electrónicos masivos automatizados para representantes</span>

        </h3>

        @include('livewire.administracion.mailer.modals.helps')

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
                    <div class="border rounded" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                        <h4 class="alert alert-secondary ">
                            Listado de mensajes registrados
                        </h4>
                        {{-- {{$mailers}} --}}
                        @include('livewire.administracion.mailer.table.mailer',['mailers'=>$mailers])
                    </div>
                </div>

                @if ($modeCreate)
                    <div class="col-7" >
                        <div>
                            <livewire:administracion.mailer.create-component />
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-7" >
                        <div>
                            <livewire:administracion.mailer.edit-component :mailer="$mailer" :key="$mailer_id" />
                        </div>
                    </div>
                @endif

                @if ($modePreview)
                    <div class="col-7" >
                        <div class="border rounded shadow-lg">
                            <h5 class="alert alert-info font-weight-bolder rounded">
                                Vista previa del correo electrónico.
                                <button type="button" class="close" wire:click='closePreviewMode()'>
                                    <span aria-hidden="true">×</span>
                                </button>
                            </h5>
                            <hr>
                            <div class="p-1 m-1">
                                @if ($representant)
                                    @include('email.mailers.control.messege')
                                @else
                                    <div class="alert alert-danger">
                                        No hay un representante destinatario para este mensaje, revise sus datos.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($modeShow)
                    <div class="col-7" >
                        <div class="border rounded shadow-lg">
                            <h5 class="alert alert-dark font-weight-bolder rounded">
                                Destinatarios del correo electrónico.
                                <button type="button" class="close" wire:click='closeShowMode()'>
                                    <span aria-hidden="true">×</span>
                                </button>
                            </h5>
                            <div class="px-2 alert-light">
                                <span class="font-weight-bold">Descripción: </span>
                                <div class="px-2 text-muted font-weight-bold">
                                    {{Str::limit($mailer->description,128,'...') ?? ''}}
                                </div>
                            </div>
                            <div class="p-1 m-1">
                                @include('livewire.administracion.mailer.table.show')
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>
