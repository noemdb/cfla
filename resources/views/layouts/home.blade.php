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

    <!-- Favicon único SAEFL -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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
                            class="w-12 h-12 md:w-16 md:h-16 rounded-lg shadow-lg transition-transform duration-300 hover:scale-105 object-contain">
                    </div>

                    <div class="flex flex-col">
                        <h1 class="text-sm sm:text-lg md:text-lg font-bold text-white">U.E. COLEGIO FRAY LUIS AMIGÓ
                        </h1>
                        <p class="hidden sm:block text-sm text-emerald-400">
                            <strong>Excelencia Educativa.</strong>
                            Formando el futuro
                        </p>
                    </div>

                </div>

                <!-- Navigation Area -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Inicio</a>
                    <a href="{{ route('home') }}#services"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-emerald-800/50 transition-colors">Servicios</a>
                    <a href="{{ route('bot.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-800/50 transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-4 0H9v2h2V9z" clip-rule="evenodd"/>
                        </svg>
                        Asistente
                    </a>

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
                                <a href="{{ route('login') }}"
                                    class="block px-4 py-2 text-sm text-gray-300 hover:bg-emerald-800/50 hover:text-white">Mod. planificación</a>
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
                <a href="{{ route('bot.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-800/50">💬 Asistente</a>
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

    <!-- Botón flotante del Chatbot -->
    <a href="{{ route('bot.index') }}"
       class="fixed bottom-6 right-6 z-[9999] w-14 h-14 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-900/40 hover:shadow-xl hover:shadow-emerald-900/50 hover:scale-110 active:scale-95 transition-all duration-200"
       aria-label="Abrir chat asistente"
       title="Asistente Virtual">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-4 0H9v2h2V9z" clip-rule="evenodd"/>
        </svg>
    </a>
</body>

</html>
