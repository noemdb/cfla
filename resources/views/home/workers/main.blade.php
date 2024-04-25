<x-card>

    @slot('header')
    <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
        Nuestro cuerpo docente
    </h3>
    @endslot

    <div class="flex">
        @forelse ($profesors as $item)
            <div data-te-chip-init data-te-ripple-init class="items-center justify-between" data-te-close="true">
                {{$item->name.' '.$item->lastname}}
            </div>
        @empty
            <div>No hay docentes con carga académica</div>
        @endforelse
    </div>


</x-card>