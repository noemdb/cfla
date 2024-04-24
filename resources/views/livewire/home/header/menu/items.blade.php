<ul class="mr-auto flex flex-col md:flex-row text-green-200 items-end" data-te-navbar-nav-ref>
    <li class="mb-4 md:mb-0 md:pr-2 border-b-2 border-green-200" data-te-nav-item-ref>
        <a class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90"
            href="#!" data-te-nav-link-ref data-te-ripple-init data-te-ripple-color="light">Inicio</a>
    </li>
    <li class="mb-4 md:mb-0 md:pr-2 border-b-2 border-green-200" data-te-nav-item-ref>
        <a class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90"
            href="#services" data-te-nav-link-ref data-te-ripple-init
            data-te-ripple-color="light">Servicios</a>
    </li>
    
    <li class="mb-4 md:mb-0 md:pr-2 border-b-2 border-green-200" data-te-nav-item-ref>
        {{-- <a class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90" href="#featured" data-te-nav-link-ref data-te-ripple-init data-te-ripple-color="light">SAEFL</a> --}}
        <x-dropdown>
            <x-slot name="trigger">
                {{-- <x-button label="SAEFL" success /> --}}
                <div class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90">
                    SAEFL
                </div>
            </x-slot>      
            <x-dropdown.item label="Escritorio" icon="desktop-computer" href="{{env('APP_URL_SAEFL')}}"/>
            <x-dropdown.item separator label="Móviles" icon="device-tablet" href="{{env('APP_URL_SAEFL').'/movile/android/welcome'}}"/>
        </x-dropdown>
    </li>

    <li class="mb-4 md:mb-0 md:pr-2 border-b-2 border-green-200" data-te-nav-item-ref>
        <a class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90"
            href="#contacts" data-te-nav-link-ref data-te-ripple-init data-te-ripple-color="light">Contáctanos</a>
    </li>

    <li class="mb-4 md:mb-0 md:pr-2 border-b-2 border-green-200" data-te-nav-item-ref>
        <a class="block transition duration-150 ease-in-out hover:text-green-700 focus:text-green-200 disabled:text-black/30 dark:hover:text-white dark:focus:text-white md:p-2 [&.active]:text-black/90"
            href="#featured" data-te-nav-link-ref data-te-ripple-init data-te-ripple-color="light">Acerca de ...</a>
    </li>

    {{-- <li class="mb-2 md:mb-0 md:pr-2" data-te-nav-item-ref>
        <a href="#"
            class="text-gray-300 hover:bg-green-700 hover:text-green px-3 py-2 rounded-md text-sm font-medium"
            type="button" data-te-offcanvas-toggle data-te-target="#offcanvasExample"
            aria-controls="offcanvasExample" data-te-ripple-init data-te-ripple-color="light">
            Más.
        </a>
    </li> --}}
</ul>