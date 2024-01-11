<header class="bg-blue-500 text-white">

    <div class="container mx-auto p-4 flex justify-between items-center">

        <!-- Logo -->
        <a href="#">
            <img src="logo.svg" class="h-12">
        </a>

        <!-- Menú Hamburguesa -->
        <button class="block md:hidden focus:outline-none">
            <svg class="h-6 w-6 fill-current text-white" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z" />
            </svg>
        </button>

        <!-- Menú -->
        <nav class="hidden md:block">
            <ul class="flex space-x-8">
                <li><a href="#" class="hover:text-gray-200">Inicio</a></li>
                <li><a href="#" class="hover:text-gray-200">Acerca de</a></li>
                <li><a href="#" class="hover:text-gray-200">Programas</a></li>
                <li><a href="#" class="hover:text-gray-200">Contacto</a></li>
                <li>
                    <a href="#" class="bg-white text-blue-500 rounded-full px-4 py-2 hover:bg-gray-200">
                        Inscribirse
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Menú responsive -->
        <nav class="fixed inset-0 z-20 hidden p-8 bg-blue-800 md:hidden" id="menu">
            <ul class="flex flex-col space-y-6 text-2xl text-white">
                <li><a href="#" class="hover:text-gray-200">Inicio</a></li>
                <li><a href="#" class="hover:text-gray-200">Acerca de</a></li>
                <li><a href="#" class="hover:text-gray-200">Programas</a></li>
                <li><a href="#" class="hover:text-gray-200">Contacto</a></li>
                <li>
                    <a href="#"
                        class="bg-white text-blue-500 rounded-full px-4 py-2 hover:bg-gray-200 self-start">
                        Inscribirse
                    </a>
                </li>
            </ul>
        </nav>

    </div>

</header>
