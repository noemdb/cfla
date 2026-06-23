<div class="container-fluid">
    <div class="d-flex justify-content-lg-between pt-2 mt-2">
        <div>
            <h3>Listado de Posts</h3>
        </div>
        <div>
            <a title="Crear ficha del estudiante" class="btn btn-primary bnt-sm" href="#" wire:click="create()" role="button">
                <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
            </a> 
        </div>
    </div>
    
    @include('livewire.administracion.blog.table.post')
</div>