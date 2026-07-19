@props(['subtitle' => 'Panel Administrativo'])

<header x-data="{ mobileOpen: false }" class="bg-white/80 backdrop-blur-md border-b border-gray-200 dark:bg-gray-900/50 dark:border-white/5 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-4">
                <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-10 h-10 rounded-lg shrink-0">
                <div class="hidden md:block">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">SAEFL</h2>
                    <p class="text-xs text-emerald-400 font-medium">{{ $subtitle }}</p>
                </div>
            </a>

            <!-- Navigation (desktop: lg+) -->
            <nav class="hidden lg:flex items-center space-x-1 mr-auto mx-2">
                {{ $leading ?? '' }}
                {{ $slot }}
            </nav>

            <!-- Navbar Info (desktop) -->
            <div class="hidden lg:block">
                {{ $navbarInfo ?? '' }}
            </div>

            <!-- Right section: Profile + Theme Toggle + Hamburger -->
            <div class="flex items-center space-x-2">
                <!-- User Profile (sm+) -->
                <div class="hidden sm:flex items-center space-x-4 px-2 py-1 bg-gray-100/80 dark:bg-gray-900/30 backdrop-blur-md rounded-lg">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->username }}</p>
                        <p class="text-xs text-emerald-500">{{ Auth::user()->role_label }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="p-2 text-gray-500 hover:text-gray-900 bg-gray-100/50 hover:bg-emerald-100 dark:text-gray-400 dark:hover:text-white dark:bg-white/5 dark:hover:bg-emerald-500/20 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Theme Toggle (inline en el header, alineado a la derecha) --}}
                <x-theme-toggle />

                <!-- Hamburger button (mobile) -->
                <div class="lg:hidden flex items-center">
                    <button @click="mobileOpen = !mobileOpen"
                            class="p-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            :aria-expanded="mobileOpen"
                            aria-label="Abrir menú de navegación">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Backdrop (mobile) --}}
    <div x-show="mobileOpen" x-cloak
         @click="mobileOpen = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    {{-- Mobile menu panel --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="lg:hidden relative z-50 border-t border-gray-200 dark:border-white/5 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl shadow-xl shadow-gray-200/50 dark:shadow-black/50">
        <div class="container mx-auto px-4 py-4 space-y-3 max-h-[calc(100vh-4rem)] overflow-y-auto" id="mobile-nav-content">

            {{-- Leading items --}}
            @php($hasLeading = filled(trim((string)($leading ?? ''))))
            @if($hasLeading)
                <div class="space-y-1 pb-3 border-b border-gray-200 dark:border-white/5">
                    {{ $leading }}
                </div>
            @endif

            {{-- Nav items (mobile-specific accordions, sin x-transition) --}}
            <div class="space-y-1">
                {{ $mobileSlot }}
            </div>

            {{-- NavbarInfo (theme toggle, etc.) --}}
            @php($hasNavbarInfo = filled(trim((string)($navbarInfo ?? ''))))
            @if($hasNavbarInfo)
                <div class="pt-3 border-t border-gray-200 dark:border-white/5 flex items-center gap-3">
                    {{ $navbarInfo }}
                </div>
            @endif

            {{-- User profile (mobile) --}}
            <div class="pt-3 border-t border-gray-200 dark:border-white/5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-emerald-500">{{ Auth::user()->role_label }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-2 text-gray-500 hover:text-gray-900 bg-gray-100/50 hover:bg-emerald-100 dark:text-gray-400 dark:hover:text-white dark:bg-white/5 dark:hover:bg-emerald-500/20 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

