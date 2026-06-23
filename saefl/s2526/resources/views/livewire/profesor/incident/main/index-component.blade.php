<div>
    <div class="card border-0 p-1">
        <i class="bi bi-list-columns-reverse" style="font-size: 2rem"></i>
        <h5 class="card-title text-muted pb-0 mb-0">Listado de estudiantes con inscripción formalizada</h5>
        <div class="card-body p-2">
            <div class="text-left">
                <div class="text-left text-muted">
                    <div><span class="font-weight-bold">Estudiante:</span> Buscar por nombre o cédula. (mínimo 3
                        caracteres)</div>
                    <div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="{{ $icon_menus['estudiant'] }} fa-1x"></i>
                                </span>
                            </div>
                            <div class="input-group-prepend">
                                {!! Form::select('filter', $filter_list, old('filter'), [
                                    'wire:model' => 'filter',
                                    'id' => 'filter',
                                    'class' => 'custom-select',
                                    'placeholder' => 'Seleccione',
                                ]) !!}
                            </div>
                            {!! Form::text('search', $search, [
                                'class' => 'form-control',
                                'wire:model.debounce.500ms' => 'search',
                                'placeholder' => 'Buscar Nombre o Cédula',
                            ]) !!}
                            <div class="input-group-prepend" wire:loading>
                                <div class=" d-flex align-items-center justify-content-center h-100">
                                    <div class="px-4">
                                        <strong>Procesando...</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Session::has('operp_ok'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {!! Session::get('operp_ok') !!}
                    </div>
                @endif

                <div class="">

                    <div class="container-fluid  px-0">

                        <div class="row">

                            <div class="col-{{ $modeIndex ? '12' : '3' }}"
                                style="{{ !$modeIndex ? 'opacity: 0.6;' : null }}">
                                <div class="border rounded"
                                    style="{{ !$modeIndex ? 'color: transparent; text-shadow: 0 0 5px rgba(0,0,0,0.5); opacity: 0.5;' : null }}">
                                    @include('livewire.profesor.incident.main.table')
                                </div>
                            </div>

                            @if ($modeCreate)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.form.create')
                                    </div>
                                </div>
                            @endif

                            @if ($modeEdit)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.form.edit')
                                    </div>
                                </div>
                            @endif

                            @if ($modeShow)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.show.index')
                                    </div>
                                </div>
                            @endif

                            @if ($modeView)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.mode.viewMail')
                                    </div>
                                </div>
                            @endif

                            @if ($modeTline)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.mode.modeTline')
                                    </div>
                                </div>
                            @endif
                            @if ($modeAction)
                                <div class="col-9">
                                    <div>
                                        @include('livewire.profesor.incident.main.mode.modeActions')
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
