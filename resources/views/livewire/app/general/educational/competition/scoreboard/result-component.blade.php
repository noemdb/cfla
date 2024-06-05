<div>

    @if ($debate)
        <div class="m-2 w-full">
        
            <div class="my-2 py-2 bg-white text-gray-900 border-t-4 border-purple-800">
                <h4 class="text-2xl font-bold dark:text-white">Resultados</h4>
        
                <div class="font-bold p-2">{{$grado->name}}</div>
        
                <div class="grid m-2 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-4 md:grid-cols-2 bg-white dark:bg-gray-800 ">
        
                    @forelse ($seccions as $item)
                        <figure class="flex flex-col items-center justify-center p-8 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 dark:border-gray-700">
                            
                            <div class="">
                                Sección:
                                <h4 class="mb-2 text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    {{$item->name}}                    
                                </h4>
                                <p class="font-normal text-gray-700 dark:text-gray-400">
                                    {{$competition->getTotalScoreForSection($item->id)}} PTS
                                </p>
                            </div>
        
                        </figure>
                    @empty
                        <div>No hay secciones</div>
                    @endforelse
        
                    
                </div>
            </div>
        </div>
    @else
        <div>Espere... No hay un debate activo</div>
    @endif
    
</div>
