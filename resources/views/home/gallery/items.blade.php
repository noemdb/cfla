<div
    class="diagnostic-card bg-gray-900/40 backdrop-blur-sm border border-emerald-500/30 rounded-xl p-4 md:p-6 shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500">
    <div id="carouselExampleGallery" class="relative group" data-te-carousel-init data-te-ride="carousel">

        <!-- Indicators -->
        <div class="absolute bottom-0 left-0 right-0 z-[2] mx-[15%] mb-4 flex list-none justify-center p-0"
            data-te-carousel-indicators>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="0" data-te-carousel-active
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="1"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 2"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="2"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 3"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="3"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 4"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="4"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 5"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="5"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 6"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="6"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 7"></button>
            <button type="button" data-te-target="#carouselExampleGallery" data-te-slide-to="7"
                class="mx-[3px] box-content h-[3px] w-[30px] flex-initial cursor-pointer border-0 border-y-[10px] border-solid border-transparent bg-emerald-500 bg-clip-padding p-0 -indent-[999px] opacity-50 transition-opacity duration-[200ms] ease-[cubic-bezier(0.25,0.1,0.25,1.0)] motion-reduce:transition-none aria-current:opacity-100"
                aria-label="Slide 8"></button>
        </div>

        <!--Carousel items-->
        <div class="relative w-full overflow-hidden after:clear-both after:block after:content-[''] rounded-xl">
            @for ($i = 1; $i <= 8; $i++)
                <div class="relative float-left -mr-[100%] hidden w-full transition-transform duration-[600ms] ease-in-out motion-reduce:transition-none"
                    @if ($i == 1) data-te-carousel-active @endif data-te-carousel-item
                    style="backface-visibility: hidden">
                    <div class="relative overflow-hidden bg-cover bg-no-repeat rounded-xl group/item"
                        style="background-position: 50%">
                        <img data-te-lazy-src="{{ asset('image/gallery/' . $i . '.jpg') }}" data-te-lazy-load-init
                            class="block w-full rounded-xl border-2 border-emerald-500/20 shadow-lg transform group-hover/item:scale-105 transition-transform duration-700 ease-out" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-60">
                        </div>

                        <!-- Overlay content -->
                        <div
                            class="absolute bottom-10 left-0 right-0 text-center opacity-0 group-hover/item:opacity-100 transition-opacity duration-500">
                            <span
                                class="bg-emerald-900/80 backdrop-blur-md text-emerald-100 px-4 py-2 rounded-full border border-emerald-500/40 text-sm font-semibold shadow-lg">
                                Explorar Instalaciones
                            </span>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Controls -->
        <button
            class="absolute bottom-0 left-0 top-0 z-[1] flex w-[10%] items-center justify-center border-0 bg-gradient-to-r from-gray-900/60 to-transparent p-0 text-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:text-white hover:no-underline hover:outline-none focus:text-white focus:no-underline focus:opacity-90 focus:outline-none motion-reduce:transition-none rounded-l-xl"
            type="button" data-te-target="#carouselExampleGallery" data-te-slide="prev">
            <span
                class="inline-block h-10 w-10 p-2 rounded-full bg-emerald-900/50 backdrop-blur-sm border border-emerald-500/30 hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="h-full w-full">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
            <span
                class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Previous</span>
        </button>
        <button
            class="absolute bottom-0 right-0 top-0 z-[1] flex w-[10%] items-center justify-center border-0 bg-gradient-to-l from-gray-900/60 to-transparent p-0 text-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:text-white hover:no-underline hover:outline-none focus:text-white focus:no-underline focus:opacity-90 focus:outline-none motion-reduce:transition-none rounded-r-xl"
            type="button" data-te-target="#carouselExampleGallery" data-te-slide="next">
            <span
                class="inline-block h-10 w-10 p-2 rounded-full bg-emerald-900/50 backdrop-blur-sm border border-emerald-500/30 hover:bg-emerald-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="h-full w-full">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
            <span
                class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Next</span>
        </button>
    </div>
</div>
