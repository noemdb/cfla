<div>
    <div>

    <div class="card-header  alert-dark mt-2 ">

        <h3 class="mb-0 pb-0">

            <div class="btn-group float-right pt-0 pb-2">
                @include('livewire.administracion.poll.question.menu.index')
            </div>

            <div class="float-right ">
                <small wire:loading.delay.shortest class="text-muted small px-2">
                    Procesando...
                </small>
            </div>

            <i class="{{$icon_menus['tma'] ?? ''}} text-info" aria-hidden="true"></i>
            <span class=" font-weight-bold">Preguntas para los procesos de consultas</span>

        </h3>

    </div>

    <div class="card-body px-0 py-1 mx-0 my-1">

        <div class="container-fluid mx-0 px-0">

            <div class="row">

                <div class="col">
                    <div class="border rounded" style="{{(!$modeIndex) ? 'opacity: 0.8;filter:': null}}">
                        <h4 class="alert alert-light border-0">
                            Listado de preguntas de los procesos consultas registrados.
                        </h4>
                        <div class="px-1">@include('livewire.administracion.poll.question.table.questions')</div>
                    </div>
                </div>

                @if ($modeCreate)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.question.mode.create')
                        </div>
                    </div>
                @endif

                @if ($modeEdit)
                    <div class="col-7" >
                        <div>
                            @include('livewire.administracion.poll.question.mode.edit')
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

</div>
