<header>
    <!-- Navigation bar -->
    <nav class="relative flex w-full items-center justify-between bg-green-900 text-green-200 py-2 shadow-lg hover:text-neutral-700 focus:text-neutral-700 dark:bg-neutral-600 dark:text-neutral-200 md:flex-wrap md:justify-start"
        data-te-navbar-ref>
        <div class="flex w-full flex-wrap items-center justify-start px-3">
            <div class="flex items-center">
                <!-- Hamburger menu button -->
                <button
                    class="text-green-200 border-0 bg-transparent px-2 text-xl leading-none transition-shadow duration-150 ease-in-out hover:text-neutral-700 focus:text-white dark:hover:text-white dark:focus:text-white md:hidden"
                    type="button" data-te-collapse-init data-te-target="#navbarSupportedContentY"
                    aria-controls="navbarSupportedContentY" aria-expanded="false" aria-label="Toggle navigation">
                    <!-- Hamburger menu icon -->
                    <span class="[&>svg]:w-5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-7 w-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Navigation links -->
            <div class="flex-shrink-0 mb-2 mr-2">
                <!-- Logo de tu aplicación -->
                {{-- @include('home.header.icon') --}}
            </div>
            <div class="!visible hidden grow basis-[100%] items-center md:!flex md:basis-auto" id="navbarSupportedContentY" data-te-collapse-item>
                @include('header.menu.items')                                
            </div>
        </div>
    </nav>
    
</header>