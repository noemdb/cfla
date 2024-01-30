<div id="carouselHeroCrossfade" class="relative" data-te-carousel-init data-te-ride="carousel">
    <!--Carousel indicators-->

    
    <div 
        class="absolute inset-x-0 bottom-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0"
        data-te-carousel-indicators>
        @foreach ($posts->take(4) as $item)
            <button 
                type="button" 
                data-te-target="#carouselHeroCrossfade" 
                data-te-slide-to="{{$loop->index}}" 
                {{ ($loop->first) ? 'data-te-carousel-active' : null}}                
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-white bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[600ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none"
                aria-current="true"
                aria-label="Slide {{$loop->iteration}}">
            </button>
        @endforeach
        
        {{-- <button type="button" data-te-target="#carouselHeroCrossfade" data-te-slide-to="1"
            class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-white bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[600ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none"
            aria-label="Slide 2"></button>
        <button type="button" data-te-target="#carouselHeroCrossfade" data-te-slide-to="2"
            class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-white bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[600ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none"
            aria-label="Slide 3"></button> --}}
    </div>

    <!--Carousel items-->
    <div class="relative w-full overflow-hidden after:clear-both after:block after:content-['']">
        <!--First item-->
        @foreach ($posts->take(4) as $item)
            @php $category = $item->category @endphp
            <div class="relative float-left -mr-[100%] w-full !transform-none opacity-0 transition-opacity duration-[600ms] ease-in-out motion-reduce:transition-none"
                data-te-carousel-fade 
                data-te-carousel-item
                {{ ($loop->first) ? 'data-te-carousel-active' : null}}>
                <img src="{{asset('image/categories/'.$category->icon.'.png')}}" class="block w-full" alt="Wild Landscape" />

                <div class="absolute ml-2 w-1/2 top-2 py-2 text-start text-white md:block">
                    <h5 class="text-md md:text-xl">{{$item->title ?? null}}</h5>
                    <div class="">{{$item->description ?? null}}</div>
                    <div class="text-sm border-t-2 mt-2 hidden sm:block w-16 sm:w-full">{{$item->body ?? null}}</div>

                    <div class="hidden sm:block">
                        <div>{{$category->name ?? '098'}}</div>
                        <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
                    </div>                    

                    <x-button info label="Más..." />
                </div>

                {{-- <div class="absolute ml-2 w-1/2 top-1/3 py-2 text-start text-white hidden sm:block">
                    
                </div> --}}

                {{-- <div class="absolute ml-2 w-1/2 top-3/4 py-2 text-start text-green-400 hidden sm:block">
                    <div>{{$category->name ?? '098'}}</div>
                    <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
                </div> --}}
                
            </div>
        @endforeach
        {{-- <!--Second item-->
        <div class="relative float-left -mr-[100%] hidden w-full !transform-none opacity-0 transition-opacity duration-[600ms] ease-in-out motion-reduce:transition-none"
            data-te-carousel-fade data-te-carousel-item>
            <img src="https://mdbcdn.b-cdn.net/img/new/slides/042.webp" class="block w-full" alt="Camera" />
        </div>
        <!--Third item-->
        <div class="relative float-left -mr-[100%] hidden w-full !transform-none opacity-0 transition-opacity duration-[600ms] ease-in-out motion-reduce:transition-none"
            data-te-carousel-fade data-te-carousel-item>
            <img src="https://mdbcdn.b-cdn.net/img/new/slides/043.webp" class="block w-full" alt="Exotic Fruits" />
        </div> --}}
    </div>

    {{-- 
    <!--Carousel controls - prev item-->
    <button
        class="absolute bottom-0 left-0 top-0 z-[1] flex w-[15%] items-center justify-center border-0 bg-none p-0 text-center text-white opacity-50 transition-opacity duration-150 ease-[cubic-bezier(0.25,0.1,0.25,1.0)] hover:text-white hover:no-underline hover:opacity-90 hover:outline-none focus:text-white focus:no-underline focus:opacity-90 focus:outline-none motion-reduce:transition-none"
        type="button" data-te-target="#carouselHeroCrossfade" data-te-slide="prev">
        <span class="inline-block h-8 w-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </span>
        <span
            class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Anterior</span>
    </button>
    <!--Carousel controls - next item-->
    <button
        class="absolute bottom-0 right-0 top-0 z-[1] flex w-[15%] items-center justify-center border-0 bg-none p-0 text-center text-white opacity-50 transition-opacity duration-150 ease-[cubic-bezier(0.25,0.1,0.25,1.0)] hover:text-white hover:no-underline hover:opacity-90 hover:outline-none focus:text-white focus:no-underline focus:opacity-90 focus:outline-none motion-reduce:transition-none"
        type="button" data-te-target="#carouselHeroCrossfade" data-te-slide="next">
        <span class="inline-block h-8 w-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </span>
        <span
            class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Siguiente</span>
    </button> 
--}}
</div>
