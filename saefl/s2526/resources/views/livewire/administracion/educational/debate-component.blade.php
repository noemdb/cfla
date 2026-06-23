<div>
    
    <div class="p-0 rounded border">
    
        <h5>
            <div class="alert-secondary d-flex justify-content-between p-2">
                <div class="">
                    <span>Competición:</span> 
                    <span class="font-weight-bold">{{$debate_competition->name}}</span>
                    <small class="text-muted small">{{$debate_competition->description}}</small>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" wire:click="create">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </h5>

        {{-- <hr> --}}

        <div class="px-2">
            @switch($mode)
                @case('index') @include('livewire.administracion.educational.table.debate') @break
                @case('create') @include('livewire.administracion.educational.form.debate.create') @break
                @case('edit') @include('livewire.administracion.educational.form.debate.edit') @break
                @case('questions') @include('livewire.administracion.educational.questions') @break
            @endswitch
        </div>

    </div>

</div>
