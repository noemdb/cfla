<x-card>

    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            EXPLORA NUESTRAS INSTALACIONES
        </h3>
    @endslot

    <div class=" lg:p-8">        
        @include('home.gallery.items')
    </div>
</x-card>