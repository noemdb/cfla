<x-card>
    
    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            Planes Educativos
        </h3>
    @endslot

    @include('livewire.home.services.items')
    
</x-card>