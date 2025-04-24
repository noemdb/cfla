<div class="mt-1 w-full border-t-4 border-emerald-800">
    
    <div class="py-2 bg-slate-200 text-gray-900 border border-gray-300">

        <h4 class="text-2xl font-bold dark:text-white ">Puntuación</h4>

        @if (!empty($grado->name))
            {{-- <div class="font-bold text-xl">{{ (!empty($grado->name)) ? $grado->name : null ;}}</div>
            <p class="font-normal text-gray-700 dark:text-gray-400">
                <span class="text-6xl font-extrabold">{{$competition->getTotalScoreForGrado($grado->id)}}</span>                             
                <small class="text-lg">Pts</small>
            </p> --}}

            <div class="font-bold text-xl">{{ (!empty($grado->name)) ? $grado->name : null ;}}</div>
            @php $seccions = $grado->activeSeccions() @endphp
            @forelse ($seccions as $item)
                <figure class="flex flex-col items-center justify-center p-8 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 dark:border-gray-700">
                    
                    <div class="">
                        
                        <h4 class="mb-2 text-xl font-normal tracking-tight text-gray-900 dark:text-white">
                            Sección {{$item->name}}                    
                        </h4>
                        <p class="font-bold text-4xl text-gray-700 dark:text-gray-400">
                            {{$competition->getTotalScoreForSection($item->id)}} PTS
                        </p>
                    </div>
    
                </figure>
            @empty
                <div>No hay secciones</div>
            @endforelse  
        @else
            <div>No hay Grado/Año</div>
        @endif 

    </div>

</div>