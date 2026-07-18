@props(['subtitle' => 'Panel Administrativo'])

<header class="bg-gray-900/50 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-10 h-10 rounded-lg">
                <div class="hidden md:block">
                    <h2 class="text-lg font-bold text-white tracking-tight">SAEFL</h2>
                    <p class="text-xs text-emerald-400 font-medium">{{ $subtitle }}</p>
                </div>
            </div>

            <!-- Navigation (slot dinámico: cada rol inyecta sus items) -->
            <nav class="hidden lg:flex items-center space-x-1 mr-auto mx-2">
                {{ $leading ?? '' }}
                {{ $slot }}
            </nav>

            <!-- Navbar Info (inyectado por vistas hijas vía named slot) -->
            {{ $navbarInfo ?? '' }}

            <!-- User Profile -->
            <div class="flex items-center space-x-4 px-2 py-1 bg-gray-900/30 backdrop-blur-md rounded-lg">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-white">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-emerald-500">{{ Auth::user()->role_label }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-emerald-500/20 rounded-lg border border-white/5 transition-all duration-300 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
