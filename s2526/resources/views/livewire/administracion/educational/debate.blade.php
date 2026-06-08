<div class="border rounded shadow">  

    <h5 class="alert-info py-3 px-2 text-dark rounded">
        <i class="{{ $icon_menus['messege'] }} fa-1x text-success"></i>
        Gestión de los <strong>Debates</strong> para la <b>Competición</b> seleccionada
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    <div class="p-1 m-1">
        
        <livewire:administracion.educational.debate-component :competition_id="$competition_id"/>

    </div>

</div>