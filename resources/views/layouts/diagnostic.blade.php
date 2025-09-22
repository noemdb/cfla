<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Diagnóstico Académico - ' . config('app.name', 'Sistema de Diagnóstico'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='2'><path stroke-linecap='round' stroke-linejoin='round' d='M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z'/><circle cx='9' cy='9' r='2'/><path d='M13 19c0 1.105.895 2 2 2s2-.895 2-2-2-4.5-2-4.5S13 17.895 13 19z'/></svg>">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles

    <!-- Meta tags para SEO -->
    <meta name="description" content="Evalúa tus conocimientos académicos con nuestro sistema de diagnóstico">
    <meta name="robots" content="noindex, nofollow">

    <!-- Prevenir zoom en móviles -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    @yield('styles')
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-gray-900 via-emerald-900 to-gray-900 min-h-screen text-gray-100">

    <x-notifications />

    <!-- Header -->
    <header class="bg-gray-900/80 backdrop-blur-sm border-b border-emerald-800/30 sticky top-0 z-10 shadow-lg">
        <div class="container-fluid mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo y título principal -->
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('image/brand/512.png') }}" alt="{{ config('app.name') }} Logo"
                            class="w-12 h-12 md:w-16 md:h-16 rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 object-contain">
                    </div>

                    <div class="flex flex-col">
                        <h1 class="text-lg md:text-2xl font-bold text-white">C.E. COLEGIO FRAY LUIS AMIGÓ</h1>
                        <p class="sm:block text-sm text-emerald-300"><strong>Diagnóstico Educativo.</strong> Evaluación
                            académica personalizada</p>
                    </div>
                </div>

                <!-- Información del usuario -->
                @auth
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-emerald-300">{{ Auth::user()->estudiant->ci_estudiant ?? 'Estudiante' }}
                            </p>
                        </div>

                        <div class="relative">
                            <button
                                class="flex items-center space-x-2 bg-emerald-800/50 hover:bg-emerald-700/50 px-3 py-2 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-white">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/60 backdrop-blur-sm border-t border-emerald-800/30 mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center space-y-3">
                <!-- Características del sistema -->
                <div class="flex items-center justify-center space-x-6 text-sm text-emerald-300 flex-wrap gap-2">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span>Evaluación personalizada</span>
                    </div>
                    {{-- <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z">
                            </path>
                        </svg>
                        <span>Resultados inmediatos</span>
                    </div> --}}
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2H6a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Datos seguros</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Progreso detallado</span>
                    </div>
                </div>

                <!-- Copyright -->
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} <strong>SAEFL</strong> @noemdb | Módulo de Diagnóstico Educativo. Todos los
                    derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    <x-notifications />

    <!-- Scripts específicos para diagnóstico -->
    <script>
        // Prevenir pérdida de progreso accidental
        let hasAnswers = false;
        let isSubmitting = false;

        window.addEventListener('beforeunload', function(e) {
            if (hasAnswers && !isSubmitting) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Tu progreso se perderá.';
            }
        });

        // Detectar cuando hay respuestas
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll(
                'input[type="radio"], input[type="checkbox"], textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    hasAnswers = true;
                });
            });

            // Marcar como enviando cuando se envía el formulario
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    isSubmitting = true;
                });
            });
        });

        // Auto-scroll suave para navegación
        function smoothScrollToElement(element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Mejorar experiencia táctil en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const interactiveElements = document.querySelectorAll('.diagnostic-card, .question-option, button');
            interactiveElements.forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });

                element.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        // Función para actualizar progreso visual
        function updateProgressBar(percentage) {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                bar.style.width = percentage + '%';
            });
        }

        // Auto-save para respuestas largas
        let autoSaveTimeout;

        function autoSaveAnswer(questionId, answer) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // Implementar auto-save via Livewire
                if (window.Livewire) {
                    window.Livewire.emit('autoSaveAnswer', questionId, answer);
                }
            }, 2000);
        }
    </script>

    @yield('script')

    <!-- Estilos adicionales para diagnóstico -->
    <style>
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

        /* Opciones de preguntas */
        .question-option {
            transition: all 0.2s ease-in-out;
        }

        .question-option:hover {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .question-option.selected {
            background-color: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }

        /* Mejorar experiencia móvil */
        @media (max-width: 768px) {
            .question-option {
                min-height: 60px;
                display: flex;
                align-items: center;
                padding: 1rem;
            }

            input[type="radio"],
            input[type="checkbox"] {
                width: 20px;
                height: 20px;
            }

            .diagnostic-card {
                margin-bottom: 1rem;
            }
        }

        /* Animaciones de progreso */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        .progress-ring {
            transition: stroke-dasharray 0.5s ease-in-out;
        }

        /* Loading spinner */
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

        /* Pulse animation para elementos importantes */
        .pulse-green {
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
        }

        /* Focus mejorado para accesibilidad */
        input:focus,
        button:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid #10b981;
            outline-offset: 2px;
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

        .glow-emerald-strong {
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

        /* Animación de entrada para elementos */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Efectos para botones */
        .btn-diagnostic {
            background: linear-gradient(135deg, #059669, #10b981);
            transition: all 0.3s ease;
        }

        .btn-diagnostic:hover {
            background: linear-gradient(135deg, #047857, #059669);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        }

        .btn-diagnostic:active {
            transform: translateY(0);
        }
    </style>
</body>

</html>
