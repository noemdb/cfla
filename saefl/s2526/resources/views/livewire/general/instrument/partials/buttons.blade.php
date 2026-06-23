<div class="d-flex justify-content-between p-2">
    <button type="button" class="btn btn-secondary w-25" {{($mode=="initial") ? 'disabled' : null}}  wire:click="previous">Anterior</button>
    <button type="button" class="btn btn-primary w-25" {{($mode=="initial") ? 'final' : null}} wire:click="next">Siguiente</button>
</div>