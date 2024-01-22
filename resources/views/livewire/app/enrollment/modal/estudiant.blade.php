<x-modal.card title="Estudiante habilitado" blur wire:model.defer="modalAssistent">

    <div class="text-3xl font-bold my-4">Estudiante habilitado para solitar matrícula.</div>

    <div>Nombre: {{$census->fullname ?? null}}</div>

    @include('livewire.app.enrollment.steper.main')

</x-modal.card>