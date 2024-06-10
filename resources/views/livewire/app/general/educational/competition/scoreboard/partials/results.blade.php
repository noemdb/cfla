<div class="mt-1 w-full border-t-4 border-emerald-800">
    
    <div class="py-2 bg-slate-200 text-gray-900 border border-gray-300">

        <h4 class="text-2xl font-bold dark:text-white ">Puntuación</h4>

        @if (!empty($grado->name))
            <div class="font-bold text-xl">{{ (!empty($grado->name)) ? $grado->name : null ;}}</div>
            <p class="font-normal text-gray-700 dark:text-gray-400">
                <span class="text-6xl font-extrabold">{{$competition->getTotalScoreForGrado($grado->id)}}</span>                             
                <small class="text-lg">Pts</small>
            </p>
        @else
            <div>No hay Grado/Año</div>
        @endif 

    </div>

</div>