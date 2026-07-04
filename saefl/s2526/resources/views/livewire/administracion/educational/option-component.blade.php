<div>   

    <h5>
        <div class="alert-secondary d-flex justify-content-between p-2">
            <div class="">
                <span>Pregunta:</span> <span class="font-weight-bold small">{{$question->text}}</span>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" wire:click="create">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </h5>


    <div class="p-1 m-1">
        @switch($mode)
            @case('index') @include('livewire.administracion.educational.table.options') @break
            @case('create') @include('livewire.administracion.educational.form.options.create') @break
            @case('edit') @include('livewire.administracion.educational.form.options.edit') @break
            {{-- @case('options') @include('livewire.administracion.educational.options') @break --}}
        @endswitch
    </div>

</div>
