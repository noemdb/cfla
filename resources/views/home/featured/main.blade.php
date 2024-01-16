{{-- <div class="p-2 border-2 rounded shadow-sm">
    <div class="flex">
        <div class="bg-gray-100 p-2 rounded">
            <x-icon name="home" class="w-20 h-20" />
        </div>
        <div class="p-2">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo reprehenderit consequuntur quis suscipit
            earum accusamus, necessitatibus recusandae at, possimus quisquam, soluta perspiciatis illum quidem animi
            nostrum ipsa! Voluptates, doloribus deserunt?
        </div>
    </div>
</div> --}}

<x-card>
    
    @slot('header')
        <h3 class="flex gap-2 bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            <x-icon name="home" class="w-20 h-20" /> Instituion
        </h3>
    @endslot

    <div class="p-2">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo reprehenderit consequuntur quis suscipit
        earum accusamus, necessitatibus recusandae at, possimus quisquam, soluta perspiciatis illum quidem animi
        nostrum ipsa! Voluptates, doloribus deserunt?
    </div>

    <div class="flex justify-end">
        <x-button primary label="Ver más" />
    </div>
    
</x-card>