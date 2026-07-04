<div>
    <div class="p-2">
        <div class="d-flex justify-content-between py-2">
            <div>
                <div class="text-muted"><strong>Listado</strong> <span >de las Servicios Ejecutados en Acción Comunitaria.</span></div>
            </div>
            <div>
                
                <div class="btn-group btn-group-sm">

                    <button class="btn btn-primary" type="button" wire:click="create()">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Registrar Actividad
                    </button>
                  
                    <a name="" id="" class="btn btn-dark" href="{{route('profesors.social_actions.community_actions.profesor.pdf',$profesor->id)}}" role="button" target="_blank">
                        <i class="far fa-file-pdf"></i>
                    </a>
                </div>
            </div>            
        </div>
        
        <div>
            @includeWhen($modeIndex,'livewire.profesor.social-actions.community-action.table.index') 
            @includeWhen($modeCreate,'livewire.profesor.social-actions.community-action.form.create')  
            @includeWhen($modeEdit,'livewire.profesor.social-actions.community-action.form.edit')  
            @includeWhen($modeShowImage,'livewire.profesor.social-actions.community-action.show.image')  
        </div>
    </div> 
</div>
