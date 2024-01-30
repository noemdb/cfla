<x-modal.card title="La cédula ingresada ya está asociada" blur wire:model="modalAssistent" max-width="sm">

    <div class="text-center">La cédula ingresada ya está asociada a un registro de soilicitud de matrícula: </div>

    <div class="flex justify-center text-3xl font-extralight my-4">{{$ci ?? null}}</div>

</x-modal.card>