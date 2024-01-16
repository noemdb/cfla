
<x-card>
    
    @slot('header')
        <h3 class=" bg-green-100 mb-4 mt-6 p-2 text-3xl font-bold text-neutral-800 dark:text-neutral-200">
            Docentes
        </h3>
    @endslot

    <ul class="w-96">
        <li
            class="w-full cursor-default border-b-2 border-neutral-100 border-opacity-100 py-4 text-neutral-500 dark:border-opacity-50">
            A disabled item
        </li>
        <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
            A second item
        </li>
        <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
            A third item
        </li>
        <li class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">
            A fourth item
        </li>
        <li class="w-full py-4">And a fifth one</li>
    </ul>
    
    
</x-card>