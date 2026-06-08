<div>
    <div>

    <div class="card-header  alert-dark mt-2 ">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.administracion.poll.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>

            <i class="{{$icon_menus['tma'] ?? ''}} text-info" aria-hidden="true"></i>
            <span class=" font-weight-bold">Gestionamiento para todos los procesos de consultas</span>
            {{-- <span class="small text-muted float-right">[{{Auth::user()->username ?? null}}]</span> --}}

        </h3>

        {{-- @include('livewire.administracion.poll.modals.helps') --}}

    </div>

    <div class="card-body px-0 py-1 mx-0 my-1">

        @if (Session::has('operp_ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {!! Session::get('operp_ok') !!}
            </div>
        @endif

        <div class="container-fluid mx-0 px-0">

            <div class="row">

                <div class="col">
                    <div class="border rounded" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                        <h4 class="alert alert-light border-0">
                            Listado de los procesos consultas registrados.
                        </h4>
                        {{-- {{$polls}} --}}
                        <div class="px-1">@include('livewire.administracion.poll.table.poll')</div>

                    </div>
                </div>

                @if ($modeCreate)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.mode.create')
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.mode.edit')
                        </div>
                    </div>
                @endif

                @if ($modeResult)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.table.result',['poll_main'=>$poll_main])
                        </div>
                    </div>
                @endif

                @if ($modeShow)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.table.show',['poll_main'=>$poll_main])
                        </div>
                    </div>
                @endif

                @if ($modePreview)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.mode.preview')
                        </div>
                    </div>
                @endif
                
                @if ($modePreviewSend)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.mode.previewSend')
                        </div>
                    </div>
                @endif

                {{--

                @if ($modeCreate)
                    <div class="col-7" >
                        <div>
                            <livewire:administracion.poll.create-component />
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-7" >
                        <div>
                            <livewire:administracion.poll.edit-component :poll="$poll" :key="$poll_main_id" />
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
                                <div class="px-2">
                                    @include('livewire.administracion.poll.table.partials.btnMode',['key'=>'preview'])
                                </div>
                            </h5>
                            <hr>
                            <div class="p-1 m-1">
                                @if ($representant)
                                    @include('email.polls.control.messege')
                                @else
                                    <div class="alert alert-danger">
                                        No hay un usuario destinatario para este mensaje, revise sus datos.
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
                                <div class="px-2">
                                    @include('livewire.administracion.poll.table.partials.btnMode',['key'=>'show'])
                                </div>
                            </h5>
                            <div class="px-2 alert-light">
                                <span class="font-weight-bold">Descripción: </span>
                                <div class="px-2 text-muted font-weight-bold">
                                    {{Str::limit($poll->description,128,'...') ?? ''}}
                                </div>
                            </div>
                            <div class="p-1 m-1">
                                @include('livewire.administracion.poll.table.show')
                            </div>
                        </div>
                    </div>
                @endif

                --}}

            </div>

        </div>

    </div>

    {{-- <div wire:ignore> @include('livewire.administracion.poll.charts.movimientocambiario') </div> --}}


</div>

</div>
