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
    <div class="relative rounded-lg w-full overflow-hidden after:clear-both after:block after:content-['']">
        <!-- items -->
        @foreach ($posts->take(4) as $item)
            @php $category = $item->category @endphp
            <div data-te-carousel-fade data-te-carousel-item
                class="relative float-left -mr-[100%] w-full !transform-none opacity-0 transition-opacity duration-[600ms] ease-in-out motion-reduce:transition-none"
                {{ ($loop->first) ? 'data-te-carousel-active' : null}}> 
                
                <div id="title" class="sm:hidden text-white border-b-2 border-green-800 p-4" style="background-color: #004400">
                    @include('livewire.home.hero.title')
                </div>

                <div class="relative min-h-64" style="background-color: #004400">               

                    <img src="{{asset('image/categories/'.$category->icon.'.png')}}" class="block w-full" alt="Wild Landscape" />

                    <div class="absolute ml-2 w-1/2 top-2 py-2 text-start text-white">

                        <div id="title" class="hidden sm:block border-b-2 border-green-800">
                            @include('livewire.home.hero.title')
                        </div>
                        
                        {{-- <div class="md:py-2 lg:py-4 min-h-9  sm:h-24 md:h-44 lg:h-full xl:h-full text-xs xl:text-lg mt-2 max-w-full overflow-hidden text-wrap word-break"> --}}
                        <div class="md:py-2 lg:py-4 min-h-9 max-h-64 md:max-h-72 lg:max-h-86 sm:h-24 md:h-44 lg:h-full xl:h-full text-xs xl:text-lg mt-2 max-w-full overflow-hidden text-wrap word-break">
                            {!!$item->body ?? null!!} 
                        </div>

                        {{-- @if ($item->saefl_image_url)                            
                            <div id="footer-out" class="hidden xl:block border-b-2 border-green-800 rounded-b max-w-48 rounded-lg" style="background-color: #004400">
                                <img src="{{asset($item->saefl_image_url)}}" class="block w-full" alt="Wild Landscape" />
                            </div> 
                        @endif  --}}

                        <div id="footer-into" class="hidden sm:block">
                            @include('livewire.home.hero.footer')
                        </div>                                           
                        
                    </div>                    
                    
                </div>

                <div id="footer-out" class="sm:hidden border-b-2 border-green-800 rounded-b" style="background-color: #004400">
                    @include('livewire.home.hero.footer')
                </div>

            </div>
        @endforeach        
        
    </div>

</div>

@includeWhen($modalShow,'livewire.home.modal.post')
