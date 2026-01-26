<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Votación - ' . config('app.name', 'Módulo de Votación'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='2'><path stroke-linecap='round' stroke-linejoin='round' d='M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'/></svg>">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles

    <!-- Meta tags para SEO y compartir -->
    <meta name="description" content="Participa en esta votación de forma segura y anónima">
    <meta name="robots" content="noindex, nofollow">

    <!-- Prevenir zoom en móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    @yield('style')
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-gray-900 via-green-900 to-gray-900 min-h-screen text-gray-100">
    <!-- Header minimalista -->
    <header class="bg-gray-900/80 backdrop-blur-sm border-b border-green-800/30 sticky top-0 z-10 shadow-lg">
        <div class="container-fluid mx-auto px-4 py-2">
            <div class="flex items-center justify-center">
                <div
                    class="text-lg text-center md:text-3xl font-bold text-white mb-2 drop-shadow-2xl uppercase border-b-2 border-green-900 py-2">
                    U.E. Colegio Fray Luis Amigó</div>
                <div class="flex items-center space-x-3">
                    <!-- Logo -->
                    <div class="mb-2">
                        <img src="{{ asset('image/brand/512.png') }}" alt="{{ config('app.name') }} Logo"
                            class="w-32 max-w-full sm:w-18 md:w-24 h-auto rounded-2xl shadow-2xl p-2 transition-transform duration-300 hover:scale-105 object-contain">
                    </div>

                    <div>
                        <h1 class="text-lg font-bold text-white">Módulo de Votación</h1>
                        <p class="text-xs text-emerald-300">Votación segura y anónima</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1 py-4">
        @yield('content')
    </main>

    <!-- Footer minimalista -->
    <footer class="bg-gray-900/60 backdrop-blur-sm border-t border-green-800/30 mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center space-y-2">
                <div class="flex items-center justify-center space-x-6 text-sm text-emerald-300">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <span>Votación segura</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <span>Anónimo</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        <span>Un voto por persona</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} <strong>SAEFL</strong> Módulo de Votación. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    <x-notifications />

    <!-- Scripts específicos para votación -->
    <script>
        // Prevenir refresh accidental
        let hasVoted = false;

        window.addEventListener('beforeunload', function(e) {
            if (!hasVoted && document.querySelector('input[name="option_id"]:checked')) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Tu selección se perderá.';
            }
        });

        // Marcar como votado cuando se envía el formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action*="voting"]');
            if (form) {
                form.addEventListener('submit', function() {
                    hasVoted = true;
                });
            }
        });

        // Auto-scroll suave para móviles
        function smoothScrollToElement(element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Mejorar experiencia táctil en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const radioLabels = document.querySelectorAll('label[for*="option"]');
            radioLabels.forEach(label => {
                label.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });

                label.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>

    @yield('script')

    <!-- Estilos adicionales para la experiencia de votación -->
    <style>
        /* Animaciones suaves */
        .vote-option {
            transition: all 0.3s ease-in-out;
        }

        .vote-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        }

        .vote-option:active {
            transform: translateY(0);
        }

        /* Mejorar la experiencia táctil */
        @media (max-width: 768px) {
            .vote-option {
                min-height: 60px;
                display: flex;
                align-items: center;
            }

            input[type="radio"] {
                width: 20px;
                height: 20px;
            }
        }

        /* Animación de carga */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Efectos de focus mejorados */
        input[type="radio"]:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
        }

        /* Gradiente animado para el fondo */
        body {
            background-attachment: fixed;
        }

        /* Gradientes personalizados para tema oscuro */
        .gradient-dark-green-primary {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
        }

        .gradient-dark-green-secondary {
            background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%);
        }

        .gradient-dark-green-accent {
            background: linear-gradient(135deg, #1f2937 0%, #064e3b 50%, #1f2937 100%);
        }

        /* Efectos de brillo sutil */
        .glow-green {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
        }

        .glow-green-strong {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
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
</body>

</html>
