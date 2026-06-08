<div>
    <div class="card border-0 p-1">
        <i class="bi bi-list-columns-reverse" style="font-size: 2rem"></i>
        <h5 class="card-title text-muted pb-0 mb-0">Listado de Incidencias registradas.</h5>
        <div class="card-body p-2">
            <div class="text-left">
                <div class="text-left text-muted">
                    <div><span class="font-weight-bold">Estudiante:</span> Buscar por nombre o cédula. (mínimo 3 caracteres)</div>
                    <div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">@</span>
                            </div>
                            {!! Form::text('search', $search, [
                                'class' => 'form-control',
                                'wire:model.debounce.500ms' => 'search',
                                'placeholder' => 'Buscar Nombre o Cédula',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="">

                    <div class="container-fluid  px-0">

                        <div class="row">

                            <div class="col-{{ $modeIndex ? '12' : '5' }}"
                                style="{{ !$modeIndex ? 'opacity: 0.6;' : null }}">
                                <div class="border rounded"
                                    style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                                    @include('livewire.profesor.incident.action.table')
                                </div>
                            </div>

                            @if ($modeCreate)
                                <div class="col-7">
                                    <div>
                                        @include('livewire.profesor.incident.action.form.create')
                                    </div>
                                </div>
                            @endif

                            @if ($modeEdit)
                                <div class="col-7">
                                    <div>
                                        @include('livewire.profesor.incident.action.form.edit')
                                    </div>
                                </div>
                            @endif

                            @if ($modeView)
                                <div class="col-7">
                                    <div>
                                        @include('livewire.profesor.incident.action.mode.viewMail')
                                    </div>
                                </div>
                            @endif

                            @if ($modeClose)
                                <div class="col-7">
                                    <div>
                                        @include('livewire.profesor.incident.action.form.close')
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
