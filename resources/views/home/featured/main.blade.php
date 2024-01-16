<x-card>
    
    @slot('header')
        <h3 class="flex items-center gap-2 bg-green-100 mt-6 p-2 text-xl font-bold text-neutral-800 dark:text-neutral-200">
            <x-icon name="home" class="w-12 h-12" /> Instituion
        </h3>
    @endslot

    <div class="text-sm p-2">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo reprehenderit consequuntur quis suscipit
        earum accusamus, necessitatibus recusandae at, possimus quisquam, soluta perspiciatis illum quidem animi
        nostrum ipsa! Voluptates, doloribus deserunt?
    </div>

    <div class="flex justify-end">
        <x-button primary label="Ver más" />
    </div>
    
</x-card>