<div id="carouselHeroCrossfade" class="relative" data-te-carousel-init data-te-ride="carousel">

    <div class="absolute inset-x-0 bottom-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0"
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

    </div>

    <!--Carousel items-->
    <div class="relative w-full overflow-hidden after:clear-both after:block after:content-['']">
        <!-- items -->
        @foreach ($posts->take(4) as $item)
            @php $category = $item->category @endphp
            <div data-te-carousel-fade data-te-carousel-item
                class="relative float-left -mr-[100%] w-full !transform-none opacity-0 transition-opacity duration-[600ms] ease-in-out motion-reduce:transition-none"
                {{ ($loop->first) ? 'data-te-carousel-active' : null}}>

                <img src="{{asset('image/categories/'.$category->icon.'.png')}}" class="block w-full" alt="Wild Landscape" />

                <div class="absolute ml-2 w-1/2 top-2 py-2 text-start text-white">

                    <h5 class="text-lg md:text-xl lg:text-2xl xl:text-3xl">{{$item->title ?? null}}</h5>
                    <div class="text-sm md:text-md lg:text-lg xl:text-xl">{{$item->description ?? null}}</div>
                    <div class="hidden sm:block md:py-2 lg:py-4 h-9 sm:h-24 md:h-44 lg:h-full xl:h-full text-xs border-t-2 mt-2 max-w-full overflow-hidden text-wrap word-break">
                        {{$item->body ?? null}}
                    </div>
                    <div class="text-sm border-t-2 mt-2 md:py-2 lg:py-4 hidden xl:block">
                        {!!$item->insert ?? null!!}                        
                    </div>

                    <div class="border-t-2 mt-2 text-xs md:text-sm lg:text-md xl:text-lg">
                        <div>{{$category->name ?? '098'}}</div>
                        <div class="text-xs">Creado: {{$item->created_at ?? null}} || Actualizado: {{$item->updated_at ?? null}}</div>
                    </div>

                    <div class="flex justify-start"><x-button sm info label="Más..." /></div>
                    
                </div>

            </div>
        @endforeach
        
    </div>

</div>
