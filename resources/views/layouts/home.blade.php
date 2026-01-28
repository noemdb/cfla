<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'UE Colegio Fray Luis Amigó'))</title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-47HF698FBL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-47HF698FBL');
    </script>

    <link rel="manifest" href="{{ asset('pwa/manifest.json') }}">

    <!-- Updated favicon to use Font Awesome stethoscope SVG -->
    {{-- <link rel="icon" type="image/svg+xml" --}}
    {{-- href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 640'><path fill='%2310b981' d='M64 112C64 85.5 85.5 64 112 64L160 64C177.7 64 192 78.3 192 96C192 113.7 177.7 128 160 128L128 128L128 256C128 309 171 352 224 352C277 352 320 309 320 256L320 128L288 128C270.3 128 256 113.7 256 96C256 78.3 270.3 64 288 64L336 64C362.5 64 384 85.5 384 112L384 256C384 333.4 329 398 256 412.8L256 432C256 493.9 306.1 544 368 544C429.9 544 480 493.9 480 432L480 346.5C442.7 333.3 416 297.8 416 256C416 203 459 160 512 160C565 160 608 203 608 256C608 297.8 581.3 333.4 544 346.5L544 432C544 529.2 465.2 608 368 608C270.8 608 192 529.2 192 432L192 412.8C119 398 64 333.4 64 256L64 112zM512 288C529.7 288 544 273.7 544 256C544 238.3 529.7 224 512 224C494.3 224 480 238.3 480 256C480 273.7 494.3 288 512 288z'/></svg>"> --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <wireui:scripts />
    @livewireStyles

    <!-- Meta tags para SEO -->
    <meta name="description" content="Evalúa tus conocimientos académicos con nuestro sistema de diagnóstico">

    @yield('styles')

    <style>
        :root {
            --primary-green: #064e3b;
            --secondary-green: #065f46;
            --accent-green: #10b981;
            --dark-bg: #111827;
            --card-bg: #1f2937;
        }

        .truncate-sm {
            @apply text-overflow-ellipsis sm:hidden;
        }

        /* Animaciones suaves para cards */
        .diagnostic-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .diagnostic-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.15);
        }

        .diagnostic-card:active {
            transform: translateY(-2px);
        }

        /* Gradientes personalizados */
        .gradient-diagnostic-primary {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
        }

        .gradient-diagnostic-secondary {
            background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%);
        }

        .gradient-diagnostic-card {
            background: linear-gradient(135deg, #1f2937 0%, #064e3b 30%, #1f2937 100%);
        }

        /* Efectos de brillo */
        .glow-emerald {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: #059669;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
    </style>
</head>

<body
    class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gradient-to-br dark:from-gray-900 dark:via-emerald-900 dark:to-gray-900 min-h-screen dark:text-gray-100 transition-colors duration-300">

    <x-notifications position="top-center" />

    <!-- Header -->
    <header
        class="bg-white/90 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-emerald-800/30 sticky top-0 z-10 shadow-lg transition-colors duration-300">
        <div class="container-fluid mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo y título principal -->
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('image/logo/logo1x1.png') }}" alt="{{ config('app.name') }} Logo"
                            class="w-12 h-12 md:w-16 md:h-16 rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 object-contain">
                    </div>

                    <div class="flex flex-col">
                        <h1 class="text-sm sm:text-lg md:text-2xl font-bold text-white">U.E. COLEGIO FRAY LUIS AMIGÓ
                        </h1>
                        <p class="hidden sm:block text-sm text-emerald-300"><strong>Excelencia Educativa.</strong>
                            Formando el
                            futuro</p>
                    </div>

                </div>

                <!-- Navigation Area -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Inicio</a>
                    <a href="{{ route('home') }}#services"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Servicios</a>

                    <!-- SAEFL Dropdown Trigger (Simplified for layout) -->
                    <div class="relative group">
                        <button
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors flex items-center">
                            SAEFL
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div
                            class="absolute right-0 w-48 mt-0 origin-top-right bg-gray-900 border border-emerald-800/50 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden group-hover:block z-50">
                            <div class="py-1">
                                <a href="{{ env('APP_URL_SAEFL') }}"
                                    class="block px-4 py-2 text-sm text-gray-300 hover:bg-emerald-800/50 hover:text-white">Escritorio</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('home') }}#contacts"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Contáctanos</a>

                    <button id="installPWA" style="display: none;"
                        class="px-3 py-2 rounded-md text-sm font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-900/50 transition-colors">
                        Instalar App
                    </button>

                    <a href="{{ route('home') }}#featured"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Acerca
                        de...</a>


                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-300 hover:text-white focus:outline-none p-2"
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-900/95 backdrop-blur-sm border-t border-emerald-800/30">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50">Inicio</a>
                <a href="{{ route('home') }}#services"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50">Servicios</a>
                <a href="{{ env('APP_URL_SAEFL') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50">SAEFL</a>
                <a href="{{ route('home') }}#contacts"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50">Contáctanos</a>
                <button id="installPWAMobile" style="display: none;"
                    class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-900/50">
                    Instalar la App
                </button>
                <a href="{{ route('home') }}#featured"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50">Acerca
                    de...</a>
            </div>
        </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer
        class="bg-white/90 dark:bg-gray-900/60 backdrop-blur-sm border-t border-gray-200 dark:border-emerald-800/30 mt-auto transition-colors duration-300">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center space-y-3">
                <!-- Copyright -->
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} <strong>SAEFL</strong> @noemdb | Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    @yield('customScripts')
    @livewireScripts
    @yield('scriptsLivewire')

    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (event) => {
            // event.preventDefault(); // Evita que el navegador muestre el banner de instalación automático
            deferredPrompt = event;

            // Muestra el botón de instalación en desktop y mobile
            const installButton = document.getElementById('installPWA');
            const installButtonMobile = document.getElementById('installPWAMobile');

            if (installButton) {
                installButton.style.display = 'block';
            }
            if (installButtonMobile) {
                installButtonMobile.style.display = 'block';
            }

            // Event listener para el botón desktop
            if (installButton) {
                installButton.addEventListener('click', () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt(); // Muestra el diálogo de instalación

                        deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('El usuario aceptó la instalación');
                            } else {
                                console.log('El usuario canceló la instalación');
                            }
                            deferredPrompt = null;
                        });
                    }
                });
            }

            // Event listener para el botón mobile
            if (installButtonMobile) {
                installButtonMobile.addEventListener('click', () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt(); // Muestra el diálogo de instalación

                        deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('El usuario aceptó la instalación');
                            } else {
                                console.log('El usuario canceló la instalación');
                            }
                            deferredPrompt = null;
                        });
                    }
                });
            }
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA instalada');
            const installButton = document.getElementById('installPWA');
            const installButtonMobile = document.getElementById('installPWAMobile');

            if (installButton) {
                installButton.style.display = 'none';
            }
            if (installButtonMobile) {
                installButtonMobile.style.display = 'none';
            }
        });

        // Auto-scroll suave para navegación
        function smoothScrollToElement(element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    </script>
</body>

</html>
