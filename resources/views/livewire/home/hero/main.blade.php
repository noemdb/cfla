<div id="carouselHeroCrossfade" class="relative diagnostic-card rounded-xl overflow-hidden shadow-2xl h-[500px] group"
    data-te-carousel-init data-te-ride="carousel">

    <!-- Carousel Indicators -->
    <div class="absolute inset-x-0 bottom-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0"
        data-te-carousel-indicators>
        @foreach ($posts->take(4) as $item)
            <button type="button" data-te-target="#carouselHeroCrossfade" data-te-slide-to="{{ $loop->index }}"
                {{ $loop->first ? 'data-te-carousel-active' : null }}
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[600ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-current="true" aria-label="Slide {{ $loop->iteration }}">
            </button>
        @endforeach
    </div>

    <!-- Carousel Items -->
    <div class="relative w-full h-full overflow-hidden after:clear-both after:block after:content-['']">
        @foreach ($posts->take(4) as $item)
            @php $category = $item->category @endphp
            <div data-te-carousel-fade data-te-carousel-item
                class="relative float-left -mr-[100%] w-full h-full !transform-none opacity-0 transition-opacity duration-[1000ms] ease-in-out motion-reduce:transition-none"
                {{ $loop->first ? 'data-te-carousel-active' : null }}>

                <!-- Background Image -->
                <div class="absolute inset-0">
                    <img src="{{ asset($item->category_image_url) }}"
                        class="block w-full h-full object-cover transform scale-100 group-hover:scale-105 transition-transform duration-[2000ms] ease-out opacity-100"
                        alt="{{ $item->title }}" />
                </div>

                <!-- Content Container -->
                <div class="absolute inset-0 flex flex-col justify-center px-8 md:px-16 lg:px-24 max-w-4xl">

                    <!-- Title Area -->
                    <div class="mb-4">
                        <div
                            class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-wider text-emerald-400 uppercase bg-emerald-900/30 border border-emerald-500/30 rounded-full backdrop-blur-sm">
                            Destacado
                        </div>
                        <div class="text-white">
                            @include('livewire.home.hero.title')
                        </div>
                    </div>

                    <!-- Body Preview -->
                    <div class="hidden sm:block mb-6">
                        <div
                            class="text-gray-200 text-sm md:text-lg leading-relaxed line-clamp-3 text-shadow-sm max-w-2xl bg-gray-900/30 p-4 rounded-lg backdrop-blur-sm border-l-4 border-emerald-500">
                            {!! strip_tags($item->body) !!}
                        </div>
                    </div>

                    <!-- Footer / Actions -->
                    <div>
                        @include('livewire.home.hero.footer')
                    </div>

                </div>
            </div>
        @endforeach
    </div>

</div>

@includeWhen($modalShow, 'livewire.home.modal.post')
