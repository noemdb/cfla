<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ' . config('app.name', 'SIA'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 640'><path fill='%2310b981' d='M64 112C64 85.5 85.5 64 112 64L160 64C177.7 64 192 78.3 192 96C192 113.7 177.7 128 160 128L128 128L128 256C128 309 171 352 224 352C277 352 320 309 320 256L320 128L288 128C270.3 128 256 113.7 256 96C256 78.3 270.3 64 288 64L336 64C362.5 64 384 85.5 384 112L384 256C384 333.4 329 398 256 412.8L256 432C256 493.9 306.1 544 368 544C429.9 544 480 493.9 480 432L480 346.5C442.7 333.3 416 297.8 416 256C416 203 459 160 512 160C565 160 608 203 608 256C608 297.8 581.3 333.4 544 346.5L544 432C544 529.2 465.2 608 368 608C270.8 608 192 529.2 192 432L192 412.8C119 398 64 333.4 64 256L64 112zM512 288C529.7 288 544 273.7 544 256C544 238.3 529.7 224 512 224C494.3 224 480 238.3 480 256C480 273.7 494.3 288 512 288z'/></svg>">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles

    <!-- Meta tags -->
    <meta name="description" content="Dashboard administrativo U.E. COLEGIO FRAY LUIS AMIGÓ">
    <meta name="robots" content="noindex, nofollow">

    @yield('styles')
</head>

<body
    class="bg-[#020617] text-gray-100 font-sans antialiased min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-950/30 via-gray-950 to-black selection:bg-emerald-500 selection:text-white bg-fixed flex flex-col">

    <div class="relative z-[100]">
        <x-notifications />
    </div>

    <!-- Navbar -->
    <header class="bg-gray-900/50 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-10 h-10 rounded-lg">
                    <div class="hidden md:block">
                        <h2 class="text-lg font-bold text-white tracking-tight">SAEFL</h2>
                        <p class="text-xs text-emerald-400 font-medium">Panel Administrativo</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="hidden lg:flex items-center space-x-6">
                    <a href="{{ route('admin.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.index') ? 'text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }} transition-colors">Dashboard</a>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.voting.dashboard') }}" class="text-sm font-medium text-gray-400 hover:text-emerald-300 transition-colors">Votaciones</a>
                        <a href="{{ url('admin/logs') }}" class="text-sm font-medium text-gray-400 hover:text-emerald-300 transition-colors">Logs</a>
                    @endif
                </nav>

                <!-- User Profile -->
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->username }}</p>
                        <p class="text-xs text-emerald-500">{{ Auth::user()->is_admin ? 'Administrador' : 'Usuario' }}</p>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-emerald-500/20 rounded-xl border border-white/5 transition-all duration-300 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/50 backdrop-blur-md border-t border-white/5 mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-center md:text-left">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} <strong>SAEFL</strong> @noemdb | Gestión Integral Educativa.
                </p>
                <div class="flex space-x-6 text-xs text-emerald-500/60 font-medium">
                    <span>Sistema Seguro</span>
                    <span>Versión 2.0</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @yield('script')

    <style>
        .diagnostic-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .diagnostic-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }

        /* Ensure notifications are above sticky navbar */
        #notifications, 
        .wireui-notifications,
        [fixed] {
            z-index: 9999 !important;
        }
    </style>
</body>

</html>
