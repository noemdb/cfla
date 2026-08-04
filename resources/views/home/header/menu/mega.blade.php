<!-- TW Elements is free under AGPL, with commercial license required for specific uses. See more details: https://tw-elements.com/license/ and contact us for queries at tailwind@mdbootstrap.com -->
<nav class="relative flex w-full items-center justify-between bg-neutral-50 py-2 text-neutral-600 shadow-lg dark:bg-neutral-700 dark:text-neutral-300 dark:shadow-black/5 lg:flex-wrap lg:justify-start"
    data-te-navbar-ref>
    <div class="px-6">
        <button
            @click="open = true"
            @keydown.escape.window="open = false"
            x-ref="menuButton"
            class="border-0 bg-transparent py-2 text-lg leading-none transition-shadow duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 dark:hover-text-white dark:focus:text-white lg:hidden"
            type="button"
            aria-controls="navbarSupportedContentX"
            aria-expanded="false"
            aria-label="Toggle navigation"
            :aria-expanded="String(open)"
            >
            <span class="[&>svg]:w-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-8 w-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </span>
        </button>
        <div class="!visible hidden flex-grow basis-[100%] items-center lg:!flex lg:basis-auto"
            id="navbarSupportedContentX" data-te-collapse-item>
            <ul class="mr-auto flex flex-row" data-te-navbar-nav-ref>
                <li data-te-nav-item-ref>
                    <a class="block py-2 pr-2  transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 dark:hover-text-white dark:focus:text-white lg:px-2"
                        href="#!" data-te-ripple-init data-te-ripple-color="light">Regular link</a>
                </li>
                <li class="static" data-te-nav-item-ref data-te-dropdown-ref>
                    <!-- New Drawer Trigger -->
                    <button @click="open = true"
                            @keydown.escape.window="open = false"
                            class="flex items-center whitespace-nowrap py-2 pr-2  transition duration-150 ease-in-out hover:text-neutral-700 focus:text-neutral-700 dark:hover-text-white dark:focus:text-white lg:px-2 relative"
                            type="button"
                            id="drawerMenuButton"
                            aria-controls="drawerMenu"
                            aria-expanded="false"
                            aria-label="Open main menu"
                            :aria-expanded="String(open)"
                            >
                        <span class="flex items-center">
                            <span class="mr-2">Menú</span>
                            <span class="[&>svg]:w-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </span>
                        </span>
                        <!-- Badge for notifications (if any) -->
                        <template x-if="unreadCount">
                            <span class="absolute -top-1 -right-2 flex h-3 w-3 items-center justify-center text-xs font-bold rounded-full bg-red-500 text-white">
                                <span x-text="unreadCount"></span>
                            </span>
                        </template>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Drawer Panel -->
