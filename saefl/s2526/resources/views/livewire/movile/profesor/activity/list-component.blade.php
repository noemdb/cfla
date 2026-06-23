<div>

    <div wire:key="modeIndexActivities">
        @includeWhen($modeIndex,'livewire.movile.profesor.activity.table.activity')
    </div>

    <div wire:key="modeEditActivities">
        {{-- @includeWhen($modeEdit,'livewire.movile.profesor.learning.form.edit') --}}
        @includeWhen($modeEdit,'livewire.movile.profesor.activity.form.edit')
    </div>

    <!-- Spinner de carga -->
    <div wire:loading class="position-fixed bottom-0 end-0 p-3 rounded-4 bg-white shadow-sm p-2 m-2">
        <div class="spinner-border text-success spinner-border-sm" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

</div>