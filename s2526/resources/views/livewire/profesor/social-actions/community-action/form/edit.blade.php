<div>

    <div class="p-1 border rounded shadow-lg">
 
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Actualizar actividad comunitaria</strong>            
            <button type="button" class="close" aria-label="Close" wire:click="close()">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {{-- <div class="alert alert-secondary fw-bold text-muted small">{{($lapso) ? $lapso->name : null}}</div> --}}

        <form wire:submit.prevent="save" class="text-start  p-2 m-2">

            @includeWhen($modeEdit,'livewire.profesor.social-actions.community-action.form.fields')  

            <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>
        
        </form>

    </div>
    
</div>
