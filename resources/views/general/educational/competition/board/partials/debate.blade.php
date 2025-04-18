<div class="m-3">

    <div class="p-5">

        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Debates de la Competición</h5>

        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">


            <div class="w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                
                @forelse ($debates as $item)
                        <button type="button" class="w-full px-4 py-2 font-medium text-left rtl:text-right border-b border-gray-200 cursor-pointer hover:bg-gray-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:text-white dark:focus:ring-gray-500 dark:focus:text-white">
                            {{$item->name}}
                        </button>
                    @empty
                        <div>No hay debates registrados</div>
                @endforelse

            </div>

        </p>
        
    </div>

</div>