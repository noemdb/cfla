<div class="border rounded shadow">  

    <h5 class="alert-danger py-3 px-2 text-dark rounded">
        <i class="{{ $icon_menus['fichaDigital'] }} fa-1x text-danger"></i>
        Gestión de <strong>Preguntas</strong> para el debate seleccionado
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    <div class="p-1 m-1">
        
        <livewire:administracion.educational.question-component :debate_id="$debate_id"/>

    </div>

</div>