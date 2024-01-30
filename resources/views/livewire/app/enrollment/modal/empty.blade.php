<x-modal.card title="No se encontrón ninguno" blur wire:model="modalAssistent" max-width="sm">

    <div class="text-center">No se encontró nigún registro asociado a la CI ingresada: </div>

    <div class="flex justify-center text-3xl font-extralight my-4">{{$ci ?? null}}</div>

</x-modal.card>