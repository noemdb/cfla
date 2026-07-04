<div>

    <div class="p-1 border rounded shadow-lg">
 
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Registrar asistencia</strong>            
            <button type="button" class="close" aria-label="Close" wire:click="close()">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form wire:submit.prevent="save" class="text-start  p-2 m-2">            

            @includeWhen($modeCreate,'livewire.profesor.social-actions.community-hour.form.estudiants')

            <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>
        
        </form>

    </div>
    
</div>
