<div class="border rounded shadow">  

    <h5 class="alert-warning py-3 px-2 text-dark rounded">
        <i class="{{ $icon_menus['options'] }} fa-1x text-success"></i>
        Gestión de <b>Opciones</b> para la pregunta seleccionada.
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    <div class="p-2 m-2">
        
        <livewire:administracion.educational.option-component :question_id="$question_id"/>

    </div>

</div>
