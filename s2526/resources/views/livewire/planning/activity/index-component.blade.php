<div>
    <div class="font-weight-bold small text-muted" wire:loading>Procesando...</div>

    @includeWhen($modeCreator, 'livewire.planning.activity.partials.create')

    @include('livewire.planning.activity.table.index')

    {{-- CRUD Modals (always rendered, hidden via Bootstrap) --}}
    @include('livewire.planning.activity.modals.create')
    @include('livewire.planning.activity.modals.edit')
    @include('livewire.planning.activity.modals.show')

</div>
