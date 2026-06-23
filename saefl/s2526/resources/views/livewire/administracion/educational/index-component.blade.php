<div>    
    
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <h3>
                <i class="{{ $icon_menus['crud'] }} fa-1x text-success"></i>
                Gestión de <strong>Competiciones Estudiantiles, preguntas y respuestas.</strong>
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right">
                    <button type="button" class="btn btn-primary" wire:click="create">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
                {{-- FIN Menu rapido --}}
            </h3>
        </div>

        <div class="card-body">

            <div class="">
                @switch($mode)
                    @case('index') @include('livewire.administracion.educational.table.index') @break
                    @case('create') @include('livewire.administracion.educational.create') @break
                    @case('edit') @include('livewire.administracion.educational.edit') @break                    
                    @case('debate') @include('livewire.administracion.educational.debate') @break                    
                @endswitch
            </div>           

        </div>

    </div>

</div>
