<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'SAEFL') }} — Asistente Virtual</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <wireui:scripts />
    @livewireStyles

    <style>
        /* Scrollbar personalizado para el chat */
        #chat-messages::-webkit-scrollbar {
            width: 4px;
        }
        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }
        #chat-messages::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 2px;
        }
        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* Animación de typing (3 dots) */
        .typing-dot {
            animation: typingBounce 1.4s infinite ease-in-out both;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        .typing-dot:nth-child(3) { animation-delay: 0s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* Animación de entrada de mensajes */
        .message-enter {
            animation: messageSlide 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Feedback táctil en botones */
        .touch-btn {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        .touch-btn:active {
            transform: scale(0.94);
        }

        /* Evitar zoom automático en iOS Safari */
        input, textarea, select {
            font-size: 16px !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-900 text-gray-100 min-h-screen flex flex-col">
    <x-notifications position="top-center" />

    <div class="flex-1 flex flex-col max-w-2xl mx-auto w-full h-screen" x-data>
        <!-- Header compacto -->
        <div class="bg-emerald-600 px-3 py-1.5 flex items-center gap-2 flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-emerald-500/30 flex items-center justify-center text-base flex-shrink-0">
                🤖
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-semibold text-white truncate">Asistente SAEFL</h1>
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                    <span class="text-xs text-emerald-200">En línea</span>
                </div>
            </div>
            <a href="/" class="text-white/80 hover:text-white transition-colors p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <!-- Contenido del Livewire -->
        <main class="flex-1 flex flex-col min-h-0">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
