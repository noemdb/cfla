
<div class="border rounded p-2">

    <x-card>
        @slot('header')
            <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
                NUESTRO GRAN EQUIPO
            </h3>
        @endslot
        @include('home.autority.items')
    </x-card>

    
</div>