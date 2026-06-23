<div>
    
    <div class="p-0 rounded border">

        <h5>
            <div class="alert-secondary d-flex justify-content-between p-2">
                <div class="">
                    Listado de las preguntas registradas para el <strong>Áreas de Formación</strong> seleccionado.
                </div>
    
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" wire:click="create">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </h5>

        <div class="p-2 m-2">
            @switch($mode)
                @case('index') @include('livewire.profesor.competition.table.questions') @break 
                @case('edit') @include('livewire.profesor.competition.form.question.edit') @break  
                @case('create') @include('livewire.profesor.competition.form.question.create') @break  
                @case('options') @include('livewire.administracion.educational.options') @break
                {{-- @case('create') @include('livewire.administracion.educational.form.question.create') @break --}}
                {{-- @case('edit') @include('livewire.administracion.educational.form.debate.edit') @break --}}
            @endswitch
        </div>

    </div>

</div>
