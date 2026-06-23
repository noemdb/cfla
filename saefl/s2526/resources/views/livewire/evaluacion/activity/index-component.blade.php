<div>
    <div class="font-weight-bold small text-muted" wire:loading>Procesando...</div>

    @includeWhen($modeCreator, 'livewire.evaluacion.activity.partials.create')
    @includeWhen($modeObservation, 'livewire.evaluacion.activity.partials.observation')

    @include('livewire.evaluacion.activity.table.index')

</div>
