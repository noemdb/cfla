<div class="m-3">

    {{-- <a href="#">
        <img class="rounded-t-lg" src="/docs/images/blog/image-1.jpg" alt="" />
    </a> --}}
    <div class="p-5">
        <a href="#">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{$competition->name}}</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{$competition->description}}</p>
        <div x-data="{ open: false }">
            <button  class=" font-bold border-gray-200 p-2 m-2" @click="open = ! open">Leer más</button>         
            <div x-show="open" @click.outside="open = false">
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{$competition->motive}}</p>
            </div>
        </div>

        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{$competition->date}}</p>
        
    </div>

</div>