<div x-data="{ open: false, unreadCount: 0 }"
     x-on:keyup.escape.window="open = false"
     x-on:click.outside="if ($event.target === $el) open = false"
     class="fixed inset-0 z-50 pointer-events-none">
    <!-- Backdrop -->
    <div @click="open = false"
         @keydown.escape="open = false"
         class="pointer-events-auto fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-200"
         :class="{ 'opacity-0 pointer-events-none': !open, 'opacity-100 pointer-events-auto': open }"></div>
    <!-- Drawer Content -->
    <aside class="pointer-events-auto flex w-full md:w-64 lg:w-[400px] bg-white border-l border-gray-200 shadow-lg transform transition-transform duration-200 ease-in-out"
           :class="{ '-translate-x-full': !open, 'translate-x-0': open }"
           :aria-hidden="!open"
           role="dialog"
           aria-modal="true">
        <!-- Drawer Header -->
        <div class="flex flex-col p-4 md:p-5 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    Menú Principal
                </h2>
                <button @click="open = false"
                        @keydown.escape="open = false"
                        class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-100"
                        aria-label="Close menu">
                    <x-heroicon-m::x-mark class="h-5 w-5" />
                </button>
            </div>
            <!-- User info (only when authenticated) -->
            @auth
                <div class="mt-3 flex items-center space-x-3">
                    <img src="{{ asset('storage/avatars/'.auth()->user()->foto_perfil ?? 'default.png') }}"
                         alt="Avatar"
                         class="h-10 w-10 rounded-full object-cover"
                         onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                    <div>
                        <p class="font-medium text-gray-900">{{ auth()->user()->nombre }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 p-4 md:p-5 overflow-y-auto space-y-6">
            <div class="gap-4 md:gap-6">
                <!-- Column 1: Navigation -->
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-800 mb-2">Navegación</h3>
                    <nav class="space-y-2">
                        <a href="{{ route('home.index') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150"
                           :class="{ 'font-medium text-blue-600': request()->routeIs('home.index') }">
                            <x-heroicon-m::home class="h-4 w-4 mr-3" />
                            <span>Inicio</span>
                        </a>
                        <a href="{{ route('censo.index') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150">
                            <x-heroicon-m::user-group class="h-4 w-4 mr-3" />
                            <span>Censo</span>
                        </a>
                        <a href="{{ route('matricula.index') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150">
                            <x-heroicon-m::clipboard-list class="h-4 w-4 mr-3" />
                            <span>Matrícula</span>
                        </a>
                        <a href="{{ route('pagos.index') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150">
                            <x-heroicon-m::credit-card class="h-4 w-4 mr-3" />
                            <span>Pagos</span>
                        </a>
                        <a href="{{ path('/notas') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150">
                            <x-heroicon-m::chart-bar class="h-4 w-4 mr-3" />
                            <span>Notas</span>
                        </a>
                        <a href="{{ path('/asistencia') }}"
                           class="flex items-center px-3 py-2 rounded hover:bg-gray-50 transition-colors duration-150">
                            <x-heroicon-m::calendar-check class="h-4 w-4 mr-3" />
                            <span>Asistencia</span>
                        </a>
                    </nav>
                </div>

                <!-- Column 2: Quick Actions -->
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-800 mb-2">Acciones rápidas</h3>
                    <div class="space-y-2">
                        <button @click="open = false"
                                class="w-full flex items-center justify-start px-4 py-3 rounded hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <x-heroicon-m::plus class="h-5 w-5 mr-4" />
                            <span class="flex-1 text-left">Nuevo Estudiante</span>
                        </button>
                        <button @click="open = false"
                                class="w-full flex items-center justify-start px-4 py-3 rounded hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <x-heroicon-m::calendar-days class="h-5 w-5 mr-4" />
                            <span class="flex-1 text-left">Calendario Escolar</span>
                        </button>
                        <button @click="open = false"
                                class="w-full flex items-center justify-start px-4 py-3 rounded hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <x-heroicon-m::document-text class="h-5 w-5 mr-4" />
                            <span class="flex-1 text-left">Generar Reportes</span>
                        </button>
                        <button @click="open = false"
                                class="w-full flex items-center justify-start px-4 py-3 rounded hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <x-heroicon-m::banknotes class="h-5 w-5 mr-4" />
                            <span class="flex-1 text-left">Registrar Pago</span>
                        </button>
                    </div>
                </div>

                <!-- Column 3: Information/User Stats -->
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-800 mb-2">Información</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                            <x-heroicon-m::chart-bar class="h-5 w-5 text-blue-500" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">Estudiantes activos</p>
                                <p class="text-xs text-gray-500">1,234</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                            <x-heroicon-m::banknotes class="h-5 w-5 text-green-500" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">Pagos pendientes</p>
                                <p class="text-xs text-gray-500">23</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                            <x-heroicon-m::bell class="h-5 w-5 text-yellow-500" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">Notificaciones</p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="unreadCount">0</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                            <x-heroicon-m::user-group class="h-5 w-5 text-purple-500" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">Docentes activos</p>
                                <p class="text-xs text-gray-500">89</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optional Footer -->
        <div class="p-4 md:p-5 border-t">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>Versión 1.0.0</span>
                <a href="#" class="hover:text-gray-700">Acerca de</a>
            </div>
        </div>
    </aside>
</div>